<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventCategory;
use App\Models\Event;

class EventCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Create categories first
        $categories = EventCategory::factory()->count(10)->create();
        $events = Event::all();
        foreach ($events as $event) {
            // Attach 1-3 random categories to each event
            $event->categories()->attach(
                $categories->random(rand(1, 3))->pluck('id')->toArray()
            );
        }
    }
}
