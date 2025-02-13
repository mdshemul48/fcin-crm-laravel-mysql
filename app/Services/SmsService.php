<?php

namespace App\Services;

use App\Models\SmsSettings;
use App\Models\SmsTemplate;
use App\Models\SentSms;
use Illuminate\Support\Facades\Http;

class SmsService
{
    private $settings;

    public function __construct()
    {
        $this->settings = SmsSettings::where('is_active', true)->first();
    }

    public function sendSms($phoneNumber, $message, $clientId = null)
    {
        if (!$this->settings) {
            return false;
        }

        $response = Http::get(config('sms.gateway.send_url'), [
            'apikey' => $this->settings->api_key,
            'secretkey' => $this->settings->secret_key,
            'callerID' => $this->settings->caller_id,
            'toUser' => $phoneNumber,
            'messageContent' => $message
        ]);

        if ($response->successful()) {
            $responseData = $response->json();
            if ($clientId) {
                SentSms::create([
                    'client_id' => $clientId,
                    'message_id' => $responseData['Message_ID'] ?? null,
                    'content' => $message,
                    'response' => json_encode($responseData),
                    'status' => $responseData['Status'] === 0 ?? 'sent'
                ]);
            }

            return $responseData;
        }

        return false;
    }

    public function sendTemplatedSms($phoneNumber, $templateType, $replacements = [])
    {
        $template = SmsTemplate::where('type', $templateType)->first();

        if (!$template) {
            return false;
        }

        $message = $template->content;

        // Define default values for all possible variables
        $defaultValues = [
            // Client related
            'client_id' => $replacements['client']->client_id ?? '',
            'username' => $replacements['client']->username ?? '',
            'status' => $replacements['client']->status ?? '',
            'phone_number' => $replacements['client']->phone_number ?? '',
            'address' => $replacements['client']->address ?? '',
            'current_balance' => $replacements['client']->current_balance ?? '0',
            'due_amount' => $replacements['client']->due_amount ?? '0',
            'bill_amount' => $replacements['client']->bill_amount ?? '0',

            // Package related
            'package_name' => $replacements['client']->package->name ?? '',
            'package_price' => $replacements['client']->package->price ?? '',

            // Payment related
            'amount' => $replacements['amount'] ?? '0',
            'discount' => $replacements['discount'] ?? '0',
            'payment_date' => $replacements['payment_date'] ?? date('d/m/Y'),
            'payment_type' => $replacements['payment_type'] ?? '',
            'month' => $replacements['month'] ?? '',
            'year' => $replacements['year'] ?? '',
            'remarks' => $replacements['remarks'] ?? ''
        ];

        // Replace all placeholders with their values
        foreach ($defaultValues as $key => $value) {
            $message = str_replace('{' . $key . '}', $value, $message);
        }

        return $this->sendSms($phoneNumber, $message, $replacements['client']->id ?? null);
    }

    public function getBalance()
    {
        if (!$this->settings) {
            return 0;
        }

        $response = Http::get(config('sms.gateway.balance_url'), [
            'client' => $this->settings->client_id
        ]);

        return $response->successful() ? json_decode($response->body(), true)['Balance'] : 0;
    }

    public function checkDeliveryStatus($messageId)
    {
        if (!$this->settings) {
            return null;
        }

        $response = Http::get(config('sms.gateway.status_url'), [
            'apikey' => $this->settings->api_key,
            'secretkey' => $this->settings->secret_key,
            'messageid' => $messageId
        ]);

        return $response->successful() ? $response->json() : null;
    }
}
