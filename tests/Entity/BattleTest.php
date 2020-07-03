<?php

namespace App\Tests\Entity;

use App\Entity\Battle;
use App\Entity\Coordinate;
use App\Entity\GameOptions;
use App\Entity\Grid;
use App\Entity\Player;
use App\Entity\Ship;
use App\Entity\Shot;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Entity\Battle
 */
class BattleTest extends TestCase
{
    /**
     * @covers ::isShipOffTheBoard
     * @dataProvider provideForOffTheBoard
     * @testdox      If Result is correct for ship off the board
     *
     * @param GameOptions $options
     * @param Ship        $ship
     * @param             $expResult
     */
    public function testIsShipOffTheBoard(GameOptions $options, Ship $ship, $expResult)
    {
        $battle = new Battle();
        $battle->setOptions($options);
        
        $this->assertSame($expResult, $battle->isShipOffTheBoard($ship));
    }
    
    public function provideForOffTheBoard()
    {
        $option1 = (new GameOptions())->setWidth(8)->setHeight(8);
        $option2 = (new GameOptions())->setWidth(10)->setHeight(10);
        
        return [
            [$option1,
             (new Ship())->setStart((new Coordinate())->setX(9)->setY('D'))
                         ->setEnd((new Coordinate())->setX(9)->setY('F')),
             true],
            [$option1,
             (new Ship())->setStart((new Coordinate())->setX(8)->setY('D'))
                         ->setEnd((new Coordinate())->setX(8)->setY('I')),
             true],
            [$option1,
             (new Ship())->setStart((new Coordinate())->setX(8)->setY('D'))
                         ->setEnd((new Coordinate())->setX(8)->setY('F')),
             false],
            [$option2,
             (new Ship())->setStart((new Coordinate())->setX(9)->setY('D'))
                         ->setEnd((new Coordinate())->setX(9)->setY('F')),
             false],
            [$option2,
             (new Ship())->setStart((new Coordinate())->setX(11)->setY('D'))
                         ->setEnd((new Coordinate())->setX(11)->setY('I')),
             true],
            [$option1,
             (new Ship())->setStart((new Coordinate())->setX(8)->setY('D'))
                         ->setEnd((new Coordinate())->setX(8)->setY('F')),
             false],
        ];
    }
    
    /**
     * @covers ::getOpponent
     * @testdox If Correct Opponent is returned
     */
    public function testgetOpponent()
    {
        $battle  = new Battle();
        $player1 = (new Player())->setId('A');
        $player2 = (new Player())->setId('B');
        
        $battle->addPlayer($player1)->addPlayer($player2);
        
        $this->assertSame($player2, $battle->getOpponent($player1));
    }
    
    /**
     * @covers ::getPlayers
     */
    public function testIfOnlyUniquePlayersAdded()
    {
        $battle  = new Battle();
        $player1 = (new Player())->setId('A');
        $player2 = (new Player())->setId('A');
        
        $battle->addPlayer($player1)->addPlayer($player2);
        
        $this->assertCount(1, $battle->getPlayers());
    }
    
    /**
     * @covers ::isShipSunk
     * @dataProvider provideForIsShipSunk
     * @testdox      Result is correct if a shot has sunk a ship
     *
     * @param Shot[] $opponentShots
     * @param Shot   $shot
     * @param bool   $expResult
     */
    public function testIsShipSunk(array $opponentShots, Shot $shot, bool $expResult)
    {
        // set up a board with a correct layout.
        $battle = (new Battle())->setOptions((new GameOptions())->setWidth(8)->setHeight(8));
        $grid   = (new Grid())->setShips([
             (new Ship())->setId('carrier')->setStart((new Coordinate())->setX(2)->setY('A'))->setEnd((new Coordinate())->setX(2)->setY('E')),
             (new Ship())->setId('battleship')->setStart((new Coordinate())->setX(3)->setY('D'))->setEnd((new Coordinate())->setX(6)->setY('D')),
             (new Ship())->setId('cruiser')->setStart((new Coordinate())->setX(4)->setY('C'))->setEnd((new Coordinate())->setX(6)->setY('C')),
             (new Ship())->setId('submarine')->setStart((new Coordinate())->setX(4)->setY('G'))->setEnd((new Coordinate())->setX(6)->setY('G')),
             (new Ship())->setId('destroyer')->setStart((new Coordinate())->setX(8)->setY('E'))->setEnd((new Coordinate())->setX(8)->setY('F')),
         ]);
        // grid is for player A
        $battle->addPlayer((new Player())->setId('A')->setGrid($grid));
        // the shot is fired by Player B
        $battle->addPlayer((new Player())->setId('B')->setGrid((new Grid())->setShots($opponentShots)->addShot($shot)));
        
        $this->assertSame($expResult, $battle->isShipSunk($battle->getPlayer('B'), $shot));
    }
    
