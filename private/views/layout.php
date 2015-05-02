<!DOCUMENT html>
<html>
<head>
  <meta charset="UTF-8" />
  <title><?php echo $title; ?> | Система выполнения абстрактных заказов</title>
  <link rel="stylesheet" href="/css/style.css" />
</head>
<body>

<div class="header">
  <h1><?php echo $title; ?></h1>
</div><!-- /.header -->

<div class="content">
<?php require_once($page); ?>

</div><!-- /.content -->

<div class="footer">

</div><!-- /.footer -->

<script src="/js/utils.js"></script>
<script src="/js/app.js"></script>

</body>
</html>
