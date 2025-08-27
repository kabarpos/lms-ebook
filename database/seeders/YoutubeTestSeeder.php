<?php

namespace Database\Seeders;

use App\Models\SectionContent;
use Illuminate\Database\Seeder;

class YoutubeTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first few section contents and add YouTube URLs for testing
        $youtubeSamples = [
            'https://www.youtube.com/watch?v=dQw4w9WgXcQ', // Rick Roll for testing
            'https://www.youtube.com/watch?v=9bZkp7q19f0', // PSY - GANGNAM STYLE
            'https://www.youtube.com/watch?v=kJQP7kiw5Fk', // Luis Fonsi - Despacito
            'https://youtu.be/fJ9rUzIMcZQ', // Queen - Bohemian Rhapsody
            'https://www.youtube.com/watch?v=60ItHLz5WEA', // Alan Walker - Faded
        ];

        $sectionContents = SectionContent::take(5)->get();

        foreach ($sectionContents as $index => $content) {
            if (isset($youtubeSamples[$index])) {
                $content->update([
                    'youtube_url' => $youtubeSamples[$index]
                ]);
                
                $this->command->info("Updated '{$content->name}' with YouTube URL: {$youtubeSamples[$index]}");
            }
        }
        
        $this->command->info('YouTube test URLs have been added to section contents.');
    }
}