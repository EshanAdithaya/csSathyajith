<?php
include('header.php');
include('userSession.php');
include_once('dashboard/db.php');

// Function to get all orders for a user
function getUserOrders($user_id) {
    global $conn;
    $sql = "SELECT orders.*, 
            (SELECT COUNT(*) FROM order_items WHERE order_items.order_id = orders.id) as item_count
            FROM orders 
            WHERE user_id = ? 
            ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

$orders = getUserOrders($_SESSION['user_id']);
?>

<div class="container">
    <div class="orders-page">
        <h1>My Orders</h1>

        <?php if (empty($orders)): ?>
            <div class="no-orders">
                <p>You haven't placed any orders yet.</p>
                <a href="index.php" class="button">Start Shopping</a>
            </div>
        <?php else: ?>
            <div class="orders-list">
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <h2>Order #<?php echo $order['id']; ?></h2>
                            <span class="status-<?php echo htmlspecialchars($order['status']); ?>">
                                <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
                            </span>
                        </div>
                        <div class="order-info">
                            <p>Order Date: <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                            <p>Items: <?php echo $order['item_count']; ?></p>
                            <p>Total Amount: $<?php echo number_format($order['total_amount'], 2); ?></p>
                        </div>
                        <div class="order-actions">
                            <a href="order_confirmation.php?order_id=<?php echo $order['id']; ?>" class="button">View Details</a>
                            <?php if ($order['status'] === 'pending'): ?>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="cancel_order_id" value="<?php echo $order['id']; ?>">
                                    <button type="submit" name="cancel_order" class="button button-cancel" 
                                            onclick="return confirm('Are you sure you want to cancel this order?')">
                                        Cancel Order
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.orders-page {
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
}

.order-card {
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-bottom: 20px;
    padding: 15px;
    background-color: #fff;
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.order-info {
    margin-bottom: 15px;
}

.order-actions {
    display: flex;
    gap: 10px;
}

.button {
    display: inline-block;
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    border: none;
    cursor: pointer;
}

.button-cancel {
    background-color: #f44336;
}

.button:hover {
    opacity: 0.9;
}

.status-pending { color: #f0ad4e; }
.status-processing { color: #5bc0de; }
.status-shipped { color: #5cb85c; }
.status-delivered { color: #5cb85c; }
.status-cancelled { color: #d9534f; }

.no-orders {
    text-align: center;
    padding: 40px;
}
</style>

<?php
// Handle order cancellation
if (isset($_POST['cancel_order'])) {
    $order_id = $_POST['cancel_order_id'];
    
    // Verify the order belongs to the current user and is in 'pending' status
    $sql = "UPDATE orders SET status = 'cancelled' 
            WHERE id = ? AND user_id = ? AND status = 'pending'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        // Redirect to refresh the page
        header("Location: orders.php?cancelled=true");
        exit();
    }
}
?>

<?php include('footer.php'); ?>