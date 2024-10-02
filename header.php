<?php
include_once('userSession.php');
include_once('dashboard/db.php');

// Fetch products from the database
$sql = "SELECT id, name, price, image_path, stock, description, light_requirement, water_requirement, max_growth FROM products  WHERE is_active = TRUE";
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
        .cart-icon {
            position: relative;
            cursor: pointer;
        }
        .cart-count {
            position: absolute;
            top: -10px;
            right: -10px;
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
                <a href="login.php" class="login-link">Login</a>
                <div class="nav__toggle" id="nav-toggle">
                    <i class="ri-menu-line"></i>
                </div>
            </div>
        </nav>
    </header>

