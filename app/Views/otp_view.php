<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    
    <!-- Link Local Bootstrap CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">

    <script src="<?= base_url('assets/js/jquery-3.6.0.min.js') ?>"></script>
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">

    <div class="container p-4 bg-white shadow rounded text-center" style="max-width: 400px;">
        <h2 class="mb-3">OTP Verification</h2>

        <div id="step1">
            <input type="text" id="phone" class="form-control mb-3 text-center" placeholder="Enter phone number">
            <button class="btn btn-primary w-100" onclick="sendOtp()">Send OTP</button>
        </div>

        <div id="step2" class="d-none">
            <input type="text" id="otp" class="form-control mb-3 text-center" placeholder="Enter OTP">
            <button class="btn btn-success w-100" onclick="verifyOtp()">Verify OTP</button>
        </div>

        <p id="message" class="text-danger mt-2 fw-bold"></p>
    </div>

    <!-- Link Local Bootstrap JS -->
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>

    <script>
        function sendOtp() {
            let phone = $('#phone').val();
            if (phone === '') {
                $('#message').text('Please enter your phone number');
                return;
            }
            $.post('/send-otp', { phone: phone }, function(response) {
                if (response.status === 'success') {
                    $('#message').text(response.message).removeClass('text-danger').addClass('text-success');
                    $('#step1').addClass('d-none');
                    $('#step2').removeClass('d-none');
                } else {
                    $('#message').text(response.message);
                }
            }, 'json');
        }

        function verifyOtp() {
            let otp = $('#otp').val();
            if (otp === '') {
                $('#message').text('Please enter the OTP');
                return;
            }
            $.post('/verify-otp', { otp: otp }, function(response) {
                if (response.status === 'success') {
                    window.location.href = "/dashboard";
                } else {
                    $('#message').text(response.message);
                }
            }, 'json');
        }
    </script>

</body>
</html>
