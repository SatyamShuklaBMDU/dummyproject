<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepositRequest;
use App\Http\Requests\WithdrawRequest;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Withdraw;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function deposit(DepositRequest $request): JsonResponse
    {
        $data = $request->validated();
        $customerId = Auth::guard('customer')->id();
        $wallet = Wallet::firstOrCreate(['id' => $customerId], [
            'total_amount' => 0,
            'available_amount' => 0,
            'used_amount' => 0,
        ]);
        $wallet->increment('total_amount', $data['amount']);
        $wallet->increment('available_amount', $data['amount']);
        Transaction::create([
            'customers_id' => $customerId,
            'wallet_id' => $wallet->id,
            'amount' => $data['amount'],
            'description' => $data['description'] ?? 'Wallet deposit',
            'transaction_type' => 'deposit',
            'type' => 'credit',
            'transaction_status' => 'success'
        ]);
        return response()->json([
            'message' => 'Deposit successful',
            'data' => $wallet
        ], 201);
    }

    public function withdrawRequest(WithdrawRequest $request): JsonResponse
    {
        $customerId = Auth::guard('customer')->id();
        $data = $request->validated();
        $result = DB::transaction(function () use ($customerId, $data) {
            $wallet = Wallet::where('id', $customerId)->first();
            if (!$wallet || $wallet->available_amount < $data['amount']) {
                return ['error' => 'Insufficient balance'];
            }
            $wallet->decrement('available_amount', $data['amount']);
            $withdraw = Withdraw::create([
                'customers_id' => $customerId,
                'amount' => $data['amount'],
                'status' => 'pending',
                'remark' => $data['remark']
            ]);
            Transaction::create([
                'customers_id' => $customerId,
                'wallet_id' => $wallet->id,
                'withdraw_id' => $withdraw->id,
                'amount' => $data['amount'],
                'description' => 'Withdraw request initiated',
                'transaction_type' => 'withdraw',
                'type' => 'debit',
                'transaction_status' => 'success'
            ]);
            return $withdraw;
        });
        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], 400);
        }
        return response()->json([
            'message' => 'Withdraw request submitted',
            'data' => $result
        ], 200);
    }
}
