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

    public function testGetStyleWithAllOptionsReturnsCssString()
    {
        $tileThemeMock = $this->getMockBuilder('Oneway\\TilePrints\\Tile\\TileTheme')
                              ->setMethods(array('getStrokeColor', 'getBackgroundColor', 'getTileSize'))
                              ->getMock();
        $tileThemeMock->expects($this->exactly(3))
                      ->method('getStrokeColor')
                      ->will($this->returnValue('#000000'));
        $tileThemeMock->expects($this->exactly(3))
                      ->method('getBackgroundColor')
                      ->will($this->returnValue('#ffffff'));
        $tileThemeMock->expects($this->once())
                      ->method('getTileSize')
                      ->will($this->returnValue(100));

        $expect = '<defs><style type="text/css"><![CDATA[' . PHP_EOL
                . '.testClass {stroke:#000000;stroke-width:8;fill:#ffffff;}' . PHP_EOL
                . ']]></style></defs>';

        $this->assertEquals($expect, $this->tileMock->getStyles($tileThemeMock));
    }

    public function testRenderReturnsTileSvg()
    {
        $offsetX = 50;
        $offsetY = 50;
        $rotation = 270;
        $tileSize = 150;
        $this->tileMock->setType('testInnerSvg');
        $this->tileMock->setRotation($rotation);

        $tileThemeMock = $this->getMockBuilder('Oneway\\TilePrints\\Tile\\TileTheme')
                              ->setMethods(array('getTileSize', 'getBackgroundColor', 'getStrokeColor'))
                              ->getMock();
        $tileThemeMock->expects($this->once())
                      ->method('getTileSize')
                      ->will($this->returnValue($tileSize));
        $tileThemeMock->expects($this->once())
                      ->method('getBackgroundColor')
                      ->will($this->returnValue('#000000'));
        $tileThemeMock->expects($this->once())
                      ->method('getStrokeColor')
                      ->will($this->returnValue('#ffffff'));

        $expected = '<g transform="translate(50, 50) rotate(270, 75, 75)" >' . PHP_EOL
                  . '<rect width="150" height="150" fill="#000000" stroke="#ffffff" stroke-width="0" />'
                  . PHP_EOL . 'INNERSVG' . PHP_EOL . '</g>' . PHP_EOL;
        $output = $this->tileMock->render($offsetX, $offsetY, $tileThemeMock);

        $this->assertSame($expected, $output);
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
