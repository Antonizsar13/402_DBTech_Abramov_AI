<?php

namespace Anton\Minesweeper;

class View
{
    public function startScreen()
    {
        \cli\line("Welcome to Minesweeper!");
    }

    public function showMap($gameMap)
    {
        $size = count($gameMap);
        \cli\line("  " . implode(" ", range(0, $size - 1)));
        foreach ($gameMap as $lineN => $line) {
            \cli\out($lineN . ' ');
            foreach ($line as $point) {
                \cli\out($point . ' ');
            }
            \cli\line();
        }
    }

    public function gameOver($result)
    {
        if ($result == 'lost') {
            \cli\line('Game Over! Вы подорвались. :(');
        } else {
            \cli\line('Поздравляем! Победа!');
        }
    }

    public function promptCoordinates()
    {
        return trim(\cli\prompt("Введите координаты (x, y):"));
    }

    public function invalidInput()
    {
        \cli\line("Неправильный формат. Введите в формате 'x, y'.");
    }

    public function invalidCoordinates()
    {
        \cli\line("Неправильные координаты!");
    }
}
