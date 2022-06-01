<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Create the positions for this game instance
        static::created(function ($game) {
            $game->positions()->createMany([
                ['location' => 0],
                ['location' => 1],
                ['location' => 2],
                ['location' => 3],
                ['location' => 4],
                ['location' => 5],
                ['location' => 6],
                ['location' => 7],
                ['location' => 8]
            ]);
        });
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }
}
