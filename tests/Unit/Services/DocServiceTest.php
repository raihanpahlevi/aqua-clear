<?php

namespace Tests\Unit\Services;

use App\Models\Stocking;
use App\Services\DocService;
use Tests\TestCase;

class DocServiceTest extends TestCase
{
    private DocService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DocService();
    }

    public function test_doc_dihitung_dari_tgl_pakan_pertama_bukan_tgl_tebar(): void
    {
        $stocking = new Stocking([
            'tgl_tebar' => '2026-05-01',
            'tgl_pakan_pertama' => '2026-05-03',
        ]);

        // Kalau DOC dihitung dari tgl_tebar hasilnya akan 31, bukan 29 — ini yang harus dicegah regresi.
        $this->assertSame(29, $this->service->forDate($stocking, '2026-06-01'));
    }

    public function test_doc_null_kalau_tgl_pakan_pertama_belum_diisi(): void
    {
        $stocking = new Stocking(['tgl_tebar' => '2026-05-01', 'tgl_pakan_pertama' => null]);

        $this->assertNull($this->service->forDate($stocking, '2026-06-01'));
    }

    public function test_sampling_due_di_doc_30_dan_kelipatan_7_setelahnya(): void
    {
        $stocking = new Stocking(['tgl_pakan_pertama' => '2026-01-01']);

        $this->assertTrue($this->service->isSamplingDue($stocking, '2026-01-31')); // DOC 30
        $this->assertTrue($this->service->isSamplingDue($stocking, '2026-02-07')); // DOC 37
        $this->assertFalse($this->service->isSamplingDue($stocking, '2026-02-05')); // DOC 35
        $this->assertFalse($this->service->isSamplingDue($stocking, '2026-01-20')); // DOC 19, belum 30
    }

    public function test_flush_out_hanya_eligible_sebelum_doc_30(): void
    {
        $stocking = new Stocking(['tgl_pakan_pertama' => '2026-01-01']);

        $this->assertTrue($this->service->isEligibleForFlushOut($stocking, '2026-01-20')); // DOC 19
        $this->assertFalse($this->service->isEligibleForFlushOut($stocking, '2026-02-01')); // DOC 31
    }
}
