<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\AdminAccountResource;

class AccountController extends Controller
{
    // GET /admin/accounts?status=draft|approved|sold&game_label=efootball|fc_mobile
    public function index(Request $request)
    {
        $query = Account::with(['images', 'credential']);

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('game_label')) $query->where('game_label', $request->game_label);

        return AdminAccountResource::collection($query->latest()->get());
    }

    // POST /admin/accounts
    public function store(Request $request)
    {
        $data = $request->validate([
            'game_label' => ['required', Rule::in(['efootball', 'fc_mobile'])],
            'name' => ['required', 'string', 'max:255'],
            'price_original' => ['required', 'integer', 'min:0'],
            'discount_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'description' => ['nullable', 'string'],
        ]);

        $account = new Account($data);
        $account->status = 'draft';
        $account->created_by = auth()->id();
        $account->save(); // code & price_final auto dari model event

        return response()->json([
            'message' => 'Account created',
            'account' => $account
        ], 201);
    }

    // GET /admin/accounts/{id}
    public function show($id)
    {
        $account = Account::with(['images', 'credential'])->findOrFail($id);
        return new AdminAccountResource($account);
    }

    // PUT /admin/accounts/{id}
    public function update(Request $request, $id)
    {
        $account = Account::findOrFail($id);

        $data = $request->validate([
            'game_label' => [Rule::in(['efootball', 'fc_mobile'])],
            'name' => ['string', 'max:255'],
            'price_original' => ['integer', 'min:0'],
            'discount_percent' => ['integer', 'min:0', 'max:100'],
            'description' => ['nullable', 'string'],
            'status' => [Rule::in(['draft', 'approved', 'sold'])],
        ]);

        // kalau kamu mau status hanya boleh via approve/sold endpoint, tinggal hapus validasi 'status'

        $account->fill($data);
        $account->save(); // code auto regen kalau game_label berubah, price_final auto recalc

        return response()->json([
            'message' => 'Account updated',
            'account' => $account
        ]);
    }

    public function destroy($id)
    {
        $account = Account::findOrFail($id);
        $account->delete();

        return response()->json(['message' => 'Account deleted']);
    }

    public function approve($id)
    {
        $account = Account::findOrFail($id);

        if ($account->status !== 'draft') {
            return response()->json(['message' => 'Only draft can be approved'], 422);
        }

        $account->update(['status' => 'approved']);

        return response()->json(['message' => 'Account approved', 'account' => $account]);
    }

    public function sold($id)
    {
        $account = Account::findOrFail($id);

        if ($account->status !== 'approved') {
            return response()->json(['message' => 'Only approved can be sold'], 422);
        }

        $account->update(['status' => 'sold']);

        return response()->json(['message' => 'Account sold', 'account' => $account]);
    }
}
