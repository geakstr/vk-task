<div class="card-block">
  Баланс: <span class="profile-balance"><?php echo $_SESSION['user']['balance']; ?></span> руб. | <a href="/actions/auth/logout">Выйти</a>
</div>

<div class="performer pending-orders card-block cf">
  <h3>Доступные для выполнения заказы (<span class="pending-orders-cnt"><?php echo count($orders['pending']); ?></span>)</h3>
  <ol class="orders-list">
<?php foreach ($orders['pending'] as $order) { ?>
    <form action="/actions/orders/perform" method="post" class="perform-order-form">
      <div class="order-title"><?php echo $order['title']; ?></div>      
      <div class="order-time"><?php echo $order['creation_time']; ?></div>    
      <div class="order-description"><?php echo $order['description']; ?></div>      
      <div class="perform-order">
        <button type="submit">
          <p>Выполнить за</p>
          <p><span class="order-price"><?php echo $order['price']; ?></span> руб.</p>
        </button>
        <input type="hidden" name="id" class="id" value="<?php echo $order['id']; ?>" />
      </div>
    </form>        
<?php } ?>
  </ol>
</div>

<div class="performer completed-orders card-block cf">
  <h3>Выполненные заказы (<span class="completed-orders-cnt"><?php echo count($orders['completed']); ?></span>)</h3>
  <ol class="orders-list">
<?php foreach ($orders['completed'] as $order) { ?>
    <li>
      <div class="order-title"><?php echo $order['title']; ?></div>
      <div class="order-time"><?php echo $order['payment_time']; ?></div>      
      <div class="order-description"><?php echo $order['description']; ?></div>      
      <div class="order-meta">
        <div class="order-price">+<?php echo $order['price']; ?> руб.</div>                
      </div>
    </li>
<?php } ?>
  </ol>
</div>