<?php

namespace Tests\Unit;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function testExpenseCreation(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->postJson('/api/expenses', [
                             'description' => 'Test Expense',
                             'amount' => 50.00,
                         ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('expenses', [
            'description' => 'Test Expense',
            'amount' => 50.00,
            'user_id' => $user->id,
        ]);
    }

    /**
     * A basic test example.
     */
    public function testExpenseUpdate(): void
    {
        $user = User::factory()->create();
        $expense = Expense::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
                         ->putJson('/api/expenses/'.$expense->id, [
                             'description' => 'Updated Expense',
                             'amount' => 100.00,
                         ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'description' => 'Updated Expense',
            'amount' => 100.00,
        ]);
    }

    /**
     * A basic test example.
     */
    public function testExpenseDeletion(): void
    {
        $user = User::factory()->create();
        $expense = Expense::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
                         ->deleteJson('/api/expenses/'.$expense->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }

    /**
     * Test expense creation authorization.
     */
    public function testExpenseCreateAuthorization(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/expenses', [
                'description' => 'Test Expense',
                'amount' => 50.00,
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('expenses', [
            'description' => 'Test Expense',
            'amount' => 50.00,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test expense update authorization.
     */
    public function testExpenseUpdateAuthorization(): void
    {
        $user = User::factory()->create();
        $expense = Expense::factory()->create(['user_id' => $user->id]);
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)
            ->putJson("/api/expenses/{$expense->id}", [
                'description' => 'Updated Expense',
                'amount' => 100.00,
            ]);

        $response->assertStatus(401);
    }





    /**
     * Test expense deletion authorization.
     */
    public function testExpenseDeleteAuthorization(): void
    {
        $user = User::factory()->create();
        $expense = Expense::factory()->create(['user_id' => $user->id]);
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)
            ->deleteJson("/api/expenses/{$expense->id}");

        // Expecting a 401 status code instead of 403
        $response->assertStatus(401);
    }
}
