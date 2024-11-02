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

    public function showGamesList($gameList)
    {
        if (empty($gameList)) {
            \cli\line("Нет сохраненных игр.");
            return;
        }

        \cli\line("Список сохраненных игр:");
        foreach ($gameList as $game) {
            \cli\line(sprintf(
                "ID: %d | Дата: %s | Игрок: %s | Размер карты: %d | Мин: %d | Статус: %s",
                $game['id'],
                $game['date'],
                $game['player_name'],
                $game['size_map'],
                $game['mines'],
                $game['game_status']
            ));
        }
    }
}
