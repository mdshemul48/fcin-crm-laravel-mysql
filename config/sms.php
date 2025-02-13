<?php

return [
    'gateway' => [
        'send_url' => env('SMS_GATEWAY_SEND_URL', 'https://smpp.revesms.com:7790/sendtext'),
        'balance_url' => env('SMS_GATEWAY_BALANCE_URL', 'https://smpp.revesms.com/sms/smsConfiguration/smsClientBalance.jsp'),
        'status_url' => env('SMS_GATEWAY_STATUS_URL', 'https://smpp.revesms.com:7790/getstatus'),
    ]
];
