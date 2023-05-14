<?


namespace IsraelNogueira\SkySession;

/**
 * 
 * SKY-SESSION
 *
 * Leve a segurança das suas sessões para o próximo nível com a Sky-Session. 
 * Proteja seus dados e mantenha o controle das suas informações com facilidade e simplicidade.
 *
 * @author    Israel Nogueira
 * @category  Autenticação, Sessão
 * @package   israel-nogueira/sky-session
 * @license   MIT
 * @version   1.0.0
 * @link      https://github.com/israel-nogueira/sky-session
 * @since     PHP v7.0
 *
 * 
 */


class session {
	private $secury = true;

	/*
	|--------------------------------------------- 
	|	CONECTA COM A SESSÃO
	|--------------------------------------------- 
	|
	|	Inicia a sessão 
	|
	*/
	public function __construct($session_name=null) 
	{
		
		if (session_status() == PHP_SESSION_NONE) {
			$ENV = parse_ini_file(realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR.'.env');
			foreach ($ENV as $key => $line){putenv($key.'='.$line);}
			session_set_cookie_params(0, '/', '', true, true);
			if (!is_null(getEnv('SESSION_SAVE_PATH')) && getEnv('SESSION_SAVE_PATH') != "") {
				session_save_path(getEnv('SESSION_SAVE_PATH'));
			}
			ini_set('session.gc_maxlifetime', 3600);
			ini_set('session.name', 'app_session_name');
			ini_set('session.cookie_httponly', 1);
			ini_set('session.cookie_samesite', 'Lax');
			ini_set('session.use_cookies', 1);
			ini_set('session.use_strict_mode', 1);
			ini_set('session.use_only_cookies', 1);
			ini_set('session.sid_length', 128);
			ini_set('session.auto_start', 0);
			ini_set('url_rewriter.tags', '');
			$sessnam = $session_name ?? getEnv('SESSION_NAME');
			session_name($sessnam);
			session_start();
		}
	}

	/*
	|--------------------------------------------- 
	|	FUNÇÕES ESTÁTICAS
	|--------------------------------------------- 
	|
	|	Caso a função não exista na classe, ele transforma nos métodos estáticos
	|	session::variável("dado") // seta valor 
	|	session::variável() // recupera valor
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
	|	Aqui recuperamos os dados na sessão, utilizando a função ->get()
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
	|	Aqui inserimos os dados na sessão,utilizando a função ->set()
	|
	*/
	public function __set($name, $value) 
	{
		return $this->set($name, $value);
	}


	/*
	|--------------------------------------------------------------------------- 
	|	Apagamos um dado da sessão
	|--------------------------------------------------------------------------- 
	*/
	public function unset($var) 
	{
		if ($this->secury) {
			unset($_SESSION[$this->crypta($var)]);
		}else{
			unset($_SESSION[$var]);
		}
	}

	/*
	|--------------------------------------------------------------------------- 
	|	Recupera um dado da sessão
	|--------------------------------------------------------------------------- 
	*/
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

	/*
	|--------------------------------------------------------------------------- 
	|	Cria uma variável na sessão, seja criptografado ou não
	|--------------------------------------------------------------------------- 
	*/
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

	/*
	|--------------------------------------------------------------------------- 
	|	Verifica e retorna de é um json valido
	|--------------------------------------------------------------------------- 
	*/
	public function isJson($string) 
	{
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}


	/*
	|--------------------------------------------------------------------------- 
	|	RETORNA TODAS AS SESSÕES
	|--------------------------------------------------------------------------- 
	|
	|	Essa função retorna todas as sessões armazenadas na instância da classe. 
	|
	*/
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
	
	/*
	|--------------------------------------------------------------------------------------- 
	|	ATUALIZANDO O ID
	|--------------------------------------------------------------------------------------- 
	|
	|	A função refreshID() é responsável por regenerar o ID da sessão atual. 
	|	Isso é útil para prevenir ataques de sessão fixa e melhorar a segurança da aplicação. 
	|	Essa função retorna true se a regeneração foi bem sucedida e false caso contrário.
	|
	|
	*/	
    public static function refreshID() 
	{
		return session_regenerate_id();
	}

	/*
	|--------------------------------------------------------------------------------------- 
	|	WRITECLOSE
	|--------------------------------------------------------------------------------------- 
	|
	|	Utilizada para escrever os dados da sessão no armazenamento e fechar a sessão. 
	|	Isso garante que todos os dados atualizados durante a execução da aplicação
	|	sejam salvos corretamente. O retorno da função é true se os dados 
	|	foram salvos com sucesso e false caso contrário.
	|
	*/	
    public static function writeClose() 
	{
		return session_write_close();
	}

	
	/*
	|--------------------------------------------------------------------------------------- 
	|	FINALIZAMOS A SESSÃO
	|--------------------------------------------------------------------------------------- 
	|
	|	A função finish() é responsável por encerrar e limpar a sessão atual do usuário. 
	|	Ela remove todos os dados armazenados na variável global $_SESSION 
	|	e destrói a sessão atual utilizando a função session_destroy(). 
	|
	|	Em seguida, a função remove quaisquer informações de cookies relacionados à sessão, 
	|	inclusive aqueles com nome de sessão antigos, utilizando a função setcookie(). 
	|
	|	Dessa forma, a função garante que todas as informações da sessão sejam removidas 
	|	e que o usuário comece uma nova sessão limpa quando necessário.
	|
	*/
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

	/*
	|--------------------------------------------------------------------------------------- 
	|	CRIPTOGRAFIA
	|--------------------------------------------------------------------------------------- 
	|	
	|	A função crypta é responsável por criptografar os dados da sessão usando a cifra AES-256-CBC, 
	|	que é considerada uma das mais seguras e amplamente utilizadas em criptografia simétrica. 
	|	Ela recebe um parâmetro $data contendo os dados a serem criptografados e retorna o resultado da criptografia.
	|
	*/
	private function crypta($data) 
	{
		return openssl_encrypt($data, 'aes-256-cbc', getEnv('SESSION_CRYPT_KEY'), 0, getEnv('SESSION_CRYPT_IV'));
	}

	/*
	|------------------------------------------------------------------------------------------------------- 
	|	DECRIPTOGRAFIA
	|------------------------------------------------------------------------------------------------------- 
	|	
	|	Já a função decrypta é responsável por descriptografar os dados da sessão que foram criptografados 
	|	pela função crypta. Ela também utiliza a cifra AES-256-CBC e recebe um parâmetro $data contendo 
	|	os dados criptografados. A função retorna os dados originais após a descriptografia.
	|
	*/
	private function decrypta($data) 
	{
		return openssl_decrypt($data, 'aes-256-cbc', getEnv('SESSION_CRYPT_KEY'), 0, getEnv('SESSION_CRYPT_IV'));
	}

}

