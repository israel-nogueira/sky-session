
<p align="center">
    <img src="https://raw.githubusercontent.com/israel-nogueira/sky-session/main/src/topo_README.jpg"/>
</p>
<p align="center">
    <a href="#instalação" target="_Self">Instalação</a> |
    <a href="#aplicação" target="_Self">Aplicação</a> |
    <a href="#uso" target="_Self">Uso</a> |
    <a href="#modo-estático" target="_Self">Função Estática</a> |
    <a href="#manipulando-a-sessão" target="_Self">Manipulando a sessão</a> |
    <a href="#criptografia" target="_Self">Criptografia</a> |
    <a href="#outras-funções" target="_Self">Outras Funções</a> 
</p>
<p align="center">
    <a href="https://packagist.org/packages/israel-nogueira/sky-session"><img src="https://poser.pugx.org/israel-nogueira/sky-session/v/stable.svg"></a>
    <a href="https://packagist.org/packages/israel-nogueira/sky-session"><img src="https://poser.pugx.org/israel-nogueira/sky-session/downloads"></a>
    <a href="https://packagist.org/packages/israel-nogueira/sky-session"><img src="https://poser.pugx.org/israel-nogueira/sky-session/license.svg"></a>
</p>


Se você está procurando uma solução simples e fácil para trabalhar com sessões criptografadas em PHP, a classe de sessões que desenvolvi pode ser a escolha certa para você. Com ela, você pode facilmente armazenar e recuperar dados sensíveis em suas sessões, mantendo-os protegidos contra invasões e vazamentos de informações.

A classe é extremamente simples de usar, com um construtor que permite configurar facilmente a criptografia da sessão e um conjunto de métodos intuitivos para armazenar e recuperar dados. Com uma documentação clara e completa, você pode começar a usar a classe em questão de minutos, sem ter que se preocupar com complexidades desnecessárias.


## Instalação

Instale via composer.

```plaintext
    composer require israel-nogueira/sky-session
```

Acrescente em seu ```.env``` na raiz do seu projeto:

```env

    #/.env

    SESSION_CRYPT_KEY={SUA_CHAVE_SECRETA}
    SESSION_CRYPT_IV={CRYPT_IV}
    SESSION_NAME={NOME_DA_SESSÃO_DEFAULT}
    SESSION_SAVE_PATH={PATH} (Opcional)

```

## USO

Feito isso, você pode iniciar a utilização da classe.<br>

```php
<?
	require '/vendor/autoload.php';
	use IsraelNogueira/SkySession/session;


	$usuario = new session();
	$usuario->nome = "João da Silva";
	$usuario->dados  = ["apelido"=>"Jão", "email"=>"jão@gmail.com"];




```

Em qualquer página você poderá chamar:

```php
<?
	require '/vendor/autoload.php';
	use IsraelNogueira/SkySession/session;

	$usuario = new session();
	echo $usuario->nome;
	print_r($usuario->dados);



```

## MODO ESTÁTICO

Você também pode utilizar a forma estática da classe.<br/>
Dessa maneira você não precisa sempre criar uma nova instancia.
Basta chamar diretamente a função e pronto.

```php
<?
	require '/vendor/autoload.php';
	use IsraelNogueira/SkySession/session;


	session::nome("João da Silva");
	session::dados(["apelido"=>"Jão", "email"=>"jão@gmail.com"]);




```

E para chamar os dados também é simples:

```php
<?
	require '/vendor/autoload.php';
	use IsraelNogueira/SkySession/session;

	echo session::nome();
	print_r(session::dados());


```

## MANIPULANDO A SESSÃO

```php
<?
	require '/vendor/autoload.php';
	use IsraelNogueira/SkySession/session;

	// criando uma informação
	session::nome("João da silva");

	//modificando uma informação
	session::nome("Maria Aparecida");
	
	// retorna uma informação 
	echo session::nome(); // aqui retorna Maria Aparecida

	// excluindo uma informação 
	session::nome(null);

	// Utilizando Arrays
	session::dados(["apelido"=>"Jão", "email"=>"jão@gmail.com"]);
	
	// utilizando apenas um parametro
	echo session::dados()['apelido']; // retorna "Jão"

	// retorna a array inteira para variável
	$_DADOS = session::dados();

	// utiliza normalmente
	echo $_DADOS['email'];


```

## Criptografia

A classe de sessões utiliza criptografia de ponta a ponta, garantindo que os dados do usuário permaneçam protegidos durante o tráfego e o armazenamento no servidor. 
Além disso, a criptografia é implementada com algoritmos robustos e altamente seguros, como o AES e o HMAC-SHA256, oferecendo uma camada adicional de proteção contra ameaças de segurança.

```txt

	A $_SESSION ficará assim:
	
	Array
	(
		[7MKM1vYOmOLkwQHlRrRT2A==] => rBKu5vB7+GWq53BboT9Qrw==
		[TjDYbihs4t79o3BMiRBEPQ==] => lm+sC7+SYOnmvHXyEdCBiiYEymgyyV4+gD7Yl7BZBfs2hez/3xiUBtXyl9w0GqT6ykDpNPHZPHASvc9PCMdbow==
	)

```


## OUTRAS FUNÇÕES

```php
<?
	require '/vendor/autoload.php';
	use IsraelNogueira/SkySession/session;

	// Retorna um dado
	session::__get($var);

	// Seta um dado novo
	session::__set($var, $value);

	// Finaliza session
	session::__finish();

	// Retorna toda a sessão
	session::__getAllSessions();

	// Atualiza o ID da sessão
	session::__refreshID();

	// Utilizada para salvar a sessão no armazenamento antes de fechar
	session::__writeClose();

	// Retorna uma string criptografada
	session::__crypta($value);

	// Retorna a string decifrada
	session::__decrypta($crypted);


?>
```
