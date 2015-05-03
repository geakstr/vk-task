<div class="card-block">
  <h1>Система выполнения абстрактных заказов</h1>

  <p class="muted">На 10% комиссиях от стоимости каждого выполненного заказа система заработала <?php echo $profit; ?> руб. :–)</p>

  <hr style="border: 0; height: 1px; background: #efefef; margin: 22px 0" />

  <h4>Доступны 4 пользователя:</h4>

  <form action="/actions/auth/login" method="post" class="login-form">
    <input type="hidden" name="email" class="email" value="customer1@email" />
    <ul class="form-msgs" class="login-form-email-msgs"></ul>
    <input type="submit" class="submit -success" value="Петр (заказчик)" />
    <ul class="form-msgs" class="login-form-server-msgs"></ul>
  </form>

  <form action="/actions/auth/login" method="post" class="login-form">
    <input type="hidden" name="email" class="email" value="customer2@email" />
    <ul class="form-msgs" class="login-form-email-msgs"></ul>
    <input type="submit" class="submit -success" value="Дмитрий (заказчик)" />
    <ul class="form-msgs" class="login-form-server-msgs"></ul>
  </form>

  <form action="/actions/auth/login" method="post" class="login-form">
    <input type="hidden" name="email" class="email" value="worker1@email" />
    <ul class="form-msgs" class="login-form-email-msgs"></ul>
    <input type="submit" class="submit -success" value="Владимир (исполнитель)" />
    <ul class="form-msgs" class="login-form-server-msgs"></ul>
  </form>

  <form action="/actions/auth/login" method="post" class="login-form">
    <input type="hidden" name="email" class="email" value="worker2@email" />
    <ul class="form-msgs" class="login-form-email-msgs"></ul>
    <input type="submit" class="submit -success" value="Максим (исполнитель)" />
    <ul class="form-msgs" class="login-form-server-msgs"></ul>
  </form>
</div>

<script src="/js/welcome-page.js"></script>