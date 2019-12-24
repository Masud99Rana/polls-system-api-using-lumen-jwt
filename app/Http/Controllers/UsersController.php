<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

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
        
        try{
           $this->validate($request, [
               'full_name' => 'required',
               'username' => 'required|min:6',
               'email' => 'required|email',
               'password' => 'required|min:6'
           ]); 
       } catch(ValidationException $e){
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
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
                'message' => $e->getMessage(),
            ], 400); // 400 bad request
        }  
    }
}
