<?php
include '../includes/config.php';
include '../includes/functions.php';
requireApproved();
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) { header('Location: cart.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address = $_POST['shipping_address'];
    $total = 0;
    foreach ($cart as $pid => $qty) {
        $stmt = $conn->prepare("SELECT price, shipping_fee, stock, type FROM products WHERE id=?");
        $stmt->bind_param("i", $pid);
        $stmt->execute();
        $prod = $stmt->get_result()->fetch_assoc();
        if ($prod['stock'] < $qty) { $error = "Insufficient stock for product #$pid."; break; }
        if ($prod['type'] == 'product') {
            $total += ($prod['price'] * $qty) + $prod['shipping_fee'];
        } else {
            $total += $prod['price'] * $qty;
        }
    }
    if (!$error) {
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total, shipping_address) VALUES (?,?,?)");
        $stmt->bind_param("ids", $_SESSION['user_id'], $total, $address);
        $stmt->execute();
        $order_id = $conn->insert_id;
        foreach ($cart as $pid => $qty) {
            $stmt = $conn->prepare("SELECT price FROM products WHERE id=?");
            $stmt->bind_param("i", $pid);
            $stmt->execute();
            $price = $stmt->get_result()->fetch_assoc()['price'];
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?,?,?,?)");
            $stmt->bind_param("iiid", $order_id, $pid, $qty, $price);
            $stmt->execute();
        }
        $_SESSION['cart'] = [];
        header("Location: my_orders.php?order_success=1"); exit;
    }
}
include '../includes/header.php';
?>
<h2>Checkout</h2>
<?php if ($error) echo "<p class='error'>$error</p>"; ?>
<form method="post">
    Shipping Address: <textarea name="shipping_address" required></textarea>
    <h4>Order Summary</h4>
    <table>
        <tr><th>Product</th><th>Qty</th><th>Price</th><th>Shipping</th></tr>
        <?php $total = 0;
        foreach ($cart as $pid => $qty):
            $p = $conn->query("SELECT * FROM products WHERE id=$pid")->fetch_assoc();
            $ship = ($p['type'] == 'product') ? $p['shipping_fee'] : 0;
            $sub = $p['price'] * $qty + $ship;
            $total += $sub; ?>
        <tr>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= $qty ?></td>
            <td>$<?= number_format($p['price'],2) ?></td>
            <td>$<?= number_format($ship,2) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <p><strong>Total: $<?= number_format($total,2) ?></strong></p>
    <button type="submit" class="btn">Place Order</button>
</form>
<?php include '../includes/footer.php'; ?>