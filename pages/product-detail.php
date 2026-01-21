<?php
require_once '../includes/db.php';
include '../includes/header.php';

// pro ID
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id == 0) {
    header('Location: products.php');
    exit();
}

// pro detil
$stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
$stmt->bindParam(':id', $product_id);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: products.php');
    exit();
}

// manage add to cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $selected_size = isset($_POST['size']) ? $_POST['size'] : '';
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    
    $cart_key = $product_id . '_' . $selected_size;
    
    if (isset($_SESSION['cart'][$cart_key])) {
        $_SESSION['cart'][$cart_key]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$cart_key] = array(
            'product_id' => $product_id,
            'name' => $product['name'],
            'price' => $product['sale_price'] ? $product['sale_price'] : $product['price'],
            'quantity' => $quantity,
            'size' => $selected_size,
            'image' => $product['image_url']
        );
    }
    
    $success_message = "Product added to cart!";
}

// sizes 
$sizes = explode(',', $product['size']);
?>

<style>
.product-detail {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 50px;
    padding: 50px 0;
}

.product-detail-image {
    width: 100%;
    border-radius: 8px;
    border: 3px solid var(--light-brown);
}

.product-detail-info h1 {
    font-size: 2.5rem;
    color: var(--dark-gray);
    margin-bottom: 20px;
}

.product-detail-price {
    font-size: 2rem;
    color: var(--primary-brown);
    font-weight: bold;
    margin-bottom: 20px;
}

.product-description {
    margin-bottom: 30px;
    line-height: 1.8;
    color: var(--dark-gray);
}

.product-meta {
    background-color: #f9f9f9;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
    border: 2px solid var(--light-brown);
}

.product-meta p {
    margin-bottom: 10px;
    color: var(--dark-gray);
}

.size-selector {
    margin-bottom: 20px;
}

.size-selector label {
    display: block;
    margin-bottom: 10px;
    font-weight: bold;
    color: var(--dark-gray);
}

.size-options {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.size-option {
    padding: 10px 20px;
    border: 2px solid var(--light-brown);
    background-color: white;
    cursor: pointer;
    border-radius: 5px;
    transition: all 0.3s;
}

.size-option:hover {
    background-color: var(--primary-brown);
    color: var(--cream);
}

.size-option input[type="radio"] {
    display: none;
}

.size-option input[type="radio"]:checked + span {
    background-color: var(--primary-brown);
    color: var(--cream);
}

.quantity-selector {
    margin-bottom: 20px;
}

.quantity-selector label {
    display: block;
    margin-bottom: 10px;
    font-weight: bold;
    color: var(--dark-gray);
}

.quantity-selector input {
    width: 80px;
    padding: 10px;
    border: 2px solid var(--light-brown);
    border-radius: 5px;
    font-size: 1rem;
}

.add-to-cart-btn {
    width: 100%;
    padding: 15px;
    background-color: var(--primary-brown);
    color: var(--cream);
    border: 2px solid var(--accent-gold);
    border-radius: 5px;
    font-size: 1.2rem;
    cursor: pointer;
    transition: all 0.3s;
}

.add-to-cart-btn:hover {
    background-color: var(--accent-gold);
    color: var(--dark-gray);
}

.success-message {
    background-color: #d4edda;
    color: #155724;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
    border: 2px solid #c3e6cb;
}

@media (max-width: 768px) {
    .product-detail {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="container">
    <?php if(isset($success_message)): ?>
        <div class="success-message"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <div class="product-detail">
        <div>
            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                 class="product-detail-image">
        </div>
        
        <div class="product-detail-info">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            
            <div class="product-detail-price">
                <?php if($product['sale_price']): ?>
                    <span class="old-price" style="font-size: 1.5rem;">
                        $<?php echo number_format($product['price'], 2); ?>
                    </span>
                    $<?php echo number_format($product['sale_price'], 2); ?>
                <?php else: ?>
                    $<?php echo number_format($product['price'], 2); ?>
                <?php endif; ?>
            </div>
            
            <div class="product-description">
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>
            
            <div class="product-meta">
                <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
                <p><strong>Material:</strong> <?php echo htmlspecialchars($product['material']); ?></p>
                <p><strong>Color:</strong> <?php echo htmlspecialchars($product['color']); ?></p>
                <p><strong>Stock:</strong> <?php echo $product['stock_quantity']; ?> available</p>
            </div>
            
            <form method="POST" action="">
                <div class="size-selector">
                    <label>Select Size:</label>
                    <div class="size-options">
                        <?php foreach($sizes as $size): ?>
                            <label class="size-option">
                                <input type="radio" name="size" value="<?php echo trim($size); ?>" required>
                                <span style="display: block; padding: 10px 20px;">
                                    <?php echo trim($size); ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="quantity-selector">
                    <label>Quantity:</label>
                    <input type="number" name="quantity" value="1" min="1" 
                           max="<?php echo $product['stock_quantity']; ?>">
                </div>
                
                <button type="submit" name="add_to_cart" class="add-to-cart-btn">
                    Add to Cart
                </button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>