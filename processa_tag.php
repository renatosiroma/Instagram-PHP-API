<?php
session_start();
ini_set("display_errors","On");
set_time_limit(0);

mysql_connect('localhost','root','123qwe') or die(mysql_error());
mysql_select_db('autopost') or die(mysql_error());

function download($url){
	$ch = curl_init($url);

	$name = end(explode("/",$url));

	$fp = fopen('./uploads/'.$name, 'wb');
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_exec($ch);
	curl_close($ch);
	fclose($fp);

	return $name;
}

require_once 'instagram.class.php';

// initialize class
$instagram = new Instagram(array(
  'apiKey'      => '',
  'apiSecret'   => '',
  'apiCallback' => 'http://dev.captura.com/salva_token.php' // must point to success.php
));

$clients_id = array('917de3612ab7400584f195615e61614a', '613968bf42fb4b1eb4a84def7743f0bd','276a4dfb36b74c4d98223a558814b79a','71f699d99c6144239f5827c010d337f6');


if(isset($_SESSION['token']))
	$instagram->setAccessToken($_SESSION['token']);

$search = "narguile";
$max = 20;

$client_id_used = $clients_id[rand(0,3)];

	$tag_media = json_decode( file_get_contents('https://api.instagram.com/v1/tags/'.$search.'/media/recent?count='.$max.'&client_id='.$client_id_used) );
	$contador = 0;
	$inseridos = 0;
	while( intval($tag_media->meta->code) == 200 ){

		foreach($tag_media->data as $media){
			if($media->type=="image"){
				
				$foto_id = $media->id;
				$username = mysql_real_escape_string($media->user->username);
				$profile_picture = mysql_real_escape_string( $media->user->profile_picture );
				$full_name = mysql_real_escape_string( $media->user->full_name );
				$bio = mysql_real_escape_string( $media->user->bio );
				$userid = $media->user->id;

				$image_url = $media->images->standard_resolution->url;
				$image_width = $media->images->standard_resolution->width;
				$image_height = $media->images->standard_resolution->height;
				$thumbnail_url = $media->images->thumbnail->url;
				$tags = mysql_real_escape_string(implode(",", $media->tags));
				$caption_text = mysql_real_escape_string($media->caption->text);
				$filter = mysql_real_escape_string($media->filter);
				$created_time = $media->created_time;
				$link = $media->link;


				$sql = "INSERT INTO fotos (tag_search, foto_id, username, profile_picture, full_name, bio, userid, image_url, image_width, image_height, thumbnail_url, tags, caption_text, filter, created_time, link) 
							VALUES ('$search', '$foto_id','$username','$profile_picture','$full_name','$bio','$userid','$image_url','$image_width','$image_height','$thumbnail_url','$tags','$caption_text','$filter','$created_time','$link');";

				if(mysql_query($sql))
					$inseridos++;
				else
					die(mysql_error());
			}
		}


		$contador++;
		if($contador==10)
			break;

		//Muda client_id e pega outro randomizado para proxima pagina
		$new_client_id =  $clients_id[rand(0,3)];
		$new_url = str_replace($client_id_used, $new_client_id, $tag_media->pagination->next_url);
		$client_id_used = $new_client_id;
		$tag_media = json_decode( file_get_contents($new_url));
	}
	

echo "Total de itens processados (video+foto):".($contador*$max)."<br>";
echo "Total de itens inseridos (apenas foto):".($inseridos)."<br>";