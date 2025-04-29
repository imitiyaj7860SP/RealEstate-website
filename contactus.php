<?php
session_start();
$isLoggedIn = isset($_SESSION['user_name']) ? true : false;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Your Property - Real Estate Registry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .nav-link {
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- White Navigation Bar -->
    <nav class="bg-white shadow-lg w-full p-4 flex justify-between items-center">
        <h1 class="text-2xl font-light nav-link text-gray-800">Real Estate Registry</h1>
        <div class="flex items-center space-x-6">
            <ul class="flex space-x-6 text-sm">
                <li class="nav-link font-bold border-b-2 border-transparent transition duration-300"><a href="index.php" class="text-gray-700 hover:text-gray-900">Home</a></li>
                <li class="nav-link font-bold border-b-2 border-transparent transition duration-300"><a href="buy.php" class="text-gray-700 hover:text-gray-900">Buy</a></li>
                <li class="nav-link border-b-2 border-transparent font-bold transition duration-300"><a href="sell.php" class="text-gray-700 hover:text-gray-900">Sell</a></li>
                <li class="nav-link border-b-2 border-transparent font-bold transition duration-300"><a href="contactus.php">Contact Us</a></li>
            </ul>
            <?php if ($isLoggedIn): ?>
                <div class="flex items-center space-x-4">
                    <a href="profile.php" class="nav-link text-gray-700 hover:text-gray-900 transition duration-300">My Profile</a>
                    <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <a href="logout.php" class="nav-link bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition duration-300">Logout</a>
                </div>
            <?php else: ?>
                <a href="login.php" class="nav-link bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition duration-300">Login</a>
            <?php endif; ?>
        </div>
    </nav>
  <!-- Hero Section -->
  <div class="relative h-[400px] mb-12">
    <img src="image2.avif" alt="Modern Building" class="w-full h-full object-cover"/>
    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <h1 class="text-white text-5xl font-bold text-center">Get in Touch</h1>
    </div>
  </div>

  <!-- Contact Section -->
  <div class="container mx-auto px-6 py-12">
    <div class="grid lg:grid-cols-2 gap-12">
      <!-- Contact Form -->
      <div class="bg-white rounded-xl shadow-xl p-8">
        <h2 class="text-3xl font-bold text-blue-700 mb-6">Send Us a Message</h2>
        <form action="contactBackend.php" method="POST" class="space-y-6">
          <input name="name" type="text" placeholder="Full Name" required
                 class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
          <input name="email" type="email" placeholder="Email Address" required
                 class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
          <input name="phone" type="tel" placeholder="Phone Number"
                 class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
          <textarea name="message" rows="5" placeholder="Your Message" required
                    class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
          <button type="submit"
                  class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg shadow-lg transition duration-300">
            <i class="fas fa-paper-plane mr-2"></i>Send Message
          </button>
        </form>
      </div>

      <!-- Contact Info -->
      <div class="bg-gradient-to-br from-blue-700 to-blue-600 rounded-xl shadow-xl p-8 text-white">
        <h2 class="text-3xl font-bold mb-8">Contact Information</h2>
        <div class="space-y-8">
          <div class="flex items-start space-x-4">
            <i class="fas fa-map-marker-alt text-2xl mt-1"></i>
            <div>
              <h4 class="text-xl font-semibold mb-2">Our Location</h4>
              <p class="text-blue-100">123 Land Registry Street, City Center</p>
            </div>
          </div>
          <div class="flex items-start space-x-4">
            <i class="fas fa-phone text-2xl mt-1"></i>
            <div>
              <h4 class="text-xl font-semibold mb-2">Phone Number</h4>
              <p class="text-blue-100">+1 234 567 8900</p>
            </div>
          </div>
          <div class="flex items-start space-x-4">
            <i class="fas fa-envelope text-2xl mt-1"></i>
            <div>
              <h4 class="text-xl font-semibold mb-2">Email Address</h4>
              <p class="text-blue-100">contact@landregistry.gov</p>
            </div>
          </div>
          <div class="flex items-start space-x-4">
            <i class="fas fa-clock text-2xl mt-1"></i>
            <div>
              <h4 class="text-xl font-semibold mb-2">Working Hours</h4>
              <p class="text-blue-100">Monday - Friday: 9:00 AM - 5:00 PM</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-gradient-to-r from-blue-700 to-blue-600 text-white mt-12 py-6">
    <div class="container mx-auto px-6 text-center">
      <p>&copy; 2025 Land Registration Portal. All rights reserved.</p>
    </div>
  </footer>
</body>
</html>
