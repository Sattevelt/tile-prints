<?php
namespace Oneway\TilePrints\Test\Tile;

use Oneway\TilePrints\Tile\Exception\IllegalRotation;

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

    /**
     * @dataProvider setRotationDataProvider
     */
    public function testSetRotationThrowsIllegalRotationException($rotation)
    {
        $this->expectException(IllegalRotation::class);
        $this->expectExceptionMessage('Invalid tile rotation value: ' . $rotation);

        $this->tileMock->setRotation($rotation);
    }


    public function testForCoverage()
    {
        $rotation = 180;
        $this->tileMock->setRotation($rotation);
        $this->assertEquals($rotation, $this->tileMock->getRotation());

        $type = 'testType';
        $this->tileMock->setType($type);
        $this->assertEquals($type, $this->tileMock->getType());
    }


    public function getExitsDataProvider()
    {
        return array(
            '1' => array(0b0, 0, 0),
            '2' => array(0b0, 90, 0),
            '3' => array(0b0, 270, 0),
            '4' => array(0b0001, 0, 0b0001),
            '5' => array(0b0001, 90, 0b0010),
            '6' => array(0b0001, 180, 0b0100),
            '7' => array(0b0001, 270, 0b1000),
            '8' => array(0b1010, 0, 0b1010),
            '9' => array(0b1010, 90, 0b0101),
            '10' => array(0b1010, 180, 0b1010),
            '11' => array(0b1010, 270, 0b0101)
        );
    }

    public function setRotationDataProvider()
    {
        return array(
            array(1),
            array(45),
            array(89),
            array(91),
            array(361)
        );
    }
}
