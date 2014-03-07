<?php
ini_set("display_errors","On");
error_reporting(E_ALL);
session_start();

require_once 'instagram.class.php';

// initialize class
$instagram = new Instagram(array(
  'apiKey'      => '917de3612ab7400584f195615e61614a',
  'apiSecret'   => '5bdde86f3bd742449d04572f9b27d8b7',
  'apiCallback' => 'http://dev.captura.com/salva_token.php' // must point to success.php
));

// receive OAuth code parameter
$code = $_GET['code'];

// check whether the user has granted access
if (isset($code)) {

  // receive OAuth token object, se existir
  $data = $instagram->getOAuthToken($code);

  if( isset($data->user) && is_object($data->user) ){
    $username = $username = $data->user->username;
    $instagram->setAccessToken($data);

    $_SESSION['token'] = $data->access_token;
    $_SESSION['user']['username'] = $data->user->username;
    $_SESSION['user']['full_name'] = $data->user->full_name;
    $_SESSION['user']['id'] = $data->user->id;
    $_SESSION['user']['profile_picture'] = $data->user->profile_picture;

  }else{
    $instagram->setAccessToken($_SESSION['token']);
  }
  
  header("Location: busca_segue.php");

} else {

  // check whether an error occurred
  if (isset($_GET['error'])) {
    echo 'An error occurred: ' . $_GET['error_description'];
  }

  echo "Alguma coisa errada, nao carregou instagram";

}

?>