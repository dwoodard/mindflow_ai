<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_store_a_task()
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'This is a test task',
            'status' => 'pending',
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
                 ->assertJson($taskData);

        $this->assertDatabaseHas('tasks', $taskData);
    }

    /** @test */
    public function it_can_show_a_task()
    {
        $task = Task::factory()->create();

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
                 ->assertJson($task->toArray());
    }

    /** @test */
    public function it_can_list_tasks()
    {
        $tasks = Task::factory()->count(5)->create();

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'title', 'description', 'status', 'created_at', 'updated_at']
                     ],
                     'links',
                     'meta'
                 ]);
    }

    /** @test */
    public function it_can_delete_a_task()
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
