<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Person;
use App\Services\ContextService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContextServiceTest extends TestCase
{
    use RefreshDatabase;

    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ContextService();
    }

    public function test_it_detects_person_by_name()
    {
        Person::create(['name' => 'John Doe']);

        $text = "I met John Doe today.";
        $detected = $this->service->detectPeople($text);

        $this->assertCount(1, $detected);
        $this->assertEquals(Person::first()->id, $detected[0]);
    }

    public function test_it_detects_person_by_keyword()
    {
        Person::create(['name' => 'Sebastian', 'keywords' => ['Seb', 'Berm']]);

        $text = "Seb was there.";
        $detected = $this->service->detectPeople($text);

        $this->assertCount(1, $detected);
    }

    public function test_it_respects_word_boundaries()
    {
        Person::create(['name' => 'Jo']);

        $text = "John went to the store.";
        $detected = $this->service->detectPeople($text);

        $this->assertEmpty($detected, "Should not match 'Jo' inside 'John'");
    }

    public function test_it_ignores_empty_names_and_keywords()
    {
        // Simulate a "broken" person record
        Person::create(['name' => ' ', 'keywords' => ['', '  ']]);
        Person::create(['name' => 'Real Person']);

        $text = "Just some text with a Real Person.";
        $detected = $this->service->detectPeople($text);

        $this->assertCount(1, $detected);
        $this->assertEquals(Person::where('name', 'Real Person')->first()->id, $detected[0]);
    }
}
