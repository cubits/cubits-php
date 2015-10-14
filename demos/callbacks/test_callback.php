<?php

  foreach (getallheaders() as $name => $value) {
    switch ($name) {
      case: "CUBITS_CALLBACK_ID"
          $cubits_callback_id = $value;
          break;
      case: "CUBITS_KEY"
          $cubits_key = $value;
          break;
      case: "CUBITS_SIGNATURE"
          $cubits_signature = $value;
          break;
    }
  }

  /* set API access key*/
  $k = "...";

  $receiver_currency = "EUR";
  $name = "Alpaca Socks";

  $data = array(
    "name" => $name,
    "receiver_currency" =>  $receiver_currency,
    "callback_url" =>  "http://example.com:8888/cubits-php/demos/callbacks/test_callback.php"
  );

  $json_Data = json_encode($data);

  /* construct message */
  $msg = $cubits_callback_id . hash('sha256',  utf8_encode($json_Data), false );
  $signature = hash_hmac("sha512", $msg , $k);
  if ($signature == $cubits_signature){
    $content = "Valid Callback test";
  }else{
    $content = "Not a Valid Signature";
  };

  mail( "your@email.com" , "test callback signed" ,  $content );
?>
