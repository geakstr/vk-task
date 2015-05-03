<div class="card-block cf">
  <div style="float: left;">
    <b>Баланс</b>:
    <span id="profile-balance" class="profile-balance"><?php echo $_SESSION['user']['balance']; ?></span> руб.
    
    <form action="/actions/balance/refill" method="post" id="balance-refill-form" style="margin-top: 11px;">
      <input type="text" name="fee" id="balance-refill-form-fee" style="width: 120px;" value="" placeholder="Руб." />      
      <input type="submit" value="Пополнить" />
      <ul class="form-msgs" id="balance-refill-form-fee-msgs"></ul>
      <ul class="form-msgs" id="balance-refill-form-server-msgs"></ul>  
    </form>
  </div>
  <div style="float: right; text-align: right;">
    <div><?php echo $_SESSION['user']['fio']; ?></div>
    <div style="margin-top: 11px;"><a href="/actions/auth/logout">Выйти</a></div>
  </div>
</div>

<form action="/actions/orders/add"
      method="post" id="add-order-form"
      class="customer add-order-form card-block -horizontal cf">

  <h3>Публикация заказа</h3>

  <input type="text"
         name="title"
         id="add-order-form-title"
         class="order-title -expanded"
         value=""
         placeholder="Краткое описание" />
  <ul class="form-msgs" id="add-order-form-title-msgs"></ul>

  <textarea name="description"
            id="add-order-form-description" 
            class="order-description -expanded"
            placeholder="Расширенное описание (не обязательно)" ></textarea>
  
  <input type="text"
         name="price"
         id="add-order-form-price" 
         class="order-price cf"
         value=""
         placeholder="Цена руб." />

  <input type="submit"
         class="order-submit -success cf"
         value="Опубликовать" />

  <ul class="form-msgs" id="add-order-form-price-msgs"></ul>
  <ul class="form-msgs" id="add-order-form-server-msgs"></ul>  
</form>

<div class="customer pending-orders card-block cf">
  <h3>Ожидают выполнения (<span id="pending-orders-cnt"><?php echo count($orders['pending']); ?></span>)</h3>
  <ol class="orders-list" id="pending-orders-list">
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
  <h3>Выполненные (<span id="completed-orders-cnt"><?php echo count($orders['completed']); ?></span>)</h3>
  <ol class="orders-list" id="completed-orders-list">
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

<script src="/js/lk-common.js"></script>
<script src="/js/lk-customer.js"></script>