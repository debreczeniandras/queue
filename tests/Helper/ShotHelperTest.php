<?php

namespace App\Tests\Helper;

use App\Entity\Battle;
use App\Entity\GameOptions;
use App\Entity\Grid;
use App\Entity\Player;
use App\Entity\Shot;
use App\Helper\ShotHelper;
use App\Helper\ValueObject\HitSeries;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Shot Helper Class
 */
class ShotHelperTest extends TestCase
{
    /**
     * @dataProvider provideShotsForLastHitSeries
     *
     * @param array     $shots
     * @param           $width
     * @param           $height
     * @param HitSeries $expSeries
     *
     * @throws \ReflectionException
     * @testdox      Last Hit Series are returned correctly
     */
    public function testLastHitSeries(array $shots, $width, $height, HitSeries $expSeries)
    {
        $battle = (new Battle())->setOptions((new GameOptions())->setWidth($width)->setHeight($height));
        $player = (new Player())->setGrid((new Grid())->setShots($shots));
        
        $reflection = new \ReflectionClass(ShotHelper::class);
        $method     = $reflection->getMethod('getHitSeries');
        $method->setAccessible(true);
        
        $this->assertEquals($expSeries, $method->invokeArgs(null, [$battle, $player]));
    }
    
    public function provideShotsForLastHitSeries()
    {
        return [
            'with an unfinished ship' => [
                [
                    (new Shot())->setX(2)->setY('A'),
                    (new Shot())->setX(2)->setY('B'),
                    (new Shot())->setX(3)->setY('B')->setHit(true),
                    (new Shot())->setX(3)->setY('C')->setHit(true),
                    (new Shot())->setX(3)->setY('D')->setHit(true),
                    (new Shot())->setX(3)->setY('A'),
                ],
                8,
                8,
                (new HitSeries(8, 8))->addHit((new Shot())->setX(3)->setY('D')->setHit(true))
                                     ->addHit((new Shot())->setX(3)->setY('C')->setHit(true))
                                     ->addHit((new Shot())->setX(3)->setY('B')->setHit(true)),
            ],
            'with an already sunk ship' => [
                [
                    (new Shot())->setX(2)->setY('A'),
                    (new Shot())->setX(2)->setY('B'),
                    (new Shot())->setX(3)->setY('B')->setHit(true),
                    (new Shot())->setX(3)->setY('C')->setHit(true),
                    (new Shot())->setX(3)->setY('D')->setHit(true)->setSunk(true),
                    (new Shot())->setX(3)->setY('A'),
                ],
                8,
                8,
                (new HitSeries(8, 8)),
            ],
            'with shots belonging to different ships (only last ships` hits series expected)' => [
                [
                    (new Shot())->setX(2)->setY('A'),
                    (new Shot())->setX(2)->setY('B'),
                    (new Shot())->setX(3)->setY('B')->setHit(true),
                    (new Shot())->setX(3)->setY('C')->setHit(true),
                    (new Shot())->setX(3)->setY('D')->setHit(true),
                    (new Shot())->setX(3)->setY('A'),
                    (new Shot())->setX(2)->setY('D')->setHit(true),
                    (new Shot())->setX(3)->setY('D')->setHit(true),
                    (new Shot())->setX(4)->setY('D')->setHit(true),
                ],
                8,
                8,
                (new HitSeries(8, 8))->addHit((new Shot())->setX(4)->setY('D')->setHit(true))
                                     ->addHit((new Shot())->setX(3)->setY('D')->setHit(true))
                                     ->addHit((new Shot())->setX(2)->setY('D')->setHit(true)),
            ],
            'with no shots yet fired' => [
                [
                ],
                8,
                8,
                (new HitSeries(8, 8)),
            ],
            'with only missing shots' => [
                [
                    (new Shot())->setX(2)->setY('A'),
                    (new Shot())->setX(2)->setY('B'),
                    (new Shot())->setX(3)->setY('B'),
                    (new Shot())->setX(3)->setY('C'),
                    (new Shot())->setX(3)->setY('D'),
                    (new Shot())->setX(3)->setY('A'),
                ],
                8,
                8,
                (new HitSeries(8, 8)),
            ],
        ];
    }
    
    /**
     * @dataProvider provideShotsForDesignatedShot
     *
     * @param array $shots
     * @param Shot  $bestShot
     *
     * @throws \ReflectionException
     * @testdox      A designated shot is returned based on already fired hits
     */
    public function testDesignatedShot(array $shots, Shot $bestShot)
    {
        $battle = (new Battle())->setOptions((new GameOptions())->setWidth(8)->setHeight(8));
        $player = (new Player())->setGrid((new Grid())->setShots($shots));
        
        $reflection = new \ReflectionClass(ShotHelper::class);
        $method     = $reflection->getMethod('getHitSeries');
        $method->setAccessible(true);
        
        $hitSeries = $method->invokeArgs(null, [$battle, $player]);
    
        $method     = $reflection->getMethod('getDesignatedShot');
        $method->setAccessible(true);
        
        $this->assertEquals($bestShot, $method->invokeArgs(null, [$hitSeries, $player]));
    }
    
    public function provideShotsForDesignatedShot()
    {
        // expected order of shots:
        // top, bottom, left, right
        return [
            'bottom horizontal length 2 (no additional shots)' => [
                [
                    (new Shot())->setX(3)->setY('H')->setHit(true),
                    (new Shot())->setX(4)->setY('H')->setHit(true),
                ],
                (new Shot())->setX(2)->setY('H'),
            ],
            'bottom horizontal length 4 touching right (no additional shots)' => [
                [
                    (new Shot())->setX(5)->setY('G')->setHit(true),
                    (new Shot())->setX(6)->setY('G')->setHit(true),
                    (new Shot())->setX(7)->setY('G')->setHit(true),
                    (new Shot())->setX(8)->setY('G')->setHit(true),
                ],
                (new Shot())->setX(4)->setY('G'),
            ],
            'middle vertical length 3 (a missing shot already fired on one side)' => [
                [
                    (new Shot())->setX(6)->setY('C')->setHit(false),
                    (new Shot())->setX(6)->setY('D')->setHit(true),
                    (new Shot())->setX(6)->setY('E')->setHit(true),
                    (new Shot())->setX(6)->setY('F')->setHit(true),
                ],
                (new Shot())->setX(6)->setY('G'),
            ],
            'left corner horizontal touching length 3 missing fire on top already ' => [
                [
                    (new Shot())->setX(1)->setY('A')->setHit(false),
                    (new Shot())->setX(1)->setY('B')->setHit(true),
                    (new Shot())->setX(1)->setY('C')->setHit(true),
                    (new Shot())->setX(1)->setY('D')->setHit(true),
                ],
                (new Shot())->setX(1)->setY('E'),
            ],
            'single shot in the middle' => [
                [
                    (new Shot())->setX(5)->setY('D')->setHit(true),
                ],
                (new Shot())->setX(5)->setY('C'),
            ],
            'single shot in the middle (with a missing shot top)' => [
                [
                    (new Shot())->setX(5)->setY('D')->setHit(true),
                    (new Shot())->setX(5)->setY('C')->setHit(false),
                ],
                (new Shot())->setX(5)->setY('E'),
            ],
        ];
    }
}
