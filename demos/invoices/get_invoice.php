<?php
  require_once('../../lib/Cubits.php');
  require_once('../credentials.php');
  require_once('../configure.php');

  $cubits = Cubits::withApiKey($_API_KEY, $_API_SECRET);

  $temp =  $cubits->getInvoice("ef73a6ed61a8c97427eaae2073b9127b");
  echo $temp->id . "<br />";
?>
