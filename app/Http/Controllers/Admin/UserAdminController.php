<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Mail\NewAccountNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Controllers\Traits\AdminViewSharedDataTrait;

class UserAdminController extends Controller
{

    use AdminViewSharedDataTrait;

    public function __construct()
    {
        $this->shareAdminViewData();
        
    }
    
    // Show the admin management page
    public function index()
    {
        // Get all staff users including the logged-in user
        $users = User::whereIn('role', ['global_admin', 'admin', 'cashier'])
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.manage-users', compact('users'));
    }

    // Store a new admin
    public function store(CreateUserRequest $request)
    {
        $firstName = trim((string) $request->first_name);
        $middleName = $request->filled('middle_name') ? trim((string) $request->middle_name) : null;
        $lastName = $request->filled('last_name') ? trim((string) $request->last_name) : '';

        $user = User::create([
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->email),
            'status' => 1,
            'notice' => null,
        ]);
    
        try {
            // Send email notification 
            Mail::to($user->email)->send(new NewAccountNotification($user, $user->email));
            $message = ['success' => 'User created successfully. Staff can login now using email as temporary password and should change it after login.'];
        } catch (\Throwable $e) {
  
            $message = [
                'success' => 'User created successfully. Staff can login now using email as temporary password and should change it after login.',
                'error' => 'Email notification failed. Please check MAIL_HOST / MAIL_FROM_ADDRESS in your .env if you need outgoing emails. Error: ' . $e->getMessage()
            ];
        }
    
        return redirect()->route('admin.users.index')->with($message);
    }
    

    // Update an admin
    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::findOrFail($id);
    
        if($user->notice !="change_password_to_activate_account"){

        // Determine ban status and set fields accordingly
        $isBanned = $request->has('ban') && $request->ban === 'on';
        $status = $isBanned ? 0 : 1;
        $notice = $isBanned ? "banned" : null;
        }
        else
        {
            $status = $user->status;
            $notice = $user->notice;
        }
        
        // Update the user
        $user->update([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'role' => $request->role,
            'status' => $status,
            'notice' => $notice,
        ]);
    
        return back()->with('success', 'User updated successfully.');
    }
    

    // Delete an admin
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
