<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Twilio\Rest\Client;

class OtpController extends Controller
{
    public function index()
    {
        return view('otp_view'); // Ensure this view exists in `app/Views/otp_view.php`
    }

    public function sendOtp()
    {
        helper(['text', 'session']); // Load session helper

        $phone = $this->request->getPost('phone');
        if (!$phone) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Phone number is required.']);
        }

        // Generate OTP
        $otp = random_string('numeric', 6);
        session()->set('otp', $otp); // Store OTP in session

        // Twilio API Credentials from `.env`
        $sid = getenv('TWILIO_SID');
        $token = getenv('TWILIO_AUTH_TOKEN');
        $from = getenv('TWILIO_PHONE_NUMBER');

        // Debugging: Ensure Twilio credentials are loading
        if (!$sid || !$token || !$from) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Twilio credentials not found! Check your .env file.',
            ]);
        }

        try {
            $client = new Client($sid, $token);
            $client->messages->create($phone, [
                'from' => $from,
                'body' => "Your OTP is: $otp"
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'OTP sent successfully!',
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Twilio Error: ' . $e->getMessage(),
            ]);
        }
    }

    public function verifyOtp()
    {
        helper('session');

        $otp = $this->request->getPost('otp');
        $sessionOtp = session()->get('otp');

        if (!$sessionOtp) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'OTP expired. Please request a new one.',
            ]);
        }

        if ($otp == $sessionOtp) {
            session()->remove('otp'); // Remove OTP after successful verification
            session()->set('is_verified', true); // Mark user as verified

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'OTP verified successfully!',
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid OTP!',
            ]);
        }
    }
}
