<?php

namespace App\Tests\Entity;

use App\Entity\Coordinate;
use App\Entity\Ship;
use PHPUnit\Framework\TestCase;

class ShipTest extends TestCase
{
    /**
     * @dataProvider provideShips
     *
     * @param Ship $ship
     * @param      $expLength
     */
    public function testShipReportsCorrectLength(Ship $ship, $expLength)
    {
        $this->assertSame($expLength, $ship->getLength());
    }
    
    /**
     * @dataProvider provideShips
     *
     * @param Ship $ship
     * @param      $expLength
     * @param      $expCoords
     */
    public function testShipReturnsCorrectCoordsBasedOnStartAndEnd(Ship $ship, $expLength, $expCoords)
    {
        $this->assertSame($expCoords, $ship->getCoordinates());
    }
    
    public function provideShips()
    {
        return [
            [(new Ship())->setStart((new Coordinate())->setX(3)->setY('A'))
                         ->setEnd((new Coordinate())->setX(3)->setY('E')),
             5,
             ['3A', '3B', '3C', '3D', '3E'],
            ],
            [(new Ship())->setStart((new Coordinate())->setX(5)->setY('C'))
                         ->setEnd((new Coordinate())->setX(8)->setY('C')),
             4,
             ['5C', '6C', '7C', '8C'],
            ],
            [(new Ship())->setStart((new Coordinate())->setX(1)->setY('E'))
                         ->setEnd((new Coordinate())->setX(1)->setY('F')),
             2,
             ['1E', '1F'],
            ],
            [(new Ship())->setStart((new Coordinate())->setX(2)->setY('D'))
                         ->setEnd((new Coordinate())->setX(4)->setY('D')),
             3,
             ['2D', '3D', '4D'],
            ],
        ];
    }
    
    /**
     * @dataProvider provideShipsForDiagonal
     *
     * @param Ship $ship
     * @param      $expResult
     */
    public function testShipReturnsCorrectValueIfAShipIsDiagonal(Ship $ship, $expResult)
    {
        $this->assertSame($expResult, $ship->isShipDiagonal());
    }
    
    public function provideShipsForDiagonal()
    {
        return [
            [(new Ship())->setStart((new Coordinate())->setX(3)->setY('A'))->setEnd((new Coordinate())->setX(3)->setY('E')), false],
            [(new Ship())->setStart((new Coordinate())->setX(2)->setY('F'))->setEnd((new Coordinate())->setX(7)->setY('F')), false],
            [(new Ship())->setStart((new Coordinate())->setX(3)->setY('A'))->setEnd((new Coordinate())->setX(4)->setY('E')), true],
            [(new Ship())->setStart((new Coordinate())->setX(8)->setY('A'))->setEnd((new Coordinate())->setX(5)->setY('F')), true],
        ];
    }
}
