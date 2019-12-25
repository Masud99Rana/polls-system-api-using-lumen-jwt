<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\ValidationException;

class QuestionsController extends Controller
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

    // show all question
    public function index(){
        $questions =  app('db')->table('questions')->get();

        return response()->json($questions);
    }

    // create new question
    public function create(Request $request){

        $input = $request->only('question', 'choices');
        
        $rules = [
            'question' => 'required|unique:questions',
            'choices' => 'required',
        ]; 

        $validator = Validator::make($input, $rules);

        if($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->messages(),
            ], 422); // 422  Unprocessable entity
        }

       try{
            $id = app('db')->table('questions')->insertGetId([
                'question' => trim($input['question']),
                'choices' => serialize($request->choices),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $user = app('db')->table('questions')->select('question', 'choices')->where('id', $id)->first();
            
            return response()->json([
                'id' => $id,
                'question' => $user->question,
                'choices' => unserialize($user->choices)
            ], 201); // resource created
         
        } catch(PDOException $e){
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400); // 400 bad request
        }  
    }
}
