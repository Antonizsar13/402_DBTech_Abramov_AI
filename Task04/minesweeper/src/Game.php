<?php

namespace Anton\Minesweeper;

class Game
{
    private $size;
    private $mines;
    private $map;
    private $gameMap;
    private $gameOver;

    public function __construct(int $size, int $mines)
    {
        $this->size = $size;
        $this->mines = $mines;
        $this->gameOver = false;

        $this->createMap();
    }

    private function createMap()
    {
        // Сначала создаем пустую карту
        $this->map = array_fill(0, $this->size, array_fill(0, $this->size, 0));
        $this->gameMap = array_fill(0, $this->size, array_fill(0, $this->size, '?'));
        $this->placeMines();
    }

    private function placeMines()
    {
        $placedMines = 0;
        while ($placedMines < $this->mines) {
            $y = rand(0, $this->size - 1);
            $x = rand(0, $this->size - 1);
            if ($this->map[$y][$x] !== '*') {
                $this->map[$y][$x] = '*';
                $placedMines++;
                $this->incrementNeighbors($x, $y);
            }
        }
    }

    private function incrementNeighbors($x, $y)
    {
        $directions = [[-1, 0], [1, 0], [0, -1], [0, 1], [-1, -1], [-1, 1], [1, -1], [1, 1]];
        foreach ($directions as [$dLine, $dColumn]) {
            $newLine = $y + $dLine;
            $newColumn = $x + $dColumn;
            if ($newLine >= 0 && $newLine < $this->size && $newColumn >= 0 && $newColumn < $this->size) {
                if ($this->map[$newLine][$newColumn] !== '*') {
                    $this->map[$newLine][$newColumn]++;
                }
            }
        }
    }

    public function play(int $x, int $y)
    {
        $this->gameMap[$y][$x] = $this->map[$y][$x];
        if ($this->map[$y][$x] == '*') {
            $this->gameOver = true;
            return 'lost';
        }

        $this->gameMap[$y][$x] = $this->map[$y][$x];

        if ($this->map[$y][$x] == 0) {
            $this->openZeroCells($x, $y);
        }

        return $this->checkWin() ? 'won' : null;
    }

    public function isGameOver(): bool
    {
        return $this->gameOver;
    }

    public function getGameMap()
    {
        return $this->gameMap;
    }

    public function getSize()
    {
        return $this->size;
    }

    private function openZeroCells($x, $y)
    {
        $directions = [[-1, 0], [1, 0], [0, -1], [0, 1], [-1, -1], [-1, 1], [1, -1], [1, 1]];
        foreach ($directions as [$dLine, $dColumn]) {
            $newLine = $y + $dLine;
            $newColumn = $x + $dColumn;

            if (isset($this->map[$newLine][$newColumn]) && $this->gameMap[$newLine][$newColumn] == '?') {
                $this->gameMap[$newLine][$newColumn] = $this->map[$newLine][$newColumn];
                if ($this->map[$newLine][$newColumn] == 0) {
                    $this->openZeroCells($newColumn, $newLine);
                }
            }
        }
    }

    private function checkWin(): bool
    {
        foreach ($this->gameMap as $line => $points) {
            foreach ($points as $column => $point) {
                if ($point == '?' && $this->map[$line][$column] != '*') {
                    return false;
                }
            }
        }
        return true;
    }

    public function getMap()
    {
        return $this->map;
    }

    public function setMap($map)
    {
        $this->map = $map;
    }

    public function getMines()
    {
        return $this->mines;
    }
}
