<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    // return $router->app->version();
    // return "Welcome, Masud Rana";
    
    return response()->json([
    	'success' => true,
    	'message' => 'Welcome to my Poll Api',
    	'Developer' => 'Masud Rana'
    ]);
});

$router->group([
	'prefix' =>'api/v1',
], function() use ($router){
	$router->get('/','ExampleController@index');
	
	// User Authentication
	$router->post('/login','UsersController@authenticate');
	
	// Users Resource
	$router->post('/users','UsersController@create');

	// Restricted Routes
	$router->group(['middleware' => 'auth'], function() use ($router){
		$router->get('/users','UsersController@index');
		$router->get('/me','UsersController@me');
		$router->patch('/me/update','UsersController@update');

		// Question Resource
		$router->post('/questions','QuestionsController@create');
		$router->get('/questions','QuestionsController@index');
	});
	
});
