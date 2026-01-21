<?php
require_once 'includes/db.php';
include 'includes/header.php';

// Get featured products
$stmt = $conn->prepare("SELECT * FROM products WHERE featured = 1 LIMIT 6");
$stmt->execute();
$featured_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-content">
        <h2>Discover Timeless Fashion</h2>
        <p>Elegant styles that never fade</p>
        <a href="pages/products.php" class="btn">Shop Collection</a>
    </div>
</section>

<!-- Featured Products -->
<section class="products-section">
    <div class="container">
        <h2 class="section-title">Featured Collection</h2>
        <div class="product-grid">
            <?php foreach($featured_products as $product): ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         class="product-image">
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-price">
                            <?php if($product['sale_price']): ?>
                                <span class="old-price">$<?php echo number_format($product['price'], 2); ?></span>
                                $<?php echo number_format($product['sale_price'], 2); ?>
                            <?php else: ?>
                                $<?php echo number_format($product['price'], 2); ?>
                            <?php endif; ?>
                        </p>
                        <a href="pages/product-detail.php?id=<?php echo $product['id']; ?>" class="btn">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>