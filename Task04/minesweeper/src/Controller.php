<?php

namespace Anton\Minesweeper;

class Controller
{
    private $game;
    private $view;
    private $db;

    public function __construct(Game $game, View $view, Database $db)
    {
        $this->game = $game;
        $this->view = $view;
        $this->db = $db;
    }

    public function startGame($saveToDatabase = false)
    {
        $move = 0;
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

            $move++;
            
            $result = $this->game->play($x, $y);

            $moves[] = [
                'move_number' => $move,
                'x' => $x,
                'y' => $y,
                'result' => $result == 'lost' ? 'Мина' : ($result == 'won' ? 'Победа' : 'Мин нет'),
            ];

            if ($result) {
                $gameData = [
                    'playername' => 'User',
                    'size_map' => $this->game->getSize(),
                    'mines' => $this->game->getMines(),
                    'map' => $this->game->getMap(),
                    'result' => $result == 'lost' ? 'Мина' : ($result == 'won' ? 'Победа' : 'Мин нет'),
                
                ];

                $this->db->saveGame($gameData);

                $this->view->gameOver($result);
                break;
            }
        }
    }
}
