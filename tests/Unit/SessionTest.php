<?php

declare(strict_types=1);

namespace IsraelNogueira\SkySession\Tests\Unit;

use IsraelNogueira\SkySession\Exceptions\ConfigurationException;
use IsraelNogueira\SkySession\Exceptions\SessionException;
use IsraelNogueira\SkySession\Services\AesEncryption;
use IsraelNogueira\SkySession\Session;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    private Session $session;
    private string $validKey;
    private string $validIv;

    protected function setUp(): void
    {
        // Limpa sessão antes de cada teste
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = [];

        $this->validKey = AesEncryption::generateKey();
        $this->validIv = AesEncryption::generateIv();

        putenv("SESSION_CRYPT_KEY={$this->validKey}");
        putenv("SESSION_CRYPT_IV={$this->validIv}");
        putenv("SESSION_NAME=test_session");
    }

    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = [];
        
        putenv('SESSION_CRYPT_KEY');
        putenv('SESSION_CRYPT_IV');
        putenv('SESSION_NAME');
    }

    public function testConstructorStartsSession(): void
    {
        $this->session = new Session(['secure' => false]);
        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());
    }

    public function testSetAndGetString(): void
    {
        $this->session = new Session(['secure' => false]);
        
        $this->session->set('name', 'John Doe');
        $result = $this->session->get('name');
        
        $this->assertEquals('John Doe', $result);
    }

    public function testSetAndGetArray(): void
    {
        $this->session = new Session(['secure' => false]);
        
        $data = ['email' => 'test@test.com', 'age' => 30];
        $this->session->set('user', $data);
        $result = $this->session->get('user');
        
        $this->assertEquals($data, $result);
    }

    public function testSetAndGetWithEncryption(): void
    {
        $this->session = new Session(['secure' => true]);
        
        $this->session->set('secret', 'sensitive data');
        $result = $this->session->get('secret');
        
        $this->assertEquals('sensitive data', $result);
    }

    public function testGetNonExistentKeyReturnsNull(): void
    {
        $this->session = new Session(['secure' => false]);
        
        $result = $this->session->get('nonexistent');
        
        $this->assertNull($result);
    }

    public function testUnset(): void
    {
        $this->session = new Session(['secure' => false]);
        
        $this->session->set('temp', 'value');
        $this->assertTrue($this->session->has('temp'));
        
        $this->session->unset('temp');
        $this->assertFalse($this->session->has('temp'));
    }

    public function testHas(): void
    {
        $this->session = new Session(['secure' => false]);
        
        $this->assertFalse($this->session->has('test'));
        
        $this->session->set('test', 'value');
        $this->assertTrue($this->session->has('test'));
    }

    public function testAll(): void
    {
        $this->session = new Session(['secure' => false]);
        
        $this->session->set('key1', 'value1');
        $this->session->set('key2', 'value2');
        $this->session->set('key3', ['a' => 1, 'b' => 2]);
        
        $all = $this->session->all();
        
        $this->assertArrayHasKey('key1', $all);
        $this->assertArrayHasKey('key2', $all);
        $this->assertArrayHasKey('key3', $all);
        $this->assertEquals('value1', $all['key1']);
        $this->assertEquals(['a' => 1, 'b' => 2], $all['key3']);
    }

    public function testAllWithEncryption(): void
    {
        $this->session = new Session(['secure' => true]);
        
        $this->session->set('encrypted1', 'secret1');
        $this->session->set('encrypted2', 'secret2');
        
        $all = $this->session->all();
        
        $this->assertArrayHasKey('encrypted1', $all);
        $this->assertArrayHasKey('encrypted2', $all);
        $this->assertEquals('secret1', $all['encrypted1']);
    }

    public function testMagicGet(): void
    {
        $this->session = new Session(['secure' => false]);
        
        $this->session->name = 'Jane';
        $this->assertEquals('Jane', $this->session->name);
    }

    public function testMagicSet(): void
    {
        $this->session = new Session(['secure' => false]);
        
        $this->session->email = 'test@example.com';
        $this->assertEquals('test@example.com', $this->session->get('email'));
    }

    public function testMagicIsset(): void
    {
        $this->session = new Session(['secure' => false]);
        
        $this->session->set('exists', 'yes');
        
        $this->assertTrue(isset($this->session->exists));
        $this->assertFalse(isset($this->session->notexists));
    }

    public function testMagicUnset(): void
    {
        $this->session = new Session(['secure' => false]);
        
        $this->session->set('remove', 'value');
        unset($this->session->remove);
        
        $this->assertFalse($this->session->has('remove'));
    }

    public function testStaticGetSet(): void
    {
        Session::getInstance(['secure' => false]);
        
        Session::username('admin');
        $this->assertEquals('admin', Session::username());
    }

    public function testStaticUnsetWithNull(): void
    {
        Session::getInstance(['secure' => false]);
        
        Session::temp('value');
        $this->assertEquals('value', Session::temp());
        
        Session::temp(null);
        $this->assertNull(Session::temp());
    }

    public function testStaticMultipleArguments(): void
    {
        Session::getInstance(['secure' => false]);
        
        $result = Session::multi('arg1', 'arg2', 'arg3');
        $stored = Session::multi();
        
        $this->assertEquals(['arg1', 'arg2', 'arg3'], $stored);
    }

    public function testStaticMethodsWithPrefix(): void
    {

		$session = Session::getInstance(['secure' => false]);
		$session->__set('prefixed', 'test');     // ✅ CORRETO
		$result = $session->__get('prefixed');   // ✅ CORRETO
        $this->assertEquals('test', $result);
    }

    public function testRegenerateId(): void
    {
        $this->session = new Session(['secure' => false]);
        
        $oldId = session_id();
        $result = $this->session->regenerateId();
        $newId = session_id();
        
        $this->assertTrue($result);
        $this->assertNotEquals($oldId, $newId);
    }

    public function testWriteClose(): void
    {
        $this->session = new Session(['secure' => false]);
        
        $this->session->set('test', 'value');
        $result = $this->session->writeClose();
        
        $this->assertTrue($result);
    }

    public function testEmptyKeyThrowsException(): void
    {
        $this->session = new Session(['secure' => false]);
        
        $this->expectException(SessionException::class);
        $this->expectExceptionMessage('Session key cannot be empty');
        
        $this->session->set('', 'value');
    }

    public function testMissingCryptKeyThrowsException(): void
    {
        putenv('SESSION_CRYPT_KEY');
        
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('SESSION_CRYPT_KEY is required');
        
        new Session(['secure' => true, 'crypt_key' => '', 'crypt_iv' => $this->validIv]);
    }

    public function testMissingCryptIvThrowsException(): void
    {
        putenv('SESSION_CRYPT_IV');
        
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('SESSION_CRYPT_IV is required');
        
        new Session(['secure' => true, 'crypt_key' => $this->validKey, 'crypt_iv' => '']);
    }

    public function testConfigurationMerge(): void
    {
        $config = [
            'name' => 'custom_session',
            'lifetime' => 7200,
            'secure' => false
        ];
        
        $this->session = new Session($config);
        
        $this->assertEquals('custom_session', session_name());
    }

    public function testSingletonPattern(): void
    {
        $instance1 = Session::getInstance(['secure' => false]);
        $instance2 = Session::getInstance();
        
        $this->assertSame($instance1, $instance2);
    }
}
