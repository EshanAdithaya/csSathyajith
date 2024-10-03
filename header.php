<?php
session_start();
include_once('dashboard/db.php');

// Fetch products from the database
$sql = "SELECT id, name, price, image_path, stock, description, light_requirement, water_requirement, max_growth FROM products WHERE is_active = TRUE";
$result = $conn->query($sql);
$products = $result->fetch_all(MYSQLI_ASSOC);

// Get cart count
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $cart_sql = "SELECT SUM(quantity) as count FROM cart WHERE user_id = ?";
    $cart_stmt = $conn->prepare($cart_sql);
    $cart_stmt->bind_param("i", $user_id);
    $cart_stmt->execute();
    $cart_result = $cart_stmt->get_result();
    $cart_count = $cart_result->fetch_assoc()['count'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Showcase - Plantex</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .nav-links {
            display: flex;
            list-style: none;
        }

        .nav-links li {
            margin: 0 1rem;
        }

        .user-section {
            display: flex;
            align-items: center;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropbtn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .logout-btn {
            color: #ff0000 !important;
        }

        .cart-icon {
            position: relative;
            cursor: pointer;
            margin-left: 1rem;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <header class="header" id="header">
        <nav class="nav container">
            <a href="#" class="nav__logo">
                <i class="ri-leaf-line nav__logo-icon"></i> Plantex
            </a>

            <div class="nav__menu" id="nav-menu">
                <ul class="nav__list">
                    <li class="nav__item">
                        <a href="index.php" class="nav__link active-link">Home</a>
                    </li>
                    <li class="nav__item">
                        <a href="productShow.php" class="nav__link">Products</a>
                    </li>
                    <li class="nav__item">
                        <a href="#faqs" class="nav__link">FAQs</a>
                    </li>
                    <li class="nav__item">
                        <a href="#contact" class="nav__link">Contact Us</a>
                    </li>
                </ul>

                <div class="nav__close" id="nav-close">
                    <i class="ri-close-line"></i>
                </div>
            </div>

            <div class="nav__btns">
                <div class="cart-icon" id="cart-icon">
                    <i class="ri-shopping-cart-line"></i>
                    <?php if ($cart_count > 0): ?>
                        <span class="cart-count"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </div>
                <i class="ri-moon-line change-theme" id="theme-button"></i>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <button class="dropbtn"><?php echo htmlspecialchars($_SESSION['username']); ?></button>
                        <div class="dropdown-content">
                            <a href="orders.php">Order History</a>
                            <a href="account.php">My Account</a>
                           
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <a href="dashboard/dashboard.php">Go to Admin Dashboard</a>
                            <?php endif; ?>
                            <a href="dashboard/logout.php" class="logout-btn">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="login-link">Login</a>
                <?php endif; ?>
                <div class="nav__toggle" id="nav-toggle">
                    <i class="ri-menu-line"></i>
                </div>
            </div>
        </nav>
    </header>
</body>
</html>
