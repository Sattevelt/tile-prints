<?php
namespace Oneway\TilePrints\Test\Tile;

use Oneway\TilePrints\Tile\AbstractTile;

class TileMock extends AbstractTile
{
    private $standardExits = 0b0;

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
}
