<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Velvet Vogue -  Fashion Collection</title>
    <link rel="stylesheet" href="/velvetvogueshe/css/style.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <h1>Velvet Vogue</h1>
                <p class="tagline">Timeless Fashion</p>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="/velvetvogueshe/index.php">Home</a></li>
                    <li><a href="/velvetvogueshe/pages/products.php">Shop</a></li>
                    <li><a href="/velvetvogueshe/pages/cart.php">Cart</a></li>
    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li><a href="/velvetvogueshe/pages/logout.php">Logout</a></li>
                        <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                            <li><a href="/velvetvogueshe/admin/dashboard.php">Admin</a></li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li><a href="/velvetvogueshe/pages/login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>