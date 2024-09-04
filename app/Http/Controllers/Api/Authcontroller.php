<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Otp;
use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Mail\passmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
 
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function index()
    {
        // Retrieve all users from the database
        $users= User::all();
//dd($users);
        // Pass the users to the view
        return view('admin.delete',compact('users'));
        //return response()->json($users);

    }
    /**
     * Handle user signup.
     */


     
    
    public function signup(Request $request)
    {
        $randomPassword = Str::random(8);

        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'mobile_number' => 'required|numeric|digits:10|unique:users,mobile_number',
        
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile_number' => $request->mobile_number,
            'password' => Hash::make($randomPassword), // Hash password for security

        ]);

        // Send email with password
        $this->sendEmail($request->email, $request->name, $randomPassword);

        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'user' => $user,
        ], 201);
    }

    /**
     * Send email with password.
     */
    public function sendEmail($toEmail, $name, $password)
    {
    
        Mail::to($toEmail)->send(new Passmail($name, $password));
    
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $validateuser = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:8',             // Minimum length
                'regex:/[A-Z]/',      // Must contain at least one uppercase letter
                'regex:/[0-9]/',      // Must contain at least one digit
            
            ],
        ],[
            
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.regex' => 'The password must contain at least one uppercase letter and one number.',

        ]);

        if ($validateuser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validateuser->errors()
            ], 422);
        }
        $DBTOKEN = DB::select('SELECT p.TOKEN  FROM PERSONAL_ACCESS_TOKENS p
        inner join  users u on P.TOKENABLE_ID = U.ID'); 

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $authUser = Auth::user();
            $token = $authUser->createToken('authToken')->plainTextToken;
            return response()->json([
                'status' => true,
                'message' => 'User logged in successfully',
                'token' => $token,
                'token_type' => 'Bearer',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Email and password do not match',
            ], 401);
        }
    }

    public function getUserData(Request $request)
    {
        // Ensure the request is authenticated
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        // Return user data
        return response()->json([
            'status' => true,
            'user' => $user,
        ], 200);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json([
            'status' => true,
            'message' => 'You logged out successfully',
        ], 200);
    }
    
    
    public function dashboard(Request $request)
    {
        $headers = $request->header('Authorization');
        // print_r($headers);

        if ($headers) {
            $authorizationValue = $headers;
            // echo $authorizationValue;
            $token = str_replace('Bearer ', '', $authorizationValue);
            // echo $token;       ///118|jhbshjcbdhbvh
            $parts = explode('|', $token);
            $tokenid=$parts[0];
            // print_r($tokenid);
            // var_dump($tokenid);
            $tokenidExists = DB::select('SELECT id FROM PERSONAL_ACCESS_TOKENS WHERE id = :token', ['token' => $tokenid]); 
            // print_r($tokenidExists);
           if ($tokenidExists) {     // Token exists in the database
            $userDetails = DB::
            select
            (
            'SELECT u.name,u.email FROM personal_access_tokens p   INNER JOIN users u ON p.tokenable_id = u.id    WHERE p.id = :token ',['token' => $tokenid]);
            // print_r($userDetails[0]->name);
           
            return response()->json([ 'status' => true, 'message' => 'Token is valid and found in the database.', 'userDetails'=>$userDetails[0]->name], 200); } 
           else {     // Token not found in the database
           
            return response()->json([ 'status' => false, 'message' => 'Token not found in the database.', 'header'=>$token,'token'=>$authorizationValue], 401); // 401 Unauthorized 
            }
      
            
        } else {
            echo "Authorization header not found.";
        }
        return;
        
    }

    public function validatetoken(Request $request){
        $headers = $request->header('Authorization');
        // print_r($headers);

        if ($headers) {
            $authorizationValue = $headers;
            // echo $authorizationValue;
            $token = str_replace('Bearer ', '', $authorizationValue);
            // echo $token;       ///118|jhbshjcbdhbvh
            $parts = explode('|', $token);
            $tokenid=$parts[0];
            // print_r($tokenid);
            // var_dump($tokenid);
            $tokenidExists = DB::select('SELECT id FROM PERSONAL_ACCESS_TOKENS WHERE id = :token', ['token' => $tokenid]); 
            // print_r($tokenidExists);
           if ($tokenidExists) {     // Token exists in the database
            $userDetails = DB::
            select
            (
            'SELECT u.name,u.email FROM personal_access_tokens p   INNER JOIN users u ON p.tokenable_id = u.id    WHERE p.id = :token ',['token' => $tokenid]);
            // print_r($userDetails[0]->name);
           
            return response()->json([ 'status' => true, 'message' => 'Token is valid and found in the database.', 'userDetails'=>$userDetails[0]->name], 200); } 
           else {     // Token not found in the database
           
            return response()->json([ 'status' => false, 'message' => 'Token not found in the database.', 'header'=>$token,'token'=>$authorizationValue], 401); // 401 Unauthorized 
            }
      
            
        } else {
            echo "Authorization header not found.";
        }
        return;
    }





    /**
     * Send OTP for password reset.
     */
    public function forgotPassword(Request $request)
{
    // Validate email format
    $request->validate([
        'email' => 'required|email',
    ]);

    // Check if the email exists in the database
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'Email is not registered.',
        ], 404);
    }

    // Generate a 6-digit OTP
    $otp = rand(100000, 999999);

    // Store or update OTP
    try {
        Otp::updateOrCreate(
            ['email' => $request->email], // Match based on email
            ['otp' => $otp, 'expires_at' => Carbon::now()->addMinutes(5)] // Set OTP and expiration
        );
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Failed to store OTP in the database.',
            'error' => $e->getMessage(),
        ], 500);
    }

    // Try sending the OTP email and handle any potential errors
    try {
        Mail::to($user->email)->send(new OtpMail($otp));
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Failed to send OTP email. Please try again later.',
            'error' => $e->getMessage(),
        ], 500);
    }
    
    return response()->json([
        'status' => true,
        'message' => 'OTP sent successfully and stored in the database.',
    ], 200);
}

    /**
     * Verify OTP for password reset.
     */
    public function otpVerification(Request $request)
{
    $validator = Validator::make($request->all(), [
        'otp' => 'required|digits:6',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid OTP format',
        ], 400);
    }

    // Find the OTP entry in the database
    $otpRecord = Otp::where('otp', $request->otp)->first();

    if (!$otpRecord) {
        return response()->json([
            'status' => false,
            'message' => 'OTP is incorrect or expired',
        ], 400);
    }

    // Find the user associated with this OTP using the email
    $user = User::where('email', $otpRecord->email)->first();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'User not found',
        ], 404);
    }

    // If OTP is correct, delete it (or mark it as used)
    $otpRecord->delete();

    // Return a success response with the user's email
    return response()->json([
        'status' => true,
        'message' => 'OTP verified successfully.',
        'email' => $user->email,
    ]);
}

    /**
     * Reset password using OTP.
     * 
     * 
     * 
     */
    public function resetPasswordWithOtp(Request $request)
{
    try {
        // Validate the request data
        $validator = Validator::make($request->all(), [
        
           // 'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid request data',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Retrieve the user by email
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Ensure password confirmation matches the password
        if ($request->password !== $request->password_confirmation) {
            return response()->json([
                'status' => false,
                'message' => 'Password confirmation does not match.',
            ], 400);
        }

        // Update the user password
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the OTP record for the email
        Otp::where('email', $request->email)->delete();
        
        return response()->json([
            'status' => true,
            'message' => 'Password reset successfully.',
        ], 200);
    } 
    catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'An error occurred. Please try again later.',
        ],500);
    }
}


