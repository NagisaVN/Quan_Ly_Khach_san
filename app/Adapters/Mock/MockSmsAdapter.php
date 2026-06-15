<?php

namespace App\Adapters\Mock;

use App\Adapters\Contracts\SmsGatewayInterface;
use App\Models\IntegrationLog;

class MockSmsAdapter implements SmsGatewayInterface
{
    public function sendSms(string $phone, string $message, array $options = []): array
    {
        IntegrationLog::create([
            'branch_id' => session('current_branch_id'),
            'provider' => 'mock_sms',
            'action' => 'send_sms',
            'request_payload' => compact('phone', 'message', 'options'),
            'response_payload' => ['status' => 'sent', 'message_id' => 'SMS'.time()],
            'status' => 'success',
        ]);

        return ['success' => true, 'message_id' => 'SMS'.time()];
    }
}
