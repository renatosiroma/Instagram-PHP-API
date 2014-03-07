<?php
ini_set("display_errors","On");
error_reporting(E_ALL);
session_start();

require_once 'instagram.class.php';

$instagram = new Instagram(array(
  'apiKey'      => '917de3612ab7400584f195615e61614a',
  'apiSecret'   => '5bdde86f3bd742449d04572f9b27d8b7',
  'apiCallback' => 'http://dev.captura.com/salva_token.php' // must point to success.php
));



$instagram->setAccessToken($_SESSION['token']);

?>

<form method="post">
	Buscar user: <input type="text" name="user"><input type="submit">
</form>

<?
if(isset($_POST['user'])){
	$busca = $instagram->searchUser($_POST['user'], 10);

	if(isset($busca->data))
	foreach ( $busca->data as $user){
		echo "<a href='busca_segue.php?seguir=".$user->id."'><img src='".$user->profile_picture."' />".$user->username."</a><br />";	
	}
	 
}elseif(isset($_GET['seguir'])){

	$user = $instagram->getUser($_GET['seguir']);
	$seguir = $instagram->modifyRelationship('follow',$_GET['seguir']);

	if( intval($seguir->meta->code)==200 && $seguir->data->outgoing_status =='follows' )
		echo "<h3><img src='".$user->data->profile_picture."' /><br>Seguindo com sucesso</h3>";


}

?>