public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit', compact('user'));
    }


    public function updateProfile(Request $request)
{
    $headers = $request->header('Authorization');

    if ($headers) {
        $authorizationValue = $headers;
        $token = str_replace('Bearer ', '', $authorizationValue);
        $parts = explode('|', $token);
        $tokenid = $parts[0];

        $tokenidExists = DB::select('SELECT id FROM PERSONAL_ACCESS_TOKENS WHERE id = :token', ['token' => $tokenid]);

        if ($tokenidExists) { // Token exists in the database
            $userDetails = DB::select(
                'SELECT u.id, u.name, u.email FROM personal_access_tokens p 
                 INNER JOIN users u ON p.tokenable_id = u.id 
                 WHERE p.id = :token',
                ['token' => $tokenid]
            );

            if ($userDetails) {
                $userId = $userDetails[0]->id;

                try {
                    // Validate the request data
                    $validator = Validator::make($request->all(), [
                        'name' => 'required|string|max:255',
                        'mobile_number' => 'required|digits_between:10,15',
                        'gender' => 'required|in:male,female,other',
                        'address' => 'required|string|max:255',
                        'password' => 'sometimes|nullable|string|min:8|confirmed',
                    ]);

                    // Check if validation fails
                    if ($validator->fails()) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Invalid request data',
                            'errors' => $validator->errors(),
                        ], 422);
                    }

                    // Retrieve the user by ID
                    $user = User::find($userId);

                    if ($user) {
                        // Update the user's profile except the email
                        $user->name = $request->name;
                        $user->mobile_number = $request->mobile_number;
                        $user->gender = $request->gender;
                        $user->address = $request->address;

                        // Update the password if provided
                        if (!empty($request->password)) {
                            $user->password = Hash::make($request->password);
                        }

                        $user->save();

                        return response()->json([
                            'status' => true,
                            'message' => 'Profile updated successfully.',
                        ], 200);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'User not found.',
                        ], 404);
                    }
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => false,
                        'message' => 'An error occurred. Please try again later.',
                    ], 500);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'User details not found.',
                ], 404);
            }
        } else { // Token not found in the database
            return response()->json([
                'status' => false,
                'message' => 'Token not found in the database.',
            ], 401); // 401 Unauthorized
        }
    } else {
        return response()->json([
            'status' => false,
            'message' => 'Authorization header not found.',
        ], 401); // 401 Unauthorized
    }
}

