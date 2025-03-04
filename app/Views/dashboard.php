<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Link Local Bootstrap CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">

    <div class="container p-4 bg-white shadow rounded text-center" style="max-width: 500px;">
        <h2 class="mb-3">Welcome to the Dashboard</h2>
        <p class="mb-4">You have successfully verified your OTP!</p>
        
        <!-- Logout Button -->
        <form action="/logout" method="POST">
            <button type="submit" class="btn btn-danger w-100">Logout</button>
        </form>
    </div>

    <!-- Link Local Bootstrap JS -->
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
