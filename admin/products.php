<?php
include '../includes/config.php';
include '../includes/functions.php';
requireAdmin();
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM products WHERE id=$id");
    header('Location: products.php'); exit;
}
$prods = $conn->query("SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id=c.id ORDER BY p.created_at DESC");
include '../includes/header.php';
?>
<h2>Product / Service Management</h2>
<a href="product_form.php" class="btn">Add New</a>
<table>
    <tr><th>ID</th><th>Name</th><th>Type</th><th>Category</th><th>Price</th><th>Stock</th><th>Actions</th></tr>
    <?php while ($p = $prods->fetch_assoc()): ?>
    <tr>
        <td><?= $p['id'] ?></td>
        <td><?= htmlspecialchars($p['name']) ?></td>
        <td><?= $p['type'] ?></td>
        <td><?= htmlspecialchars($p['cat_name']) ?></td>
        <td>$<?= number_format($p['price'],2) ?></td>
        <td><?= $p['stock'] ?></td>
        <td>
            <a href="product_form.php?id=<?= $p['id'] ?>" class="btn">Edit</a>
            <a href="products.php?delete=<?= $p['id'] ?>" class="btn" style="background:red;" onclick="return confirm('Delete?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<?php include '../includes/footer.php'; ?>