<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Hash;
use Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        try {

            $users = User::orderBy('id','desc')->where('role','user');
            if(request('hobby_name')){
                $search_text = request('hobby_name');
                $users = $users->whereHas('hobi',function ($q) use($search_text)
                {
                    $q->Where('name', 'like', '%' . $search_text . '%');
                });
            }
            $users = $users->paginate(5);
            return $this->sendResponse($users, 'User list successfully.');
            
        } catch (Exception $e) {
            Log::debug($e);
            abort('500');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rule = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'photo' => 'required|image|mimes:png,jpg,jpeg',
            'password' => 'required|min:8',
            'mobile_number' => 'required|numeric|digits:10',
        ];

        $msg = [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Enter valid email.',
            'email.unique' => 'Enter unique email.',
            'photo.image' => 'A Profile photo is required.',
            'photo.required' => 'A Profile photo is required.',
            'photo.mimes' => 'Only PNG, JPG, JPEG images are allowed.',
            'mobile_number.required' => 'Mobile Number is required.',
            'mobile_number.numeric' => 'Mobile Number use only number.',
            'mobile_number.digits' => 'Moble number add only 10 digits.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password length must be at least 8 characters.'
        ];

        $validate = Validator::make($request->all(), $rule, $msg);

        if ($validate->fails())
        {
             return $this->sendError('Validation error.', ['errors' => $validate->errors()],400);     
        }   
        try {
            $data = request()->all();

            $image = $data['photo'];
            $uploadPath = public_path('/uploads/userProfile');
            $extension = $image->getClientOriginalExtension();
            $fileName = time() . "_" . rand(11111, 99999) . '.' . $extension;
            $image->move($uploadPath, $fileName);
            $data['photo'] = $fileName;
            $data['role'] = 'user';
            $data['password'] = Hash::make($data['password']);
            $user = User::create($data);
            if($data['hobby']){
                $data['hobby'] = explode(',', $data['hobby']);
                $user->hobi()->sync($data['hobby']);
            }
            
            return $this->sendResponse($user, 'User created successfully.');
        } catch (Exception $e) {
            Log::debug($e);
            abort('500');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            
        } catch (Exception $e) {
            Log::debug($e);
            abort('500');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rule = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'mobile_number' => 'required|numeric|digits:10',
        ];

        if(request('photo')){
            $rule['photo'] = 'required|image|mimes:png,jpg,jpeg';
        }

        $msg = [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Enter valid email.',
            'email.unique' => 'Enter unique email.',
            'photo.image' => 'A Profile photo is required.',
            'photo.required' => 'A Profile photo is required.',
            'photo.mimes' => 'Only PNG, JPG, JPEG images are allowed.',
            'mobile_number.required' => 'Mobile Number is required.',
            'mobile_number.numeric' => 'Mobile Number use only number.',
            'mobile_number.digits' => 'Moble number add only 10 digits.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password length must be at least 8 characters.'
        ];

        $validate = Validator::make($request->all(), $rule, $msg);

        if ($validate->fails())
        {
             return $this->sendError('Validation error.', ['errors' => $validate->errors()],400);     
        }  

        try {
            $data = request()->all();
            $user  = User::where('id',$id)->first();
            if($data['photo']){
                $image = $data['photo'];
                $uploadPath = public_path('/uploads/userProfile');
                
                if(\File::exists($uploadPath.'/'.$user['photo']) && $user['photo']){
                     \File::delete($uploadPath.'/'.$user['photo']);
                }

                $extension = $image->getClientOriginalExtension();
                $fileName = time() . "_" . rand(11111, 99999) . '.' . $extension;
                $image->move($uploadPath, $fileName);
                $data['photo'] = $fileName;
            }
            $data['role'] = 'user';
            if($data['password']){
                $data['password'] = Hash::make($data['password']);
            }
            
            $user = $user->update($data);
            
            return $this->sendResponse($user, 'User updated successfully.');
        } catch (Exception $e) {
            Log::debug($e);
            abort('500');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::where('id',$id)->first();
            if(!$user){
                return $this->sendError('User not found.', [],404);     
            }
            $uploadPath = public_path('/uploads/userProfile');
         if(\File::exists($uploadPath.'/'.$user['photo']) && $user['photo']){
                 \File::delete($uploadPath.'/'.$user['photo']);
            }
            $user->delete();

            return $this->sendResponse(1, 'User deleted successfully.');
        } catch (Exception $e) {
            Log::debug($e);
            abort('500');
        }
    }

    public function updateHobby(Request $request)
    {
        $rule = [
            'hobby' => 'required',
        ];

        $msg = [
            'hobby.required' => 'Hobby is required.',
        ];

        $validate = Validator::make($request->all(), $rule, $msg);

        if ($validate->fails())
        {
             return $this->sendError('Validation error.', ['errors' => $validate->errors()],400);     
        }  

        try {
            $data = request()->all();
            $data['hobby'] = array_filter(explode(',', $data['hobby']));
            $user = auth('sanctum')->user();
            $user->hobi()->sync($data['hobby']);
            
            return $this->sendResponse($data['hobby'], 'Hobby updated successfully.');

        } catch (Exception $e) {
            Log::debug($e);
            abort('500');
        }
    }

    public function listUserByAdmin(Request $request)
    {

        try {
            $user = auth('sanctum')->user();
            if($user['role'] != 'super_admin'){
                return $this->sendError('Unauthorised.', 'Not appropriate role to aceess this.',401);     

            }
            $users = User::orderBy('id','desc');
            if(request('hobby_name')){
                $search_text = request('hobby_name');
                $users = $users->whereHas('hobi',function ($q) use($search_text)
                {
                    $q->Where('name', 'like', '%' . $search_text . '%');
                });
            }
            $users = $users->paginate(5);
            return $this->sendResponse($users, 'User list successfully.');

        } catch (Exception $e) {
            Log::debug($e);
            abort('500');
        }
    }
}
