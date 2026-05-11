<?php
include '../includes/config.php';
include '../includes/functions.php';
requireAdmin();
$edit = isset($_GET['id']);
if ($edit) {
    $id = (int)$_GET['id'];
    $prod = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name']; $desc = $_POST['description'];
    $price = (float)$_POST['price']; $image = $_POST['image'];
    $cat = (int)$_POST['category_id']; $type = $_POST['type'];
    $condition = $_POST['condition']; $shipping = (float)$_POST['shipping_fee'];
    $stock = (int)$_POST['stock'];

    if ($edit) {
        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, image=?, category_id=?, type=?, `condition`=?, shipping_fee=?, stock=? WHERE id=?");
        $stmt->bind_param("ssdsissdii", $name, $desc, $price, $image, $cat, $type, $condition, $shipping, $stock, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, image, category_id, type, `condition`, shipping_fee, stock) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssdsissdi", $name, $desc, $price, $image, $cat, $type, $condition, $shipping, $stock);
    }
    $stmt->execute();
    header('Location: products.php'); exit;
}
$categories = $conn->query("SELECT * FROM categories");
include '../includes/header.php';
?>
<h2><?= $edit ? 'Edit' : 'Add' ?> Product / Service</h2>
<form method="post">
    Name: <input type="text" name="name" value="<?= $edit ? htmlspecialchars($prod['name']) : '' ?>" required>
    Description: <textarea name="description"><?= $edit ? htmlspecialchars($prod['description']) : '' ?></textarea>
    Price: <input type="number" step="0.01" name="price" value="<?= $edit ? $prod['price'] : '' ?>" required>
    Image URL: <input type="text" name="image" value="<?= $edit ? htmlspecialchars($prod['image']) : '' ?>" placeholder="https://...">
    Category: <select name="category_id">
        <?php while ($cat = $categories->fetch_assoc()): ?>
            <option value="<?= $cat['id'] ?>" <?= $edit && $prod['category_id']==$cat['id']?'selected':'' ?>><?= $cat['name'] ?></option>
        <?php endwhile; ?>
    </select>
    Type: <select name="type">
        <option value="product" <?= $edit && $prod['type']=='product'?'selected':'' ?>>Product</option>
        <option value="service" <?= $edit && $prod['type']=='service'?'selected':'' ?>>Service</option>
    </select>
    Condition: <select name="condition">
        <option value="New" <?= $edit && $prod['condition']=='New'?'selected':'' ?>>New</option>
        <option value="Used" <?= $edit && $prod['condition']=='Used'?'selected':'' ?>>Used</option>
        <option value="Refurbished" <?= $edit && $prod['condition']=='Refurbished'?'selected':'' ?>>Refurbished</option>
    </select>
    Shipping Fee: <input type="number" step="0.01" name="shipping_fee" value="<?= $edit ? $prod['shipping_fee'] : '0' ?>">
    Stock: <input type="number" name="stock" value="<?= $edit ? $prod['stock'] : '1' ?>">
    <button type="submit" class="btn">Save</button>
</form>
<?php include '../includes/footer.php'; ?>