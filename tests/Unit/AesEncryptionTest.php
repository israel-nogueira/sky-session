<?php

declare(strict_types=1);

namespace IsraelNogueira\SkySession\Tests\Unit;

use IsraelNogueira\SkySession\Exceptions\EncryptionException;
use IsraelNogueira\SkySession\Services\AesEncryption;
use PHPUnit\Framework\TestCase;

class AesEncryptionTest extends TestCase
{
    private AesEncryption $encryption;
    private string $validKey;
    private string $validIv;

    protected function setUp(): void
    {
        $this->validKey = AesEncryption::generateKey();
        $this->validIv = AesEncryption::generateIv();
        $this->encryption = new AesEncryption($this->validKey, $this->validIv);
    }

    public function testEncryptDecrypt(): void
    {
        $data = 'test data';
        $encrypted = $this->encryption->encrypt($data);
        $decrypted = $this->encryption->decrypt($encrypted);

        $this->assertNotEquals($data, $encrypted);
        $this->assertEquals($data, $decrypted);
    }

    public function testEncryptComplexData(): void
    {
        $data = 'Special chars: áéíóú ñ @#$%^&*()';
        $encrypted = $this->encryption->encrypt($data);
        $decrypted = $this->encryption->decrypt($encrypted);

        $this->assertEquals($data, $decrypted);
    }

    public function testEncryptEmptyDataThrowsException(): void
    {
        $this->expectException(EncryptionException::class);
        $this->expectExceptionMessage('Data cannot be empty');
        
        $this->encryption->encrypt('');
    }

    public function testDecryptEmptyDataThrowsException(): void
    {
        $this->expectException(EncryptionException::class);
        $this->expectExceptionMessage('Data cannot be empty');
        
        $this->encryption->decrypt('');
    }

    public function testInvalidKeyLengthThrowsException(): void
    {
        $this->expectException(EncryptionException::class);
        $this->expectExceptionMessage('Encryption key must be 32 bytes');
        
        new AesEncryption('short_key', $this->validIv);
    }

    public function testEmptyKeyThrowsException(): void
    {
        $this->expectException(EncryptionException::class);
        $this->expectExceptionMessage('Encryption key cannot be empty');
        
        new AesEncryption('', $this->validIv);
    }

    public function testInvalidIvThrowsException(): void
    {
        $this->expectException(EncryptionException::class);
        $this->expectExceptionMessage('IV must be a valid base64 string');
        
        new AesEncryption($this->validKey, 'invalid_iv!!!');
    }

    public function testEmptyIvThrowsException(): void
    {
        $this->expectException(EncryptionException::class);
        $this->expectExceptionMessage('IV cannot be empty');
        
        new AesEncryption($this->validKey, '');
    }

    public function testGenerateKeyReturnsValidKey(): void
    {
        $key = AesEncryption::generateKey();
        
        $this->assertEquals(32, mb_strlen($key, '8bit'));
        $this->assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $key);
    }

    public function testGenerateIvReturnsValidIv(): void
    {
        $iv = AesEncryption::generateIv();
        $decoded = base64_decode($iv, true);
        
        $this->assertNotFalse($decoded);
        $this->assertEquals(16, mb_strlen($decoded, '8bit'));
    }

    public function testDecryptInvalidDataThrowsException(): void
    {
        $this->expectException(EncryptionException::class);
        $this->expectExceptionMessage('Decryption failed');
        
        $this->encryption->decrypt('invalid_encrypted_data');
    }

    public function testDifferentInstancesProduceDifferentEncryption(): void
    {
        $data = 'test';
        
        $encryption1 = new AesEncryption($this->validKey, $this->validIv);
        $encrypted1 = $encryption1->encrypt($data);
        
        $newKey = AesEncryption::generateKey();
        $newIv = AesEncryption::generateIv();
        $encryption2 = new AesEncryption($newKey, $newIv);
        $encrypted2 = $encryption2->encrypt($data);
        
        $this->assertNotEquals($encrypted1, $encrypted2);
    }

    public function testSameInstanceProducesSameEncryption(): void
    {
        $data = 'test';
        
        $encrypted1 = $this->encryption->encrypt($data);
        $encrypted2 = $this->encryption->encrypt($data);
        
        $this->assertEquals($encrypted1, $encrypted2);
    }
}
