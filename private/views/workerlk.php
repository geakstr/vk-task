<div class="card-block cf">
  <div style="float: left;">
    <b>Баланс</b>:
    <span id="profile-balance" class="profile-balance"><?php echo $_SESSION['user']['balance']; ?></span> руб.
  </div>
  <div style="float: right;">
    <?php echo $_SESSION['user']['fio']; ?>
    |
    <a href="/actions/auth/logout">Выйти</a>
  </div>
</div>

<div class="worker pending-orders card-block cf">
  <h3>Лента заказов (<span id="pending-orders-cnt"><?php echo count($orders['pending']); ?></span>)</h3>
  <ol class="orders-list" id="pending-orders-list">
<?php foreach ($orders['pending'] as $order) { ?>
    <form action="/actions/orders/work" method="post" class="work-order-form">
      <div class="order-title"><?php echo $order['title']; ?></div>      
      <div class="order-time"><?php echo $order['creation_time']; ?></div>    
      <div class="order-description"><?php echo $order['description']; ?></div>      
      <div class="work-order">
        <button type="submit">
          <p>Выполнить за</p>
          <p><span class="order-price"><?php echo number_format($order['price'], '2', '.', ''); ?></span> руб.</p>
        </button>
        <input type="hidden" name="id" class="id" value="<?php echo $order['id']; ?>" />
      </div>
    </form>        
<?php } ?>
  </ol>
</div>

<div class="worker completed-orders card-block cf">
  <h3>Выполненные (<span id="completed-orders-cnt"><?php echo count($orders['completed']); ?></span>)</h3>
  <ol class="orders-list" id="completed-orders-list">
<?php foreach ($orders['completed'] as $order) { ?>
    <li>
      <div class="order-title"><?php echo $order['title']; ?></div>
      <div class="order-time"><?php echo $order['payment_time']; ?></div>      
      <div class="order-description"><?php echo $order['description']; ?></div>      
      <div class="order-meta">
        <div class="order-price">+<?php echo number_format($order['price'], '2', '.', ''); ?> руб.</div>                
      </div>
    </li>
<?php } ?>
  </ol>
</div>

<script src="/js/lk-common.js"></script>
<script src="/js/lk-worker.js"></script>