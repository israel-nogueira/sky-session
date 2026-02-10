<?php

declare(strict_types=1);

namespace IsraelNogueira\SkySession;

use IsraelNogueira\SkySession\Contracts\EncryptionInterface;
use IsraelNogueira\SkySession\Contracts\SessionInterface;
use IsraelNogueira\SkySession\Exceptions\ConfigurationException;
use IsraelNogueira\SkySession\Exceptions\SessionException;
use IsraelNogueira\SkySession\Services\AesEncryption;

/**
 * Session
 * 
 * Gerenciador de sessões seguro com criptografia
 * 
 * @package IsraelNogueira\SkySession
 * @version 2.0.0
 * @author Israel Nogueira
 * @license MIT
 */
class Session implements SessionInterface
{
    private bool $isSecure;
    private ?EncryptionInterface $encryption;
    private array $config;
    private static ?self $instance = null;

    /**
     * @param array $config Configurações da sessão
     * @throws ConfigurationException
     * @throws SessionException
     */
    public function __construct(array $config = [])
    {
        $this->config = $this->mergeConfig($config);
        $this->isSecure = $this->config['secure'];

        if ($this->isSecure) {
            $this->encryption = $this->createEncryption();
        } else {
            $this->encryption = null;
        }

        $this->startSession();
    }

