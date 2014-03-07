<?php
session_start();
ini_set("display_errors","On");
error_reporting(E_ALL);

require 'instagram.class.php';

// initialize class
$instagram = new Instagram(array(
  'apiKey'      => '917de3612ab7400584f195615e61614a',
  'apiSecret'   => '5bdde86f3bd742449d04572f9b27d8b7',
  'apiCallback' => 'http://dev.captura.com/salva_token.php' // must point to success.php
));

// create login URL
$loginUrl = $instagram->getLoginUrl(array('comments','relationships','likes','basic'));

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instagram - OAuth Login</title>
    <link rel="stylesheet" type="text/css" href="assets/style.css">
    <style>
      .login {
        display: block;
        font-size: 20px;
        font-weight: bold;
        margin-top: 50px;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <header class="clearfix">
        <h1>Instagram <span>display your photo stream</span></h1>
      </header>
      <div class="main">
        <ul class="grid">
          <li><img src="assets/instagram-big.png" alt="Instagram logo"></li>
          <li>
            
            <?php if(!isset($_SESSION['token'])){ ?>
              <a class="login" href="<? echo $loginUrl ?>">» Login com Instagram</a>
              <h>Use seu instagram para logar.</h4>
            <?php }else{ ?>
              Você está logado como <?=$_SESSION['user']['full_name']?><br>
              Escolha uma ação: <br>
              <p><a href='busca_segue.php'>Buscar user</a></p>
            <?php } ?>

          </li>
        </ul>
      </div>
    </div>
  </body>
</html>