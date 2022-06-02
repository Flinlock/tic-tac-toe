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
    public function it_has_an_optional_victor()
    {
        // $this->withoutExceptionHandling();
        
        $game = new Game();
        $game->save();
        $game->refresh();
        
        $this->assertNull($game->victor);
    
        $game->victor = 'x';
        $game->save();
        $game->refresh();

        $this->assertEquals('x', $game->victor);
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

    /** @test */
    public function it_knows_open_positions()
    {
        // $this->withoutExceptionHandling();
        
        $game = new Game();
        $game->save();
        
        $this->post($game->path() . '/move?location=0&value=x');
        $this->post($game->path() . '/move?location=1&value=o');
        $this->post($game->path() . '/move?location=2&value=x');

        $game->refresh();

        $this->assertEquals(['3', '4', '5', '6', '7', '8'], $game->getOpenPositions());
    }

    /** @test */
    public function it_can_recommend_a_defensive_computer_move()
    {
        // $this->withoutExceptionHandling();
        $game = new Game();
        $game->save();
        
        $this->post($game->path() . '/move?location=0&value=x');
        $game->refresh();
        
        $this->assertFalse($game->recommendComputerMove());

        $this->post($game->path() . '/move?location=1&value=x');
        $game->refresh();

        $this->assertEquals(2, $game->recommendComputerMove());
    }

    /** @test */
    public function it_has_a_path()
    {
        // $this->withoutExceptionHandling();
        
        $game = new Game();
        $game->save();
        $game->refresh();

        $this->assertEquals('/games/' . $game->id, $game->path());
    }

    /** @test */
    public function it_can_update_game_status()
    {
        // $this->withoutExceptionHandling();
        
        $game = new Game();
        $game->save();
        $game->refresh();

        $game->updateStatus();

        $this->assertEquals('active', $game->status);
    }
}
