<?php

declare(strict_types=1);

namespace IsraelNogueira\SkySession\Services;

use IsraelNogueira\SkySession\Contracts\EncryptionInterface;
use IsraelNogueira\SkySession\Exceptions\EncryptionException;

/**
 * AesEncryption
 * 
 * Implementação de criptografia AES-256-CBC
 * 
 * @package IsraelNogueira\SkySession\Services
 */
class AesEncryption implements EncryptionInterface
{
    private const CIPHER = 'aes-256-cbc';

    private string $key;
    private string $iv;

    /**
     * @param string $key Chave de criptografia (32 bytes)
     * @param string $iv Vetor de inicialização (16 bytes base64)
     * @throws EncryptionException
     */
    public function __construct(string $key, string $iv)
    {
        $this->validateKey($key);
        $this->validateIv($iv);

        $this->key = $key;
        $this->iv = $iv;
    }

    /**
     * {@inheritDoc}
     */
    public function encrypt(string $data): string
    {
        if (empty($data)) {
            throw new EncryptionException('Data cannot be empty');
        }

        $encrypted = openssl_encrypt(
            $data,
            self::CIPHER,
            $this->key,
            0,
            base64_decode($this->iv)
        );

        if ($encrypted === false) {
            throw new EncryptionException('Encryption failed');
        }

        return $encrypted;
    }

    /**
     * {@inheritDoc}
     */
    public function decrypt(string $data): string
    {
        if (empty($data)) {
            throw new EncryptionException('Data cannot be empty');
        }

        $decrypted = openssl_decrypt(
            $data,
            self::CIPHER,
            $this->key,
            0,
            base64_decode($this->iv)
        );

        if ($decrypted === false) {
            throw new EncryptionException('Decryption failed');
        }

        return $decrypted;
    }

    /**
     * Valida a chave de criptografia
     *
     * @param string $key
     * @throws EncryptionException
     */
    private function validateKey(string $key): void
    {
        if (empty($key)) {
            throw new EncryptionException('Encryption key cannot be empty');
        }

        if (mb_strlen($key, '8bit') !== 32) {
            throw new EncryptionException('Encryption key must be 32 bytes');
        }
    }

    /**
     * Valida o vetor de inicialização
     *
     * @param string $iv
     * @throws EncryptionException
     */
    private function validateIv(string $iv): void
    {
        if (empty($iv)) {
            throw new EncryptionException('IV cannot be empty');
        }

        $decoded = base64_decode($iv, true);
        
        if ($decoded === false) {
            throw new EncryptionException('IV must be a valid base64 string');
        }

        if (mb_strlen($decoded, '8bit') !== 16) {
            throw new EncryptionException('IV must be 16 bytes when decoded');
        }
    }

    /**
     * Gera uma chave aleatória segura
     *
     * @return string
     */
    public static function generateKey(): string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * Gera um IV aleatório seguro
     *
     * @return string
     */
    public static function generateIv(): string
    {
        return base64_encode(random_bytes(16));
    }
}
