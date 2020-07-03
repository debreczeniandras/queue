<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @testdox Battle Controller
 */
class BattleControllerTest extends WebTestCase
{
    /**
     * @testdox      Set options method returns a correct battle object
     * @return string|null
     */
    public function testSetOptions(): string
    {
        $client = static::createClient();
        $client->request('POST', '/api/v1/battles', [], [], ['CONTENT_TYPE' => 'application/json'],
                         '{"width": 8, "height": 8}');
        
        $response = $client->getResponse();
        
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($response->headers->has('Location'));
        
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertNotEmpty($data['id']);
        $this->assertArrayHasKey('options', $data);
        $this->assertEquals(8, $data['options']['width']);
        $this->assertEquals(8, $data['options']['height']);
        $this->assertArrayHasKey('state', $data);
        $this->assertEquals('ready', $data['state']);
        
        return $response->headers->get('Location');
    }
    
    /**
     * @depends testSetOptions
     * @testdox Battle object is returned.
     *
     * @param $url
     *
     * @return string
     */
    public function testGetBattle($url)
    {
        $client = static::createClient();
        $client->request('GET', $url);
        
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertNotEmpty($data['id']);
        $this->assertArrayHasKey('options', $data);
        
        return $url;
    }
    
    /**
     * @dataProvider provideTestPlayerConfig
     * @depends      testGetBattle
     * @testdox      Setting grid layout returns correct responses
     *
     * @param array $payload
     * @param int   $expStatus
     * @param bool  $expLocation
     * @param       $url
     */
    public function testSetUpPlayer(array $payload, int $expStatus, bool $expLocation, string $expErrorMessage, $url)
    {
        $client = static::createClient();
        $client->request('PUT', $url, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($payload));
        
        $response = $client->getResponse();
        $this->assertEquals($expStatus, $response->getStatusCode());
        $this->assertEquals($expLocation, $response->headers->has('Location'));
        $this->assertStringContainsString($expErrorMessage, $response->getContent());
    }
    
    /**
     * @depends testGetBattle
     * @testdox Battle is deleted.
     *
     * @param $url
     */
    public function testDeleteBattle($url)
    {
        $client = static::createClient();
        $client->request('DELETE', $url);
        
        $response = $client->getResponse();
        $this->assertEquals(204, $response->getStatusCode());
    }
    
