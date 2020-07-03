<?php

namespace App\Tests\Entity;

use App\Entity\Coordinate;
use App\Entity\Grid;
use App\Entity\Ship;
use App\Entity\Shot;
use PHPUnit\Framework\TestCase;

class GridTest extends TestCase
{
    public function testShotHasBeenAdded()
    {
        $shot = new Shot();
        $grid = new Grid();
        
        $grid->addShot($shot);
        $this->assertCount(1, $grid->getShots());
    }
    
    /**
     * @dataProvider provideShotsForHasShot
     *
     * @param Shot[] $shots
     * @param Shot   $shot
     * @param bool   $expResult
     */
    public function testGridReportsIfItHasShot(array $shots, Shot $shot, bool $expResult)
    {
        $grid = new Grid();
        $grid->setShots($shots);
        
        $this->assertSame($expResult, $grid->hasShot($shot));
    }
    
    public function provideShotsForHasShot()
    {
        $shots = [
            (new Shot())->setX(1)->setY('G'),
            (new Shot())->setX(3)->setY('A'),
            (new Shot())->setX(3)->setY('B'),
            (new Shot())->setX(5)->setY('A'),
            (new Shot())->setX(8)->setY('C'),
        ];
        
        return [
            [$shots, (new Shot())->setX(8)->setY('C'), true],
            [$shots, (new Shot())->setX(3)->setY('F'), false],
            [$shots, (new Shot())->setX(3)->setY('B'), true],
            [$shots, (new Shot())->setX(4)->setY('B'), false],
        ];
    }
    
    /**
     * @dataProvider provideShotCoordinates
     *
     * @param array $shots
     * @param array $expShotCoords
     */
    public function testGridReturnsCorrectShotCoordinates(array $shots, array $expShotCoords)
    {
        $grid = new Grid();
        $grid->setShots($shots);
        
        $this->assertSame($expShotCoords, $grid->getShotCoordinates());
    }
    
    public function provideShotCoordinates()
    {
        return [
            [
                [
                    (new Shot())->setX(1)->setY('G'),
                    (new Shot())->setX(3)->setY('A'),
                    (new Shot())->setX(3)->setY('B'),
                    (new Shot())->setX(5)->setY('A'),
                    (new Shot())->setX(8)->setY('C'),
                ],
                ['1G', '3A', '3B', '5A', '8C'],
                [
                    (new Shot())->setX(2)->setY('F'),
                    (new Shot())->setX(1)->setY('G'),
                    (new Shot())->setX(4)->setY('A'),
                ],
                ['2F', '1G', '4A'],
            ],
        ];
    }
    
    /**
     * @dataProvider provideShots
     *
     * @param Shot[] $shots
     * @param Shot   $shot
     * @param bool   $expResult
     */
    public function testGridHasShotReturnsCorrectValue(array $shots, Shot $shot, bool $expResult)
    {
        $grid = new Grid();
        $grid->setShots($shots);
        
        $this->assertSame($expResult, $grid->hasShot($shot));
    }
    
    public function provideShots()
    {
        $sampleShots = [
            (new Shot())->setX(1)->setY('G'),
            (new Shot())->setX(3)->setY('A'),
            (new Shot())->setX(3)->setY('B'),
            (new Shot())->setX(5)->setY('A'),
            (new Shot())->setX(8)->setY('C'),
        ];
        
        return [
            [$sampleShots, (new Shot())->setX(3)->setY('A'), true],
            [$sampleShots, (new Shot())->setX(5)->setY('F'), false],
            [$sampleShots, (new Shot())->setX(1)->setY('G'), true],
            [$sampleShots, (new Shot())->setX(3)->setY('F'), false],
        ];
    }
    
    /**
     * @dataProvider provideShips
     *
     * @param Ship[] $ships
     * @param Shot   $shot
     * @param bool   $expResult
     */
    public function testShotIsHitReturnsCorrectValue(array $ships, Shot $shot, bool $expResult)
    {
        $grid = new Grid();
        $grid->setShips($ships);
        
        $this->assertSame($expResult, $grid->isHit($shot));
    }
    