public function updatePassword(Request $request)
{
    $headers = $request->header('Authorization');

    if ($headers) {
        $authorizationValue = $headers;
        $token = str_replace('Bearer ', '', $authorizationValue);
        $parts = explode('|', $token);
        $tokenid = $parts[0];

        $tokenidExists = DB::select('SELECT id FROM personal_access_tokens WHERE id = :token', ['token' => $tokenid]);

        if ($tokenidExists) { // Token exists in the database
            $userDetails = DB::select(
                'SELECT u.id, u.password FROM personal_access_tokens p 
                 INNER JOIN users u ON p.tokenable_id = u.id 
                 WHERE p.id = :token',
                ['token' => $tokenid]
            );

            if ($userDetails) {
                $userId = $userDetails[0]->id;
                $hashedPassword = $userDetails[0]->password;

                try {
                    // Validate the request data
                    $validator = Validator::make($request->all(), [
                        'old_password' => 'required|string',
                        'password' => [
                'required',
                'string',
                'min:8',             // Minimum length
                'regex:/[A-Z]/',      // Must contain at least one uppercase letter
                'regex:/[0-9]/',      // Must contain at least one digit
                'confirmed',          // Must match the password_confirmation field
            ],
                        'password_confirmation' => 'required|string|min:8',
                    ]);

                    if ($validator->fails()) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Validation failed',
                            'errors' => $validator->errors(),
                        ], 400);
                    }

                    // Verify the old password
                    if (!Hash::check($request->old_password, $hashedPassword)) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Old password does not match.',
                        ], 400);
                    }

                    // Update the password
                    DB::table('users')
                        ->where('id', $userId)
                        ->update(['password' => Hash::make($request->password)]);

                    return response()->json([
                        'status' => true,
                        'message' => 'Password reset successfully.',
                    ], 200);
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => false,
                        'message' => 'An error occurred. Please try again later.',
                    ], 500);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found.',
                ], 404);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Token not found.',
            ], 404);
        }
    } else {
        return response()->json([
            'status' => false,
            'message' => 'Authorization header not found.',
        ], 400);
    }
}
public function destroy($id)
    {
        // Find the user by ID and delete
        $user = User::findOrFail($id);
        $user->delete();

        // Redirect back with a success message
        return back()->with('success', 'User deleted successfully!');
    }


}