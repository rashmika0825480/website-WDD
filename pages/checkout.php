<?php
require_once '../includes/db.php';
include '../includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if cart is empty
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    header('Location: cart.php');
    exit();
}

// Calculate totals
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$tax = $subtotal * 0.08;
$shipping = $subtotal > 100 ? 0 : 10;
$total = $subtotal + $tax + $shipping;

// Handle checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $zip = trim($_POST['zip']);
    $phone = trim($_POST['phone']);
    
    $full_address = "$address, $city, $state $zip. Phone: $phone";
    
    // Insert order
    $order_stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, status) 
                                  VALUES (:user_id, :total, :address, 'pending')");
    $order_stmt->bindParam(':user_id', $_SESSION['user_id']);
    $order_stmt->bindParam(':total', $total);
    $order_stmt->bindParam(':address', $full_address);
    
    if ($order_stmt->execute()) {
        $order_id = $conn->lastInsertId();
        
        // Insert order items
        foreach ($_SESSION['cart'] as $item) {
            $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) 
                                        VALUES (:order_id, :product_id, :quantity, :price)");
            $item_stmt->bindParam(':order_id', $order_id);
            $item_stmt->bindParam(':product_id', $item['product_id']);
            $item_stmt->bindParam(':quantity', $item['quantity']);
            $item_stmt->bindParam(':price', $item['price']);
            $item_stmt->execute();
            
            // Update stock
            $update_stock = $conn->prepare("UPDATE products SET stock_quantity = stock_quantity - :qty 
                                           WHERE id = :id");
            $update_stock->bindParam(':qty', $item['quantity']);
            $update_stock->bindParam(':id', $item['product_id']);
            $update_stock->execute();
        }
        
        // Clear cart
        $_SESSION['cart'] = array();
        
        header('Location: order-success.php?order_id=' . $order_id);
        exit();
    }
}
?>

<style>
.checkout-container {
    max-width: 800px;
    margin: 50px auto;
}

.checkout-form {
    background-color: white;
    padding: 40px;
    border: 2px solid var(--light-brown);
    border-radius: 8px;
    margin-bottom: 30px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.order-summary-box {
    background-color: #f9f9f9;
    padding: 30px;
    border: 2px solid var(--light-brown);
    border-radius: 8px;
}
</style>

<div class="container">
    <div class="checkout-container">
        <h1 class="section-title">Checkout</h1>
        
        <form method="POST" action="" class="checkout-form">
            <h2 style="margin-bottom: 20px; color: var(--dark-gray);">Shipping Information</h2>
            
            <div class="form-group">
                <label>Street Address</label>
                <input type="text" name="address" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>City</label>
                    <input type="text" name="city" required>
                </div>
                
                <div class="form-group">
                    <label>State</label>
                    <input type="text" name="state" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>ZIP Code</label>
                    <input type="text" name="zip" required>
                </div>
                
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" required>
                </div>
            </div>
            
            <div class="order-summary-box">
                <h3 style="margin-bottom: 15px;">Order Summary</h3>
                <?php foreach($_SESSION['cart'] as $item): ?>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span><?php echo $item['name']; ?> (x<?php echo $item['quantity']; ?>)</span>
                        <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    </div>
                <?php endforeach; ?>
                <hr style="margin: 15px 0;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span>Subtotal:</span>
                    <span>$<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span>Tax:</span>
                    <span>$<?php echo number_format($tax, 2); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span>Shipping:</span>
                    <span><?php echo $shipping == 0 ? 'FREE' : '$' . number_format($shipping, 2); ?></span>
                </div>
                <hr style="margin: 15px 0;">
                <div style="display: flex; justify-content: space-between; font-size: 1.3rem; font-weight: bold; color: var(--primary-brown);">
                    <span>Total:</span>
                    <span>$<?php echo number_format($total, 2); ?></span>
                </div>
            </div>
            
            <button type="submit" name="place_order" class="submit-btn" style="margin-top: 20px;">
                Place Order
            </button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>