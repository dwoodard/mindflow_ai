<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Task;

class TaskRoutesTest extends TestCase
{
    use RefreshDatabase;

    // Test for TaskController store route
    public function test_tasks_store_route()
    {
        $response = $this->post('/api/tasks');
        $response->assertStatus(302);
        $response = $this->followRedirects($response);
        $response->assertStatus(200);
    }

    // Test for TaskController show route
    public function test_tasks_show_route()
    {
        $task = Task::factory()->create();
        $response = $this->get('/api/tasks/' . $task->id);
        $response->assertStatus(200);
    }

    // Test for TaskController index route
    public function test_tasks_index_route()
    {
        $response = $this->get('/api/tasks');
        $response->assertStatus(200);
    }

    // Test for TaskController destroy route
    public function test_tasks_destroy_route()
    {
        $task = Task::factory()->create();
        $response = $this->delete('/api/tasks/' . $task->id);
        $response->assertStatus(204);
    }

    // Test for TaskController update route
    public function test_tasks_update_route()
    {
        $task = Task::factory()->create();
        $response = $this->put('/api/tasks/' . $task->id, [
            'name' => 'Updated Task'
        ]);
        $response->assertStatus(200);
    }
}
