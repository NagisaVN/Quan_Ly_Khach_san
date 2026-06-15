<?php

namespace App\Adapters\Contracts;

interface DoorLockGatewayInterface
{
    public function issueKey(int $roomId, string $guestName, array $options = []): array;

    public function revokeKey(int $roomId, string $keyId): array;
}
