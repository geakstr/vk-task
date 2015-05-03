<div class="card-block">
  <form action="/actions/auth/login" method="post" id="login-form" class="login-form -horizontal cf">
    <input type="text" name="email" class="email" value="customer1@email" placeholder="Эл. почта" />
    <ul class="form-msgs" id="login-form-email-msgs"></ul>
    <input type="submit" class="submit -success" value="Войти" />
    <ul class="form-msgs" id="login-form-server-msgs"></ul>
  </form>
</div>

<script src="/js/welcome-page.js"></script>