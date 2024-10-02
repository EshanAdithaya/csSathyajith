<?php
include('header.php');
include('userSession.php');
include_once('dashboard/db.php');

// Function to get order details
function getOrderDetails($order_id) {
    global $conn;
    $sql = "SELECT orders.*, users.username, users.email 
            FROM orders 
            JOIN users ON orders.user_id = users.id 
            WHERE orders.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to get order items
function getOrderItems($order_id) {
    global $conn;
    $sql = "SELECT order_items.*, products.name, products.price 
            FROM order_items 
            JOIN products ON order_items.product_id = products.id 
            WHERE order_items.order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$order = getOrderDetails($order_id);
$order_items = getOrderItems($order_id);

// Check if order exists and belongs to the current user
if (!$order || $order['user_id'] != $_SESSION['user_id']) {
    header("Location: index.php");
    exit();
}
?>

<div class="container">
    <div class="order-confirmation">
        <h1>Order Confirmation</h1>
        
        <div class="order-details">
            <h2>Order #<?php echo htmlspecialchars($order_id); ?></h2>
            <p>Status: <span class="status-<?php echo htmlspecialchars($order['status']); ?>"><?php echo ucfirst(htmlspecialchars($order['status'])); ?></span></p>
            <p>Order Date: <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
        </div>

        <div class="customer-details">
            <h2>Customer Information</h2>
            <p>Name: <?php echo htmlspecialchars($order['username']); ?></p>
            <p>Email: <?php echo htmlspecialchars($order['email']); ?></p>
        </div>

        <div class="order-items">
            <h2>Order Items</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td>$<?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3">Total</td>
                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="actions">
            <a href="index.php" class="button">Continue Shopping</a>
            <a href="orders.php" class="button">View All Orders</a>
        </div>
    </div>
</div>

<style>
.order-confirmation {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.order-details, .customer-details, .order-items {
    margin-bottom: 30px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

th, td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

tfoot td {
    font-weight: bold;
}

.status-pending { color: #f0ad4e; }
.status-processing { color: #5bc0de; }
.status-shipped { color: #5cb85c; }
.status-delivered { color: #5cb85c; }

.actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.button {
    display: inline-block;
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    text-decoration: none;
    border-radius: 4px;
}

.button:hover {
    background-color: #45a049;
}
</style>

<?php include('footer.php'); ?>