<?php
require_once '../includes/db.php';
include '../includes/header.php';

// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$color = isset($_GET['color']) ? $_GET['color'] : '';
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : 1000;

// Build query
$sql = "SELECT * FROM products WHERE 1=1";

if ($category) {
    $sql .= " AND category = :category";
}
if ($color) {
    $sql .= " AND color LIKE :color";
}
$sql .= " AND price BETWEEN :min_price AND :max_price";

$stmt = $conn->prepare($sql);

if ($category) {
    $stmt->bindParam(':category', $category);
}
if ($color) {
    $color_param = "%$color%";
    $stmt->bindParam(':color', $color_param);
}
$stmt->bindParam(':min_price', $min_price);
$stmt->bindParam(':max_price', $max_price);

$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get unique categories and colors
$categories_stmt = $conn->query("SELECT DISTINCT category FROM products");
$categories = $categories_stmt->fetchAll(PDO::FETCH_COLUMN);

$colors_stmt = $conn->query("SELECT DISTINCT color FROM products");
$colors = $colors_stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<style>
.filters-section {
    background-color: white;
    padding: 30px;
    margin: 30px 0;
    border: 2px solid var(--light-brown);
    border-radius: 8px;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-group label {
    margin-bottom: 5px;
    font-weight: bold;
    color: var(--dark-gray);
}

.filter-group select,
.filter-group input {
    padding: 8px;
    border: 2px solid var(--light-brown);
    border-radius: 5px;
    font-size: 1rem;
}

.filter-btn {
    grid-column: 1 / -1;
    padding: 12px;
    background-color: var(--primary-brown);
    color: var(--cream);
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1.1rem;
}

.filter-btn:hover {
    background-color: var(--accent-gold);
    color: var(--dark-gray);
}
</style>

<div class="container">
    <h1 class="section-title">Shop Our Collection</h1>
    
    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" action="">
            <div class="filters-grid">
                <div class="filter-group">
                    <label>Category</label>
                    <select name="category">
                        <option value="">All Categories</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat; ?>" 
                                <?php if($category == $cat) echo 'selected'; ?>>
                                <?php echo $cat; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Color</label>
                    <select name="color">
                        <option value="">All Colors</option>
                        <?php foreach($colors as $col): ?>
                            <option value="<?php echo $col; ?>" 
                                <?php if($color == $col) echo 'selected'; ?>>
                                <?php echo $col; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Min Price</label>
                    <input type="number" name="min_price" value="<?php echo $min_price; ?>" min="0">
                </div>
                
                <div class="filter-group">
                    <label>Max Price</label>
                    <input type="number" name="max_price" value="<?php echo $max_price; ?>" min="0">
                </div>
                
                <button type="submit" class="filter-btn">Apply Filters</button>
            </div>
        </form>
    </div>
    
    <!-- Products Grid -->
    <div class="product-grid">
        <?php if(count($products) > 0): ?>
            <?php foreach($products as $product): ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         class="product-image">
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p style="color: var(--dark-gray); font-size: 0.9rem; margin-bottom: 10px;">
                            <?php echo htmlspecialchars($product['category']); ?>
                        </p>
                        <p class="product-price">
                            <?php if($product['sale_price']): ?>
                                <span class="old-price">$<?php echo number_format($product['price'], 2); ?></span>
                                $<?php echo number_format($product['sale_price'], 2); ?>
                            <?php else: ?>
                                $<?php echo number_format($product['price'], 2); ?>
                            <?php endif; ?>
                        </p>
                        <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="grid-column: 1/-1; text-align: center; font-size: 1.2rem; color: var(--dark-gray);">
                No products found matching your filters.
            </p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>