<?php

namespace App\Http\Controllers;

use App\Models\SmsSettings;
use App\Models\SmsTemplate;
use App\Services\SmsService;
use Illuminate\Http\Request;

class SmsSettingsController extends Controller
{
    public function index()
    {
        $settings = SmsSettings::first();
        $templates = SmsTemplate::all();
        $smsService = new SmsService();
        $balance = $smsService->getBalance();

        return view('sms.settings', compact('settings', 'templates', 'balance'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gateway_name' => 'required',
            'api_key' => 'required',
            'secret_key' => 'required',
            'caller_id' => 'required',
            'client_id' => 'nullable'
        ]);

        SmsSettings::updateOrCreate(
            ['id' => 1],
            array_merge($validated, ['is_active' => true])
        );

        return redirect()->back()->with('success', 'SMS settings updated successfully');
    }

    public function storeTemplate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'content' => 'required',
            'type' => 'required|in:payment,bill,custom'
        ]);

        SmsTemplate::create($validated);

        return redirect()->back()->with('success', 'Template created successfully');
    }

    public function updateTemplate(Request $request, SmsTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required',
            'content' => 'required',
            'type' => 'required|in:payment,bill,custom'
        ]);

        $template->update($validated);

        return response()->json(['message' => 'Template updated successfully']);
    }

    public function destroyTemplate(SmsTemplate $template)
    {
        $template->delete();
        return response()->json(['message' => 'Template deleted successfully']);
    }
}
