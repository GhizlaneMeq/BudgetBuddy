<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
/**
 
* @OA\Info(
* title="Student API",
* version="1.0.0",
* description="API to manage students",
* )
*/
class ExpenseController extends Controller
{
    /**
     * Display a listing of the expenses.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Get(
     *     path="/api/expenses",
     *     tags={"expenses"},
     *     summary="Get all expenses",
     *     description="Retrieve a list of all expenses",
     *     security={{"sanctum": {}}},
     *     @OA\Response(response="200", description="List of expenses"),
     *     @OA\Response(response="404", description="No expense found")
     * )
     */
    public function index(Request $request)
    {
        $expenses = Expense::where('user_id', $request->user()->id)->get();
        return response()->json($expenses);
    }
    

    /**
     * Store a newly created expense in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Post(
     *     path="/api/expenses",
     *     tags={"expenses"},
     *     summary="Create a new expense",
     *     description="Create a new expense with provided name and age",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"description", "amount"},
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="amount", type="integer")
     *         )
     *     ),
     *     @OA\Response(response="201", description="expense created"),
     *     @OA\Response(response="400", description="Bad request")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        $expense = new Expense();
        $expense->user_id = $request->user()->id;
        $expense->description = $request->description;
        $expense->amount = $request->amount;
        $expense->save();

        return response()->json($expense, 201);
    }

    /**
     * Display the specified expense.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Get(
     *     path="/api/expenses/{id}",
     *     tags={"expenses"},
     *     summary="Get a expense by ID",
     *     description="Retrieve a expense by its ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the expense to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Expense found"),
     *     @OA\Response(response="404", description="Expense not found")
     * )
     */
    public function show(Request $request, $id)
    {
        try {
            $expense = Expense::findOrFail($id);

            if ($request->user()->cannot('view', $expense)) {
                abort(403);
            }

            return response()->json($expense);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Expense not found'], 404);
        }
    }


    /**
     * Update the specified expense in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'description' => 'required|string',
                'amount' => 'required|numeric',
            ]);

            $expense = Expense::findOrFail($id);

            if ($request->user()->cannot('update', $expense)) {
                abort(403);
            }

            $expense->description = $request->description;
            $expense->amount = $request->amount;
            $expense->save();

            return response()->json($expense);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Expense not found'], 404);
        }
    }

    /**
     * Remove the specified expense from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Delete(
     *     path="/api/expenses/{id}",
     *     tags={"expenses"},
     *     summary="Delete an expense",
     *     description="Delete an expense by its ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the expense to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="204", description="Expense deleted"),
     *     @OA\Response(response="404", description="Expense not found")
     * )
     */
    public function destroy(Request $request, $id)
    {
        try {
            $expense = Expense::findOrFail($id);

            if ($request->user()->cannot('delete', $expense)) {
                abort(403);
            }

            $expense->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Expense not found'], 404);
        }
    }
}
