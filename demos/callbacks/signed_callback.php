<?php
  require_once('../../lib/Cubits.php');
  require_once('../credentials.php');
  require_once('../configure.php');
  $cubits = Cubits::withApiKey($_API_KEY, $_API_SECRET);

  $receiver_currency = "EUR";
  $name = "Alpaca Socks";
  $callback_url = "http://example.com:8888/cubits-php/demos/callbacks/test_callback.php";

  $temp =  $cubits->createChannel($receiver_currency, $name, null, null, $callback_url, null );

  echo $temp->callback_url;
?>