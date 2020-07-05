<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @testdox Message Controller
 */
class MessageQueueControllerTest extends WebTestCase
{
    /**
     * @testdox      If queue responds with empty value and not found
     * @return string|null
     */
    public function testGetQueue(): string
    {
        $client = static::createClient();
        $client->request('GET', '/api/v1/queue');
        
        $response = $client->getResponse();
        
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertTrue($response->headers->has('X-Count'));
        $this->assertEquals(0, $response->headers->get('X-Count'));
    }
    
    /**
     * @testdox      Set options method returns a correct battle object
     * @return string|null
     */
    public function testInserted(): string
    {
        $client = static::createClient();
        $client->request('POST', '/api/v1/queue', [], [], ['CONTENT_TYPE' => 'application/json'],
                         '{"priority": 1, "value": "Test Value"}');
        
        $response = $client->getResponse();
        
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($response->headers->has('Location'));
        
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertNotEmpty($data['id']);
        $this->assertArrayHasKey('priority', $data);
        $this->assertArrayHasKey('value', $data);
        $this->assertEquals(1, $data['priority']);
        $this->assertEquals('Test Value', $data['value']);
        
        return $response->headers->get('Location');
    }
    
    /**
     * @testdox      Set options method returns a correct battle object
     * @return string|null
     */
    public function testIfPopWorks(): string
    {
        $client = static::createClient();
        
        // insert some items
        $client->request('POST', '/api/v1/queue', [], [], ['CONTENT_TYPE' => 'application/json'],
                         '{"priority": 1, "value": "Test Value 1"}');
        $client->request('POST', '/api/v1/queue', [], [], ['CONTENT_TYPE' => 'application/json'],
                         '{"priority": 2, "value": "Test Value 2"}');
        $client->request('POST', '/api/v1/queue', [], [], ['CONTENT_TYPE' => 'application/json'],
                         '{"priority": 3, "value": "Test Value 3"}');
        $client->request('POST', '/api/v1/queue', [], [], ['CONTENT_TYPE' => 'application/json'],
                         '{"priority": 4, "value": "Test Value 4"}');
        
        // pop the last
        $client->request('POST', '/api/v1/queue/messages');
        $response = $client->getResponse();
        
        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertNotEmpty($data['id']);
        $this->assertArrayHasKey('priority', $data);
        $this->assertArrayHasKey('value', $data);
        $this->assertEquals(4, $data['priority']);
        $this->assertEquals('Test Value 4', $data['value']);
    
    
        $client->request('POST', '/api/v1/queue/messages');
        $response = $client->getResponse();
    
        $this->assertEquals(200, $response->getStatusCode());
    
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertNotEmpty($data['id']);
        $this->assertArrayHasKey('priority', $data);
        $this->assertArrayHasKey('value', $data);
        $this->assertEquals(4, $data['priority']);
        $this->assertEquals('Test Value 4', $data['value']);
        
        
        return $response->headers->get('Location');
    }
}
