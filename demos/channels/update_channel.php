<?php
  require_once('../../lib/Cubits.php');
  require_once('../credentials.php');
  require_once('../configure.php');

  $cubits = Cubits::withApiKey($_API_KEY, $_API_SECRET);

  $channelId = "7ff31a5843887cbaffb9adb3fcb2aebd";
  $receiver_currency = "USD";
  $txs_callback_url = "http://example.com/callback/tx";
  $name = "Alpaca Socks";


  $temp =  $cubits->updateChannel($channelId, $receiver_currency, $name, null, null, null, $txs_callback_url);
  echo  $temp->channel_url . "<br />";
?>