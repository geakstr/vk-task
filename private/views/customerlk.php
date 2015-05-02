<a href="/auth/logout">Выйти</a>
<form action="actions/orders/add" method="post" class="add-order-form card-block -horizontal cf">
  <h3>Публикация заказа</h3>
  <input type="text" name="title" class="order-title -expanded" value="" placeholder="Краткое описание" />
  <ul class="form-msgs title-msgs"></ul>
  <textarea name="description" class="order-description -expanded" placeholder="Расширенное описание" ></textarea>
  
  <input type="text" name="price" class="order-price cf" value="" placeholder="Цена руб." />
  <input type="submit" class="order-submit -success cf" value="Опубликовать" />

  <ul class="form-msgs price-msgs"></ul>
  <ul class="form-msgs server-msgs"></ul>  
</form>

<div class="pending-orders card-block cf">
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

<div class="completed-orders card-block cf">
  <h3>Выполненные (<?php echo count($orders['completed']); ?>)</h3>
  <ol class="orders-list">
<?php foreach ($orders['completed'] as $order) { ?>
    <li>
      <span class="order-title"><?php echo $order['title']; ?></span>
      <div class="order-meta">
        <div class="order-price">-<?php echo $order['price']; ?> руб.</div>
        <div class="order-time"><?php echo $order['payment_time']; ?></div>        
      </div>
      <div class="order-description"><?php echo $order['description']; ?></div>      
    </li>
<?php } ?>
  </ol>
</div>