<?php
namespace Oneway\TilePrints\Tile;

use Exception;
use stdClass;

/**
 * Class TileCanvas
 *
 * Responsible for generating a grid of tiles such that no tile exits are 'open' ie unconnected.
 * Will aggregate all tiles' render output and wrap them in a SVG container element.
 *
 * @package Oneway\TilePrints\Tile
 */
class TileCanvas
{
    /** @var int */
    private $rows = 4;

    /** @var int */
    private $cols = 4;

    /** @var TileFactory */
    private $tileFactory;

    /** @var TileInterface[]  */
    private $tiles = array();

    /** @var array */
    private $directions = array();

    const DIRECTION_TOP = 1;
    const DIRECTION_RIGHT = 2;
    const DIRECTION_BOTTOM = 4;
    const DIRECTION_LEFT = 8;

    public function __construct($cols = 4, $rows = 4, TileFactory $tileFactory)
    {
        $this->setRows($rows);
        $this->setCols($cols);
        $this->tileFactory = $tileFactory;
        $this->directions = array(
            self::DIRECTION_TOP,
            self::DIRECTION_RIGHT,
            self::DIRECTION_BOTTOM,
            self::DIRECTION_LEFT
        );
    }

    /**
     * @return int
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @param int $rows
     * @return TileCanvas
     */
    public function setRows($rows)
    {
        $this->rows = $rows;
        return $this;
    }

    /**
     * @return int
     */
    public function getCols()
    {
        return $this->cols;
    }

    /**
     * @param int $cols
     * @return TileCanvas
     */
    public function setCols($cols)
    {
        $this->cols = $cols;
        return $this;
    }

    /**
     * Generate tiles for a grid of dimensions set by $this->cols and $this->rows.
     * For each tile, we look up what exits are possible, forbidden and required.
     * All possible tile types and rotations are concidered and from all eligible ones a random tile is
     * selected.
     * The resulting grid should have no unconnected tile exits (in it's solved state).
     *
     * Although we are selecting tiles for a grid, the tiles are stored in a 1-dimensional array.
     * Working with 2-dimensional arrays to represent columns and rows always confuses me more than
     * the actual problem i'm trying to solve.
     *
     * This method is quite long and should be refactored once the logic is deemed stable enough.
     *
     * @throws \Exception
     */
    public function generate($style)
    {
        $rows = $this->getRows();
        $cols = $this->getCols();
        $allExits = new stdClass();
        $totalTiles = $rows * $cols;

        for ($i = 1; $i <= $totalTiles; $i++) {
            $allExits->possible = 0;
            $allExits->forbidden = 0;
            $allExits->required= 0;

            foreach ($this->directions as $direction) {
                if ($this->isTileIsAtBorder($i, $direction)) {
                    $allExits->forbidden += $direction;
                } else {
                    $tile = $this->getTileInDirection($i, $direction);
                    $this->getAllTypeExits($direction, $allExits, $tile);
                }
            }

            $eligibleTiles = $this->getEligibleTileTypes($allExits, $style);
            $selectedTile = $eligibleTiles[array_rand($eligibleTiles)];

            $this->tiles[$i] = $selectedTile;
        }
    }

    private function getAllTypeExits(
        $direction,
        $allExits,
        TileInterface $compareTile = null
    ) {
        // This is dirty! Bitshifting would be more suitable.
        $reverseDirection = ($direction <= self::DIRECTION_RIGHT)
                          ? $direction * 4
                          : $direction / 4;

        if ($compareTile == null) {
            // Tile above has not been defined
            $allExits->possible += $direction;
        } else {
            // Check tile above for required exits
            if ($compareTile->getExits() & $reverseDirection) {
                // Tile above has exit at bottom
                $allExits->required += $direction;
                $allExits->possible += $direction;
            } else {
                // Tile above has no exit at bottom
                $allExits->forbidden += $direction;
            }
        }
    }

