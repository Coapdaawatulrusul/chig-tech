<?php
include '../includes/config.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $conn->prepare("INSERT INTO contacts (name, email, message) VALUES (?,?,?)");
    $stmt->bind_param("sss", $_POST['name'], $_POST['email'], $_POST['message']);
    $stmt->execute();
    $msg = "<span class='success'>Message sent!</span>";
}
include '../includes/header.php';
?>
<h2>Contact Us</h2>
<?= $msg ?>
<form method="post">
    Name: <input type="text" name="name" required>
    Email: <input type="email" name="email" required>
    Message: <textarea name="message" rows="5" required></textarea>
    <button type="submit" class="btn">Send Message</button>
</form>
<?php include '../includes/footer.php'; ?>