<?php

namespace Anton\Minesweeper;

class Controller
{
    private $game;
    private $view;

    public function __construct(Game $game, View $view)
    {
        $this->game = $game;
        $this->view = $view;
    }

    public function startGame($saveToDatabase = false)
    {
        $this->view->startScreen();
        if (!$saveToDatabase) {
            \cli\line("Примечание: Игра пока не сохраняется в базе данных.");
        }
        while (!$this->game->isGameOver()) {
            $this->view->showMap($this->game->getGameMap());

            $input = $this->view->promptCoordinates();
            if (strpos($input, ',') === false) {
                $this->view->invalidInput();
                continue;
            }

            list($x, $y) = explode(',', $input);
            $x = (int)trim($x);
            $y = (int)trim($y);

            if ($x < 0 || $x >= $this->game->getSize() || $y < 0 || $y >= $this->game->getSize()) {
                $this->view->invalidCoordinates();
                continue;
            }

            $result = $this->game->play($x, $y);
            if ($result) {
                $this->view->gameOver($result);
                break;
            }
        }
    }
}
