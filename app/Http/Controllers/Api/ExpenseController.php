<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpenseController extends BaseController
{
    protected $fcm;

    public function __construct(FirebaseNotificationService $fcm)
    {
        $this->fcm = $fcm;
    }

    public function index()
    {
        $expenses = Expense::with(['category', 'creator'])->get();
        return $this->sendResponse(ExpenseResource::collection($expenses), 'Expenses retrieved successfully.');
    }

    public function store(Request $request)
    {
        $validator = $this->validateRequest($request, [
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();
        try {
            $expense = Expense::create([
                'category_id' => $request->category_id,
                'amount' => $request->amount,
                'quantity' => $request->quantity,
                'date' => $request->date,
                'description' => $request->description,
                'created_by' => auth()->id(),
            ]);

            $user = auth()->user();


            if ($user && $user->device_token) {
                $this->fcm->sendToDevice(
                    $user->device_token,
                    'Expense Created',
                    'A new expense has been added.',
                    [
                        'expense_id' => $expense->id,
                        'amount' => $expense->amount,
                    ]
                );
            }


            DB::commit();
            return $this->sendResponse(new ExpenseResource($expense), 'Expense created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Expense Store Error: ' . $e->getMessage());
            return $this->sendError('Failed to create expense.' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $expense = Expense::with(['category', 'creator'])->find($id);

        if (!$expense) {
            return $this->sendError('Expense not found.');
        }

        return $this->sendResponse(new ExpenseResource($expense), 'Expense retrieved successfully.');
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::find($id);


        if (!$expense) {
            return $this->sendError('Expense not found.');
        }

        $validator = $this->validateRequest($request, [
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();
        try {
            $expense->update($request->only('category_id', 'amount', 'quantity', 'date', 'description'));
            DB::commit();

            return $this->sendResponse(new ExpenseResource($expense), 'Expense updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Expense Update Error: ' . $e->getMessage());
            return $this->sendError('Failed to update expense.');
        }
    }

    public function destroy($id)
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return $this->sendError('Expense not found.');
        }

        DB::beginTransaction();
        try {
            $expense->delete();
            DB::commit();

            return $this->sendResponse([], 'Expense deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Expense Delete Error: ' . $e->getMessage());
            return $this->sendError('Failed to delete expense.');
        }
    }
}

