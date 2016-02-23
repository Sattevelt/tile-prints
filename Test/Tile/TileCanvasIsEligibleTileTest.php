<?php
namespace Oneway\TilePrints\Test\Tile;

use Oneway\TilePrints\Tile\TileCanvas;
use Oneway\TilePrints\Tile\TileFactory;
use PHPUnit_Framework_TestCase;
use ReflectionMethod;
use stdClass;

class TileCanvasIsEligibleTileTest extends PHPUnit_Framework_TestCase
{
    /** @var TileCanvas */
    private $tileCanvas;

    /** @var ReflectionMethod */
    private $tileCanvasMethod;

    public function setUp()
    {
        $tileFactory = new TileFactory();
        $tileCanvas = new TileCanvas(1, 1, $tileFactory);
        $tileCanvasMethod = new ReflectionMethod($tileCanvas, 'isEligibleTile');
        $tileCanvasMethod->setAccessible(true);

        $this->tileCanvas = $tileCanvas;
        $this->tileCanvasMethod = $tileCanvasMethod;
    }

    public function testIsEligibleTileWithForbiddenExitReturnsFalse()
    {
        $tileMock = new TileMock();
        $tileMock->setStandardExits(0b1111);
        $exits = new stdClass();
        $exits->forbidden = 0b1111;

        $result = $this->tileCanvasMethod->invoke($this->tileCanvas, $exits, $tileMock);

        $this->assertFalse($result);
    }

    public function testIsEligibleTileWithNoPossibleExitsReturnsFalse()
    {
        $tileMock = new TileMock();
        $tileMock->setStandardExits(0b1111);
        $exits = new stdClass();
        $exits->forbidden = 0b0000;
        $exits->possible = 0b0000;

        $result = $this->tileCanvasMethod->invoke($this->tileCanvas, $exits, $tileMock);

        $this->assertFalse($result);
    }

    public function testIsEligibleWithNoMatchingRequiredExitsReturnsFalse()
    {
        $tileMock = new TileMock();
        $tileMock->setStandardExits(0b0000);
        $exits = new stdClass();
        $exits->forbidden = 0b0000;
        $exits->possible = 0b1111;
        $exits->required = 0b0001;

        $result = $this->tileCanvasMethod->invoke($this->tileCanvas, $exits, $tileMock);

        $this->assertFalse($result);
    }

    public function testIsEligibleWithMatchingRequiredAndPossibleExitsReturnsTrue()
    {
        $tileMock = new TileMock();
        $tileMock->setStandardExits(0b0100);
        $exits = new stdClass();
        $exits->forbidden = 0b0000;
        $exits->possible = 0b1111;
        $exits->required = 0b0100;

        $result = $this->tileCanvasMethod->invoke($this->tileCanvas, $exits, $tileMock);

        $this->assertTrue($result);
    }
}
