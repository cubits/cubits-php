<?php
  require_once('../../lib/Cubits.php');
  require_once('../credentials.php');
  require_once('../configure.php');

  $cubits = Cubits::withApiKey($_API_KEY, $_API_SECRET);

  $temp =  $cubits->sendMoney("3Pj4mJfK62n9mjMRcHYs96nd15UQLHHhPS", "0.1025");
  echo var_dump($temp);
?>