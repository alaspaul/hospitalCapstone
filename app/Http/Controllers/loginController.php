<?php

namespace App\Http\Controllers;
use App\Models\resident;


use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Hash;
use Illuminate\Http\Response;
use Auth;
class loginController extends Controller
{
    public function username()
{
    return 'resident_userName';
}

    public function loginResident(request $request){
        $request->validate([
            'resident_userName' => 'required',
            'resident_password' => 'required',
        ]);


        $credentials = [
            'resident_userName' => $request->input('resident_userName'),
            'password' => $request->input('resident_password'),
        ];
        try {
            
    
            if (Auth::guard('custom')->attempt($credentials)){
                $resident = resident::where('resident_userName', $request['resident_userName'])->first();
                $token = bin2hex(random_bytes(10));
                return response()->json(['token'=>'R-'. $token, 'resident' => $resident],200);
            }
        } catch (ValidationException $e) {
            return response()->json(['error'=> 'login error'], 422);
        }

        return response()->json(['error' => 'invalid credentials'],401);
    }

    

    
    public function logoutRes() {
        auth('web')->logout();


        return redirect('login')->withSuccess('Logged out successfully.');
  
    }
}