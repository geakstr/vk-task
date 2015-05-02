<div class="card-block">
  Баланс: <span class="profile-balance"><?php echo $_SESSION['user']['balance']; ?></span> руб. | <a href="/actions/auth/logout">Выйти</a>
</div>

<form action="actions/orders/add" method="post" class="customer add-order-form card-block -horizontal cf">
  <h3>Публикация заказа</h3>
  <input type="text" name="title" class="order-title -expanded" value="" placeholder="Краткое описание" />
  <ul class="form-msgs title-msgs"></ul>
  <textarea name="description" class="order-description -expanded" placeholder="Расширенное описание (не обязательно)" ></textarea>
  
  <input type="text" name="price" class="order-price cf" value="" placeholder="Цена руб." />
  <input type="submit" class="order-submit -success cf" value="Опубликовать" />

  <ul class="form-msgs price-msgs"></ul>
  <ul class="form-msgs server-msgs"></ul>  
</form>

<div class="customer pending-orders card-block cf">
  <h3>Не готовые заказы (<span class="pending-orders-cnt"><?php echo count($orders['pending']); ?></span>)</h3>
  <ol class="orders-list">
<?php foreach ($orders['pending'] as $order) { ?>
    <li>
      <div class="order-title"><?php echo $order['title']; ?></div>      
      <div class="order-time"><?php echo $order['creation_time']; ?></div>    
      <div class="order-description"><?php echo $order['description']; ?></div>   
      <div class="order-meta">
        <div class="order-price"><?php echo $order['price']; ?> руб.</div>        
      </div>   
    </li>
<?php } ?>
  </ol>
</div>

<div class="customer completed-orders card-block cf">
  <h3>Готовые заказы (<span class="completed-orders-cnt"><?php echo count($orders['completed']); ?></span>)</h3>
  <ol class="orders-list">
<?php foreach ($orders['completed'] as $order) { ?>
    <li>
      <div class="order-title"><?php echo $order['title']; ?></div>
      <div class="order-time"><?php echo $order['payment_time']; ?></div>      
      <div class="order-description"><?php echo $order['description']; ?></div>      
      <div class="order-meta">
        <div class="order-price">-<?php echo $order['price']; ?> руб.</div>        
      </div>
    </li>
<?php } ?>
  </ol>
</div>