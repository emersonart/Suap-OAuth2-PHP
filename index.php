<?php 


require 'class/Suap_OAuth2.php';
use SuapOAuth2\Suap;

$suap =  new Suap();
$params = [
	'client_id' => SUAP_CLIENT_ID,
	'client_secret' => SUAP_CLIENT_SECRET,
	'redirect_uri' => SUAP_REDIRECT_URI
];
$suap->init($params);

echo "<pre>";
var_dump($suap->get_dados());
echo "</pre>";

//$suap->logout();
print_r($suap->get_last_error());
//$suap->login();
?>
