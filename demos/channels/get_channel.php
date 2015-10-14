<?php
  require_once('../../lib/Cubits.php');
  require_once('../credentials.php');
  require_once('../configure.php');

  $cubits = Cubits::withApiKey($_API_KEY, $_API_SECRET);

  $temp =  $cubits->getChannel("7ff31a5843887cbaffb9adb3fcb2aebd");
  echo var_dump($temp) . "<br />";
?>
