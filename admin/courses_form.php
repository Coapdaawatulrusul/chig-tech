<?php
include '../includes/config.php';
include '../includes/functions.php';
requireAdmin();
$edit = isset($_GET['id']);
if ($edit) {
    $id = (int)$_GET['id'];
    $c = $conn->query("SELECT * FROM courses WHERE id=$id")->fetch_assoc();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title']; $desc = $_POST['description'];
    $price = (float)$_POST['price']; $image = $_POST['image'];
    $duration = $_POST['duration'];

    if ($edit) {
        $stmt = $conn->prepare("UPDATE courses SET title=?, description=?, price=?, image=?, duration=? WHERE id=?");
        $stmt->bind_param("ssdssi", $title, $desc, $price, $image, $duration, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO courses (title, description, price, image, duration) VALUES (?,?,?,?,?)");
        $stmt->bind_param("ssdss", $title, $desc, $price, $image, $duration);
    }
    $stmt->execute();
    header('Location: courses.php'); exit;
}
include '../includes/header.php';
?>
<h2><?= $edit ? 'Edit' : 'Add' ?> Course</h2>
<form method="post">
    Title: <input type="text" name="title" value="<?= $edit ? htmlspecialchars($c['title']) : '' ?>" required>
    Description: <textarea name="description"><?= $edit ? htmlspecialchars($c['description']) : '' ?></textarea>
    Price: <input type="number" step="0.01" name="price" value="<?= $edit ? $c['price'] : '' ?>" required>
    Image URL: <input type="text" name="image" value="<?= $edit ? htmlspecialchars($c['image']) : '' ?>">
    Duration: <input type="text" name="duration" value="<?= $edit ? htmlspecialchars($c['duration']) : '' ?>" placeholder="e.g., 6 weeks">
    <button type="submit" class="btn">Save</button>
</form>
<?php include '../includes/footer.php'; ?>