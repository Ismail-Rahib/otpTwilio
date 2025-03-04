<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        if (!session()->get('is_verified')) {
            return redirect()->to('/');
        }

        return view('dashboard');
    }

    public function logout()
    {
        // Destroy session or remove the "is_verified" session data
        session()->remove('is_verified'); // Assuming 'is_verified' is the session data to check if the user is logged in

        // Redirect back to OTP verification page
        return redirect()->to('/');
    }
}
