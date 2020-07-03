<?php

namespace App\Tests\Entity;

use App\Entity\Coordinate;
use PHPUnit\Framework\TestCase;

class CoordinateTest extends TestCase
{
    public function testYSetAsUpperCase()
    {
        $coord = (new Coordinate())->setY('a');
        
        $this->assertEquals('A', $coord->getY());
    }
    
    /**
     * @dataProvider provideYCoords
     *
     * @param Coordinate $coord
     * @param            $asciiValue
     */
    public function testGetYAsciiReturnsCorrectAsciiValue(Coordinate $coord, $asciiValue)
    {
        $this->assertEquals($asciiValue, $coord->getYAscii());
    }
    
    /**
     * @dataProvider provideYAsciiCoords
     *
     * @param Coordinate $coord
     * @param            $letter
     */
    public function testAfterSettingYAsAsciiReturnsCorrectLetter(Coordinate $coord, $letter)
    {
        $this->assertEquals($letter, $coord->getY());
    }
    
    public function provideYCoords()
    {
        return [
            [(new Coordinate())->setY('A'), 1],
            [(new Coordinate())->setY('B'), 2],
            [(new Coordinate())->setY('C'), 3],
            [(new Coordinate())->setY('D'), 4],
            [(new Coordinate())->setY('E'), 5],
            [(new Coordinate())->setY('Z'), 26],
        ];
    }
    
    public function provideYAsciiCoords()
    {
        return [
            [(new Coordinate())->setYAscii(1), 'A'],
            [(new Coordinate())->setYAscii(2), 'B'],
            [(new Coordinate())->setYAscii(3), 'C'],
            [(new Coordinate())->setYAscii(26), 'Z'],
        ];
    }
}
