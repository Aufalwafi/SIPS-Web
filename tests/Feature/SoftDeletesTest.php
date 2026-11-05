<?php

namespace Tests\Feature;

use App\Models\Kelompok;
use App\Models\AnggotaKelompok;
use App\Models\Kegiatan;
use App\Models\Kups;
use App\Models\PotensiKelompok;
use App\Models\PermasalahanKelompok;
use App\Models\Pendamping;
use Tests\TestCase;

class SoftDeletesTest extends TestCase
{
    public function test_vital_models_use_soft_deletes_trait(): void
    {
        $traits = function ($class) {
            return class_uses_recursive($class);
        };

        $this->assertContains(\Illuminate\Database\Eloquent\SoftDeletes::class, $traits(Kelompok::class));
        $this->assertContains(\Illuminate\Database\Eloquent\SoftDeletes::class, $traits(AnggotaKelompok::class));
        $this->assertContains(\Illuminate\Database\Eloquent\SoftDeletes::class, $traits(Kegiatan::class));
        $this->assertContains(\Illuminate\Database\Eloquent\SoftDeletes::class, $traits(Kups::class));
        $this->assertContains(\Illuminate\Database\Eloquent\SoftDeletes::class, $traits(PotensiKelompok::class));
        $this->assertContains(\Illuminate\Database\Eloquent\SoftDeletes::class, $traits(PermasalahanKelompok::class));
        $this->assertContains(\Illuminate\Database\Eloquent\SoftDeletes::class, $traits(Pendamping::class));
    }
}
