<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller {

    // ----------- [ ' For Register ' ] ------------

    public function register( Request $request ) {
        // ------------[ 'For Validation' ]------------
        $validator = validator::make( $request->all(), [
            'first_name' => 'required|min:3',
            'last_name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'phone' => 'required|min:10',
            'password' => 'required|min:8'
        ], [
            'first_name.required'=>'Name Is Required.',
            'last_name.required'=>'last_Name Is Required.',
            'email.required'=>'email Is Required.',
            'phone.required'=>'phone Is Required.',
            'password.required'=>'password Is Required.'
        ] );
        if ( $validator->fails() ) {
            return response()->json( [
                'message' => 'Required Field',
            ] );
            // return redirect()->back()->withErrors( $validator )->withInput();
        } else {
            // -----------------[ ' Create new user ' ] ---------------------
            try {
                $user = new User();
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->password = app( 'hash' )->make( $request->password );
                $token = $user->token = Str::random( 60 );
                if ( $user->save() ) {
                    return response()->json( [
                        'status' => '200',
                        'message' => 'User Register Success',
                        'token' => $token
                    ] );
                } else {
                    return response()->json( [
                        'status' => '400',
                        'message' => 'User Register Faild'
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

