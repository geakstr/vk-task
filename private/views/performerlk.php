<a href="/actions/auth/logout">Выйти</a>

<div class="performer pending-orders card-block cf">
  <h3>Ожидают выполнения (<?php echo count($orders['pending']); ?>)</h3>
  <ol class="orders-list">
<?php foreach ($orders['pending'] as $order) { ?>
    <li>
      <span class="order-title"><?php echo $order['title']; ?></span>
      <div class="order-meta">
        <div class="order-price"><?php echo $order['price']; ?> руб.</div>
        <div class="order-time"><?php echo $order['creation_time']; ?></div>        
      </div>
      <div class="order-description"><?php echo $order['description']; ?></div>      
    </li>
<?php } ?>
  </ol>
</div>

<div class="performer completed-orders card-block cf">
  <h3>Выполненные (<?php echo count($orders['completed']); ?>)</h3>
  <ol class="orders-list">
<?php foreach ($orders['completed'] as $order) { ?>
    <li>
      <span class="order-title"><?php echo $order['title']; ?></span>
      <div class="order-meta">
        <div class="order-price">+<?php echo $order['price']; ?> руб.</div>
        <div class="order-time"><?php echo $order['payment_time']; ?></div>        
      </div>
      <div class="order-description"><?php echo $order['description']; ?></div>      
    </li>
<?php } ?>
  </ol>
</div>