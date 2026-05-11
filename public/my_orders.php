<?php
include '../includes/config.php';
include '../includes/functions.php';
requireLogin();
$orders = $conn->query("SELECT * FROM orders WHERE user_id=".$_SESSION['user_id']." ORDER BY created_at DESC");
include '../includes/header.php';
?>
<h2>My Orders</h2>
<?php if (isset($_GET['order_success'])) echo "<p class='success'>Order placed successfully!</p>"; ?>
<table>
    <tr><th>Order ID</th><th>Total</th><th>Status</th><th>Date</th></tr>
    <?php while ($o = $orders->fetch_assoc()): ?>
    <tr>
        <td><?= $o['id'] ?></td>
        <td>$<?= number_format($o['total'],2) ?></td>
        <td><?= $o['status'] ?></td>
        <td><?= $o['created_at'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>
<?php include '../includes/footer.php'; ?>