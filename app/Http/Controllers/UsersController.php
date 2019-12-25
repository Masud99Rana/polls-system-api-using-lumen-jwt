<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\ValidationException;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    // show all users
    public function index(){
        $users =  app('db')->table('users')->get();

        return response()->json($users);
    }

    // create new user
    public function create(Request $request){
        
       //  try{
       //     $this->validate($request, [
       //         'full_name' => 'required',
       //         'username' => 'required|min:6',
       //         'email' => 'required|email',
       //         'password' => 'required|min:6'
       //     ]); 
       // } catch(ValidationException $e){
            
       //      return response()->json([
       //          'success' => false,
       //          'message' => $e->getMessage(),
       //      ], 422); // 422  Unprocessable entity
       // }
       // 
        $input = $request->only('full_name', 'username', 'email','password');
        
        $rules = [
            'full_name' => 'required',
            'username' => 'required|min:6|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]; 

        $validator = Validator::make($input, $rules);

        if($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->messages(),
            ], 422); // 422  Unprocessable entity
        }

       try{
            $id = app('db')->table('users')->insertGetId([
                'full_name' => trim($request->input('full_name')),
                'username' => strtolower(trim($request->input('username'))),
                'email' => strtolower(trim($request->input('email'))),
                'password' => app('hash')->make($request->input('password')),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $user = app('db')->table('users')->select('full_name', 'username', 'email')->where('id', $id)->first();
            
            return response()->json([
                'id' => $id,
                'full_name' => $user->full_name,
                'username' => $user->username,
                'email' => $user->email,
            ], 201); // resource created
         
        } catch(PDOException $e){
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400); // 400 bad request
        }  
    }

    public function authenticate(Request $request){
        
        $credentials = $request->only('email', 'password');

        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ];

        $validator = Validator::make($credentials, $rules);
        
        if($validator->fails()) {
         
            return response()->json([
                'success' => false,
                'error' => $validator->messages(),
            ], 422); // 422  Unprocessable entity
        }

        $token = app('auth')->attempt($request->only('email', 'password'));

        
        if($token){
            return response()->json([
                'success' => true,
                'message' => 'User authenticated',
                'token' => $token,
            ]);
        }
        
        return response()->json([
            'success' => false,
            'error' => 'Invalid Credentials',
        ], 400); // 400  Bad request
    }

    public function me(){
        $user = app('auth')->user();

        if($user){
            return response()->json([
                'success' => true,
                'message' => 'User profile found',
                'user' => $user,
            ]);
        }
        
        return response()->json([
            'success' => false,
            'error' => 'Not found',
        ], 404);
    }

    /**
     * API Update user profile
     */
    public function update(Request $request)
    {   
        $input = $request->only('full_name');

        $rules =[
            'full_name' => 'required'
        ];

        $validator = Validator::make($input, $rules);
       
        if($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->messages(),
            ], 422); // 422  Unprocessable entity
        }


        try{
            $user = app('auth')->user();
            $user->update(['full_name' => $request->full_name]);

            
            return response()->json([
                'id' => $user->id,
                'full_name' => $user->full_name,
                'username' => $user->username,
                'email' => $user->email,
            ], 201); // resource created
         
        } catch(PDOException $e){
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400); // 400 bad request
        }
    }

}
