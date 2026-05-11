<?php
include '../includes/config.php';
include '../includes/functions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    foreach ($_POST['qty'] as $pid => $qty) {
        $qty = (int)$qty;
        if ($qty <= 0) unset($_SESSION['cart'][$pid]);
        else $_SESSION['cart'][$pid] = $qty;
    }
    header('Location: cart.php'); exit;
}
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][(int)$_GET['remove']]);
    header('Location: cart.php'); exit;
}
$cart = $_SESSION['cart'] ?? [];
include '../includes/header.php';
?>
<h2>Shopping Cart</h2>
<?php if (empty($cart)): ?>
    <p>Your cart is empty.</p>
<?php else: ?>
<form method="post">
    <input type="hidden" name="update" value="1">
    <table>
        <tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th>Remove</th></tr>
        <?php
        $total = 0;
        foreach ($cart as $pid => $qty):
            $p = $conn->query("SELECT * FROM products WHERE id=$pid")->fetch_assoc();
            if (!$p) continue;
            $sub = $p['price'] * $qty;
            $total += $sub;
        ?>
        <tr>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td>$<?= number_format($p['price'],2) ?></td>
            <td><input type="number" name="qty[<?= $pid ?>]" value="<?= $qty ?>" min="1" max="<?= $p['stock'] ?>" style="width:60px;"></td>
            <td>$<?= number_format($sub,2) ?></td>
            <td><a href="cart.php?remove=<?= $pid ?>" class="btn" style="background:red;">Remove</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <button type="submit" class="btn">Update Cart</button>
</form>
<p><strong>Total: $<?= number_format($total,2) ?></strong></p>
<a href="checkout.php" class="btn">Proceed to Checkout</a>
<?php endif; ?>
<?php include '../includes/footer.php'; ?>