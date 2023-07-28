<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Hash;
use Validator;
use Exception;
use Log;
use App\Models\User;
use Auth;

class AuthController extends Controller
{

   public function login(Request $request)
   {
   		$rule = [
            'email' => 'required',
            'password' => 'required',  
        ];

        $validate = Validator::make($request->all(), $rule);

        if ($validate->fails())
        {
             return $this->sendError('Validation error.', ['errors' => $validate->errors()]);     
        }   
	   	try
		{
			$data = request()->all();

			if(Auth::attempt(['email' => $data['email'], 'password' => $data['password']])){ 
	            $user = Auth::user(); 
	            $user->tokens()->where('tokenable_id', $user['id'])->delete();
	            $success['name'] =  $user;
	            $success['token'] =  $user->createToken('7span')->plainTextToken; 
	   
	            return $this->sendResponse($success, 'User login successfully.');
	        } 
	        else{ 
	            return $this->sendError('Unauthorised.', ['error'=>'Check your email and password.'],401);
	        } 
			
		}
		catch(Exception $e)
		{
			dd($e);
			Log::debug($e);
			abort('500');
		}  
   }
}


