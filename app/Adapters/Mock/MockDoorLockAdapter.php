<?php

namespace App\Adapters\Mock;

use App\Adapters\Contracts\DoorLockGatewayInterface;
use App\Models\IntegrationLog;

class MockDoorLockAdapter implements DoorLockGatewayInterface
{
    public function issueKey(int $roomId, string $guestName, array $options = []): array
    {
        $keyId = 'KEY'.str_pad((string) $roomId, 4, '0', STR_PAD_LEFT).time();

        IntegrationLog::create([
            'branch_id' => session('current_branch_id'),
            'provider' => 'mock_door_lock',
            'action' => 'issue_key',
            'request_payload' => compact('roomId', 'guestName', 'options'),
            'response_payload' => compact('keyId'),
            'status' => 'success',
        ]);

        return ['success' => true, 'key_id' => $keyId];
    }

    public function revokeKey(int $roomId, string $keyId): array
    {
        IntegrationLog::create([
            'provider' => 'mock_door_lock',
            'action' => 'revoke_key',
            'request_payload' => compact('roomId', 'keyId'),
            'response_payload' => ['revoked' => true],
            'status' => 'success',
        ]);

        return ['success' => true];
    }
}
