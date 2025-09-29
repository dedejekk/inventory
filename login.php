<?php
session_start();
include "config.php";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    $data  = mysqli_fetch_assoc($query);

    if ($data) {
        if (password_verify($password, $data['password'])) {
            // simpan session
            $_SESSION['username'] = $data['username'];
            $_SESSION['role'] = $data['role'];

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Inventory Barang</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-screen flex items-center justify-center bg-gradient-to-r from-[#0f2a1d] to-[#6b9071]">

  <div class="bg-white shadow-lg rounded-2xl flex overflow-hidden w-[900px] h-[500px]">
    <!-- Bagian kiri (gambar/ilustrasi) -->
    <div class="bg-[#6b9071] flex items-center justify-center w-1/2">
      <img src="assets/img/mnr-logo-white.png" alt="Inventory Illustration" class="w-3/4">
    </div>

    <!-- Bagian kanan (form login) -->
    <div class="w-1/2 p-10 flex flex-col justify-center">
      <h2 class="text-3xl font-bold text-[#0f2a1d] mb-6 text-center">Login</h2>
      
      <?php if (!empty($error)): ?>
        <p class="text-red-500 mb-4 text-center"><?= $error ?></p>
      <?php endif; ?>

      <form action="" method="POST" class="space-y-5">
        <div>
          <label class="block text-gray-700 font-medium">Username</label>
          <input type="text" name="username" required
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0f2a1d]">
        </div>

        <div>
          <label class="block text-gray-700 font-medium">Password</label>
          <input type="password" name="password" required
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#6b9071]">
        </div>

        <button type="submit" name="login"
          class="w-full bg-[#0f2a1d] text-white py-2 rounded-lg hover:bg-[#6b9071] transition">
          Login
        </button>
      </form>

      <p class="text-center text-sm mt-6 text-gray-600">
        Don’t have an account? 
        <a href="register.php" class="text-[#0f2a1d] font-semibold hover:underline">Register</a>
      </p>
    </div>
  </div>

</body>
</html>
