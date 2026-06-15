<?php

namespace App\Adapters\Mock;

use App\Adapters\Contracts\OtaGatewayInterface;
use App\Models\IntegrationLog;

class MockOtaAdapter implements OtaGatewayInterface
{
    public function syncBooking(array $bookingData): array
    {
        IntegrationLog::create([
            'branch_id' => $bookingData['branch_id'] ?? session('current_branch_id'),
            'provider' => 'mock_ota',
            'action' => 'sync_booking',
            'request_payload' => $bookingData,
            'response_payload' => ['ota_ref' => 'OTA'.time(), 'status' => 'synced'],
            'status' => 'success',
        ]);

        return ['success' => true, 'ota_ref' => 'OTA'.time()];
    }

    public function fetchReservations(array $filters = []): array
    {
        IntegrationLog::create([
            'provider' => 'mock_ota',
            'action' => 'fetch_reservations',
            'request_payload' => $filters,
            'response_payload' => ['count' => 0, 'reservations' => []],
            'status' => 'success',
        ]);

        return ['reservations' => []];
    }
}
