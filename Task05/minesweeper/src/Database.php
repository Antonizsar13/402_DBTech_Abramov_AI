<?php

namespace Anton\Minesweeper;

use RedBeanPHP\R as R;

class Database
{
    public function __construct()
    {
        R::setup('sqlite:bin/minesweeper.db');
    }

    public function saveGame($gameData)
    {
        $game = R::dispense('game');
        $game->date = date('Y-m-d H:i:s');
        $game->player_name = $gameData['player_name'];
        $game->size_map = $gameData['size_map'];
        $game->mines = $gameData['mines'];
        $game->map = json_encode($gameData['map']);
        $game->game_status = $gameData['game_status'];

        R::store($game);

        $this->saveMoves($game->id, $gameData['moves']);
    }

    private function saveMoves($gameId, $moves)
    {
        if (!empty($moves)) {
            foreach ($moves as $move) {
                $moveBean = R::dispense('move');
                $moveBean->game_id = $gameId;
                $moveBean->move_number = $move['move_number'];
                $moveBean->x = $move['x'];
                $moveBean->y = $move['y'];
                $moveBean->result = $move['result'];
                R::store($moveBean);
            }
        }
    }

    public function listGames()
    {
        return R::findAll('game');
    }

    public function getGameById($id)
    {
        $game = R::load('game', $id);

        if (!empty($game)) {
            $moves = R::find('move', 'game_id = ?', [$id]);
            $game->moves = $moves;
        }

        return $game;
    }
}
