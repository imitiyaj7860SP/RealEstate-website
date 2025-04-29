<?php
session_start();
require_once 'db_connection.php';
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Land Real Estate Registration</title>
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
    <!-- Transparent Navigation Bar -->
    <nav class="absolute top-0 left-0 w-full p-4 flex justify-between items-center text-white z-10">
        <h1 class="text-2xl font-light nav-link">Real Estate Registry</h1>
        <div class="flex items-center space-x-6">
            <ul class="flex space-x-6 text-white text-sm">
                <li class="nav-link font-bold border-b-2 border-transparent transition duration-300"><a href="index.php">Home</a></li>
                <li class="nav-link font-bold border-b-2 border-transparent transition duration-300"><a href="buy.php">Buy</a></li>
                <li class="nav-link border-b-2 border-transparent font-bold transition duration-300"><a href="sell.php">Sell</a></li>
                <li class="nav-link border-b-2 border-transparent font-bold transition duration-300"><a href="contactus.php">Contact Us</a></li>
            </ul>
            <?php if ($isLoggedIn): ?>
                <div class="flex items-center space-x-4">
                    <a href="profile.php" class="nav-link text-white  border-b-2 border-transparent transition duration-300">My Profile</a>
                    <span class="text-white">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <a href="logout.php" class="nav-link bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition duration-300">Logout</a>
                </div>
            <?php else: ?>
                <a href="login.php" class="nav-link bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition duration-300">Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <header class="bg-cover bg-center h-96 flex flex-col items-center justify-center text-white text-center relative" style="background-image: url('main.jpeg');">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="relative z-10 bg-black bg-opacity-50 p-6 rounded-xl w-3/4">
            <h2 class="text-4xl font-bold">Find Your Dream Property</h2>
            <p class="mt-2">Explore the best properties available for sale and rent.</p>
            
            <form action="buy.php" method="GET" class="mt-4">
                <div class="flex bg-white p-2 rounded-md shadow-lg">
                    <input type="text" name="location" placeholder="Search by location" class="p-2 w-full outline-none text-gray-800">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-300">Search</button>
                </div>
            </form>
        </div>
    </header>
    <section class="p-10 text-center">
        <h3 class="text-3xl font-semibold mb-6">Featured Properties</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="buy.php?property_type=apartment" class="cursor-pointer">
                <div class="p-4 bg-white shadow-lg rounded-xl transform transition duration-300 hover:-translate-y-2">
                    <img src="Luxury Apartment.jpg" class="w-full rounded-md" alt="Property">
                    <h4 class="text-xl font-bold mt-3">Apartment</h4>
                    <p class="text-gray-600">$250,000 - New York</p>
                </div>
            </a>
            <a href="buy.php?property_type=villa" class="cursor-pointer">
                <div class="p-4 bg-white shadow-lg rounded-xl transform transition duration-300 hover:-translate-y-2">
                    <img src="modern villa.jpg" class="w-full rounded-md" alt="Property">
                    <h4 class="text-xl font-bold mt-3">Villa</h4>
                    <p class="text-gray-600">$450,000 - California</p>
                </div>
            </a>
            <a href="buy.php?property_type=land" class="cursor-pointer">
                <div class="p-4 bg-white shadow-lg rounded-xl transform transition duration-300 hover:-translate-y-2">
                    <img src="City Apartment.jpg" class="w-full rounded-md" alt="Property">
                    <h4 class="text-xl font-bold mt-3">Land</h4>
                    <p class="text-gray-600">$300,000 - Chicago</p>
                </div>
            </a>
        </div>
    </section>
    <section class="p-10 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <h3 class="text-3xl font-semibold mb-6 text-center">Our Services</h3>
            <p class="text-gray-600 text-center mb-12">We connect buyers, sellers, and real estate enthusiasts to build a seamless property ecosystem.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Buy Properties Card -->
                <div class="bg-white p-8 rounded-xl shadow-lg text-center transform transition duration-300 hover:-translate-y-2">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold mb-4">Find Your Dream Home</h4>
                    <p class="text-gray-600 mb-6">Discover a wide range of properties that match your preferences. Browse through apartments, villas, and lands with detailed information.</p>
                    <a href="buy.php" class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-700">
                        Browse Properties →
                    </a>
                </div>

                <!-- Sell Property Card -->
                <div class="bg-white p-8 rounded-xl shadow-lg text-center transform transition duration-300 hover:-translate-y-2">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold mb-4">List Your Property</h4>
                    <p class="text-gray-600 mb-6">Ready to sell? List your property with us and reach genuine buyers. Get the best value for your property with our extensive network.</p>
                    <a href="sell.php" class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-700">
                        Start Selling →
                    </a>
                </div>

                <!-- Contact Card -->
                <div class="bg-white p-8 rounded-xl shadow-lg text-center transform transition duration-300 hover:-translate-y-2">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold mb-4">Get in Touch</h4>
                    <p class="text-gray-600 mb-6">Have questions about buying or selling? Our expert team is here to help you with all your real estate needs.</p>
                    <a href="contactus.php" class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-700">
                        Contact Us →
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Impact Statistics Section -->
    <section class="p-10 bg-white">
        <div class="max-w-7xl mx-auto">
            <h3 class="text-3xl font-semibold mb-3 text-center">Our Impact</h3>
            <p class="text-gray-600 text-center mb-12">Building trust through successful property transactions.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
                <div>
                    <h4 class="text-4xl font-bold text-blue-600 mb-2">1,200+</h4>
                    <p class="text-gray-600">Properties Sold</p>
                </div>
                <div>
                    <h4 class="text-4xl font-bold text-blue-600 mb-2">45</h4>
                    <p class="text-gray-600">Cities Covered</p>
                </div>
                <div>
                    <h4 class="text-4xl font-bold text-blue-600 mb-2">2,500</h4>
                    <p class="text-gray-600">Active Users</p>
                </div>
                <div>
                    <h4 class="text-4xl font-bold text-blue-600 mb-2">98%</h4>
                    <p class="text-gray-600">Client Satisfaction</p>
                </div>
            </div>
        </div>
    </section>
    
    <footer class="bg-gray-900 text-white p-6 text-center">
        <p>&copy; 2025 Land Real Estate Registry. All Rights Reserved.</p>
    </footer>
</body>
</html> 