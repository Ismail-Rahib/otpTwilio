# 🔐 OTP Verification with Twilio in CodeIgniter 4 🚀

This project implements OTP (One-Time Password) verification using Twilio in CodeIgniter 4 with AJAX. Users receive an OTP via SMS and must verify it to access a dashboard.

## ✨ Features
- 📲 Send OTP via Twilio
- 🔑 Verify OTP using session handling
- 🎯 Redirect to a dashboard after successful verification
- 🚪 Logout functionality

## 📌 Prerequisites
Ensure you have the following installed:
- ✅ PHP 7.4+
- ✅ Composer
- ✅ CodeIgniter 4
- ✅ Twilio Account (for sending SMS)
- ✅ XAMPP or any local server environment

## ⚡ Setup Instructions

### 1️⃣ Clone the Repository
```sh
git clone https://github.com/Ismail-Rahib/otpTwilio.git
cd otpTwilio
```

### 2️⃣ Install Dependencies
```sh
composer install
```

### 3️⃣ Install Twilio SDK 📦
```sh
composer require twilio/sdk
```

### 4️⃣ Configure `.env` File 🛠️
Create a `.env` file in the root directory and add your Twilio credentials:
```ini
TWILIO_SID="your_twilio_sid"
TWILIO_AUTH_TOKEN="your_twilio_auth_token"
TWILIO_PHONE_NUMBER="your_twilio_phone_number"
```
Ensure your `.env` file is **not committed** to Git by adding it to `.gitignore`.

### 5️⃣ Start the Development Server ▶️
```sh
php spark serve
```
Your application will run on `http://localhost:8080`.

## 📂 Project Structure
```
app/
├── Controllers/
│   ├── OtpController.php  # Handles OTP sending & verification
│   ├── DashboardController.php  # Handles dashboard access & logout
├── Views/
│   ├── otp_view.php  # OTP verification page
│   ├── dashboard.php  # Dashboard page
├── Config/
│   ├── Routes.php  # Define application routes
public/
├── assets/
│   ├── css/bootstrap.min.css
│   ├── js/bootstrap.bundle.min.js
│   ├── js/jquery-3.6.0.min.js
```

## 🔗 API Endpoints

### 1️⃣ Send OTP
**URL:** `/send-otp`
**Method:** `POST`
**Request Body:** `{ "phone": "+1234567890" }`
**Response:**
```json
{
  "status": "success",
  "message": "OTP sent successfully!"
}
```

### 2️⃣ Verify OTP
**URL:** `/verify-otp`
**Method:** `POST`
**Request Body:** `{ "otp": "123456" }`
**Response:**
```json
{
  "status": "success",
  "message": "OTP verified successfully!"
}
```

## 🛠️ Usage
1. Open `http://localhost:8080` in your browser.
2. Enter your phone number and click "Send OTP" 📩.
3. Enter the received OTP and verify 🔑.
4. If successful, you are redirected to the dashboard 🏆.
5. Click "Logout" to end the session 🚪.

## 📜 License
This project is open-source under the [MIT License](LICENSE).
