<?php include '../includes/config.php'; include '../includes/header.php'; ?>
<h2>Training Courses</h2>
<div class="product-grid">
    <?php
    $res = $conn->query("SELECT * FROM courses ORDER BY id DESC");
    while ($c = $res->fetch_assoc()):
    ?>
    <div class="card">
        <img src="<?= htmlspecialchars($c['image']) ?: 'https://via.placeholder.com/250x150' ?>" alt="">
        <h4><?= htmlspecialchars($c['title']) ?></h4>
        <p><?= htmlspecialchars($c['description']) ?></p>
        <p>Duration: <?= htmlspecialchars($c['duration']) ?></p>
        <p><strong>$<?= number_format($c['price'],2) ?></strong></p>
        <a href="#" class="btn">Enroll (Coming Soon)</a>
    </div>
    <?php endwhile; ?>
</div>
<?php include '../includes/footer.php'; ?>