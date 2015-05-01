<form action="actions/orders/add" method="post" class="add-order-form card-block -horizontal cf">
  <h3>Публикация заказа</h3>
  <input type="text" name="title" class="order-title" value="" placeholder="Краткое описание" />
  <textarea name="description" class="order-description" placeholder="Расширенное описание" ></textarea>
  <input type="text" name="price" class="order-price" value="" placeholder="Цена руб." />
  <input type="submit" class="order-submit -success" value="Создать" />
</form>

<div class="pending-orders card-block cf">
  <h3>Ожидают выполнения (<?php echo $orders['pending_cnt']; ?>)</h3>
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
  <h3>Выполненные (<?php echo $orders['completed_cnt']; ?>)</h3>
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