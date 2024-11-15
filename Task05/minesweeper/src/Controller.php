<?php

namespace Anton\Minesweeper;

class Controller
{
    private $game;
    private $view;
    private $db;

    public function __construct(Game $game, View $view, Database $db = null)
    {
        $this->game = $game;
        $this->view = $view;
        $this->db = $db;
    }

    public function startGame($nameUser = 'NoName')
    {
        $move = 0;
        $this->view->startScreen();
        if (!$this->db) {
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
                    'player_name' => $nameUser,
                    'size_map' => $this->game->getSize(),
                    'mines' => $this->game->getMines(),
                    'map' => $this->game->getMap(),
                    'game_status' => $result == 'lost' ? 'Проиграл' : ($result == 'won' ? 'Победа' : 'Не закончена'),
                    'moves' => $moves
                ];

                $this->db->saveGame($gameData);

                $this->view->gameOver($result);
                break;
            }
        }
    }

    public function replayGame($gameData)
    {
        $this->game->setMap(json_decode($gameData['map']));

        $this->view->startScreen();
        $this->view->showMap($this->game->getGameMap());

        foreach ($gameData['moves'] as $move) {
            $x = $move['x'];
            $y = $move['y'];

            $result = $this->game->play($x, $y);
            $this->view->showMap($this->game->getGameMap());
            if ($result) {
                $this->view->gameOver($result);
                break;
            }
        }
    }
}
