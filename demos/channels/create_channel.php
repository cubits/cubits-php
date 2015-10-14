<?php
  require_once('../../lib/Cubits.php');
  require_once('../credentials.php');
  require_once('../configure.php');

  $cubits = Cubits::withApiKey($_API_KEY, $_API_SECRET);

  $receiver_currency = "EUR";
  $txs_callback_url = "http://example.com/callback/tx";
  $name = "Alpaca Socks";

  $temp =  $cubits->createChannel($receiver_currency, null, null, null, null, null, $txs_callback_url);
  echo  $temp->id . "<br />";
?>