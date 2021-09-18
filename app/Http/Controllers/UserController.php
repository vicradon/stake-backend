<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Mail\RegisteredSupervisor;
use App\Mail\TestMail;
use App\Models\ProjectCategory;
use App\Models\Supervisor;
use App\Models\User;
use App\Models\Student;
use Exception;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

// use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $project_categories = implode(',', ProjectCategory::all()->pluck('name')->toArray());
        $request->validate([
            'role' => 'string|in:student,supervisor',
            'name' => 'string|max:255',
            'email' => 'string|email|max:255',
            'supervisor_category' => "in:{$project_categories}",
            'student_under_supervisor' => 'numeric'
        ]);

        $users = User::where('email', '!=', NULL);

        /* supervisors
            - by category
            - by name
            - by email
        */

        /* students of a supervisor
            - By accepted/rejected projects
            - By name
        */

        if ($request->has('role')) {
            if ($request->role === 'supervisor') {
                $users->where('profile_type', 'App\Models\Supervisor');
            } else if ($request->role === 'student') {
                $users->where('profile_type', 'App\Models\Student');
            }
        }
        if ($request->has('name')) {
            $users->where('name', 'like', "%{$request->name}%");
        }
        if ($request->has('email')) {
            $users->where('email', $request->email);
        }

        $users = $users->get();

        # supervisors with same project category
        if ($request->has('supervisor_category')) {
            global $project_category_id;
            $project_category_id = ProjectCategory::firstWhere('name', $request->supervisor_category)->id;

            $users = $users->filter(function ($user) {
                $profile = $user->profile()->first();
                global $project_category_id;
                return $profile->project_category_id === $project_category_id;
            });
        }

        if ($request->has('students_under_supervisor')) {
            global $supervisor_id;
            $supervisor_id = (int)($request->students_under_supervisor);

            $users = $users->filter(function ($user) {
                $profile = $user->profile()->first();
                global $supervisor_id;
                return $profile->supervisor_id === $supervisor_id;
            });
        }

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        return $user;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::find($id);
        $user->update([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::destroy($id);
    }

    function isLocalhost($whitelist = ['127.0.0.1', '::1'])
    {
        return in_array($_SERVER['REMOTE_ADDR'], $whitelist);
    }

    public function avatarUpload(Request $request)
    {
        $user = $request->user();
        $avatar_path = $request->file('avatar')->store('public/avatars');
        $protocol = $this->isLocalhost() ? "http://" : "https://";
        $avatar_url = $protocol . $_SERVER['HTTP_HOST'] . "/storage" . substr($avatar_path, 6);

        $user->avatar_url = $avatar_url;
        $user->save();

        return response()->json([
            'success' => false,
            'user' => $user
        ]);
    }


    public function unboardSupervisor(Request $request)
    {
        $authzResponse = Gate::inspect('unboardSupervisor', $request->user());

        if ($authzResponse->denied()) {
            return response()->json([
                'success' => false,
                'message' => $authzResponse->message()
            ], 403);
        }
        $project_categories = implode(',', ProjectCategory::all()->pluck('name')->toArray());

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'project_category' => "required|string|in:{$project_categories}",
            'department' => 'required|string'
        ]);

        $password = "password";
        $coordinator_profile = $request->user()->profile()->first();

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($password),
            'department' => $validatedData['department']
        ]);


        $supervisor = Supervisor::create();
        $supervisor->user()->save($user);
        $user->supervisor()->save($supervisor);
        $supervisor->coordinator()->associate($coordinator_profile)->save();

        $projectCategory = ProjectCategory::firstWhere('name', $validatedData['project_category']);
        $supervisor->projectCategory()->associate($projectCategory)->save();

        $returned_supervisor = Supervisor::where('id', $supervisor->id)
            ->where('coordinator_id', $coordinator_profile->id)
            ->withCount('student')
            ->with('projectCategory')
            ->with('user')
            ->first();

        try {
            Mail::to($user)->send(new RegisteredSupervisor($user, $request->user(), $password));
        } catch (Exception $e) {
            return response()->json([
                'success' => true,
                'supervisor' => $returned_supervisor,
                'message' => 'supervisor email is not valid so no email was sent',
            ], 201);
        }

        return response()->json([
            'success' => true,
            'supervisor' => $returned_supervisor,
            'message' => "An email with further instructions has been sent to the supervisor's email"
        ], 201);
    }

    public function studentsUnderSupervisor(Request $request)
    {
        if ($request->has('supervisor_id')) {
            $supervisor = Supervisor::find($request->supervisor_id);
            $students = Student::with('user')->with('project', 'project.projectCategory')->where('supervisor_id', $supervisor->id)->get();

            return response()->json([
                'success' => true,
                'students' => $students
            ]);
        }
        $supervisor = $request->user()->profile()->first();
        $students = Student::with('user')->with('project', 'project.projectCategory')->where('supervisor_id', $supervisor->id)->get();

        return response()->json([
            'success' => true,
            'students' => $students
        ]);
    }
    public function getSupervisors(Request $request)
    {
        $coordinator_profile = $request->user()->profile()->first();
        $supervisors = Supervisor::where('coordinator_id', $coordinator_profile->id)->withCount('student')->with('projectCategory')->with('user')->get();

        return response()->json([
            'success' => true,
            'supervisors' => $supervisors
        ]);
    }
    public function updatePassword(Request $request)
    {
        $validatedData = $request->validate([
            'password' => 'string|max:255',
        ]);

        $user = $request->user();
        $user->password = Hash::make($validatedData['password']);
        $user->save();

        return response()->json([
            'success' => true
        ]);
    }

    public function getSupervisorCount(Request $request)
    {
        $supervisor_count = Supervisor::count();

        return response()->json([
            'supervisor_count' => $supervisor_count,
            'success' => true
        ]);
    }
}

// Student::has('supervisor')->with('user')->get();
// User::has('profile')->with('profile')->get();