<?php
namespace Suap;

require dirname(__DIR__).DIRECTORY_SEPARATOR."includes".DIRECTORY_SEPARATOR."constants.php";

class Suap{

	/**
	 *
	 *	@param {} token_expires - Tempo do Cookie retornado pela api 
	 *
	*/
	public $token_expires;

	/**
	 *
	 *	@param {} token - Token code fornecedido pela api
	 *
	*/
	protected $token;

	/**
	 *
	 *	@param {} client_id - ID da aplicação que deve ser gerado em https://suap.ifrn.edu.br/api/
	 *
	*/
	protected $client_id;

	/**
	 *
	 *	@param {} client_secret - Chave secreta da aplicação que deve ser gerado em https://suap.ifrn.edu.br/api/
	 *
	*/
	protected $client_secret;

	/**
	 *
	 *	@param {} login_url - Url para autenticação que será gerada na inicialização da classe
	 *
	*/
	protected $login_url;

	/**
	 *
	 *	@param {} scope - Escopos de autorização enviados pela API
	 *
	*/
	protected $scope;

	/**
	 *
	 *	@param {} access_token - Token de autenticação para envio de requisição que será gerado ao autenticar usuário
	 *
	*/
	protected $access_token;

	/**
	 *
	 *	@param {} data - dados retornados pela requisição ao resource_url
	 *
	*/
	protected $data;

	/**
	 *
	 *	@param {} refresh_token - token para revalidação do token, fornecido pela API
	 *
	*/
	protected $refresh_token;

	/**
	 *
	 *	@param {} last_error - array que guarda o último erro de requisição
	 *
	*/
	protected $last_error;

	/*
	 *
	 * 
	 * 
	 *	@param DEFAULTS 
	 * 
	 * 
	 *
	*/

	/**
	 *
	 *	@param {} cookie_name - Nome do cookie a ser utilizado para permanência da autenticação
	 *
	*/
	protected $cookie_name = 'SUAP_AUTH_eth';

	/**
	 *
	 *	@param {} url - url base do suap
	 *
	*/
	protected $url = 'https://suap.ifrn.edu.br/';

	/**
	 *
	 *	@param {} resource_url - url para requisição de dados
	 *
	*/
	protected $resource_url = 'https://suap.ifrn.edu.br/api/eu/';

	/**
	 *
	 *	@param {} authorization_url - url para solicitar autorização da aplicação
	 *
	*/
	protected $authorization_url = 'https://suap.ifrn.edu.br/o/authorize/';

	/**
	 *
	 *	@param {} logout_url - url que revoga o token solicitado, garantindo o logout.
	 *
	*/
	protected $logout_url = 'https://suap.ifrn.edu.br/o/revoke_token/';

	/**
	 *
	 *	@param {} logout_url - url que recupera o access_tokoen para envio de requisições
	 *
	*/
	protected $token_url = 'https://suap.ifrn.edu.br/o/token/';

	/**
	 *
	 *	@param {} redirect_url - url de retorno da api, pode ser alterado na inicilização da classe. É PRECISO QUE A URL
	 *	FORNECIDA ESTEJA NA LISTA PERMITIDA DA SUA APLICAÇÃO
	*/
	protected $redirect_uri = 'suap_auth/';

	/**
	 *
	 *	@param {} response_type - tipo de resposta esperada para autenticação, não alterar.
	 *
	*/
	protected $response_type  = 'code';

	/**
	 *
	 *	@param {} grant_type - tipo de autenticação, não alterar.
	 *
	*/
	protected $grant_type = 'authorization-code';

	/**
	 *
	 *	@param {} token_type - tipo de token usado na autenticação, não alterar.
	 *
	*/
	protected $token_type = 'Bearer';

	/**
	 *
	 *	@param {} Log_folder - Pasta para logs
	 *
	*/
	protected $Log_folder = __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.'suap_logs';

	/**
	 *
	 *	@param {} logs - Ativar logs
	 *
	*/
	protected $logs = TRUE;

	/**
	 *
	 *	@param {} POST - Variável de apoio para solicitar POST
	 *
	*/
	protected $POST = 'POST';

	/**
	 *
	 *	@param {} GET - Variável de apoio para solicitar GET
	 *
	*/
	protected $GET = 'GET';




  public function __construct(){
    //setando o id do cliente
		if(!is_dir($this->Log_folder)){
			mkdir($this->Log_folder);
		}
	}