    public function provideForIsShipSunk()
    {
        return [
            [
                [
                    (new Shot())->setX(3)->setY('A'),
                    (new Shot())->setX(4)->setY('A'),
                    (new Shot())->setX(5)->setY('A'),
                    (new Shot())->setX(4)->setY('C')->setHit(true),
                    (new Shot())->setX(5)->setY('C')->setHit(true),
                ],
                (new Shot())->setX(6)->setY('C')->setHit(true),
                true,
            ],
            [
                [
                    (new Shot())->setX(3)->setY('A'),
                    (new Shot())->setX(4)->setY('A'),
                    (new Shot())->setX(5)->setY('A'),
                    (new Shot())->setX(5)->setY('C')->setHit(true),
                ],
                (new Shot())->setX(6)->setY('C')->setHit(true),
                false,
            ],
            [
                [
                    (new Shot())->setX(3)->setY('A'),
                    (new Shot())->setX(4)->setY('A'),
                    (new Shot())->setX(5)->setY('A'),
                    (new Shot())->setX(2)->setY('A')->setHit(true),
                    (new Shot())->setX(2)->setY('B')->setHit(true),
                    (new Shot())->setX(2)->setY('C')->setHit(true),
                ],
                (new Shot())->setX(2)->setY('D')->setHit(true),
                false,
            ],
            [
                [
                    (new Shot())->setX(3)->setY('A'),
                    (new Shot())->setX(4)->setY('A'),
                    (new Shot())->setX(5)->setY('A'),
                    (new Shot())->setX(2)->setY('A')->setHit(true),
                    (new Shot())->setX(2)->setY('B')->setHit(true),
                    (new Shot())->setX(2)->setY('C')->setHit(true),
                    (new Shot())->setX(2)->setY('D')->setHit(true),
                ],
                (new Shot())->setX(2)->setY('E')->setHit(true),
                true,
            ],
            [
                [
                    (new Shot())->setX(3)->setY('A'),
                    (new Shot())->setX(4)->setY('A'),
                    (new Shot())->setX(5)->setY('A'),
                    (new Shot())->setX(2)->setY('D')->setHit(true),
                    (new Shot())->setX(3)->setY('D')->setHit(true),
                    (new Shot())->setX(4)->setY('D')->setHit(true),
                ],
                (new Shot())->setX(6)->setY('D')->setHit(true),
                false,
            ],
            [
                [
                    (new Shot())->setX(3)->setY('A'),
                    (new Shot())->setX(4)->setY('A'),
                    (new Shot())->setX(5)->setY('A'),
                    (new Shot())->setX(2)->setY('D')->setHit(true),
                    (new Shot())->setX(3)->setY('D')->setHit(true),
                    (new Shot())->setX(4)->setY('D')->setHit(true),
                    (new Shot())->setX(5)->setY('D')->setHit(true),
                ],
                (new Shot())->setX(6)->setY('D')->setHit(true),
                true,
            ],
        ];
    }
    
    /**
     * @covers ::isWon
     * @dataProvider provideForIsBattleWon
     * @testdox      Result correct if the last shot/hit sunk the last ship, marking the battle 'won'.
     *
     * @param Shot[] $opponentShots
     * @param Shot   $shot
     * @param bool   $expResult
     */
    public function testIsBattleWon(array $opponentShots, Shot $shot, bool $expResult)
    {
        // set up a board with a correct layout.
        $battle = (new Battle())->setOptions((new GameOptions())->setWidth(8)->setHeight(8));
        $grid   = (new Grid())->setShips([
                                             (new Ship())->setId('carrier')->setStart((new Coordinate())->setX(2)->setY('A'))->setEnd((new Coordinate())->setX(2)->setY('E')),
                                             (new Ship())->setId('battleship')->setStart((new Coordinate())->setX(3)->setY('D'))->setEnd((new Coordinate())->setX(6)->setY('D')),
                                             (new Ship())->setId('cruiser')->setStart((new Coordinate())->setX(4)->setY('C'))->setEnd((new Coordinate())->setX(6)->setY('C')),
                                             (new Ship())->setId('submarine')->setStart((new Coordinate())->setX(4)->setY('G'))->setEnd((new Coordinate())->setX(6)->setY('G')),
                                             (new Ship())->setId('destroyer')->setStart((new Coordinate())->setX(8)->setY('E'))->setEnd((new Coordinate())->setX(8)->setY('F')),
                                         ]);
        $battle->addPlayer((new Player())->setId('A')->setGrid($grid));
        $battle->addPlayer((new Player())->setId('B')->setGrid((new Grid())->setShots($opponentShots)->addShot($shot)));
        
        $this->assertSame($expResult, $battle->isWon($battle->getPlayer('B')));
    }
    
