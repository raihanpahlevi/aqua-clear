<?php

namespace Tests\Unit\Services;

use App\Services\HarvestService;
use Tests\TestCase;

class HarvestServiceTest extends TestCase
{
    public function test_pendapatan_is_berat_kali_harga(): void
    {
        $service = new HarvestService();

        $this->assertEqualsWithDelta(13_500_000.0, $service->pendapatan(300, 45000), 0.01);
    }
}
