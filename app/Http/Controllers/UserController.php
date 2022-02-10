<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Laravel\Passport\AuthCode;
use Illuminate\Support\Facades\Hash;
use Auth;

class UserController extends Controller {

    // ----------- [ ' For Register ' ] ------------

    public function register( Request $request ) {
        // ------------[ 'For Validation' ]------------
        try {
            $validator = Validator::make( $request->all(), [
                'first_name' => 'required|min:3',
                'last_name' => 'required|min:3',
                'email' => 'required|email|unique:users',
                'phone' => 'required|min:10',
                'password' => 'required|min:6'
            ] );
            if ( $validator->fails() ) {
                return response( [ 'errors'=>$validator->errors()->all() ], 422 );
            } else {
                // -----------------[ ' Create new user ' ] ---------------------
                $user = new User();
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->password = app( 'hash' )->make( $request->password );
                $token = $user->token = Str::random( 60 );
                if ( $user->save() ) {
                    return response()->json( [
                        'status' => 200,
                        'message' => 'User Register Success',
                        'data' => [
                            'token' => $token
                        ]
                    ] );
                } else {
                    return response()->json( [
                        'status' => 400,
                        'message' => 'User Register Faild',
                        'data' => [
                            'token' => []
                        ]
                    ] );
                }
            }

        } catch ( \Exception $e ) {
            return response()->json( [
                'status' => 422,
                'message' => $e->getMessage()
            ] );
        }

    }

    // ---------------- [ ' Login ' ] -----------------

    public function login( Request $request ) {
        // ----------- [ ' laravel Validation ' ] ----------------
        $validator = Validator::make( $request->all(), [
            'phone' => 'required|min:10',
            'password' => 'required|min:6',
        ] );
        if ( $validator->fails() ) {
            return response( [ 'errors'=>$validator->errors()->all() ], 422 );
        } else {
            try {
                $user = User::where( 'phone', $request->phone )->first();
                if ( !$user || !Hash::check( $request->password, $user->password ) ) {
                    return response()->json( [
                        'status' => 400,
                        'message' => 'login Faild'
                    ] );
                } else {
                    return response()->json( [
                        'status' => 200,
                        'message' => 'User Login Success'
                    ] );
                }
            } catch ( \Exception $e ) {
                return response()->json( [
                    'message' => $e->getMessage()
                ] );

            }
        }
    }
}

