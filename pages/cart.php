<?php
require_once '../includes/db.php';
include '../includes/header.php';

// Initializ cart ifn't exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

//  cart updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantity'] as $key => $qty) {
            if ($qty > 0) {
                $_SESSION['cart'][$key]['quantity'] = intval($qty);
            } else {
                unset($_SESSION['cart'][$key]);
            }
        }
        $update_message = "Cart updated successfully!";
    }
    
    if (isset($_POST['remove_item'])) {
        $remove_key = $_POST['remove_item'];
        unset($_SESSION['cart'][$remove_key]);
        $update_message = "Item removed from cart!";
    }
}

// Calculate 
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$tax = $subtotal * 0.08; // 8% tax
$shipping = $subtotal > 100 ? 0 : 10; // Free shipping over $100
$total = $subtotal + $tax + $shipping;
?>

<style>
.cart-container {
    max-width: 1000px;
    margin: 50px auto;
}

.cart-table {
    width: 100%;
    background-color: white;
    border: 2px solid var(--light-brown);
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 30px;
}

.cart-table th {
    background-color: var(--dark-gray);
    color: var(--cream);
    padding: 15px;
    text-align: left;
}

.cart-table td {
    padding: 15px;
    border-bottom: 1px solid var(--light-brown);
}

.cart-item-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 5px;
}

.quantity-input {
    width: 60px;
    padding: 5px;
    border: 2px solid var(--light-brown);
    border-radius: 5px;
}

.remove-btn {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
}

.remove-btn:hover {
    background-color: #c82333;
}

.cart-summary {
    background-color: white;
    padding: 30px;
    border: 2px solid var(--light-brown);
    border-radius: 8px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.summary-row.total {
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--primary-brown);
    border-bottom: none;
}

.checkout-btn {
    width: 100%;
    padding: 15px;
    background-color: var(--primary-brown);
    color: var(--cream);
    border: 2px solid var(--accent-gold);
    border-radius: 5px;
    font-size: 1.2rem;
    cursor: pointer;
    text-decoration: none;
    display: block;
    text-align: center;
    margin-top: 20px;
}

.checkout-btn:hover {
    background-color: var(--accent-gold);
    color: var(--dark-gray);
}

.empty-cart {
    text-align: center;
    padding: 50px;
    background-color: white;
    border: 2px solid var(--light-brown);
    border-radius: 8px;
}

.continue-shopping {
    display: inline-block;
    margin-top: 20px;
    padding: 12px 30px;
    background-color: var(--primary-brown);
    color: var(--cream);
    text-decoration: none;
    border-radius: 5px;
}
</style>

<div class="container">
    <div class="cart-container">
        <h1 class="section-title">Shopping Cart</h1>
        
        <?php if(isset($update_message)): ?>
            <div class="success-message"><?php echo $update_message; ?></div>
        <?php endif; ?>
        
        <?php if(count($_SESSION['cart']) > 0): ?>
            <form method="POST" action="">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Size</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($_SESSION['cart'] as $key => $item): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                                             class="cart-item-image">
                                        <span><?php echo htmlspecialchars($item['name']); ?></span>
                                    </div>
                                </td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($item['size']); ?></td>
                                <td>
                                    <input type="number" name="quantity[<?php echo $key; ?>]" 
                                           value="<?php echo $item['quantity']; ?>" 
                                           min="0" class="quantity-input">
                                </td>
                                <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                <td>
                                    <button type="submit" name="remove_item" value="<?php echo $key; ?>" 
                                            class="remove-btn">Remove</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <button type="submit" name="update_cart" class="btn">Update Cart</button>
            </form>
            
            <div class="cart-summary">
                <h2 style="margin-bottom: 20px; color: var(--dark-gray);">Order Summary</h2>
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>$<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Tax (8%):</span>
                    <span>$<?php echo number_format($tax, 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span><?php echo $shipping == 0 ? 'FREE' : '$' . number_format($shipping, 2); ?></span>
                </div>
                <div class="summary-row total">
                    <span>Total:</span>
                    <span>$<?php echo number_format($total, 2); ?></span>
                </div>
                
                <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <h2>Your cart is empty</h2>
                <p>Add some beautiful vintage pieces to your cart!</p>
                <a href="products.php" class="continue-shopping">Continue Shopping</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>