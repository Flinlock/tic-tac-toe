<?php

namespace Tests\Unit;

use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PositionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_a_location()
    {
        // $this->withoutExceptionHandling();
        $position = new Position([
            'game_id' => 1,
            'location' => 1,
        ]);

        $position->save();
        $position->refresh();

        $this->assertEquals(1, $position->location);
    }

    /** @test */
    public function it_has_a_starting_value_of_null()
    {
        // $this->withoutExceptionHandling();
        
        $position = new Position([
            'game_id' => 1,
            'location' => 1,
        ]);

        $position->save();
        $position->refresh();

        $this->assertNull($position->value);
    }
}
