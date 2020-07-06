<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use SymfonyBundles\RedisBundle\Redis\ClientInterface;

/**
 * @testdox Message Controller
 */
class MessageQueueControllerTest extends WebTestCase
{
    private ClientInterface $redis;
    private KernelBrowser   $client;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->client = static::createClient();
        
        /** @var ClientInterface $redisClient */
        $redisClient = self::$container->get(ClientInterface::class);
        $this->redis = $redisClient;
        $this->redis->flushdb();
    }
    
    public function tearDown(): void
    {
        parent::tearDown();
        
        $this->redis->flushdb();
    }
    
    /**
     * @testdox  If queue responds with empty value and not found
     */
    public function testGetQueue()
    {
        $this->client->request('GET', '/api/v1/queue');
        
        $response = $this->client->getResponse();
        
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertTrue($response->headers->has('X-Count'));
        $this->assertEquals(0, $response->headers->get('X-Count'));
    }
    
    /**
     * @testdox      If new value is inserted properly
     */
    public function testInserted()
    {
        $this->client->request('POST', '/api/v1/queue', [], [], ['CONTENT_TYPE' => 'application/json'],
                               '{"priority": 1, "value": "Test Value"}');
        
        $response = $this->client->getResponse();
        
        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHasHeader('Location');
        
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertNotEmpty($data['id']);
        $this->assertArrayHasKey('priority', $data);
        $this->assertArrayHasKey('value', $data);
        $this->assertEquals(1, $data['priority']);
        $this->assertEquals('Test Value', $data['value']);
    
        $this->assertResponseHeaderSame('X-Count', 1);
        
        // test queue length
        $this->client->request('GET', '/api/v1/queue');
        
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('X-Count', 1);
    }
    
    /**
     * @testdox      If empty queue returns 404 for pop
     */
    public function testPopReachingEmptyQueue()
    {
        // insert some items
        $this->client->request('POST', '/api/v1/queue', [], [], ['CONTENT_TYPE' => 'application/json'],
                               '{"priority": 1, "value": "Test Value 1"}');
        $this->client->request('POST', '/api/v1/queue', [], [], ['CONTENT_TYPE' => 'application/json'],
                               '{"priority": 2, "value": "Test Value 2"}');
        
        // pop the messages
        $this->client->request('POST', '/api/v1/queue/messages');
        $this->client->request('POST', '/api/v1/queue/messages');
        
        // this should now return 404
        $this->client->request('POST', '/api/v1/queue/messages');
        
        $this->assertResponseStatusCodeSame(404);
    }
    
    /**
     * @testdox      If inserted elements are returned when 'pop'
     */
    public function testPop()
    {
        // insert some items
        $this->client->request('POST', '/api/v1/queue', [], [], ['CONTENT_TYPE' => 'application/json'],
                               '{"priority": 1, "value": "Test Value 1"}');
        $this->client->request('POST', '/api/v1/queue', [], [], ['CONTENT_TYPE' => 'application/json'],
                               '{"priority": 2, "value": "Test Value 2"}');
        $this->client->request('POST', '/api/v1/queue', [], [], ['CONTENT_TYPE' => 'application/json'],
                               '{"priority": 3, "value": "Test Value 3"}');
        $this->client->request('POST', '/api/v1/queue', [], [], ['CONTENT_TYPE' => 'application/json'],
                               '{"priority": 4, "value": "Test Value 4"}');
        
        // pop the last
        $this->client->request('POST', '/api/v1/queue/messages');
        $response = $this->client->getResponse();
        
        $this->assertResponseStatusCodeSame(200);
        
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertNotEmpty($data['id']);
        $this->assertArrayHasKey('priority', $data);
        $this->assertArrayHasKey('value', $data);
        $this->assertEquals(4, $data['priority']);
        $this->assertEquals('Test Value 4', $data['value']);
        
        $this->assertResponseHeaderSame('X-Count', 3);
        
        // test queue length
        $this->client->request('GET', '/api/v1/queue');
        
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('X-Count', 3);
        
        
        // pop another
        $this->client->request('POST', '/api/v1/queue/messages');
        $response = $this->client->getResponse();
        
        $this->assertResponseStatusCodeSame(200);
        
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertNotEmpty($data['id']);
        $this->assertArrayHasKey('priority', $data);
        $this->assertArrayHasKey('value', $data);
        $this->assertEquals(3, $data['priority']);
        $this->assertEquals('Test Value 3', $data['value']);
        
        // test queue length
        $this->client->request('GET', '/api/v1/queue');
        
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('X-Count', 2);
    }
}
