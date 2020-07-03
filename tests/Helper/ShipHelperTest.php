<?php

namespace App\Tests\Helper;

use App\Entity\Coordinate;
use App\Helper\GridHelper;
use App\Helper\ShipHelper;
use PHPUnit\Framework\TestCase;

class ShipHelperTest extends TestCase
{
    /**
     * @dataProvider provideConfig
     *
     * @param Coordinate $start
     * @param string     $layout
     * @param string     $shipId
     * @param Coordinate $expEnd
     */
    public function testIfShipEndIsCalculatedCorrectlyBasedOnStart(
        Coordinate $start,
        string $layout,
        string $shipId,
        Coordinate $expEnd
    ) {
        $this->assertEquals($expEnd, ShipHelper::calcEnd($start, $layout, $shipId));
    }
    
    public function provideConfig()
    {
        return [
            [(new Coordinate())->setX(1)->setY('A'), GridHelper::VERTICAL, 'carrier', (new Coordinate())->setX(1)->setY('E')],
            [(new Coordinate())->setX(1)->setY('A'), GridHelper::HORIZONTAL, 'carrier', (new Coordinate())->setX(5)->setY('A')],
            [(new Coordinate())->setX(5)->setY('F'), GridHelper::VERTICAL, 'cruiser', (new Coordinate())->setX(5)->setY('H')],
            [(new Coordinate())->setX(5)->setY('F'), GridHelper::HORIZONTAL, 'cruiser', (new Coordinate())->setX(7)->setY('F')],
            [(new Coordinate())->setX(4)->setY('C'), GridHelper::HORIZONTAL, 'destroyer', (new Coordinate())->setX(5)->setY('C')],
        ];
    }
}
