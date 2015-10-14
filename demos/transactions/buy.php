<?php
  require_once('../../lib/Cubits.php');
  require_once('../credentials.php');
  require_once('../configure.php');

  $cubits = Cubits::withApiKey($_API_KEY, $_API_SECRET);

  $temp =  $cubits->buy("EUR", "10.50");
  echo $temp->id . "<br />";
?>
