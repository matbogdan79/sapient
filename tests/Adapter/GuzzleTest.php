<?php
namespace ParagonIE\Sapient\UnitTests\Adapter;

use GuzzleHttp\Psr7\{
    Request,
    Response
};
use ParagonIE\Sapient\Adapter\Guzzle;
use ParagonIE\Sapient\CryptographyKeys\{
    SealingPublicKey,
    SealingSecretKey,
    SharedAuthenticationKey,
    SharedEncryptionKey,
    SigningPublicKey,
    SigningSecretKey
};
use PHPUnit\Framework\TestCase;

class GuzzleTest extends TestCase
{
    /** @var Guzzle */
    protected $adapter;

    /** @var SealingSecretKey */
    protected $clientSealSecret;

    /** @var SealingPublicKey */
    protected $clientSealPublic;

    /** @var SealingSecretKey */
    protected $serverSealSecret;

    /** @var SealingPublicKey */
    protected $serverSealPublic;

    /** @var SigningSecretKey */
    protected $clientSignSecret;

    /** @var SigningPublicKey */
    protected $clientSignPublic;

    /** @var SigningSecretKey */
    protected $serverSignSecret;

    /** @var SigningPublicKey */
    protected $serverSignPublic;
    /** @var SharedAuthenticationKey */
    protected $sharedAuthenticationKey;

    /** @var SharedEncryptionKey */
    protected $sharedEncryptionKey;

    /**
     * Populate the methods.
     */
    public function setup()
    {
        $this->adapter = new Guzzle();
        $this->clientSignSecret = SigningSecretKey::generate();
        $this->clientSignPublic = $this->clientSignSecret->getPublickey();

        $this->serverSignSecret = SigningSecretKey::generate();
        $this->serverSignPublic = $this->serverSignSecret->getPublickey();

        $this->clientSealSecret = SealingSecretKey::generate();
        $this->clientSealPublic = $this->clientSealSecret->getPublickey();

        $this->serverSealSecret = SealingSecretKey::generate();
        $this->serverSealPublic = $this->serverSealSecret->getPublickey();

        $this->sharedEncryptionKey = SharedEncryptionKey::generate();
        $this->sharedAuthenticationKey = SharedAuthenticationKey::generate();
    }

    /**
     * @covers Guzzle
     */
    public function testReturnTypeForCreateRequest()
    {
        $this->assertInstanceOf(
            Request::class,
            $this->adapter->createSignedJsonRequest('POST', '/', [], $this->serverSignSecret)
        );
        $this->assertInstanceOf(
            Request::class,
            $this->adapter->createSealedJsonRequest('POST', '/', [], $this->serverSealPublic)
        );
        $this->assertInstanceOf(
            Request::class,
            $this->adapter->createSymmetricEncryptedJsonRequest('POST', '/', [], $this->sharedEncryptionKey)
        );
        $this->assertInstanceOf(
            Request::class,
            $this->adapter->createSymmetricAuthenticatedJsonRequest('POST', '/', [], $this->sharedAuthenticationKey)
        );
    }


    /**
     * @covers Guzzle
     */
    public function testReturnTypeForCreateResponse()
    {
        $this->assertInstanceOf(
            Response::class,
            $this->adapter->createSignedJsonResponse(200, [], $this->serverSignSecret)
        );
        $this->assertInstanceOf(
            Response::class,
            $this->adapter->createSealedJsonResponse(200, [], $this->serverSealPublic)
        );
        $this->assertInstanceOf(
            Response::class,
            $this->adapter->createSymmetricEncryptedJsonResponse(200, [], $this->sharedEncryptionKey)
        );
        $this->assertInstanceOf(
            Response::class,
            $this->adapter->createSymmetricAuthenticatedJsonResponse(200, [], $this->sharedAuthenticationKey)
        );
    }
}
