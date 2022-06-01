<?php

namespace Tests\Feature;

use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GameplayTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function new_moves_can_be_posted()
    {
        $this->withoutExceptionHandling();
        
        $game = new Game();
        $game->save();
        $game->refresh();

        $this->post($game->path() . '/move?location=0&value=x')
            ->assertStatus(200);

        $this->post($game->path() . '/move?location=1&value=x')
            ->assertStatus(200);
        
        $position = $game->positions()->where('location', 0)->first();
        $this->assertEquals('x', $position->value);

        $position = $game->positions()->where('location', 1)->first();
        $this->assertEquals('x', $position->value);
    }

    /** @test */
    public function winning_moves_trigger_complete_status_with_victor()
    {
        $this->withoutExceptionHandling();
        $game = new Game();
        $game->save();
        
        $this->post($game->path() . '/move?location=0&value=x');
        $this->post($game->path() . '/move?location=1&value=x');
        $this->post($game->path() . '/move?location=2&value=x');

        $game->updateStatus();
        $game->refresh();

        $this->assertEquals('complete', $game->status);
        $this->assertEquals('x', $game->victor);

        $game = new Game();
        $game->save();
        
        $this->post($game->path() . '/move?location=3&value=x');
        $this->post($game->path() . '/move?location=4&value=x');
        $this->post($game->path() . '/move?location=5&value=x');

        $game->updateStatus();
        $game->refresh();

        $this->assertEquals('complete', $game->status);
        $this->assertEquals('x', $game->victor);

        $game = new Game();
        $game->save();
        
        $this->post($game->path() . '/move?location=6&value=x');
        $this->post($game->path() . '/move?location=7&value=x');
        $this->post($game->path() . '/move?location=8&value=x');

        $game->updateStatus();
        $game->refresh();

        $this->assertEquals('complete', $game->status);
        $this->assertEquals('x', $game->victor);

        $game = new Game();
        $game->save();
        
        $this->post($game->path() . '/move?location=6&value=x');
        $this->post($game->path() . '/move?location=7&value=x');
        $this->post($game->path() . '/move?location=8&value=x');

        $game->updateStatus();
        $game->refresh();

        $this->assertEquals('complete', $game->status);
        $this->assertEquals('x', $game->victor);

        $game = new Game();
        $game->save();
        
        $this->post($game->path() . '/move?location=0&value=x');
        $this->post($game->path() . '/move?location=3&value=x');
        $this->post($game->path() . '/move?location=6&value=x');

        $game->updateStatus();
        $game->refresh();

        $this->assertEquals('complete', $game->status);
        $this->assertEquals('x', $game->victor);

        $game = new Game();
        $game->save();
        
        $this->post($game->path() . '/move?location=1&value=x');
        $this->post($game->path() . '/move?location=4&value=x');
        $this->post($game->path() . '/move?location=7&value=x');

        $game->updateStatus();
        $game->refresh();

        $this->assertEquals('complete', $game->status);
        $this->assertEquals('x', $game->victor);

        $game = new Game();
        $game->save();
        
        $this->post($game->path() . '/move?location=2&value=x');
        $this->post($game->path() . '/move?location=5&value=x');
        $this->post($game->path() . '/move?location=8&value=x');

        $game->updateStatus();
        $game->refresh();

        $this->assertEquals('complete', $game->status);
        $this->assertEquals('x', $game->victor);

        $game = new Game();
        $game->save();
        
        $this->post($game->path() . '/move?location=6&value=x');
        $this->post($game->path() . '/move?location=4&value=x');
        $this->post($game->path() . '/move?location=2&value=x');

        $game->updateStatus();
        $game->refresh();

        $this->assertEquals('complete', $game->status);
        $this->assertEquals('x', $game->victor);

        $game = new Game();
        $game->save();
        
        $this->post($game->path() . '/move?location=0&value=x');
        $this->post($game->path() . '/move?location=4&value=x');
        $this->post($game->path() . '/move?location=8&value=x');

        $game->updateStatus();
        $game->refresh();

        $this->assertEquals('complete', $game->status);
        $this->assertEquals('x', $game->victor);
    }

    /** @test */
    public function stalemates_trigger_complete_status_with_no_victor()
    {
        // $this->withoutExceptionHandling();
        $game = new Game();
        $game->save();
        
        $this->post($game->path() . '/move?location=0&value=x');
        $this->post($game->path() . '/move?location=1&value=o');
        $this->post($game->path() . '/move?location=2&value=x');
        $this->post($game->path() . '/move?location=3&value=x');
        $this->post($game->path() . '/move?location=4&value=o');
        $this->post($game->path() . '/move?location=5&value=x');
        $this->post($game->path() . '/move?location=6&value=o');
        $this->post($game->path() . '/move?location=7&value=x');
        $this->post($game->path() . '/move?location=8&value=o');

        $game->updateStatus();
        $game->refresh();

        $this->assertEquals('complete', $game->status);
        $this->assertNull($game->victor);
    }

    /** @test */
    public function new_games_can_be_requested()
    {
        $this->withoutExceptionHandling();
        $response = $this->get('/games/new')
            ->assertStatus(200);

        $this->assertStringContainsString('id', $response->getContent());
    }
}
