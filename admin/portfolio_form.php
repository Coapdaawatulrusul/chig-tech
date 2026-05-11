<?php
include '../includes/config.php';
include '../includes/functions.php';
requireAdmin();
$edit = isset($_GET['id']);
if ($edit) {
    $id = (int)$_GET['id'];
    $p = $conn->query("SELECT * FROM portfolio WHERE id=$id")->fetch_assoc();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title']; $desc = $_POST['description'];
    $image = $_POST['image']; $link = $_POST['link'];

    if ($edit) {
        $stmt = $conn->prepare("UPDATE portfolio SET title=?, description=?, image=?, link=? WHERE id=?");
        $stmt->bind_param("ssssi", $title, $desc, $image, $link, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO portfolio (title, description, image, link) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $title, $desc, $image, $link);
    }
    $stmt->execute();
    header('Location: portfolio.php'); exit;
}
include '../includes/header.php';
?>
<h2><?= $edit ? 'Edit' : 'Add' ?> Portfolio Item</h2>
<form method="post">
    Title: <input type="text" name="title" value="<?= $edit ? htmlspecialchars($p['title']) : '' ?>" required>
    Description: <textarea name="description"><?= $edit ? htmlspecialchars($p['description']) : '' ?></textarea>
    Image URL: <input type="text" name="image" value="<?= $edit ? htmlspecialchars($p['image']) : '' ?>">
    Link: <input type="text" name="link" value="<?= $edit ? htmlspecialchars($p['link']) : '' ?>">
    <button type="submit" class="btn">Save</button>
</form>
<?php include '../includes/footer.php'; ?>