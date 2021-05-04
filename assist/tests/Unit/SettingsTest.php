<?php

namespace Tests\Unit;
use Tests\Testcase;
use HasFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Auth;

class SettingsTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    public function testDummy() {
        $this->expectNotToPerformAssertions();
    }

}
