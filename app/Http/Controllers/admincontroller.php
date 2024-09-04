<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

class AdminController extends Controller
{
    public function reset(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password'=>  'required|string',
            'phone' => 'required|string|max:255',
            'gender' => 'required|string',
            'address' => 'required|string',
            //'password'=>  'required|string'
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create the admin user
        $admin = Admin::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password'=>$request->password,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'address' => $request->address,
            //'password'=>$request->password
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Admin created successfully',
            'admin' => $admin,
        ], 201);
    }
    public function passwordupdate(Request $request)
    {
        // Validate the input fields
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:8|confirmed', // Confirmed means new_password_confirmation must match
        ]);
    
        // If validation fails, return with errors
        if ($validator->fails()) {
            //dd('iuyjhgtrfds');

            return response()->json([
                'status' => false,
                'message' => 'error found in data',
            
            ], 500);
           // return redirect()->back()->withErrors($validator)->withInput();
        }
    
        // Get the current authenticated admin
        $admin = Auth::guard('admin')->user();
    
        // Check if the old password is correct
        if (!Hash::check($request->input('old_password'), $admin->password)) {
            return redirect()->back()->with('error', 'The old password is incorrect.');
        }
    
        // Update the password in the database
        $admin->password = Hash::make($request->input('new_password'));
    dd('hhhu');
        // Save the changes
        //$admin->save();
    
        // Optionally, you can
    }
}