    public function provideTestPlayerConfig()
    {
        return [
            'one ship is missing - returns 400' => [
                [
                    [
                        'id' => 'A',
                        'type' => 0,
                        'grid' => [
                            'ships' => [
                                ['id' => 'carrier', 'start' => ['x' => 2, 'y' => 'A'], 'end' => ['x' => 2, 'y' => 'E']],
                                ['id' => 'cruiser', 'start' => ['x' => 4, 'y' => 'C'], 'end' => ['x' => 6, 'y' => 'C']],
                                ['id' => 'submarine', 'start' => ['x' => 4, 'y' => 'G'], 'end' => ['x' => 6, 'y' => 'G']],
                                ['id' => 'destroyer', 'start' => ['x' => 8, 'y' => 'E'], 'end' => ['x' => 8, 'y' => 'F']],
                            ],
                        ],
                    ],
                    [
                        'id' => 'B',
                        'type' => 1,
                    ],
                ],
                400,
                false,
                'This collection should contain exactly',
            ],
            'one ship is added twice - returns 400' => [
                [
                    [
                        'id' => 'A',
                        'type' => 0,
                        'grid' => [
                            'ships' => [
                                ['id' => 'carrier', 'start' => ['x' => 2, 'y' => 'A'], 'end' => ['x' => 2, 'y' => 'E']],
                                ['id' => 'carrier', 'start' => ['x' => 2, 'y' => 'A'], 'end' => ['x' => 2, 'y' => 'E']],
                                ['id' => 'cruiser', 'start' => ['x' => 4, 'y' => 'C'], 'end' => ['x' => 6, 'y' => 'C']],
                                ['id' => 'submarine', 'start' => ['x' => 4, 'y' => 'G'], 'end' => ['x' => 6, 'y' => 'G']],
                                ['id' => 'destroyer', 'start' => ['x' => 8, 'y' => 'E'], 'end' => ['x' => 8, 'y' => 'F']],
                            ],
                        ],
                    ],
                    [
                        'id' => 'B',
                        'type' => 1,
                    ],
                ],
                400,
                false,
                'This ship is already added to the board',
            ],
            'ship with id carrier has wrong size (should be 5 instead of 6) - returns 400' => [
                [
                    [
                        'id' => 'A',
                        'type' => 0,
                        'grid' => [
                            'ships' => [
                                ['id' => 'carrier', 'start' => ['x' => 2, 'y' => 'A'], 'end' => ['x' => 2, 'y' => 'F']],
                                ['id' => 'battleship', 'start' => ['x' => 3, 'y' => 'D'], 'end' => ['x' => 6, 'y' => 'D']],
                                ['id' => 'cruiser', 'start' => ['x' => 4, 'y' => 'C'], 'end' => ['x' => 6, 'y' => 'C']],
                                ['id' => 'submarine', 'start' => ['x' => 4, 'y' => 'G'], 'end' => ['x' => 6, 'y' => 'G']],
                                ['id' => 'destroyer', 'start' => ['x' => 8, 'y' => 'E'], 'end' => ['x' => 8, 'y' => 'F']],
                            ],
                        ],
                    ],
                    [
                        'id' => 'B',
                        'type' => 1,
                    ],
                ],
                400,
                false,
                'ship is supposed to be 5 long',
            ],
            'a ship is neither vertical nor horizontal - returns 400' => [
                [
                    [
                        'id' => 'A',
                        'type' => 0,
                        'grid' => [
                            'ships' => [
                                ['id' => 'carrier', 'start' => ['x' => 2, 'y' => 'A'], 'end' => ['x' => 3, 'y' => 'C']],
                                ['id' => 'battleship', 'start' => ['x' => 3, 'y' => 'D'], 'end' => ['x' => 6, 'y' => 'D']],
                                ['id' => 'cruiser', 'start' => ['x' => 4, 'y' => 'C'], 'end' => ['x' => 6, 'y' => 'C']],
                                ['id' => 'submarine', 'start' => ['x' => 4, 'y' => 'G'], 'end' => ['x' => 6, 'y' => 'G']],
                                ['id' => 'destroyer', 'start' => ['x' => 8, 'y' => 'E'], 'end' => ['x' => 8, 'y' => 'F']],
                            ],
                        ],
                    ],
                    [
                        'id' => 'B',
                        'type' => 1,
                    ],
                ],
                400,
                false,
                'The ship is supposed to be either vertical or horizontal',
            ],
            'a ship crosses another ship - returns 400' => [
                [
                    [
                        'id' => 'A',
                        'type' => 0,
                        'grid' => [
                            'ships' => [
                                ['id' => 'carrier', 'start' => ['x' => 2, 'y' => 'A'], 'end' => ['x' => 2, 'y' => 'E']],
                                ['id' => 'battleship', 'start' => ['x' => 2, 'y' => 'D'], 'end' => ['x' => 5, 'y' => 'D']],
                                ['id' => 'cruiser', 'start' => ['x' => 4, 'y' => 'C'], 'end' => ['x' => 6, 'y' => 'C']],
                                ['id' => 'submarine', 'start' => ['x' => 4, 'y' => 'G'], 'end' => ['x' => 6, 'y' => 'G']],
                                ['id' => 'destroyer', 'start' => ['x' => 8, 'y' => 'E'], 'end' => ['x' => 8, 'y' => 'F']],
                            ],
                        ],
                    ],
                    [
                        'id' => 'B',
                        'type' => 1,
                    ],
                ],
                400,
                false,
                'ship is overlapping on the board',
            ],
            'one ship is off the board - returns 400' => [
                [
                    [
                        'id' => 'A',
                        'type' => 0,
                        'grid' => [
                            'ships' => [
                                ['id' => 'carrier', 'start' => ['x' => 2, 'y' => 'A'], 'end' => ['x' => 2, 'y' => 'E']],
                                ['id' => 'battleship', 'start' => ['x' => 3, 'y' => 'D'], 'end' => ['x' => 6, 'y' => 'D']],
                                ['id' => 'cruiser', 'start' => ['x' => 4, 'y' => 'C'], 'end' => ['x' => 6, 'y' => 'C']],
                                ['id' => 'submarine', 'start' => ['x' => 7, 'y' => 'G'], 'end' => ['x' => 9, 'y' => 'G']],
                                ['id' => 'destroyer', 'start' => ['x' => 8, 'y' => 'E'], 'end' => ['x' => 8, 'y' => 'F']],
                            ],
                        ],
                    ],
                    [
                        'id' => 'B',
                        'type' => 1,
                    ],
                ],
                400,
                false,
                'This ship is off the board',
            ],
            'non-unique players - returns 400' => [
                [
                    [
                        'id' => 'A',
                        'type' => 0,
                    ],
                    [
                        'id' => 'A',
                        'type' => 1,
                    ],
                ],
                400,
                false,
                'person \"A\" already exists',
            ],
            'wrong id for a user (should be A or B) - returns 400' => [
                [
                    [
                        'id' => 'A',
                        'type' => 1,
                    ],
                    [
                        'id' => 'C',
                        'type' => 1,
                    ],
                ],
                400,
                false,
                'This value is not valid',
            ],
            'wrong player type (should be 0 or 1) - returns 400' => [
                [
                    [
                        'id' => 'A',
                        'type' => 0,
                        'grid' => [
                            'ships' => [
                                ['id' => 'carrier', 'start' => ['x' => 2, 'y' => 'A'], 'end' => ['x' => 2, 'y' => 'E']],
                                ['id' => 'battleship', 'start' => ['x' => 3, 'y' => 'D'], 'end' => ['x' => 6, 'y' => 'D']],
                                ['id' => 'cruiser', 'start' => ['x' => 4, 'y' => 'C'], 'end' => ['x' => 6, 'y' => 'C']],
                                ['id' => 'submarine', 'start' => ['x' => 4, 'y' => 'G'], 'end' => ['x' => 6, 'y' => 'G']],
                                ['id' => 'destroyer', 'start' => ['x' => 8, 'y' => 'E'], 'end' => ['x' => 8, 'y' => 'F']],
                            ],
                        ],
                    ],
                    [
                        'id' => 'B',
                        'type' => 2,
                    ],
                ],
                400,
                false,
                'This value is not valid',
            ],
            'one player missing - returns 400' => [
                [
                    [
                        'id' => 'A',
                        'type' => 1,
                    ],
                ],
                400,
                false,
                'This collection should contain exactly 2 elements.',
            ],
            'correct layout - returns 204' => [
                [
                    [
                        'id' => 'A',
                        'type' => 0,
                        'grid' => [
                            'ships' => [
                                ['id' => 'carrier', 'start' => ['x' => 2, 'y' => 'A'], 'end' => ['x' => 2, 'y' => 'E']],
                                ['id' => 'battleship', 'start' => ['x' => 3, 'y' => 'D'], 'end' => ['x' => 6, 'y' => 'D']],
                                ['id' => 'cruiser', 'start' => ['x' => 4, 'y' => 'C'], 'end' => ['x' => 6, 'y' => 'C']],
                                ['id' => 'submarine', 'start' => ['x' => 4, 'y' => 'G'], 'end' => ['x' => 6, 'y' => 'G']],
                                ['id' => 'destroyer', 'start' => ['x' => 8, 'y' => 'E'], 'end' => ['x' => 8, 'y' => 'F']],
                            ],
                        ],
                    ],
                    [
                        'id' => 'B',
                        'type' => 1,
                    ],
                ],
                204,
                true,
                '',
            ],
            'second attempt to set up the board should no longer be allowed - return 400' => [
                [
                    [
                        'id' => 'A',
                        'type' => 0,
                        'grid' => [
                            'ships' => [
                                ['id' => 'carrier', 'start' => ['x' => 2, 'y' => 'A'], 'end' => ['x' => 2, 'y' => 'E']],
                                ['id' => 'battleship', 'start' => ['x' => 3, 'y' => 'D'], 'end' => ['x' => 6, 'y' => 'D']],
                                ['id' => 'cruiser', 'start' => ['x' => 4, 'y' => 'C'], 'end' => ['x' => 6, 'y' => 'C']],
                                ['id' => 'submarine', 'start' => ['x' => 4, 'y' => 'G'], 'end' => ['x' => 6, 'y' => 'G']],
                                ['id' => 'destroyer', 'start' => ['x' => 8, 'y' => 'E'], 'end' => ['x' => 8, 'y' => 'F']],
                            ],
                        ],
                    ],
                    [
                        'id' => 'B',
                        'type' => 1,
                    ],
                ],
                400,
                false,
                'is not allowed at this state',
            ],
        ];
    }
}
