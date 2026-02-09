<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use App\Http\Resources\PublicAccountResource;

class PublicAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = Account::with('images')
            ->where('status', 'approved');

        if ($request->filled('game_label')) {
            $query->where('game_label', $request->game_label);
        }

        return PublicAccountResource::collection($query->latest()->get());
    }

    public function show($id)
    {
        $account = Account::with('images')
            ->where('status', 'approved')
            ->findOrFail($id);

        return new PublicAccountResource($account);
    }

    public function whatsappLink(Request $request, $id)
    {
        $account = Account::query()
            ->where('status', 'approved')
            ->findOrFail($id);

        $adminWa = config('app.admin_whatsapp', env('ADMIN_WHATSAPP', '6281230090953'));

        $buyerName = $request->query('name');
        $buyerWa   = $request->query('wa');

        $text = "Halo Admin, saya mau tanya akun:\n"
            . "Kode: {$account->code}\n"
            . "Game: {$account->game_label}\n"
            . "Nama: {$account->name}\n"
            . "Harga Awal: Rp " . number_format((int)$account->price_original, 0, ',', '.') . "\n"
            . "Diskon: {$account->discount_percent}%\n"
            . "Harga Jadi: Rp " . number_format((int)$account->price_final, 0, ',', '.') . "\n";

        if ($buyerName || $buyerWa) {
            $text .= "\nPembeli:\n";
            if ($buyerName) $text .= "- Nama: {$buyerName}\n";
            if ($buyerWa)   $text .= "- WA: {$buyerWa}\n";
        }

        $url = "https://wa.me/{$adminWa}?text=" . urlencode($text);

        return response()->json(['whatsapp_url' => $url]);
    }
}
