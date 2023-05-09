![GalaxyDB](https://raw.githubusercontent.com/israel-nogueira/sky-session/main/src/topo_README.jpg)

---

[![Latest Stable Version](https://poser.pugx.org/israel-nogueira/sky-session/v/stable.svg)](https://packagist.org/packages/israel-nogueira/sky-session)
[![Total Downloads](https://poser.pugx.org/israel-nogueira/sky-session/downloads)](https://packagist.org/packages/israel-nogueira/sky-session)
[![License](https://poser.pugx.org/israel-nogueira/sky-session/license.svg)](https://packagist.org/packages/israel-nogueira/sky-session)


Se você está procurando uma solução simples e fácil para trabalhar com sessões criptografadas em PHP, a classe de sessões que desenvolvi pode ser a escolha certa para você. Com ela, você pode facilmente armazenar e recuperar dados sensíveis em suas sessões, mantendo-os protegidos contra invasões e vazamentos de informações.

A classe é extremamente simples de usar, com um construtor que permite configurar facilmente a criptografia da sessão e um conjunto de métodos intuitivos para armazenar e recuperar dados. Com uma documentação clara e completa, você pode começar a usar a classe em questão de minutos, sem ter que se preocupar com complexidades desnecessárias.


## Instalação

Instale via composer.

```plaintext
    composer require israel-nogueira/sky-session
```

Acrescente em seu ```.env```:

```env

    #/vendor/israel-nogueira/sky-session/src/.env

    SESSION_CRYPT_KEY={SUA_CHAVE_SECRETA}
    SESSION_CRYPT_IV={CRYPT_IV}
    SESSION_NAME={NOME_DA_SESSÃO_DEFAULT}
    SESSION_SAVE_PATH={PATH} (Opcional)

```

## APLICAÇÃO

Acrescente em seu *composer.json*, pois  é a configuração que dá inicio a sessão em todas as páginas;
```json
   {
        "autoload": {
            "psr-4": {
                "IsraelNogueira\\SkySession\\": "src/"
            },
            "files": [
                "/vendor/israel-nogueira/sky-session/src/session.init.php"
            ]
        }
    }


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

	Array
	(
		[7MKM1vYOmOLkwQHlRrRT2A==] => rBKu5vB7+GWq53BboT9Qrw==
		[TjDYbihs4t79o3BMiRBEPQ==] => lm+sC7+SYOnmvHXyEdCBiiYEymgyyV4+gD7Yl7BZBfs2hez/3xiUBtXyl9w0GqT6ykDpNPHZPHASvc9PCMdbow==
	)

```