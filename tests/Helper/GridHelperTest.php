<?php

namespace App\Tests\Helper;

use App\Entity\Battle;
use App\Entity\GameOptions;
use App\Helper\GridHelper;
use PHPUnit\Framework\TestCase;

class GridHelperTest extends TestCase
{
    public function testIfRandomGridHasNoOverlappingShips()
    {
        $battle     = (new Battle())->setOptions((new GameOptions())->setHeight(8)->setWidth(8));
        $randomGrid = GridHelper::getRandomGrid($battle);
        
        $hasOverlappingShips = false;
        foreach ($randomGrid->getShips() as $ship) {
            if ($randomGrid->isShipOverlapping($ship)) {
                $hasOverlappingShips = true;
                
                break;
            }
        }
        
        $this->assertFalse($hasOverlappingShips);
    }
    
    public function testIfRandomGridHasNoShipsInDiagonal()
    {
        $battle     = (new Battle())->setOptions((new GameOptions())->setHeight(8)->setWidth(8));
        $randomGrid = GridHelper::getRandomGrid($battle);
        
        $hasDiagonalShip = false;
        foreach ($randomGrid->getShips() as $ship) {
            if ($ship->isShipDiagonal()) {
                $hasDiagonalShip = true;
                
                break;
            }
        }
        
        $this->assertFalse($hasDiagonalShip);
    }
    
    public function testIfRandomGridHasNoShipsOffTheBoard()
    {
        $battle     = (new Battle())->setOptions((new GameOptions())->setHeight(8)->setWidth(8));
        $randomGrid = GridHelper::getRandomGrid($battle);
        
        $hasShipOffTheBoard = false;
        foreach ($randomGrid->getShips() as $ship) {
            if ($battle->isShipOffTheBoard($ship)) {
                $hasShipOffTheBoard = true;
            }
            break;
        }
        
        $this->assertFalse($hasShipOffTheBoard);
    }
}
