<?php
namespace Oneway\TilePrints\Tile;

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

    /** @var TileInterface[]  */
    private $tiles = array();

    const DIRECTION_TOP = 1;
    const DIRECTION_RIGHT = 2;
    const DIRECTION_BOTTOM = 4;
    const DIRECTION_LEFT = 8;

    public function __construct($cols = 4, $rows = 4)
    {
        $this->setRows($rows);
        $this->setCols($cols);
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
    public function generate()
    {
        $tiles = array();
        $rows = $this->getRows();
        $cols = $this->getCols();
        $allExits = new stdClass();
        $totalTiles = $rows * $cols;

        for ($i = 1; $i <= $totalTiles; $i++) {
            $allExits->possible = 0;
            $allExits->forbidden = 0;
            $allExits->required= 0;

            // What is above
            if ($i - $cols <= 0) {
                // Top row can not have exit at top
                $allExits->forbidden += self::DIRECTION_TOP;
            } else {
                $tile = isset($tiles[$i - $cols]) ? $tiles[$i - $cols] : null;
                $this->getAllExitTypes(self::DIRECTION_TOP, $allExits, $tile);
            }

            // What is to the right
            if ($i % $cols <= 0) {
                // Top row can not have exit at top
                $allExits->forbidden += self::DIRECTION_RIGHT;
            } else {
                $tile = isset($tiles[$i + 1]) ? $tiles[$i + 1] : null;
                $this->getAllExitTypes(self::DIRECTION_RIGHT, $allExits, $tile);
            }

            // What is below
            if ($i + $cols > $totalTiles) {
                // Top row can not have exit at top
                $allExits->forbidden += self::DIRECTION_BOTTOM;
            } else {
                $tile = isset($tiles[$i + $cols]) ? $tiles[$i + $cols] : null;
                $this->getAllExitTypes(self::DIRECTION_BOTTOM, $allExits, $tile);
            }

            // What is to the left
            if (($i - 1) % $cols == 0) {
                // Top row can not have exit at top
                $allExits->forbidden += self::DIRECTION_LEFT;
            } else {
                $tile = isset($tiles[$i - 1]) ? $tiles[$i - 1] : null;
                $this->getAllExitTypes(self::DIRECTION_LEFT, $allExits, $tile);
            }

            echo "**********************************\n";
            echo "Tile $i has the following exit types: \n";
            echo '- possibleExits: ' . sprintf('%04d', decbin($allExits->possible)) . "\n";
            echo '- requiredExits: ' . sprintf('%04d', decbin($allExits->required)) . "\n";
            echo '- forbiddenExits: ' . sprintf('%04d', decbin($allExits->forbidden)) . "\n";

            $eligibleTiles = $this->getEligibleTileTypes(
                $allExits->possible,
                $allExits->forbidden,
                $allExits->required
            );
            $selectedTile = $eligibleTiles[array_rand($eligibleTiles, 1)];

            echo "Selected the following tile:\n- ";
            echo $selectedTile['type'] . ' | ';
            echo $selectedTile['rotation'] . ' | ';
            echo sprintf('%04d', decbin($selectedTile['exits'])) . "\n";

            $tileObj = TileFactory::getInstance('doubleCurvy');
            $tileObj->setType($selectedTile['type']);
            $tileObj->setRotation($selectedTile['rotation']);
            $tiles[$i] = $tileObj;
        }

        $this->tiles = $tiles;
    }

    private function getAllExitTypes(
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
     * @param int $possibleExits
     * @param int $forbiddenExits
     * @param int $requiredExits
     * @return array[] Tiles that fit all requirements given in the 3 exit ints.
     *                 array('type' =>, 'rotation' =>, 'exits' =>);
     */
    public function getEligibleTileTypes($possibleExits, $forbiddenExits, $requiredExits)
    {
        $eligibleTiles = array();
        $allTiles = array( // All possible combo's. Filtered in giant if statement below.
            array('type' => 'zero', 'rotation' => 0, 'exits' => 0b0000),
            array('type' => 'one', 'rotation' => 0, 'exits' => 0b0001),
            array('type' => 'one', 'rotation' => 90, 'exits' => 0b0010),
            array('type' => 'one', 'rotation' => 180, 'exits' => 0b0100),
            array('type' => 'one', 'rotation' => 270, 'exits' => 0b1000),
            array('type' => 'twoStraight', 'rotation' => 0, 'exits' => 0b0101),
            array('type' => 'twoStraight', 'rotation' => 90, 'exits' => 0b1010),
            array('type' => 'twoAngle', 'rotation' => 0, 'exits' => 0b0011),
            array('type' => 'twoAngle', 'rotation' => 90, 'exits' => 0b0110),
            array('type' => 'twoAngle', 'rotation' => 180, 'exits' => 0b1100),
            array('type' => 'twoAngle', 'rotation' => 270, 'exits' => 0b1001),
            array('type' => 'three', 'rotation' => 0, 'exits' => 0b0111),
            array('type' => 'three', 'rotation' => 90, 'exits' => 0b1110),
            array('type' => 'three', 'rotation' => 180, 'exits' => 0b1101),
            array('type' => 'three', 'rotation' => 270, 'exits' => 0b1011),
            array('type' => 'four', 'rotation' => 0, 'exits' => 0b1111)
        );
        echo "The following tiles are rejected: \n";
        foreach ($allTiles as $tile) {
            if (// Has forbidden exits?
                ($tile['exits'] & $forbiddenExits)
            ) {
                echo ' - ' . $tile['type'] . ' | ' . $tile['rotation'] . ' : forbidden' . "\n";
                continue;
            }
            if (// Has exit         of direction...           that is not part of $possibleExits
                ($tile['exits'] & self::DIRECTION_TOP && ! ($possibleExits & self::DIRECTION_TOP)) ||
                ($tile['exits'] & self::DIRECTION_RIGHT && ! ($possibleExits & self::DIRECTION_RIGHT)) ||
                ($tile['exits'] & self::DIRECTION_BOTTOM && ! ($possibleExits & self::DIRECTION_BOTTOM)) ||
                ($tile['exits'] & self::DIRECTION_LEFT && ! ($possibleExits & self::DIRECTION_LEFT))
            ) {
                echo ' - ' . $tile['type'] . ' | ' . $tile['rotation'] . ' : possible' . "\n";
                continue;
            }
            if (// Required exit...  of direction...           is not available for current tile
                ($requiredExits & self::DIRECTION_TOP && ! ($tile['exits'] & self::DIRECTION_TOP)) ||
                ($requiredExits & self::DIRECTION_RIGHT && ! ($tile['exits'] & self::DIRECTION_RIGHT)) ||
                ($requiredExits & self::DIRECTION_BOTTOM && ! ($tile['exits'] & self::DIRECTION_BOTTOM)) ||
                ($requiredExits & self::DIRECTION_LEFT && ! ($tile['exits'] & self::DIRECTION_LEFT))
            ) {
                echo ' - ' . $tile['type'] . ' | ' . $tile['rotation'] . ' : required' . "\n";
                continue;
            }

            $eligibleTiles[] = $tile;
        }

        return $eligibleTiles;
    }

    private function getRandomRotation()
    {
        return rand(1, 4) * 90;
    }
}
