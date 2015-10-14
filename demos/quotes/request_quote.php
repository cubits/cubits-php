<?php
  require_once('../../lib/Cubits.php');
  require_once('../credentials.php');
  require_once('../configure.php');

  $cubits = Cubits::withApiKey($_API_KEY, $_API_SECRET);

  $sender = array(
    'currency' => 'EUR'
  );
  $receiver = array(
    'currency' => 'BTC',
    'amount' => '10'
  );
  $params = array(
    'operation' => 'buy',
    'sender' => $sender,
    'receiver' => $receiver,
   );

  $temp = $cubits->post('quotes',$params);
  echo  $temp . "<br />";
?>