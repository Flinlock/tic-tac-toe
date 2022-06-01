<?php

namespace Tests\Unit;

use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_a_default_status_of_active()
    {
        $this->withoutExceptionHandling();
        $game = new Game();
        $game->save();
        $game->refresh();
        
        $this->assertEquals('active', $game->status);
    }

    /** @test */
    public function it_has_nine_positions()
    {
        // $this->withoutExceptionHandling();

        $game = new Game();
        $game->save();
        $game->refresh();

        $this->assertInstanceOf('App\Models\Position', $game->positions->first());
        $this->assertCount(9, $game->positions);
    }
}
