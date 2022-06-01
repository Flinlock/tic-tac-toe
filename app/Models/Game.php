<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    /**
     * All possible victory conditions
     */
    private $victoryConditions = [
        [0, 1, 2],
        [3, 4, 5],
        [6, 7, 8],
        [0, 3, 6],
        [1, 4, 7],
        [2, 5, 8],
        [0, 4, 8],
        [6, 4, 2]
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        /**
         * Create nine blank positions for this game instance
         */
        static::created(function ($game) {
            $game->positions()->createMany([
                ['location' => '0'],
                ['location' => '1'],
                ['location' => '2'],
                ['location' => '3'],
                ['location' => '4'],
                ['location' => '5'],
                ['location' => '6'],
                ['location' => '7'],
                ['location' => '8']
            ]);
        });
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    /**
     * Return an array of open positions that can be played in
     */
    public function getOpenPositions()
    {
        $openPositions = $this->positions()->whereNull('value')->get();

        return $openPositions->pluck('location')->toArray();
    }

    /**
     * Check to see if either victory conditions have been met
     * Or if all positions have been played in
     */
    public function updateStatus()
    {
        // First check for X victory since X goes first
        $this->checkConditions('x');
        $this->checkConditions('o');

        // See if all positions are filled
        if ($this->positions()->whereNull('value')->count() == 0) {
            $this->status = 'complete';
            $this->save();
        }
    }

    /**
     * Check the moves that have been played against all known victory conditions
     * If a player is victorious the game status and victor are updated accordingly
     */
    private function checkConditions($value)
    {
        $xPositions = $this->positions()->where('value', $value)->get();
        if ($xPositions->count() > 2) {
            $xPositions = $xPositions->pluck('location')->toArray();

            foreach ($this->victoryConditions as $condition) {
                // if all three condition positions are in the array of xPositions then X is victorious
                $diff = array_diff($condition, $xPositions);
                if (count($diff) == 0) {
                    $this->status = 'complete';
                    $this->victor = $value;
                    $this->save();
                }
            }
        }
    }

    public function path()
    {
        return '/games/' . $this->id;
    }
}
