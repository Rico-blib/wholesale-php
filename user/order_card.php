<div class="card mb-4 shadow-sm">
  <div class="card-header bg-success text-white d-flex justify-content-between">
    <span>Order ID: <?= $order['id'] ?> | <?= $order['payment_method'] ?></span>
    <span>Status: <strong><?= $order['status'] ?></strong></span>
  </div>
  <div class="card-body">
    <?php
      $order_id = $order['id'];
      $items = $conn->prepare("
        SELECT oi.*, p.name AS product_name, p.image AS product_image
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
      ");
      $items->bind_param("i", $order_id);
      $items->execute();
      $items_result = $items->get_result();
    ?>
    <div class="row">
      <?php while ($item = $items_result->fetch_assoc()): ?>
        <div class="col-md-4 mb-3">
          <div class="border rounded p-2 h-100 d-flex flex-column">
            <img src="../images/<?= htmlspecialchars($item['product_image']) ?>" class="img-fluid rounded mb-2" style="height: 150px; object-fit: cover;">
            <h6><?= htmlspecialchars($item['product_name']) ?></h6>
            <small>Qty: <?= $item['quantity'] ?> × KSh <?= number_format($item['price'], 2) ?></small>
            <div class="mt-auto fw-bold">Total: KSh <?= number_format($item['price'] * $item['quantity'], 2) ?></div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
    <div class="fw-bold text-end mt-3">Order Total: KSh <?= number_format($order['total_amount'], 2) ?></div>
  </div>
  <div class="card-footer text-muted">
    Ordered on <?= date('F j, Y, g:i a', strtotime($order['created_at'])) ?>
  </div>
</div>