  public function init($config = NULL){

		if(!isset($config['client_id']) || !isset($config['client_secret'])){
			return FALSE;
		}
		$this->client_id = $config['client_id'];

		$this->client_secret = $config['client_secret'];

		$this->redirect_uri = (isset($config['redirect_uri']) ? $config['redirect_uri'] : base_url($this->config['redirect_uri']));

		$this->set_login_url();
		
		$this->set_token();

    $this->login();
	}
	
	public function set_token($token = NULL){

		if($token){
			$this->token = $token;
		}else{
			if($cookie = $this->get_cookie()){
				$this->token = $cookie['access_token'];
			}else{
				if(isset($_GET['code'])){
					$this->token = $_GET['code'];
				}else{
					$this->token = NULL;
				}
				
			}
		}
		
    return $this;
	}

	public function logout(){
		$params = [
			'token' => $this->access_token,
			'client_id' => $this->client_id,
			'client_secret' => $this->client_secret
		];
		$url = $this->logout_url;
		$result = $this->send_request($url,$params,NULL,$this->POST);

		

		if(is_array($result)){
			if(!isset($result['error'])){
				$this->revoke();
			}else{
				$result['url'] = $url;
				$result['params'] = $params;
				$this->handle_error($result,'request');
			}
		}
		return FALSE;
		
	}

	public function get_access_token(){
		return $this->access_token;
	}
	
  public function get_dados(){
    $headers = [
			'Authorization: '.$this->token_type." ".($this->access_token),
		];
		var_dump($this->token_type,$this->access_token);
    $this->data = $this->send_request($this->resource_url,NULL,$headers);
    return $this->data;
	}
	
  public function get_escope(){
    return $this->scope;
	}
	
  public function get_client(){
    return $this->client_id;
  }

  public function get_login_url(){
    return $this->login_url;
	}
	
	public function get_last_error($type = 'array'){
		switch($type) {
			case 'string':
				return $this->recursive_implode($this->last_error);
				//return "<p>".implode("</p><p>",$this->last_error)."</p>";
				break;
			case 'json':
				return json_encode($this->last_error);
				break;
			case 'object':
				return (object)$this->last_error;
					break;
			default:
				return $this->last_error;
				break;
		}
	}

	public function login(){
		if(!$this->token){
		 	header('Location: '.$this->login_url);
		}elseif($this->is_authenticated() && isset($this->get_cookie()['access_token'])){
			var_dump($this->get_cookie());
			$this->unset_cookie();
			exit();
			if(!$this->refresh_token($this->get_cookie()['refresh_token'])){
				$this->unset_cookie()->login();
			} 
		}else{
			$params =  [
				'grant_type'=>str_replace('-','_',$this->grant_type),
				'code'=>$this->token,
				'client_id'=>$this->client_id,
				'client_secret'=>$this->client_secret,
				'redirect_uri'=>$this->redirect_uri
			];
			$result = $this->send_request($this->token_url,$params,NULL,$this->POST);
			var_dump($result);
				if(isset($result['access_token'])){
					$this->access_token = $result['access_token'];
					$this->token_expires = $result['expires_in'];
					$this->token_type = $result['token_type'];
					$this->scope = explode(' ',$result['scope']);
					$this->refresh_token = $result['refresh_token'];
					
					$this->set_cookie($result);
		
					
				}else{
					$result['url'] = $this->token_url;
					$result['params'] = $params;
					$this->handle_error($result);
				}
		}
	}
	
	private function set_login_url(){

    $this->login_url = $this->authorization_url.
      "?response_type=".$this->response_type.
      "&grant_type=".$this->grant_type.
      "&client_id=".$this->client_id.
			"&redirect_uri=".$this->redirect_uri.
			'&scopes=informacao+email+dados';
			
		return $this->login_url;

  }

  

	public function is_authenticated(){
		return $this->is_valid();
	}

	private function is_valid(){
		$data =$this->get_cookie();

		if(is_array($data) && isset($data['access_token'])){
			return $this;
		}
		return false;
	}

	private function set_cookie($data){
		if($this->get_cookie()){
			$this->unset_cookie();
		}
	
		
		$data['created_at'] = date('Y-m-d H:i:s');
		$data_serialize = serialize($data);
		setcookie($this->cookie_name,$data_serialize,time()+$data['expires_in'],'/');
		return $this;
	}

	public function get_cookie($key = NULL){
		if(isset($_COOKIE[$this->cookie_name]) && !empty($_COOKIE[$this->cookie_name])){
			$cookie = unserialize($_COOKIE[$this->cookie_name]);
			if($key){
				$cookie = $cookie[$key];
			}
			return $cookie;
		}
		return false;
		
	}

