<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccountCredentialController extends Controller
{
    public function show($id)
    {
        $account = Account::with('credential')->findOrFail($id);

        if (!$account->credential) {
            return response()->json(['message' => 'Credential not set'], 404);
        }

        $c = $account->credential;

        return response()->json([
            'id' => $c->id,
            'account_id' => $c->account_id,
            'login_type' => $c->login_type,
            'username' => $c->username,
            'email' => $c->email,
            'password' => $c->getRawOriginal('password'),
            'note' => $c->note,
            'created_at' => $c->created_at,
            'updated_at' => $c->updated_at,
        ]);
    }

    public function upsert(Request $request, $id)
    {
        $account = Account::findOrFail($id);

        $data = $request->validate([
            'login_type' => ['required', Rule::in(['google', 'konami', 'facebook', 'apple', 'other'])],
            'username'   => ['nullable', 'string', 'max:255'],
            'email'      => ['nullable', 'email', 'max:255'],
            'password'   => ['required', 'string', 'max:255'],
            'note'       => ['nullable', 'string'],
        ]);

        if (empty($data['username']) && empty($data['email'])) {
            return response()->json(['message' => 'username or email is required'], 422);
        }

        $credential = $account->credential()->updateOrCreate(
            ['account_id' => $account->id],
            $data
        );

        return response()->json([
            'message' => 'Credential saved',
            'credential' => [
                'id' => $credential->id,
                'account_id' => $credential->account_id,
                'login_type' => $credential->login_type,
                'username' => $credential->username,
                'email' => $credential->email,
                'password' => $credential->getRawOriginal('password'),
                'note' => $credential->note,
                'created_at' => $credential->created_at,
                'updated_at' => $credential->updated_at,
            ],
        ]);
    }

    public function destroy($id)
    {
        $account = Account::with('credential')->findOrFail($id);

        if (!$account->credential) {
            return response()->json(['message' => 'Credential not set'], 404);
        }

        $account->credential->delete();

        return response()->json(['message' => 'Credential deleted']);
    }
}
