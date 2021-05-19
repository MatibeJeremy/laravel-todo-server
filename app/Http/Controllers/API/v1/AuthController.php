<?php
namespace App\Http\Controllers\API\v1;
use App\Http\Controllers\API\v1\BaseController as BaseController;

use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AuthController extends BaseController
{
    use ApiResponser;

    public function register(Request $request)
    {
        $attr = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6'
        ]);

        $user = User::create([
            'name' => $attr['name'],
            'password' => bcrypt($attr['password']),
            'email' => $attr['email']
        ]);

        if($user ==! null){

            Mail::to($user->email)->send(new \App\Mail\VerifyAccount($user));

            return $this->success([
                'token' => $user->createToken('API Token')->plainTextToken
            ]);
        }

        return $this->error('Failed',401);

    }

    public function login(Request $request)
    {
        $attr = $request->validate([
            'email' => 'required|string|email|',
            'password' => 'required|string|min:6'
        ]);

        if (!Auth::attempt($attr)) {
            return $this->error('Credentials not match', 401);
        }
        $user = auth()->user();

        //Make sure user is verified

        if($user->email_verified_at == null){
            return $this->sendError('Unauthorised.', ['error'=>'Email not verified']);
        }

        $success['token'] =  auth()->user()->createToken('API Token')->plainTextToken;
        $success['verification'] =  $user->email_verified_at;

        return $this->sendResponse($success, 'User is signed in');
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Tokens Revoked'
        ];
    }

    public function verify($user)
    {
        $user = User::where('email', '=', $user)->first();


        // $bool = $user->hasVerifiedEmail();
        if($user->email_verified_at == null){
            $user->email_verified_at = Carbon::now();
            $user->save();
            return 'User is verified';
        }

        return 'User was verified';
    }
}