    /**
     * Cria instância singleton
     *
     * @param array $config
     * @return self
     */
    public static function getInstance(array $config = []): self
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $key, mixed $value): void
    {
        $this->validateKey($key);

        $serialized = $this->serialize($value);
        
        if ($this->isSecure && $this->encryption !== null) {
            $encryptedKey = $this->encryption->encrypt($key);
            $encryptedValue = $this->encryption->encrypt($serialized);
            $_SESSION[$encryptedKey] = $encryptedValue;
        } else {
            $_SESSION[$key] = $serialized;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $key): mixed
    {
        $this->validateKey($key);

        if ($this->isSecure && $this->encryption !== null) {
            $encryptedKey = $this->encryption->encrypt($key);
            
            if (!isset($_SESSION[$encryptedKey])) {
                return null;
            }

            $decryptedValue = $this->encryption->decrypt($_SESSION[$encryptedKey]);
            return $this->unserialize($decryptedValue);
        }

        if (!isset($_SESSION[$key])) {
            return null;
        }

        return $this->unserialize($_SESSION[$key]);
    }

    /**
     * {@inheritDoc}
     */
    public function unset(string $key): void
    {
        $this->validateKey($key);

        if ($this->isSecure && $this->encryption !== null) {
            $encryptedKey = $this->encryption->encrypt($key);
            unset($_SESSION[$encryptedKey]);
        } else {
            unset($_SESSION[$key]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $key): bool
    {
        $this->validateKey($key);

        if ($this->isSecure && $this->encryption !== null) {
            $encryptedKey = $this->encryption->encrypt($key);
            return isset($_SESSION[$encryptedKey]);
        }

        return isset($_SESSION[$key]);
    }

    /**
     * {@inheritDoc}
     */
    public function all(): array
    {
        if ($this->isSecure && $this->encryption !== null) {
            $result = [];
            
            foreach ($_SESSION as $encryptedKey => $encryptedValue) {
                try {
                    $key = $this->encryption->decrypt($encryptedKey);
                    $value = $this->encryption->decrypt($encryptedValue);
                    $result[$key] = $this->unserialize($value);
                } catch (\Exception $e) {
                    // Skip invalid entries
                    continue;
                }
            }
            
            return $result;
        }

        $result = [];
        foreach ($_SESSION as $key => $value) {
            $result[$key] = $this->unserialize($value);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function regenerateId(): bool
    {
        return session_regenerate_id(true);
    }

    /**
     * {@inheritDoc}
     */
    public function destroy(): void
    {
        $_SESSION = [];
        
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        $this->clearAllCookies();
    }

    /**
     * Fecha a escrita da sessão
     *
     * @return bool
     */
    public function writeClose(): bool
    {
        return session_write_close();
    }

    /**
     * Métodos mágicos para acesso dinâmico
     */
    public function __get(string $name): mixed
    {
        return $this->get($name);
    }

    public function __set(string $name, mixed $value): void
    {
        $this->set($name, $value);
    }

    public function __isset(string $name): bool
    {
        return $this->has($name);
    }

    public function __unset(string $name): void
    {
        $this->unset($name);
    }

    /**
     * Métodos estáticos dinâmicos
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        $instance = self::getInstance();

        // Métodos com prefixo __ chamam métodos reais
        if (str_starts_with($name, '__')) {
            $method = substr($name, 2);
            return $instance->$method(...$arguments);
        }

        // Comportamento dinâmico de get/set
        $count = count($arguments);

        if ($count === 0) {
            return $instance->get($name);
        }

        if ($count === 1) {
            if ($arguments[0] === null) {
                $instance->unset($name);
                return null;
            }
            
            $instance->set($name, $arguments[0]);
            return $arguments[0];
        }

        $instance->set($name, $arguments);
        return $arguments;
    }

    /**
     * Inicia a sessão
     *
     * @throws SessionException
     */
    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        if (headers_sent($file, $line)) {
            throw new SessionException(
                "Headers already sent in {$file} on line {$line}"
            );
        }

        $this->configureSessionParams();

        if (!session_start()) {
            throw new SessionException('Failed to start session');
        }
    }

    /**
     * Configura parâmetros da sessão
     */
    private function configureSessionParams(): void
    {
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', $this->config['cookie_samesite']);
        ini_set('session.gc_maxlifetime', (string)$this->config['lifetime']);
        ini_set('session.sid_length', '128');
        ini_set('session.auto_start', '0');
        ini_set('url_rewriter.tags', '');

        if (!empty($this->config['save_path'])) {
            session_save_path($this->config['save_path']);
        }

        session_name($this->config['name']);

        session_set_cookie_params([
            'lifetime' => $this->config['lifetime'],
            'path' => $this->config['cookie_path'],
            'domain' => $this->config['cookie_domain'],
            'secure' => $this->config['cookie_secure'],
            'httponly' => true,
            'samesite' => $this->config['cookie_samesite']
        ]);
    }

    /**
     * Mescla configurações
     */
    private function mergeConfig(array $config): array
    {
        $defaults = [
            'name' => $this->getEnv('SESSION_NAME', 'sky_session'),
            'lifetime' => (int)$this->getEnv('SESSION_LIFETIME', '3600'),
            'secure' => (bool)$this->getEnv('SESSION_SECURE', 'true'),
            'cookie_path' => $this->getEnv('SESSION_COOKIE_PATH', '/'),
            'cookie_domain' => $this->getEnv('SESSION_COOKIE_DOMAIN', ''),
            'cookie_secure' => (bool)$this->getEnv('SESSION_COOKIE_SECURE', 'true'),
            'cookie_samesite' => $this->getEnv('SESSION_COOKIE_SAMESITE', 'Lax'),
            'save_path' => $this->getEnv('SESSION_SAVE_PATH', ''),
            'crypt_key' => $this->getEnv('SESSION_CRYPT_KEY', ''),
            'crypt_iv' => $this->getEnv('SESSION_CRYPT_IV', ''),
        ];

        return array_merge($defaults, $config);
    }

    /**
     * Cria instância de criptografia
     *
     * @throws ConfigurationException
     */
    private function createEncryption(): EncryptionInterface
    {
        if (empty($this->config['crypt_key'])) {
            throw new ConfigurationException('SESSION_CRYPT_KEY is required for secure sessions');
        }

        if (empty($this->config['crypt_iv'])) {
            throw new ConfigurationException('SESSION_CRYPT_IV is required for secure sessions');
        }

        return new AesEncryption(
            $this->config['crypt_key'],
            $this->config['crypt_iv']
        );
    }

    /**
     * Serializa valor
     */
    private function serialize(mixed $value): string
    {
        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_THROW_ON_ERROR);
        }

        return (string)$value;
    }

    /**
     * Desserializa valor
     */
    private function unserialize(string $value): mixed
    {
        if ($this->isJson($value)) {
            return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        }

        return $value;
    }

    /**
     * Verifica se string é JSON válido
     */
    private function isJson(string $string): bool
    {
        if (empty($string)) {
            return false;
        }

        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Valida chave
     *
     * @throws SessionException
     */
    private function validateKey(string $key): void
    {
        if (empty($key)) {
            throw new SessionException('Session key cannot be empty');
        }
    }

    /**
     * Limpa todos os cookies
     */
    private function clearAllCookies(): void
    {
        if (empty($_COOKIE)) {
            return;
        }

        foreach ($_COOKIE as $name => $value) {
            setcookie($name, '', time() - 3600, '/');
        }
    }

    /**
     * Obtém variável de ambiente
     */
    private function getEnv(string $key, string $default = ''): string
    {
        $value = getenv($key);
        return $value !== false ? $value : $default;
    }
}
