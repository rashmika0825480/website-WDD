<?php
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../pages/login.php');
    exit();
}

include '../includes/header.php';


$total_products = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_orders = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_users = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_revenue = $conn->query("SELECT SUM(total_amount) FROM orders")->fetchColumn();


$recent_orders = $conn->query("SELECT o.*, u.username FROM orders o 
                              JOIN users u ON o.user_id = u.id 
                              ORDER BY o.created_at DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $sale_price = !empty($_POST['sale_price']) ? floatval($_POST['sale_price']) : null;
    $image_url = trim($_POST['image_url']);
    $category = trim($_POST['category']);
    $subcategory = trim($_POST['subcategory']);
    $gender = $_POST['gender'];
    $size = trim($_POST['size']);
    $color = trim($_POST['color']);
    $material = trim($_POST['material']);
    $stock = intval($_POST['stock']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $new_arrival = isset($_POST['new_arrival']) ? 1 : 0;
    
    $stmt = $conn->prepare("INSERT INTO products (name, description, price, sale_price, image_url, 
                           category, subcategory, gender, size, color, material, stock_quantity, 
                           featured, new_arrival) 
                           VALUES (:name, :desc, :price, :sale_price, :image, :cat, :subcat, :gender, 
                           :size, :color, :material, :stock, :featured, :new_arrival)");
    
    $stmt->execute([
        ':name' => $name,
        ':desc' => $description,
        ':price' => $price,
        ':sale_price' => $sale_price,
        ':image' => $image_url,
        ':cat' => $category,
        ':subcat' => $subcategory,
        ':gender' => $gender,
        ':size' => $size,
        ':color' => $color,
        ':material' => $material,
        ':stock' => $stock,
        ':featured' => $featured,
        ':new_arrival' => $new_arrival
    ]);
    
    $success_msg = "Product added successfully!";
}
?>

<style>
.admin-dashboard {
    padding: 40px 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    margin-bottom: 50px;
}

.stat-card {
    background-color: white;
    padding: 30px;
    border: 2px solid var(--light-brown);
    border-radius: 8px;
    text-align: center;
}

.stat-number {
    font-size: 2.5rem;
    color: var(--primary-brown);
    font-weight: bold;
    margin-bottom: 10px;
}

.stat-label {
    color: var(--dark-gray);
    font-size: 1.1rem;
}

.admin-section {
    background-color: white;
    padding: 30px;
    border: 2px solid var(--light-brown);
    border-radius: 8px;
    margin-bottom: 30px;
}

.admin-section h2 {
    color: var(--dark-gray);
    margin-bottom: 20px;
    border-bottom: 2px solid var(--primary-brown);
    padding-bottom: 10px;
}

.orders-table {
    width: 100%;
    border-collapse: collapse;
}

.orders-table th {
    background-color: var(--dark-gray);
    color: var(--cream);
    padding: 12px;
    text-align: left;
}

.orders-table td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
}

.status-badge {
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 0.9rem;
}

.status-pending {
    background-color: #ffc107;
    color: #000;
}

.status-processing {
    background-color: #17a2b8;
    color: #fff;
}

.status-shipped {
    background-color: #007bff;
    color: #fff;
}

.status-delivered {
    background-color: #28a745;
    color: #fff;
}
</style>

<div class="container">
    <div class="admin-dashboard">
        <h1 class="section-title">Admin Dashboard</h1>
        
        <?php if(isset($success_msg)): ?>
            <div class="success-message"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_products; ?></div>
                <div class="stat-label">Total Products</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_orders; ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_users; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number">$<?php echo number_format($total_revenue, 2); ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>
        
       
        <div class="admin-section">
            <h2>Add New Product</h2>
            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Category</label>
                        <input type="text" name="category" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4" 
                              style="width: 100%; padding: 10px; border: 2px solid var(--light-brown); border-radius: 5px;"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" step="0.01" name="price" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Sale Price (optional)</label>
                        <input type="number" step="0.01" name="sale_price">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Image URL</label>
                    <input type="url" name="image_url" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Subcategory</label>
                        <input type="text" name="subcategory">
                    </div>
                    
                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender" 
                                style="width: 100%; padding: 10px; border: 2px solid var(--light-brown); border-radius: 5px;">
                            <option value="Women">Women</option>
                            <option value="Unisex">Unisex</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Sizes (comma separated)</label>
                        <input type="text" name="size" placeholder="XS,S,M,L,XL">
                    </div>
                    
                    <div class="form-group">
                        <label>Color</label>
                        <input type="text" name="color">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Material</label>
                        <input type="text" name="material">
                    </div>
                    
                    <div class="form-group">
                        <label>Stock Quantity</label>
                        <input type="number" name="stock" value="0">
                    </div>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: inline-flex; align-items: center; margin-right: 20px;">
                        <input type="checkbox" name="featured" style="width: auto; margin-right: 5px;">
                        Featured Product
                    </label>
                    
                    <label style="display: inline-flex; align-items: center;">
                        <input type="checkbox" name="new_arrival" style="width: auto; margin-right: 5px;">
                        New Arrival
                    </label>
                </div>
                
                <button type="submit" name="add_product" class="btn">Add Product</button>
            </form>
        </div>
        
        
        <div class="admin-section">
            <h2>Recent Orders</h2>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($recent_orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['username']); ?></td>
                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>