    public function provideShips()
    {
        $sampleShips = [
            (new Ship())->setStart((new Coordinate())->setX(3)->setY('A'))
                        ->setEnd((new Coordinate())->setX(3)->setY('E')),
            (new Ship())->setStart((new Coordinate())->setX(5)->setY('C'))
                        ->setEnd((new Coordinate())->setX(8)->setY('C')),
            (new Ship())->setStart((new Coordinate())->setX(1)->setY('E'))
                        ->setEnd((new Coordinate())->setX(1)->setY('F')),
        ];
        
        return [
            [$sampleShips, (new Shot())->setX(3)->setY('C'), true],
            [$sampleShips, (new Shot())->setX(5)->setY('F'), false],
            [$sampleShips, (new Shot())->setX(1)->setY('E'), true],
            [$sampleShips, (new Shot())->setX(3)->setY('F'), false],
        ];
    }
    
    public function testShipHasBeenAdded()
    {
        $ship = new Ship();
        $grid = new Grid();
        
        $grid->addShip($ship);
        $this->assertCount(1, $grid->getShips());
    }
    
    public function testShipCoordinatesAreReturned()
    {
        $grid = new Grid();
        $grid->addShip((new Ship())->setStart((new Coordinate())->setX(3)->setY('A'))
                                   ->setEnd((new Coordinate())->setX(3)->setY('E')))
             ->addShip((new Ship())->setStart((new Coordinate())->setX(5)->setY('C'))
                                   ->setEnd((new Coordinate())->setX(8)->setY('C')))
             ->addShip((new Ship())->setStart((new Coordinate())->setX(1)->setY('E'))
                                   ->setEnd((new Coordinate())->setX(1)->setY('F')));
        
        $this->assertSame(
            ['3A', '3B', '3C', '3D', '3E', '5C', '6C', '7C', '8C', '1E', '1F'],
            $grid->getShipCoordinates()
        );
    }
    
    public function testShipCoordinatesAreReturnedWithAnExcludedShip()
    {
        $grid  = new Grid();
        $ship1 = (new Ship())->setId('carrier')->setStart((new Coordinate())->setX(3)->setY('A'))
                             ->setEnd((new Coordinate())->setX(3)->setY('E'));
        $ship2 = (new Ship())->setId('cruiser')->setStart((new Coordinate())->setX(5)->setY('C'))
                             ->setEnd((new Coordinate())->setX(8)->setY('C'));
        $ship3 = (new Ship())->setId('submarine')->setStart((new Coordinate())->setX(1)->setY('E'))
                             ->setEnd((new Coordinate())->setX(1)->setY('F'));
        
        $grid->addShip($ship1)
             ->addShip($ship2)
             ->addShip($ship3);
        
        $this->assertSame(
            ['3A', '3B', '3C', '3D', '3E', '1E', '1F'],
            $grid->getShipCoordinates($ship2)
        );
    }
    
    /**
     * @dataProvider provideShipsForOverlap
     */
    public function testGridReportsCorrectlyIfShipIsOverlapping(array $ships, Ship $ship, $expResult)
    {
        $grid = new Grid();
        $grid->setShips($ships);
        
        $this->assertSame($expResult, $grid->isShipOverlapping($ship));
    }
    
    public function provideShipsForOverlap()
    {
        $ships = [(new Ship())->setId('carrier')->setStart((new Coordinate())->setX(3)->setY('A'))
                              ->setEnd((new Coordinate())->setX(3)->setY('E')),
                  (new Ship())->setId('cruiser')->setStart((new Coordinate())->setX(5)->setY('C'))
                              ->setEnd((new Coordinate())->setX(8)->setY('C')),
                  (new Ship())->setId('submarine')->setStart((new Coordinate())->setX(1)->setY('E'))
                              ->setEnd((new Coordinate())->setX(1)->setY('F'))];
        
        $overlapping = (new Ship())->setId('destroyer')->setStart((new Coordinate())->setX(2)->setY('C'))
                                   ->setEnd((new Coordinate())->setX(5)->setY('C'));
        
        $notOverlappingShip = (new Ship())->setId('destroyer')->setStart((new Coordinate())->setX(5)->setY('F'))
                                          ->setEnd((new Coordinate())->setX(8)->setY('F'));
        
        return [
            [$ships, $overlapping, true,],
            [$ships, $notOverlappingShip, false,],
        ];
    }
}
