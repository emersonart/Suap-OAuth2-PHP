<?php 
defined('SUAP_CLIENT_ID')		||  define('SUAP_CLIENT_ID','YOUR_CLIENT_ID');
defined('SUAP_CLIENT_SECRET')		|| define('SUAP_CLIENT_SECRET','YOUR_CLIENT_SECRET');

require 'class/Suap_OAuth2.php';
use Suap\Suap;

$suap =  new Suap();
$params = [
	'client_id' => SUAP_CLIENT_ID,
	'client_secret' => SUAP_CLIENT_SECRET,
	'redirect_uri' => 'http://localhost/suap_teste/noci/'
];
$suap->init($params);

echo "<pre>";
var_dump($suap->get_dados());
echo "</pre>";

//$suap->logout();
print_r($suap->get_last_error());
//$suap->login();
?>
