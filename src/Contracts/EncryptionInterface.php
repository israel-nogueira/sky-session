<?php

declare(strict_types=1);

namespace IsraelNogueira\SkySession\Contracts;

/**
 * Interface EncryptionInterface
 * 
 * Contrato para serviços de criptografia
 * 
 * @package IsraelNogueira\SkySession\Contracts
 */
interface EncryptionInterface
{
    /**
     * Criptografa dados
     *
     * @param string $data
     * @return string
     */
    public function encrypt(string $data): string;

    /**
     * Descriptografa dados
     *
     * @param string $data
     * @return string
     */
    public function decrypt(string $data): string;
}
