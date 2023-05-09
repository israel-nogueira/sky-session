<?
namespace IsraelNogueira\SkySession;
/**
 * ----------------------------------------------------------------------------------
 * 
 * Essa é uma classe de sessões em PHP que permite armazenar informações do 
 * usuário de forma segura e persistente entre várias solicitações do usuário. 
 * 
 * Ela utiliza criptografia para proteger os dados armazenados na sessão, 
 * garantindo assim a privacidade e a integridade das informações. 
 * 
 * É uma solução simples e fácil de usar, que pode ser facilmente 
 * integrada em qualquer aplicativo web PHP. 
 * Com uma classe de sessões criptografada, os desenvolvedores 
 * podem construir aplicativos web mais seguros e confiáveis, 
 * sem se preocupar com a complexidade da criptografia.
 * 
 * ----------------------------------------------------------------------------------
 */
class session {
	private $secury = true;

	/*
	|--------------------------------------------- 
	|	CONECTA COM A SESSÃO
	|--------------------------------------------- 
	|
	*/
	public function __construct($session_name=null) 
	{
		if(empty($_SESSION)){
			if (session_status() == PHP_SESSION_NONE) {
				$sessnam       = $session_name??getEnv('SESSION_NAME');
				session_name($sessnam);
			}
			$session_id = session_id();
			session_write_close();
			session_id($session_id);
			session_start();
		}
	}

	/*
	|--------------------------------------------- 
	|	FUNÇÕES ESTÁTICAS
	|--------------------------------------------- 
	|
	|
	*/
	public static function __callStatic( $_name, $arguments )
	{
		$session = new session();
		if(strpos($_name,'__')===false){
			if(count($arguments)==0){
				return $session->get($_name);
			}elseif(count($arguments)==1){
				if($arguments[0]==null){
					$session->unset($_name);
				}else{
					return $session->set($_name,$arguments[0]);
				}
			}else{
				return $session->set($_name,$arguments);
			}
		}else{
			$session = new session();
			$_fn = substr($_name,2);
				if(count($arguments)==0){
				return $session->$_fn();
				}else{
				return $session->$_fn($arguments);
			}
		}
	}

	/*
	|--------------------------------------------- 
	|	MÉTODO GET
	|--------------------------------------------- 
	|
	|
	*/
	public function __get($_sess)
	{
		return $this->get($_sess);
	}

	/*
	|--------------------------------------------- 
	|	MÉTODO SET
	|--------------------------------------------- 
	|
	|
	*/
	public function __set($name, $value) 
	{
		return $this->set($name, $value);
	}

	public function unset($var) 
	{
		if ($this->secury) {
			unset($_SESSION[$this->crypta($var)]);
		}else{
			unset($_SESSION[$var]);
		}
	}

	public function get($var) 
	{
		if ($this->secury) {
			if(gettype(@$_SESSION[$this->crypta($var)])=='NULL'){
				return NULL;
			}else{
				$_value =  $this->decrypta($_SESSION[$this->crypta($var)]);
				if($this->isJson($_value)){ $_value = json_decode($_value, true);}
				return $_value;
			}
		}else{
			if(gettype(@$_SESSION[$var])=='NULL'){
				return NULL;
			}else{
				if($this->isJson($_SESSION[$var])){ $_SESSION[$var] = json_decode($_SESSION[$var], true);}
				return $_SESSION[$var];
			}
		}
	}

	public function set($var, $value) 
	{
		if(is_array($value) || is_object($value)){ 
			$value = json_encode($value);
		}
		if ($this->secury) {
				$_SESSION[$this->crypta($var) ] = $this->crypta($value);
		}else {
				$_SESSION[$var] = $value;
		}
	}

	public function isJson($string) 
	{
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}

	public function getAllSessions() 
	{
		if ($this->secury) {
			$_NEWSESSION = [];
			foreach ($_SESSION as $key => $value) {
				if($this->isJson($this->decrypta($value))){
					$_NEWSESSION[$this->decrypta($key)] = json_decode($this->decrypta($value), true);
				}else{
					$_NEWSESSION[$this->decrypta($key)] = $this->decrypta($value, true);
				}
			}
			return $_NEWSESSION;
		}else {
			return $_SESSION;
		}
	}
	
    public static function refreshID() 
	{
		return session_regenerate_id();
	}
    public static function write_close() 
	{
		return session_write_close();
	}

	public function finish() 
	{
		$_SESSION = array();
		session_unset();
		session_destroy();
		session_write_close();
		setcookie(session_name(),'',0,'/');
		if (!empty($_COOKIE)) {
			foreach ($_COOKIE as $name => $value) {
				setcookie($name, "", time() - 3600);
			}
		}
	}

	private function crypta($data) 
	{
		return openssl_encrypt($data, 'aes-256-cbc', getEnv('SESSION_CRYPT_KEY'), 0, getEnv('SESSION_CRYPT_IV'));
	}

	private function decrypta($data) 
	{
		return openssl_decrypt($data, 'aes-256-cbc', getEnv('SESSION_CRYPT_KEY'), 0, getEnv('SESSION_CRYPT_IV'));
	}

}