	public function unset_cookie(){
		setcookie($this->cookie_name,'',-1,'/');
		unset($_COOKIE[$this->cookie_name]);
		return $this;
	}

  private function set_data(){
		$headers = [
			'Authorization: '.$this->token_type." ".($this->access_token),
		];
		
		$this->data = $this->send_request($this->resource_url,NULL,$headers);
		$data = $this->data;
		if(isset($data['error'])){
			$data['url'] = $this->resource_url;
			$data['params'] = [
				'headers' => $headers
			];
			$this->handle_error($data);
		}
    return $this;
	}

	public function refresh_token($token = NULL){

		$headers = [
			'Authorization: '.$this->token_type." ".$this->access_token,
		];

		$url = $this->token_url;

		$params = [
			'grant_type' => 'refresh_token',
			'refresh_token' => ($token ? $token : $this->refresh_token),
			'client_id' => $this->client_id,
			'client_secret' => $this->client_secret,
		];

		$result = $this->send_request($url,$params,$headers,$this->POST);

		if(!isset($result['error'])){
      $this->access_token = $result['access_token'];
      $this->token_expires = $result['expires_in'];
      $this->token_type = $result['token_type'];
      $this->scope = explode(' ',$result['scope']);
			$this->refresh_token = $result['refresh_token'];
			
			$this->set_cookie($result);
    }else{
			$result['url'] = $url;
			$result['params'] = $params;
			$this->handle_error($result);
			return FALSE;
		}
		return $this;

	}

	private function send_request($url, $params=NULL, $headers=[],$method = 'GET'){


    $ch = curl_init();
	
		if($params){
			if(is_array($params)){
				if($method == 'POST'){
					curl_setopt($ch, CURLOPT_POST, 1);
    			curl_setopt($ch, CURLOPT_POSTFIELDS,$params);
				}else{
					$url = $url."?".http_build_query($params);
				}
			}else{
				$url = $url."?".$params;
			}
		}
		curl_setopt($ch,CURLOPT_URL,$url);

		if($headers){
			$headers = [
				'Authorization: '.$this->token_type." ".$this->access_token,
			];
			curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
		}
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    $result = curl_exec($ch);

    curl_close($ch);

    $result = json_decode($result,true);
    return $result;
  }

	private function handle_error($data,$type = 'request'){
		$data['occurried_in'] = date('Y-m-d H:i:s');
		$this->last_error = $data;
		$this->set_log($data);
		return $this->last_error;
	}

	private function revoke(){
		$this->unset_cookie();
		$this->access_token = NULL;
		$this->token = NULL;
		$this->token_expires = NULL;
		$this->scope = NULL;
		$this->data = NULL;
		$this->access_token = NULL;

		return TRUE;
	}

	private function recursive_implode(array $array, $glue = '<br/>',$glue_key = ':', $include_keys = true, $trim_all = false){
		$glued_string = '';

		// Itera recursivamente o array e concatena em string
		array_walk_recursive($array, function($value, $key) use ($glue,$glue_key, $include_keys, &$glued_string)
		{
			//caso esteja setado para incluir as chaves, junta com a cola de chave
			$include_keys and $glued_string .= "[".$key."]".$glue_key." ";
			//junta os valores com a cola passada
			$glued_string .= $value.$glue;
		});

		// remove a ultima "cola" da string
		strlen($glue) > 0 and $glued_string = substr($glued_string, 0, -strlen($glue));

		// se trim_all estiver setada, remove todos os espaços da string
		$trim_all and $glued_string = preg_replace("/(\s)/ixsm", '', $glued_string);

		return (string) $glued_string;
	}

	private function set_log($data){
		if(!$this->logs) return false;

		date_default_timezone_set('America/Sao_Paulo');

		if(is_array($data)){
			$text = $this->recursive_implode($data,',',' -> ');
		}else{
			$text = '['.$data.']';
		}
		
		$date = date('Y-m-d');
		$time = date('H:i:s');

		$text = '['.$date.' '.$time.' | '.$_SERVER['REMOTE_ADDR'].'] => '.$text."\n";
		$archive = $this->Log_folder.DIRECTORY_SEPARATOR.'Log_'.$date.".php";
		if(!file_exists($archive)){
			$text = '<?php defined("SUAP_CLASS") OR die("403 Forbiden"); ?>'."\n".$text;
		}

		$open = fopen("$archive","a+b");
		fwrite($open,$text);
		fclose($open);
		
		return true;
	}
}
