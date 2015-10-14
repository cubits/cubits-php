<?php
  require_once('../../lib/Cubits.php');
  require_once('../credentials.php');
  require_once('../configure.php');
  
  $cubits = Cubits::withApiKey($_API_KEY, $_API_SECRET);
  $params = array(
    'reference' => '15',
    'description' => 'Order monkey'
   );

  $temp =  $cubits->createInvoice("Alpaca socks", "10.00", "EUR", $params);
  echo $temp->id . "<br />";
?>
