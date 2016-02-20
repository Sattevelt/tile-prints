<?php
namespace Oneway\TilePrints\Test\Tile;

class AbstractTileTest extends \PHPUnit_Framework_TestCase
{
    /** @var TileMock */
    private $tileMock;

    public function setUp()
    {
        $tileMock = new TileMock();
        $tileMock->setRotation(0);
        $tileMock->setType('test');
        $tileMock->setStandardExits(0b0);
        $this->tileMock = $tileMock;
    }

    /**
     * @dataProvider getExitsDataProvider
     */
    public function testGetExitsReturnsZero($standardExits, $rotation, $expected)
    {
        $this->tileMock->setStandardExits($standardExits);
        $this->tileMock->setRotation($rotation);

        $this->assertSame($expected, $this->tileMock->getExits());
    }

    public function getExitsDataProvider()
    {
        return array(
            '1' => array(0b0, 0, 0),
            '2' => array(0b0, 0, 0),
            '3' => array(0b0, 0, 0),
            '4' => array(0b0, 0, 0)
        );
    }
}
