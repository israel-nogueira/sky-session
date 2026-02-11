# 🚀 Sky Session

[![Latest Version](https://img.shields.io/packagist/v/israel-nogueira/sky-session.svg)](https://packagist.org/packages/israel-nogueira/sky-session)
[![PHP Version](https://img.shields.io/packagist/php-v/israel-nogueira/sky-session.svg)](https://packagist.org/packages/israel-nogueira/sky-session)
[![License](https://img.shields.io/packagist/l/israel-nogueira/sky-session.svg)](https://packagist.org/packages/israel-nogueira/sky-session)
[![Total Downloads](https://img.shields.io/packagist/dt/israel-nogueira/sky-session.svg)](https://packagist.org/packages/israel-nogueira/sky-session)

**Gerenciamento de sessões moderno, seguro e testado para PHP 8.1+**

Leve a segurança das suas sessões para o próximo nível com Sky Session. Criptografia AES-256-CBC, PSR-4 compliant, 100% testado e fácil de usar.
---
## 📋 Índice

- [Características](#-características)
- [Requisitos](#-requisitos)
- [Instalação](#-instalação)
- [Configuração](#-configuração)
- [Uso Básico](#-uso-básico)
- [Uso Avançado](#-uso-avançado)
- [API Completa](#-api-completa)
- [Testes](#-testes)
- [Segurança](#-segurança)
- [Contribuindo](#-contribuindo)
- [Licença](#-licença)

---

## ✨ Características

✅ **Criptografia AES-256-CBC** - Proteção de ponta a ponta dos dados  
✅ **PHP 8.1+** - Type hints, strict types e recursos modernos  
✅ **PSR-4 Compliant** - Estrutura profissional  
✅ **100% Testado** - Cobertura completa com PHPUnit  
✅ **Singleton Pattern** - Instância única e eficiente  
✅ **Magic Methods** - API intuitiva e flexível  
✅ **Static Methods** - Uso sem instanciação  
✅ **Arrays & Objects** - Suporte nativo com JSON  
✅ **Zero Dependências** - Apenas extensões nativas do PHP  
✅ **Documentação Completa** - PHPDoc em todos os métodos  

---

## 📦 Requisitos

- PHP >= 8.1
- ext-openssl
- ext-json
- ext-mbstring

---

## 🔧 Instalação

```bash
composer require israel-nogueira/sky-session
```

---

## ⚙️ Configuração

### 1. Crie o arquivo `.env` na raiz do projeto:

```env
SESSION_NAME=my_app_session
SESSION_LIFETIME=3600
SESSION_SECURE=true
SESSION_CRYPT_KEY=your_32_byte_hex_key_here
SESSION_CRYPT_IV=your_base64_iv_here
SESSION_COOKIE_PATH=/
SESSION_COOKIE_DOMAIN=
SESSION_COOKIE_SECURE=true
SESSION_COOKIE_SAMESITE=Lax
```

### 2. Gere chaves seguras:

```bash
# Gerar chave de criptografia
php -r "echo bin2hex(random_bytes(16));"

# Gerar IV
php -r "echo base64_encode(random_bytes(16));"
```

---

## 🚀 Uso Básico

### Forma Orientada a Objetos

```php
<?php

use IsraelNogueira\SkySession\Session;

// Criar instância
$session = new Session();

// Definir valores
$session->set('username', 'john_doe');
$session->set('user_data', [
    'email' => 'john@example.com',
    'role' => 'admin'
]);

// Recuperar valores
echo $session->get('username'); // john_doe
$userData = $session->get('user_data');

// Verificar existência
if ($session->has('username')) {
    echo 'Usuário logado!';
}

// Remover valor
$session->unset('username');

// Obter todas as variáveis
$all = $session->all();
```

### Usando Magic Methods

```php
<?php

use IsraelNogueira\SkySession\Session;

$session = new Session();

// Set usando propriedade
$session->username = 'jane_doe';
$session->cart = ['item1', 'item2'];

// Get usando propriedade
echo $session->username; // jane_doe
print_r($session->cart);

// Isset
if (isset($session->username)) {
    echo 'Username existe!';
}

// Unset
unset($session->cart);
```

### Usando Métodos Estáticos

```php
<?php

use IsraelNogueira\SkySession\Session;

// Set
Session::username('admin');
Session::preferences(['theme' => 'dark', 'lang' => 'pt-BR']);

// Get
echo Session::username(); // admin
$prefs = Session::preferences();

// Unset (passando null)
Session::username(null);

// Múltiplos argumentos
Session::data('arg1', 'arg2', 'arg3'); // armazena como array
```

---

## 🎯 Uso Avançado

### Configuração Personalizada

```php
<?php

use IsraelNogueira\SkySession\Session;

$session = new Session([
    'name' => 'custom_session',
    'lifetime' => 7200,
    'secure' => true,
    'cookie_path' => '/',
    'cookie_domain' => '.example.com',
    'cookie_secure' => true,
    'cookie_samesite' => 'Strict',
    'crypt_key' => 'your_key',
    'crypt_iv' => 'your_iv'
]);
```

### Singleton Pattern

```php
<?php

use IsraelNogueira\SkySession\Session;

// Primeira chamada cria a instância
$session1 = Session::getInstance(['secure' => true]);

// Próximas chamadas retornam a mesma instância
$session2 = Session::getInstance();

// $session1 === $session2 (true)
```

### Trabalhando com Arrays e Objetos

```php
<?php

use IsraelNogueira\SkySession\Session;

$session = new Session();

// Arrays complexos
$session->set('cart', [
    'items' => [
        ['id' => 1, 'name' => 'Product A', 'qty' => 2],
        ['id' => 2, 'name' => 'Product B', 'qty' => 1]
    ],
    'total' => 150.00,
    'discount' => 10.00
]);

// Objetos (convertidos automaticamente para array)
$user = new stdClass();
$user->name = 'John';
$user->email = 'john@example.com';
$session->set('user', $user);

// Recuperação mantém estrutura
$cart = $session->get('cart');
echo $cart['items'][0]['name']; // Product A
```

### Regeneração de ID

```php
<?php

use IsraelNogueira\SkySession\Session;

$session = new Session();

// Regenerar ID (recomendado após login)
if ($loginSuccess) {
    $session->regenerateId();
    $session->set('authenticated', true);
}
```

### Destruição Segura

```php
<?php

use IsraelNogueira\SkySession\Session;

$session = new Session();

// Logout completo
$session->destroy();
// Remove todos os dados, cookies e destrói a sessão
```

### Modo Sem Criptografia

```php
<?php

use IsraelNogueira\SkySession\Session;

// Para desenvolvimento ou quando criptografia não é necessária
$session = new Session(['secure' => false]);

$session->set('debug', 'visible data');
// Dados ficam visíveis em $_SESSION
```

---

## 📚 API Completa

### Métodos de Instância

| Método | Descrição | Retorno |
|--------|-----------|---------|
| `set(string $key, mixed $value): void` | Define um valor | void |
| `get(string $key): mixed` | Recupera um valor | mixed\|null |
| `unset(string $key): void` | Remove um valor | void |
| `has(string $key): bool` | Verifica existência | bool |
| `all(): array` | Retorna todas variáveis | array |
| `regenerateId(): bool` | Regenera ID da sessão | bool |
| `destroy(): void` | Destrói a sessão | void |
| `writeClose(): bool` | Fecha escrita da sessão | bool |

### Métodos Estáticos

```php
// Chamadas dinâmicas
Session::variableName($value); // Set
Session::variableName();        // Get
Session::variableName(null);    // Unset

// Métodos com prefixo __
Session::__set($key, $value);
Session::__get($key);
Session::__regenerateId();
Session::__destroy();
```

### Magic Methods

```php
$session->property = $value;    // __set
$value = $session->property;    // __get
isset($session->property);      // __isset
unset($session->property);      // __unset
```

---

## 🧪 Testes

### Para Desenvolvedores (Clone do Repositório)

Se você clonou o repositório para contribuir:

```bash
# Instalar dependências
composer install

# Executar todos os testes
composer test

# Com cobertura
composer test:coverage

# Análise estática
composer phpstan

# Code Style
composer cs:check  # Verificar
composer cs:fix    # Corrigir
```

### Para Usuários (Instalação via Composer)

Se você instalou via `composer require israel-nogueira/sky-session`, os testes estão no pacote instalado.

Para rodar os testes do pacote:

```bash
# Windows
php vendor\bin\phpunit vendor\israel-nogueira\sky-session\tests\Unit\

# Linux/Mac
php vendor/bin/phpunit vendor/israel-nogueira/sky-session/tests/Unit/
```

**Nota:** Os testes são executados automaticamente no CI/CD antes de cada release. Você não precisa rodá-los para usar a biblioteca com segurança.

---

## 🔒 Segurança

### Práticas Implementadas

✅ **Criptografia AES-256-CBC** - Padrão militar  
✅ **Strict Mode** - Previne session fixation  
✅ **HTTP Only Cookies** - Proteção contra XSS  
✅ **Secure Cookies** - Apenas HTTPS  
✅ **SameSite** - Proteção CSRF  
✅ **Session ID Regeneration** - Após autenticação  
✅ **Validação de Entrada** - Todos os parâmetros  
✅ **Exceptions** - Tratamento robusto de erros  

### Reportar Vulnerabilidades

Envie para: **israel@feats.com.br**

---

## 🤝 Contribuindo

1. Fork o projeto
2. Crie uma branch (`git checkout -b feature/nova-funcionalidade`)
3. Commit suas mudanças (`git commit -m 'feat: adiciona nova funcionalidade'`)
4. Push para a branch (`git push origin feature/nova-funcionalidade`)
5. Abra um Pull Request

### Padrões

- ✅ PSR-12 Code Style
- ✅ PHPStan Level Max
- ✅ Testes para novas funcionalidades
- ✅ Documentação PHPDoc
- ✅ Conventional Commits

---

## 📄 Licença

MIT License - veja [LICENSE](LICENSE) para detalhes.

---

## 👤 Autor

**Israel Nogueira**

- Email: israel@feats.com.br
- GitHub: [@israel-nogueira](https://github.com/israel-nogueira)

---

## ⭐ Mostre seu apoio

Se este projeto te ajudou, dê uma ⭐!

---

## 📝 Changelog

### [2.0.0] - 2024

#### 🎉 Adicionado
- Refatoração completa para PHP 8.1+
- Suporte a type hints e strict types
- Interfaces e contratos (PSR)
- Testes unitários com 100% cobertura
- Exceptions personalizadas
- Singleton pattern
- Configuração via array
- Métodos estáticos dinâmicos

#### 🔧 Modificado
- Nome da classe para PascalCase
- Estrutura PSR-4
- Documentação PHPDoc completa
- README modernizado

#### ❌ Removido
- Suporte PHP < 8.1
- Tags PHP curtas
- Dependência de parse_ini_file
- Código legado

---

**Desenvolvido com ❤️ por Israel Nogueira**
