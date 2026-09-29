<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class CiaMonitoringTest extends TestCase
{
    public function test_cia_logging_channel_records_monitoring_event(): void
    {
        Log::channel('cia')->info('CIA monitoring test event', [
            'event' => 'health_check',
        ]);

        $logFiles = glob(storage_path('logs/cia*.log'));

        $this->assertNotEmpty($logFiles);

        $logContent = file_get_contents($logFiles[0]);

        $this->assertStringContainsString(
            'CIA monitoring test event',
            $logContent
        );
    }
}
