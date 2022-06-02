<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

class GameplayController extends Controller
{
    /**
     * Record a new gameplay move and return the game status as an array
     */
    public function move(Game $game)
    {
        $location = request('location');
        $value = request('value');

        $position = $game->positions()->where('location', $location)->first();
        $position->value = $value;
        $position->save();

        $game->updateStatus();

        return [
            'status' => $game->status,
            'victor' => $game->victor ?? false,
            'openPositions' => $game->getOpenPositions()
        ];
    }

    /**
     * Start a new game
     */
    public function new()
    {
        // First clear out any unfinished games
        $incomplete = Game::where('status', 'active')->get();
        foreach ($incomplete as $i) {
            $i->delete();
        }
        $game = new Game();
        $game->save();
        $game->refresh();

        return $game->toArray();
    }
}
