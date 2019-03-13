<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use App\UserAdmin;
use App\UserDinas;
use App\UserKominfo;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    // Route Handler
    public function showRegistrationForm()
    {
        $auth = Auth::user();
        $this->redirectIfNotLoggedIn($auth);
        $this->redirectIfNotAdmin($auth);

        // TODO: enable this
//        return view('auth.register');
    }

    // Route Handler
    public function registerHandler(Request $request)
    {
        $auth = Auth::user();
        $this->redirectIfNotLoggedIn($auth);
        $this->redirectIfNotAdmin($auth);

        $this->registerUserHandler($request);
        // TODO: enable this
//        return view('auth.register');
    }

    private function registerUserHandler(Request $request)
    {
        $data = $request->all();
        $success = $this->registerUser($data);

        if ($success) {
            $this->displayRegisterSuccess();
        } else {
            $this->displayRegisterFailed();
        }
    }

    private function registerUser($data)
    {

        $validator = $this->validator($data);
        if ($validator->fails()) {
            $this->displayValidationErrors($validator->errors()->all());
            return false;
        }

        $this->createUser($data);
        return true;
    }

    /****************************
     *   Checking Validity
     ****************************/


    protected function validator(array $data)
    {
        // TODO : check if dinas_id is required here

        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    /****************************
     *   User Creation
     ****************************/

    private function createUser($data)
    {
        $user = $this->create($data);
        $userType = trim($data['user_type']);

        // TODO: revisit this for register when DB scheme is fixed

        if ($userType == User::TYPE_DINAS) {
            $this->createUserDinas($user, $data);

        } else if ($userType == User::TYPE_KOMINFO) {
            $this->createUserKominfo($user);

        } else if ($userType == User::TYPE_ADMIN) {
            $this->createAdmin($user, $data);
        }
    }

    private function create(array $data)
    {
        return User::create([
            'fullname' => trim($data['name']),
            'username' => trim($data['username']),
            'email' => trim($data['email']),
            'password' => Hash::make(trim($data['password'])),
        ]);
    }

    private function createUserDinas($user, $data)
    {
        return UserDinas::create([
            'id' => $user->id,
            'dinas_id' => $data['dinas_id'],
            'role' => $data['role']
        ]);
    }

    private function createUserKominfo($user)
    {
        return UserKominfo::create([
            'id' => $user->id
        ]);
    }

    private function createAdmin($user, $data)
    {
        return UserAdmin::create([
            'id' => $user->id,
            'dinas_id' => $data['dinas_id']
        ]);
    }

    private function displayRegisterSuccess()
    {
        echo '<div class="alert alert-success alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Success !</strong> New user has successfully registered!
        </div>';
    }

    private function displayRegisterFailed()
    {
        echo '<div class="alert alert-warning alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        Registration <strong>failed !</strong> Try to contact admin for further assistance!
        </div>';
    }

    private function displayValidationErrors($errors)
    {
        foreach ($errors as $error) {
            echo '<div class="alert alert-warning alert-dismissible fade show text-center">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            ' . $error . '</div>';
        }
    }
}
