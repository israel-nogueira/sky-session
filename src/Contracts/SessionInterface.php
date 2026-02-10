<?php

declare(strict_types=1);

namespace IsraelNogueira\SkySession\Contracts;

/**
 * Interface SessionInterface
 * 
 * Contrato para gerenciamento de sessões seguras
 * 
 * @package IsraelNogueira\SkySession\Contracts
 */
interface SessionInterface
{
    /**
     * Define um valor na sessão
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, mixed $value): void;

    /**
     * Recupera um valor da sessão
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed;

    /**
     * Remove um valor da sessão
     *
     * @param string $key
     * @return void
     */
    public function unset(string $key): void;

    /**
     * Verifica se uma chave existe na sessão
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Retorna todas as sessões
     *
     * @return array
     */
    public function all(): array;

    /**
     * Regenera o ID da sessão
     *
     * @return bool
     */
    public function regenerateId(): bool;

    /**
     * Destrói a sessão
     *
     * @return void
     */
    public function destroy(): void;
}
