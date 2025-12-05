<?php

namespace Tests\Feature;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoalManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_their_goals()
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create(['title' => 'Test Goal']);

        $response = $this->actingAs($user)->get(route('goals.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Goal');
    }

    /** @test */
    public function user_cannot_view_other_users_goals()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $goal = Goal::factory()->for($user1)->create();

        $response = $this->actingAs($user2)->get(route('goals.show', $goal));

        $response->assertForbidden();
    }

    /** @test */
    public function user_can_create_a_goal()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('goals.store'), [
            'title' => 'Read 50 Pages',
            'description' => 'Daily reading habit',
            'target_value' => 50,
            'unit' => 'pages',
            'category' => 'learning',
            'priority' => 'high',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('goals', [
            'user_id' => $user->id,
            'title' => 'Read 50 Pages',
            'target_value' => 50,
            'unit' => 'pages',
        ]);
    }

    /** @test */
    public function goal_creation_validates_required_fields()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('goals.store'), [
            'title' => '', // Empty title
            'target_value' => -5, // Invalid target
        ]);

        $response->assertSessionHasErrors(['title', 'target_value', 'unit']);
    }

    /** @test */
    public function user_can_update_their_goal()
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create(['title' => 'Old Title']);

        $response = $this->actingAs($user)->put(route('goals.update', $goal), [
            'title' => 'Updated Title',
            'current_value' => 10,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('goals', [
            'id' => $goal->id,
            'title' => 'Updated Title',
            'current_value' => 10,
        ]);
    }

    /** @test */
    public function user_cannot_update_other_users_goal()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $goal = Goal::factory()->for($user1)->create();

        $response = $this->actingAs($user2)->put(route('goals.update', $goal), [
            'title' => 'Hacked Title',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('goals', [
            'title' => 'Hacked Title',
        ]);
    }

    /** @test */
    public function goal_validates_current_value_does_not_exceed_target()
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create([
            'target_value' => 100,
            'current_value' => 50,
        ]);

        $response = $this->actingAs($user)->put(route('goals.update', $goal), [
            'current_value' => 150, // Exceeds target
        ]);

        $response->assertSessionHasErrors('current_value');
    }

    /** @test */
    public function completing_a_goal_updates_streak()
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create([
            'status' => 'active',
            'target_value' => 100,
            'current_value' => 99,
        ]);

        $response = $this->actingAs($user)->post(route('goals.complete', $goal));

        $response->assertRedirect();
        $goal->refresh();
        
        $this->assertEquals('completed', $goal->status);
        $this->assertNotNull($goal->completed_at);
        $this->assertEquals(100, $goal->current_value);
        
        // Check streak was created/updated
        $this->assertEquals(1, $user->fresh()->streak->current_streak);
    }

    /** @test */
    public function user_can_reopen_completed_goal()
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('goals.reopen', $goal));

        $response->assertRedirect();
        $goal->refresh();
        
        $this->assertEquals('active', $goal->status);
        $this->assertNull($goal->completed_at);
    }

    /** @test */
    public function user_can_delete_their_goal()
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete(route('goals.destroy', $goal));

        $response->assertRedirect();
        $this->assertSoftDeleted('goals', ['id' => $goal->id]);
    }

    /** @test */
    public function user_can_increment_goal_progress()
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create([
            'current_value' => 10,
            'target_value' => 100,
        ]);

        $response = $this->actingAs($user)->post(route('goals.increment', $goal), [
            'amount' => 5,
        ]);

        $response->assertRedirect();
        $goal->refresh();
        
        $this->assertEquals(15, $goal->current_value);
    }

    /** @test */
    public function goal_auto_completes_when_target_reached()
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create([
            'current_value' => 95,
            'target_value' => 100,
            'status' => 'active',
        ]);

        $goal->incrementProgress(5);

        $this->assertEquals('completed', $goal->status);
        $this->assertNotNull($goal->completed_at);
    }

    /** @test */
    public function guest_cannot_access_goals()
    {
        $response = $this->get(route('goals.index'));
        $response->assertRedirect(route('login'));

        $response = $this->post(route('goals.store'), []);
        $response->assertRedirect(route('login'));
    }
}