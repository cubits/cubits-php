<?php
  require_once('../../lib/Cubits.php');
  require_once('../credentials.php');
  require_once('../configure.php');

  $cubits = Cubits::withApiKey($_API_KEY, $_API_SECRET);

  $temp =  $cubits->sell("0,01250000", "EUR");
  echo $temp->id . "<br />";
?>
