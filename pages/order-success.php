<?php
require_once '../includes/db.php';
include '../includes/header.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id == 0 || !isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Get order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = :id AND user_id = :user_id");
$stmt->bindParam(':id', $order_id);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: ../index.php');
    exit();
}
?>

<style>
.success-container {
    max-width: 600px;
    margin: 80px auto;
    text-align: center;
    background-color: white;
    padding: 50px;
    border: 2px solid var(--light-brown);
    border-radius: 8px;
}

.success-icon {
    font-size: 5rem;
    color: #28a745;
    margin-bottom: 20px;
}

.order-number {
    font-size: 1.5rem;
    color: var(--primary-brown);
    margin: 20px 0;
}
</style>

<div class="container">
    <div class="success-container">
        <div class="success-icon">✓</div>
        <h1 style="color: var(--dark-gray); margin-bottom: 20px;">Order Placed Successfully!</h1>
        <p class="order-number">Order #<?php echo $order_id; ?></p>
        <p style="margin-bottom: 30px; color: var(--dark-gray);">
            Thank you for your purchase! We'll send you an email confirmation shortly.
        </p>
        <p style="margin-bottom: 20px;">
            Total Amount: <strong>$<?php echo number_format($order['total_amount'], 2); ?></strong>
        </p>
        <a href="../index.php" class="btn">Continue Shopping</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>