    public function provideForIsBattleWon()
    {
        return [
            'a lot of hits missing' => [
                [
                    (new Shot())->setX(3)->setY('A'),
                    (new Shot())->setX(4)->setY('A'),
                    (new Shot())->setX(5)->setY('A'),
                    (new Shot())->setX(4)->setY('C')->setHit(true),
                    (new Shot())->setX(5)->setY('C')->setHit(true),
                ],
                (new Shot())->setX(6)->setY('C')->setHit(true),
                false,
            ],
            'one hit is missing' => [
                [
                    (new Shot())->setX(3)->setY('A'),
                    (new Shot())->setX(2)->setY('A')->setHit(true),
                    (new Shot())->setX(2)->setY('B')->setHit(true),
                    (new Shot())->setX(2)->setY('C')->setHit(true),
                    (new Shot())->setX(2)->setY('D')->setHit(true),
                    (new Shot())->setX(2)->setY('E')->setHit(true)->setSunk(true),
                    (new Shot())->setX(2)->setY('G'),
                    (new Shot())->setX(4)->setY('C')->setHit(true),
                    (new Shot())->setX(5)->setY('C')->setHit(true),
                    (new Shot())->setX(6)->setY('C')->setHit(true)->setSunk(true),
                    (new Shot())->setX(3)->setY('F'),
                    (new Shot())->setX(3)->setY('D')->setHit(true),
                    (new Shot())->setX(4)->setY('D')->setHit(true),
                    (new Shot())->setX(5)->setY('D')->setHit(true),
                    (new Shot())->setX(6)->setY('D')->setHit(true)->setSunk(true),
                    (new Shot())->setX(4)->setY('H'),
                    (new Shot())->setX(4)->setY('G')->setHit(true),
                    (new Shot())->setX(5)->setY('G')->setHit(true),
                    (new Shot())->setX(6)->setY('G')->setHit(true)->setSunk(true),
                    (new Shot())->setX(8)->setY('C'),
                ],
                (new Shot())->setX(8)->setY('F')->setHit(true)->setSunk(true),
                false,
            ],
            'should return won' => [
                [
                    (new Shot())->setX(3)->setY('A'),
                    (new Shot())->setX(2)->setY('A')->setHit(true),
                    (new Shot())->setX(2)->setY('B')->setHit(true),
                    (new Shot())->setX(2)->setY('C')->setHit(true),
                    (new Shot())->setX(2)->setY('D')->setHit(true),
                    (new Shot())->setX(2)->setY('E')->setHit(true)->setSunk(true),
                    (new Shot())->setX(2)->setY('G'),
                    (new Shot())->setX(4)->setY('C')->setHit(true),
                    (new Shot())->setX(5)->setY('C')->setHit(true),
                    (new Shot())->setX(6)->setY('C')->setHit(true)->setSunk(true),
                    (new Shot())->setX(3)->setY('F'),
                    (new Shot())->setX(3)->setY('D')->setHit(true),
                    (new Shot())->setX(4)->setY('D')->setHit(true),
                    (new Shot())->setX(5)->setY('D')->setHit(true),
                    (new Shot())->setX(6)->setY('D')->setHit(true)->setSunk(true),
                    (new Shot())->setX(4)->setY('H'),
                    (new Shot())->setX(4)->setY('G')->setHit(true),
                    (new Shot())->setX(5)->setY('G')->setHit(true),
                    (new Shot())->setX(6)->setY('G')->setHit(true)->setSunk(true),
                    (new Shot())->setX(8)->setY('C'),
                    (new Shot())->setX(8)->setY('E')->setHit(true),
                ],
                (new Shot())->setX(8)->setY('F')->setHit(true)->setSunk(true),
                true,
            ],
        ];
    }
}
