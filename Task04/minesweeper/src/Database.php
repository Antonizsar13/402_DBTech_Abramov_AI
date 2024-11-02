<?php

namespace Anton\Minesweeper;

class Database
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = new \PDO('sqlite:bin/minesweeper.db');
        $this->createTables();
    }

    private function createTables()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS games (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                date DATE,
                player_name TEXT,
                size_map INTEGER,
                mines INTEGER,
                map TEXT,
                game_status Text
        )");

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS moves (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            game_id INTEGER,
            move_number INTEGER,
            x INTEGER,
            y INTEGER,
            result TEXT,
            FOREIGN KEY (game_id) REFERENCES games(id)
        )");
    }

    public function saveGame($gameData)
    {
        $playerName = $gameData['player_name'];
        $sizeMap = $gameData['size_map'];
        $mines = $gameData['mines'];
        $map = $gameData['map'];
        $gameStatus = $gameData['game_status'];
        $moves = $gameData['moves'];

        $stmt = $this->pdo->prepare("INSERT OR IGNORE INTO games (date, player_name, size_map, mines, map, game_status) 
                                  VALUES (:date, :player_name, :size_map, :mines, :map, :game_status)");
        $stmt->execute([
            ':date' => date('Y-m-d H:i:s'),
            ':player_name' => $playerName,
            ':size_map' => $sizeMap,
            ':mines' => $mines,
            ':map' => json_encode($map),
            ':game_status' => $gameStatus
        ]);

        $id = $this->pdo->lastInsertId();

        $this->saveMoves($id, $moves);
    }

    private function saveMoves($id, $moves)
    {
        if ($id && !empty($moves)) {
            $stmt = $this->pdo->prepare("INSERT INTO moves (game_id, move_number, x, y, result) 
                                       VALUES (:game_id, :move_number, :x, :y, :result)");
            foreach ($moves as $move) {
                $stmt->execute([
                    ':game_id' => $id,
                    ':move_number' => $move['move_number'],
                    ':x' => $move['x'],
                    ':y' => $move['y'],
                    ':result' => $move['result']
                ]);
            }
        }
    }

    public function listGames()
    {
        $stmt = $this->pdo->query("SELECT * FROM games");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getGameById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM games WHERE id = ?");
        $stmt->execute([$id]);
        $game = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($game) {
            $stmtMoves = $this->pdo->prepare("SELECT * FROM moves WHERE game_id = ? ORDER BY move_number ASC");
            $stmtMoves->execute([$id]);
            $moves = $stmtMoves->fetchAll(\PDO::FETCH_ASSOC);

            $game['moves'] = $moves;
        }

        return $game;
    }
}
