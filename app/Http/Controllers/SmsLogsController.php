<?php

namespace App\Http\Controllers;

use App\Models\SentSms;
use Illuminate\Http\Request;

class SmsLogsController extends Controller
{
    public function index(Request $request)
    {
        $query = SentSms::with('client')->latest();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('client', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(20);

        return view('sms.logs', compact('logs'));
    }
}
