<?php
session_start();


$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'property_db';

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("<script>alert('Database connection failed');</script>");
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];
    $address = $_POST['address'];
    $country = $_POST['country'];
    $pincode = $_POST['pincode'];
    $terms = isset($_POST['terms']);

    
    if (!$terms) $errors[] = "You must accept terms and conditions.";
    if (empty($name)) $errors[] = "Name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email.";
    if (!preg_match("/^[0-9]{10}$/", $phone)) $errors[] = "Invalid phone number.";
    if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters.";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match.";
    if (!in_array($role, ['Buyer', 'Seller', 'Government Official'])) $errors[] = "Invalid role.";

    
    $aadhar_path = null;
    $id_proof_path = null;
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    if (!empty($_FILES['aadhar']['name'])) {
        $aadhar_path = $upload_dir . time() . '_aadhar_' . basename($_FILES['aadhar']['name']);
        move_uploaded_file($_FILES['aadhar']['tmp_name'], $aadhar_path);
    }

    if (!empty($_FILES['id_proof']['name'])) {
        $id_proof_path = $upload_dir . time() . '_id_' . basename($_FILES['id_proof']['name']);
        move_uploaded_file($_FILES['id_proof']['tmp_name'], $id_proof_path);
    }

    
    if (empty($errors)) {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, role, address, country, pincode, aadhar_path, id_proof_path, created_at)
                                    VALUES (:name, :email, :phone, :password, :role, :address, :country, :pincode, :aadhar, :id_proof, NOW())");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':phone' => $phone,
                ':password' => $hash,
                ':role' => $role,
                ':address' => $address,
                ':country' => $country,
                ':pincode' => $pincode,
                ':aadhar' => $aadhar_path,
                ':id_proof' => $id_proof_path
            ]);
            echo "<script>alert('✅ Registration successful!'); window.location.href='login.php';</script>";
            exit;
        } catch (PDOException $e) {
            echo "<script>alert('❌ Error: Email already exists or DB error.');</script>";
        }
    } else {
        echo "<script>alert('⚠️ Errors:\\n" . implode("\\n", $errors) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Registration</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-green-900 via-black to-blue-900 min-h-screen flex items-center justify-center p-4">
  <div class="max-w-xl w-full bg-white p-6 rounded-xl shadow-2xl border-t-4 border-green-500">
    <h2 class="text-3xl font-extrabold text-green-600 mb-6 text-center">🌍 Sign Up</h2>

    <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-800 mb-1">Full Name</label>
          <input type="text" name="name" required placeholder="John Doe"
                 class="w-full p-2 rounded-lg border-2 border-green-300 bg-gray-100 text-black"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-800 mb-1">Email</label>
          <input type="email" name="email" required placeholder="you@example.com"
                 class="w-full p-2 rounded-lg border-2 border-green-300 bg-gray-100 text-black"/>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-800 mb-1">Password</label>
          <input type="password" name="password" required placeholder="••••••••"
                 class="w-full p-2 rounded-lg border-2 border-green-300 bg-gray-100 text-black"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-800 mb-1">Confirm Password</label>
          <input type="password" name="confirm_password" required placeholder="••••••••"
                 class="w-full p-2 rounded-lg border-2 border-green-300 bg-gray-100 text-black"/>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-800 mb-1">Phone Number</label>
          <input type="tel" name="phone" required placeholder="9876543210"
                 class="w-full p-2 rounded-lg border-2 border-green-300 bg-gray-100 text-black"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-800 mb-1">User Role</label>
          <select name="role" required
                  class="w-full p-2 rounded-lg border-2 border-green-300 bg-gray-100 text-black">
            <option value="">Select Role</option>
            <option>Buyer</option>
            <option>Seller</option>
            <option>Government Official</option>
          </select>
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-800 mb-1">Residential Address</label>
        <input type="text" name="address" required placeholder="123 Green St, City"
               class="w-full p-2 rounded-lg border-2 border-green-300 bg-gray-100 text-black"/>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-800 mb-1">Country</label>
          <input type="text" name="country" required placeholder="India"
                 class="w-full p-2 rounded-lg border-2 border-green-300 bg-gray-100 text-black"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-800 mb-1">Pincode</label>
          <input type="text" name="pincode" required placeholder="400001"
                 class="w-full p-2 rounded-lg border-2 border-green-300 bg-gray-100 text-black"/>
        </div>
      </div>

      
      <div>
        <label class="block text-sm font-medium text-gray-800 mb-1">Upload Aadhar (optional)</label>
        <input type="file" name="aadhar"
               class="w-full p-2 rounded-lg border-2 border-green-300 bg-white text-gray-800"/>
      </div>

      
      <div>
        <label class="block text-sm font-medium text-gray-800 mb-1">Upload ID Proof (optional)</label>
        <input type="file" name="id_proof"
               class="w-full p-2 rounded-lg border-2 border-green-300 bg-white text-gray-800"/>
      </div>

      <div class="flex items-center space-x-2">
        <input type="checkbox" name="terms" required class="accent-green-600 w-4 h-4"/>
        <label class="text-sm text-gray-700">I agree to the <a href="#" class="text-blue-500 hover:underline">Terms</a></label>
      </div>

      <div class="text-center pt-2">
        <button type="submit"
                class="bg-gradient-to-r from-green-500 to-blue-600 hover:from-green-600 hover:to-blue-700 text-white px-6 py-2 rounded-lg font-semibold shadow-lg transform hover:scale-105 transition duration-300">
          🚀 Register
        </button>
      </div>
    </form>
  </div>
</body>
</html>
