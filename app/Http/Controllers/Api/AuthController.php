<?php

namespace App\Http\Controllers\Api;

use App\Models\Agent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $r){
  $r->validate(['email'=>'required|email','password'=>'required']);
  $agent = Agent::where('email', $r->email)->first();
  if(!$agent || !Hash::check($r->password, $agent->password)){
    return response()->json(['message'=>'Invalid credentials'],401);
  }
  return ['token' => $agent->createToken('api-token')->plainTextToken];
}

public function logout(Request $r){
  $r->user()->currentAccessToken()->delete();
  return response()->json(['message'=>'Logged out']);
}
}
