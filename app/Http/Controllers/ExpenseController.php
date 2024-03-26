<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /* $expenses = Auth::user()->expenses()->get(); */
        
        return response()->json(['status' => 'success', 'expenses' => $expenses], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'description' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        $expense = new Expense;
        $expense->description = $validatedData['description'];
        $expense->amount = $validatedData['amount'];
        $expense->user_id = Auth::id();
        $expense->save();

        return response()->json(['status' => 'success', 'expense' => $expense], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $expense = Auth::user()->expenses()->find($id);

        if (!$expense) {
            return response()->json(['status' => 'error', 'message' => 'Expense not found'], 404);
        }

        return response()->json(['status' => 'success', 'expense' => $expense], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'description' => 'string',
            'amount' => 'numeric',
        ]);

        $expense = Auth::user()->expenses()->find($id);

        if (!$expense) {
            return response()->json(['status' => 'error', 'message' => 'Expense not found'], 404);
        }

        if (isset($validatedData['description'])) {
            $expense->description = $validatedData['description'];
        }

        if (isset($validatedData['amount'])) {
            $expense->amount = $validatedData['amount'];
        }

        $expense->save();

        return response()->json(['status' => 'success', 'expense' => $expense], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $expense = Auth::user()->expenses()->find($id);

        if (!$expense) {
            return response()->json(['status' => 'error', 'message' => 'Expense not found'], 404);
        }

        $expense->delete();

        return response()->json(['status' => 'success', 'message' => 'Expense deleted successfully'], 200);
    }
}