    /**
     * Render the configured tiles as SVG with given theme.
     *
     * @param TileTheme $theme Valueobject that holds the graphical style in which the tiles are rendered.
     * @param bool $renderInSolvedState If true, the 'solved' state will be rendered.
     *                                  Otherwise a random rotion is used for each tile.
     * @return string   Generated SVG string
     */
    public function render(TileTheme $theme, $renderInSolvedState = false)
    {
        $cols = $this->getCols();
        $offset = 150;
        $svg = sprintf(
            '<svg width="%1$s" height="%2$s">',
            $this->getCols() * $theme->getTileSize() + $offset * 2,
            $this->getRows() * $theme->getTileSize() + $offset * 2
        );

        $svg .= $this->tiles[1]->getStyles($theme);
            /** @var TileInterface $tile */
        foreach ($this->tiles as $index => $tile) {
            $col = ($index - 1) % $cols;
            $row = floor(($index - 1) / $cols);

            if (! $renderInSolvedState) {
                $tile->setRotation($this->getRandomRotation());
            }
            $svg .= $tile->render(
                $col * $theme->getTileSize() + $offset,
                $row * $theme->getTileSize() + $offset,
                $theme
            );

        }
        $svg .= '</svg>';

        return $svg;
    }

    /**
     * From all possible tiles, return only the ones that fit with the given exits.
     * The exit ints are used as a 4 bit binairy number, each bit representing a direction.
     * A candidate tile should have exits that:
     * - Do not occur in $forbiddenExits
     * - Are all available in $possibleExits
     * - At least match all of the $requiredExits
     *
     * @param stdClass $exits
     * @param $style
     * @return \array[] Tiles that fit all requirements given in the 3 exit ints.
     *                 array('type' =>, 'rotation' =>, 'exits' =>);
     */
    public function getEligibleTileTypes($exits, $style)
    {
        $tile = $this->tileFactory->getInstance($style);
        $tileTypes = $tile->getTypes();
        $eligibleTiles = array();
        $this->directions = array(
            self::DIRECTION_TOP,
            self::DIRECTION_RIGHT,
            self::DIRECTION_BOTTOM,
            self::DIRECTION_LEFT
        );

        foreach ($tileTypes as $tileType) {
            for ($rotation = 0; $rotation < 360; $rotation += 90) {
                $tile->setType($tileType);
                $tile->setRotation($rotation);

                if ($this->isEligibleTile($exits, $tile)) {
                    $eligibleTiles[] = clone $tile;
                }
            }
        }

        return $eligibleTiles;
    }

    private function isEligibleTile($allExits, TileInterface $tile)
    {
        $tileExits = $tile->getExits();

        if ($tileExits & $allExits->forbidden) {
            return false;
        }

        foreach ($this->directions as $direction) {
            if (($tileExits & $direction && !($allExits->possible & $direction)) ||
                ($allExits->required & $direction && !($tileExits & $direction))
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $currentIndex
     * @param $direction
     * @return null|TileInterface
     * @throws Exception
     */
    private function getTileInDirection($currentIndex, $direction)
    {
        $cols = $this->getCols();

        switch ($direction) {
            case self::DIRECTION_TOP:
                $index = $currentIndex - $cols;
                break;
            case self::DIRECTION_RIGHT:
                $index = $currentIndex + 1;
                break;
            case self::DIRECTION_BOTTOM:
                $index = $currentIndex + $cols;
                break;
            case self::DIRECTION_LEFT:
                $index = $currentIndex - 1;
                break;
            default:
                throw new Exception('Illegal direction');
        }
        $tile = isset($this->tiles[$index]) ? $this->tiles[$index] : null;

        return $tile;
    }

    /**
     * @param $currentIndex
     * @param $direction
     * @return bool
     * @throws Exception
     */
    private function isTileIsAtBorder($currentIndex, $direction)
    {
        $cols = $this->getCols();
        $totalTiles = $cols * $this->getRows();

        switch ($direction) {
            case self::DIRECTION_TOP:
                $atBorder = ($currentIndex - $cols <= 0);
                break;
            case self::DIRECTION_RIGHT:
                $atBorder = ($currentIndex % $cols <= 0);
                break;
            case self::DIRECTION_BOTTOM:
                $atBorder = ($currentIndex + $cols > $totalTiles);
                break;
            case self::DIRECTION_LEFT:
                $atBorder = (($currentIndex - 1) % $cols == 0);
                break;
            default:
                throw new Exception('Illegal direction');
        }

        return $atBorder;
    }

    private function getRandomRotation()
    {
        return rand(1, 4) * 90;
    }
}
