<?php
include '../includes/config.php';
include '../includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
$prod = $conn->query("SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id=c.id WHERE p.id=$id")->fetch_assoc();
if (!$prod) { echo "Product not found."; exit; }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    requireApproved();
    $pid = $prod['id'];
    $qty = (int)$_POST['quantity'] ?: 1;
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    if (isset($_SESSION['cart'][$pid])) $_SESSION['cart'][$pid] += $qty;
    else $_SESSION['cart'][$pid] = $qty;
    header("Location: cart.php"); exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    requireApproved();
    $userId = $_SESSION['user_id'];
    $rating = (int)$_POST['rating'];
    $comment = $_POST['comment'];
    $stmt = $conn->prepare("INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?,?,?,?)");
    $stmt->bind_param("iiis", $userId, $id, $rating, $comment);
    $stmt->execute();
    header("Location: product_detail.php?id=$id&review=ok"); exit;
}

include '../includes/header.php';
?>
<h2><?= htmlspecialchars($prod['name']) ?></h2>
<img src="<?= htmlspecialchars($prod['image']) ?: 'https://via.placeholder.com/400x300' ?>" style="max-width:400px;">
<p><strong>Price:</strong> $<?= number_format($prod['price'],2) ?></p>
<p><strong>Category:</strong> <?= htmlspecialchars($prod['cat_name']) ?></p>
<p><strong>Type:</strong> <?= $prod['type'] ?></p>
<?php if ($prod['type'] == 'product'): ?>
    <p><strong>Condition:</strong> <?= htmlspecialchars($prod['condition']) ?></p>
    <p><strong>Shipping Fee:</strong> $<?= number_format($prod['shipping_fee'],2) ?></p>
    <p><strong>Stock:</strong> <?= $prod['stock'] ?></p>
<?php endif; ?>
<p><?= nl2br(htmlspecialchars($prod['description'])) ?></p>

<?php if (isLoggedIn() && isApproved()): ?>
<form method="post">
    <input type="hidden" name="add_to_cart" value="1">
    Quantity: <input type="number" name="quantity" value="1" min="1" max="<?= $prod['stock'] ?>" style="width:80px;">
    <button type="submit" class="btn">Add to Cart</button>
</form>
<?php elseif (!isLoggedIn()): ?>
    <a href="login.php" class="btn">Login to Purchase</a>
<?php else: ?>
    <p>Your account is pending approval.</p>
<?php endif; ?>

<h3>Reviews</h3>
<?php
$revs = $conn->query("SELECT r.*, u.name FROM reviews r JOIN users u ON r.user_id=u.id WHERE r.product_id=$id ORDER BY r.created_at DESC");
while ($r = $revs->fetch_assoc()):
?>
<div style="border-bottom:1px solid #ccc; padding:10px 0;">
    <strong><?= htmlspecialchars($r['name']) ?></strong> - Rating: <?= $r['rating'] ?>/5
    <p><?= htmlspecialchars($r['comment']) ?></p>
</div>
<?php endwhile; ?>
<?php if (isApproved()): ?>
<h4>Leave a Review</h4>
<form method="post">
    <input type="hidden" name="submit_review" value="1">
    Rating: <select name="rating"><option>5</option><option>4</option><option>3</option><option>2</option><option>1</option></select>
    Comment: <textarea name="comment" required></textarea>
    <button type="submit" class="btn">Submit Review</button>
</form>
<?php endif; ?>
<?php include '../includes/footer.php'; ?>