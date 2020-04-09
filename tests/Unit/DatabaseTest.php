<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    public function testExample()
    {
        $this->assertTrue(true);
    }
    /* public function testDatabase()
    {
        $this->assertDatabaseHas('users', [
            'email' => 'k.saipanas@gmail.com',
        ]);
    } */
}
