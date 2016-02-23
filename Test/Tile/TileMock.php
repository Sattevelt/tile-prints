<?php
namespace Oneway\TilePrints\Test\Tile;

use Oneway\TilePrints\Tile\AbstractTile;

class TileMock extends AbstractTile
{
    private $standardExits = 0b0;

    protected $styles = array(
        '.testClass' => array(
            'stroke' => '#color#',
            'stroke-width' => 8,
            'fill' => '#bgcolor#'
        )
    );

    /**
     * @param $type
     * @return int
     */
    public function getStandardExits($type)
    {
        return $this->standardExits;
    }

    public function setStandardExits($standardExits)
    {
        $this->standardExits = $standardExits;
    }

    public function getTypes()
    {
        // TODO: Implement getTypes() method.
    }

    public function renderTestInnerSvg()
    {
        return 'INNERSVG';
    }
}
