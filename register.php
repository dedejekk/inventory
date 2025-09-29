<?php
include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = isset($_POST['role']) ? mysqli_real_escape_string($conn, $_POST['role']) : "user";

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // cek dulu apakah username sudah ada
    $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' LIMIT 1");

    if (mysqli_num_rows($check) > 0) {
        // kalau sudah ada
        header("Location: register.php?error=username_taken");
        exit;
    }

    $sql = "INSERT INTO users (username, password, role) 
            VALUES ('$username', '$hashed', '$role')";

    if (mysqli_query($conn, $sql)) {
        header("Location: login.php?msg=register_success");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - Inventory Barang</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-screen flex items-center justify-center bg-gradient-to-r from-[#0f2a1d] to-[#6b9071]">

  <div class="bg-white shadow-lg rounded-2xl flex overflow-hidden w-[900px] h-[500px]">
    
    <!-- Bagian kiri (gambar/ilustrasi) -->
    <div class="bg-[#6b9071] flex items-center justify-center w-1/2">
      <img src="assets/img/mnr-logo-white.png" alt="Register Illustration" class="w-3/4">
    </div>
    
    <!-- Bagian kanan (form register) -->
    <div class="w-1/2 p-10 flex flex-col justify-center">
      <h2 class="text-3xl font-bold text-[#0f2a1d] mb-6 text-center">Register</h2>

      <?php if (!empty($message)): ?>
        <p class="text-center mb-4 <?= strpos($message,'✅')!==false ? 'text-green-600' : 'text-red-500' ?>">
          <?= $message ?>
        </p>
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
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0f2a1d]">
        </div>

      

        <button type="submit" name="register"
          class="w-full bg-[#0f2a1d] text-white py-2 rounded-lg hover:bg-[#6b9071] transition">
          Register
        </button>
      </form>

      <p class="text-center text-sm mt-6 text-gray-600">
        Already have an account? 
        <a href="login.php" class="text-[#0f2a1d] font-semibold hover:underline">Login</a>
      </p>
    </div>
  </div>

</body>
</html>
