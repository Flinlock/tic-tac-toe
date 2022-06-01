<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

class GameplayController extends Controller
{
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

    public function new()
    {
        $game = new Game();
        $game->save();
        $game->refresh();

        return $game->toArray();
    }
}
