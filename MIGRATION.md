# 🔄 Guia de Migração v1.x → v2.0

## 📋 Visão Geral

A versão 2.0 traz melhorias significativas de arquitetura, segurança e qualidade de código.

**⚠️ BREAKING CHANGES:** Esta é uma versão major com mudanças incompatíveis.

---

## 🎯 Principais Mudanças

### 1. Requisitos

| Item | v1.x | v2.0 |
|------|------|------|
| PHP | >= 7.0 | >= 8.1 |
| Extensões | openssl | openssl, json, mbstring |
| Autoload | PSR-4 | PSR-4 |

### 2. Nome da Classe

```php
// ❌ v1.x
use IsraelNogueira\SkySession\session;

// ✅ v2.0
use IsraelNogueira\SkySession\Session;
```

### 3. Métodos Renomeados

| v1.x | v2.0 | Motivo |
|------|------|--------|
| `getAllSessions()` | `all()` | Simplicidade |
| `finish()` | `destroy()` | Padrão PHP |
| `refreshID()` | `regenerateId()` | Padrão PHP |

### 4. Configuração

**v1.x:** Dependia de `parse_ini_file('.env')`

```php
// .env (v1.x)
SESSION_CRYPT_KEY=...
SESSION_CRYPT_IV=...
SESSION_NAME=...

// PHP (v1.x)
$session = new session();
```

**v2.0:** Configuração via array OU variáveis de ambiente

```php
// Opção 1: Array (recomendado)
$session = new Session([
    'name' => 'my_session',
    'secure' => true,
    'crypt_key' => 'your_key',
    'crypt_iv' => 'your_iv'
]);

// Opção 2: Variáveis de ambiente
putenv('SESSION_CRYPT_KEY=...');
putenv('SESSION_CRYPT_IV=...');
$session = new Session();
```

---

## 📝 Guia Passo a Passo

### Passo 1: Verificar Requisitos

```bash
# Verificar versão do PHP
php -v  # Deve ser >= 8.1

# Verificar extensões
php -m | grep -E '(openssl|json|mbstring)'
```

### Passo 2: Atualizar Composer

```json
{
    "require": {
        "israel-nogueira/sky-session": "^2.0"
    }
}
```

```bash
composer update israel-nogueira/sky-session
```

### Passo 3: Atualizar Imports

```php
// ❌ Antes (v1.x)
use IsraelNogueira\SkySession\session;

// ✅ Depois (v2.0)
use IsraelNogueira\SkySession\Session;
```

### Passo 4: Atualizar Instanciação

```php
// ❌ Antes (v1.x)
$sess = new session('session_name');

// ✅ Depois (v2.0) - Opção 1
$sess = new Session(['name' => 'session_name']);

// ✅ Depois (v2.0) - Opção 2
putenv('SESSION_NAME=session_name');
$sess = new Session();
```

### Passo 5: Atualizar Chamadas de Métodos

```php
// ❌ Antes (v1.x)
$all = $sess->getAllSessions();
$sess->finish();
$sess->refreshID();

// ✅ Depois (v2.0)
$all = $sess->all();
$sess->destroy();
$sess->regenerateId();
```

### Passo 6: Atualizar Configuração de Criptografia

**v1.x:**
```php
// Arquivo .env na raiz
SESSION_CRYPT_KEY=abc123...
SESSION_CRYPT_IV=xyz789...

// Carregado automaticamente por parse_ini_file
```

**v2.0:**
```php
// Opção 1: Via array (mais flexível)
$session = new Session([
    'secure' => true,
    'crypt_key' => getenv('SESSION_CRYPT_KEY'),
    'crypt_iv' => getenv('SESSION_CRYPT_IV')
]);

// Opção 2: Via putenv (compatível)
putenv('SESSION_CRYPT_KEY=' . $yourKey);
putenv('SESSION_CRYPT_IV=' . $yourIv);
$session = new Session(['secure' => true]);
```

---

## 🔐 Novos Recursos Disponíveis

### 1. Método `has()`

```php
// ✅ v2.0
if ($session->has('username')) {
    echo 'Usuário logado!';
}
```

### 2. Validação Rigorosa

```php
// ✅ v2.0 lança exceção se configuração inválida
try {
    $session = new Session([
        'secure' => true,
        'crypt_key' => '', // Inválido!
    ]);
} catch (ConfigurationException $e) {
    echo $e->getMessage();
}
```

### 3. Singleton Pattern

```php
// ✅ v2.0
$instance1 = Session::getInstance();
$instance2 = Session::getInstance();
// $instance1 === $instance2
```

### 4. Type Safety

```php
// ✅ v2.0 - Type hints em todos os métodos
public function set(string $key, mixed $value): void
public function get(string $key): mixed
public function has(string $key): bool
```

---

## ⚠️ Incompatibilidades

### 1. Tags PHP Curtas Removidas

```php
// ❌ v1.x
<?
// código

// ✅ v2.0
<?php
// código
```

### 2. parse_ini_file Removido

```php
// ❌ v1.x - Carregava .env automaticamente
$session = new session();

// ✅ v2.0 - Configuração explícita
$session = new Session(['crypt_key' => '...']);
```

### 3. Propriedade $secury Renomeada

```php
// ❌ v1.x - Acesso interno
$this->secury

// ✅ v2.0
$this->isSecure  // Privada, use configuração
```

---

## 🧪 Testando a Migração

### 1. Criar Script de Teste

```php
<?php

use IsraelNogueira\SkySession\Session;

// Configurar
putenv('SESSION_CRYPT_KEY=' . bin2hex(random_bytes(16)));
putenv('SESSION_CRYPT_IV=' . base64_encode(random_bytes(16)));

// Testar
$session = new Session(['secure' => true]);
$session->set('test', 'value');

if ($session->get('test') === 'value') {
    echo "✅ Migração OK!\n";
} else {
    echo "❌ Erro na migração\n";
}

$session->destroy();
```

### 2. Executar Testes

```bash
php test-migration.php
```

---

## 📊 Checklist de Migração

- [ ] PHP atualizado para >= 8.1
- [ ] Composer atualizado
- [ ] Imports atualizados (`session` → `Session`)
- [ ] Métodos renomeados aplicados
- [ ] Configuração migrada (array ou putenv)
- [ ] Tags PHP atualizadas (`<?` → `<?php`)
- [ ] Chaves de criptografia validadas (32 bytes key, 16 bytes IV)
- [ ] Testes rodando com sucesso
- [ ] Código em produção validado

---

## 🆘 Problemas Comuns

### Erro: "Encryption key must be 32 bytes"

```php
// ❌ Chave muito curta
'crypt_key' => 'abc123'

// ✅ Gerar chave válida
'crypt_key' => bin2hex(random_bytes(16))
```

### Erro: "IV must be 16 bytes when decoded"

```php
// ❌ IV inválido
'crypt_iv' => 'xyz'

// ✅ Gerar IV válido
'crypt_iv' => base64_encode(random_bytes(16))
```

### Erro: "Class 'session' not found"

```php
// ❌ Import errado
use IsraelNogueira\SkySession\session;

// ✅ Import correto
use IsraelNogueira\SkySession\Session;
```

---

## 📞 Suporte

Problemas na migração?

- 📧 Email: israel@feats.com.br
- 🐛 Issues: [GitHub Issues](https://github.com/israel-nogueira/sky-session/issues)
- 📚 Docs: [README.md](README.md)

---

**Boa migração! 🚀**
