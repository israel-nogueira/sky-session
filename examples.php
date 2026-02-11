<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use IsraelNogueira\SkySession\Session;

// ========================================
// CONFIGURAÇÃO DE AMBIENTE (DEVE VIR ANTES DE TUDO)
// ========================================
putenv('SESSION_CRYPT_KEY=' . bin2hex(random_bytes(16)));
putenv('SESSION_CRYPT_IV=' . base64_encode(random_bytes(16)));

// ========================================
// EXEMPLO 1: Uso Básico
// ========================================

echo "=== EXEMPLO 1: Uso Básico ===\n\n";

$session = new Session(['secure' => false]);

// Setando valores
$session->set('username', 'john_doe');
$session->set('email', 'john@example.com');

// Recuperando valores
echo "Username: " . $session->get('username') . "\n";
echo "Email: " . $session->get('email') . "\n\n";


// ========================================
// EXEMPLO 2: Magic Methods
// ========================================

echo "=== EXEMPLO 2: Magic Methods ===\n\n";

// Set usando propriedade
$session->name = 'Jane Doe';
$session->age = 25;

// Get usando propriedade
echo "Name: {$session->name}\n";
echo "Age: {$session->age}\n\n";

// Isset
if (isset($session->name)) {
    echo "✓ Name exists\n\n";
}


// ========================================
// EXEMPLO 3: Arrays e Objetos
// ========================================

echo "=== EXEMPLO 3: Arrays e Objetos ===\n\n";

// Array complexo
$session->cart = [
    'items' => [
        ['id' => 1, 'name' => 'Product A', 'price' => 10.00],
        ['id' => 2, 'name' => 'Product B', 'price' => 20.00]
    ],
    'total' => 30.00
];

// Recuperando
$cart = $session->cart;
echo "Cart Total: $" . $cart['total'] . "\n";
echo "First Item: " . $cart['items'][0]['name'] . "\n\n";


// ========================================
// EXEMPLO 4: Métodos Estáticos
// ========================================

echo "=== EXEMPLO 4: Métodos Estáticos ===\n\n";

// Se0t0
Session::token('abc123xyz');
Session::preferences(['theme' => 'dark', 'lang' => 'pt-BR']);

// Get
echo "Token: " . Session::token() . "\n";
print_r(Session::preferences());
echo "\n";


// ========================================
// EXEMPLO 5: Modo Seguro (Criptografado)
// ========================================

echo "=== EXEMPLO 5: Modo Seguro ===\n\n";

$secureSession = new Session(['secure' => true]);

// Dados sensíveis criptografados
$secureSession->set('password_hash', 'super_secret_hash');
$secureSession->set('credit_card', '1234-5678-9012-3456');

echo "✓ Dados criptografados armazenados com segurança\n";
echo "Password Hash: " . $secureSession->get('password_hash') . "\n\n";


// ========================================
// EXEMPLO 6: Verificações e Remoções
// ========================================

echo "=== EXEMPLO 6: Verificações e Remoções ===\n\n";

$session->temporary = 'temp value';

// Verificar
if ($session->has('temporary')) {
    echo "✓ Temporary exists\n";
}

// Remover
$session->unset('temporary');

// Verificar novamente
if (!$session->has('temporary')) {
    echo "✓ Temporary removed\n\n";
}


// ========================================
// EXEMPLO 7: Obter Todas as Variáveis
// ========================================

echo "=== EXEMPLO 7: Todas as Variáveis ===\n\n";

$all = $session->all();
echo "Total de variáveis: " . count($all) . "\n";
print_r($all);
echo "\n";


// ========================================
// EXEMPLO 8: Regenerar ID
// ========================================

echo "=== EXEMPLO 8: Regenerar ID ===\n\n";

$oldId = session_id();
$session->regenerateId();
$newId = session_id();

echo "Old Session ID: {$oldId}\n";
echo "New Session ID: {$newId}\n";
echo "✓ ID regenerado com sucesso\n\n";


// ========================================
// EXEMPLO 9: Simulação de Login
// ========================================

echo "=== EXEMPLO 9: Simulação de Login ===\n\n";

class LoginExample
{
    private Session $session;

    public function __construct()
    {
        $this->session = Session::getInstance();
    }

    public function login(string $username, string $password): bool
    {
        // Simulação de validação
        if ($username === 'admin' && $password === 'password123') {
            // Regenerar ID por segurança
            $this->session->regenerateId();

            // Armazenar dados do usuário
            $this->session->set('authenticated', true);
            $this->session->set('user', [
                'username' => $username,
                'role' => 'admin',
                'last_login' => date('Y-m-d H:i:s')
            ]);

            return true;
        }

        return false;
    }

    public function isAuthenticated(): bool
    {
        return $this->session->get('authenticated') === true;
    }

    public function getUser(): ?array
    {
        return $this->session->get('user');
    }

    public function logout(): void
    {
        $this->session->destroy();
    }
}

$login = new LoginExample();

// Fazer login
if ($login->login('admin', 'password123')) {
    echo "✓ Login successful\n";

    if ($login->isAuthenticated()) {
        $user = $login->getUser();
        echo "Welcome, {$user['username']}!\n";
        echo "Role: {$user['role']}\n";
        echo "Last Login: {$user['last_login']}\n\n";
    }
}


// ========================================
// EXEMPLO 10: Limpeza
// ========================================

echo "=== EXEMPLO 10: Limpeza Final ===\n\n";

echo "✓ Destruindo sessão...\n";
$session->destroy();
echo "✓ Sessão destruída com sucesso!\n";