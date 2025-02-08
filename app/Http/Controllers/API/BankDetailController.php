<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BankDetailRequest;
use App\Models\BankDetails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankDetailController extends Controller
{
    public function store(BankDetailRequest $request): JsonResponse
    {
        $customerId = Auth::guard('customer')->id();
        $data = $request->validated();
        $bankDetail = BankDetails::create([
            'customers_id' => $customerId,
            'bank_name' => $data['bank_name'] ?? null,
            'account_number' => $data['account_number'] ?? null,
            'ifsc_code' => $data['ifsc_code'] ?? null,
            'branch_name' => $data['branch_name'] ?? null,
            'account_holder_name' => $data['account_holder_name'] ?? null,
            'phone_pay_number' => $data['phone_pay_number'] ?? null,
            'paytm_number' => $data['paytm_number'] ?? null,
            'google_pay_number' => $data['google_pay_number'] ?? null,
            'status' => $data['status'] ?? true
        ]);
        return response()->json([
            'message' => 'Bank details saved successfully',
            'data' => $bankDetail
        ], 201);
    }

    public function getBankDetails(): JsonResponse
    {
        $customerId = Auth::guard('customer')->id();
        $bankDetails = BankDetails::where('customers_id', $customerId)
            ->select('bank_name', 'account_number', 'ifsc_code', 'branch_name', 'account_holder_name')
            ->first();
        if (!$bankDetails) {
            return response()->json(['message' => 'No bank details found'], 404);
        }
        return response()->json([
            'message' => 'Bank details retrieved successfully',
            'data' => $bankDetails
        ], 200);
    }

    public function getPaymentNumber(Request $request): JsonResponse
    {
        $type = $request->query('type');
        $validTypes = ['phone_pay', 'google_pay', 'paytm'];
        if (!in_array($type, $validTypes)) {
            return response()->json(['message' => 'Invalid Parameters'], 404);;
        }
        $customerId = Auth::guard('customer')->id();
        $paymentNumber = BankDetails::where('customers_id', $customerId)
            ->select("{$type}_number as number")
            ->first();
        if (!$paymentNumber || empty($paymentNumber->number)) {
            return response()->json(['message' => 'No payment number found for the given type'], 200);
        }
        return response()->json([
            'message' => ucfirst(str_replace('_', ' ', $type)) . ' number retrieved successfully',
            'data' => $paymentNumber
        ], 200);
    }
}
