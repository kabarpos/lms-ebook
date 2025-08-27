<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get section contents with YouTube URLs
$sectionContents = \App\Models\SectionContent::whereNotNull('youtube_url')
    ->with(['courseSection.course'])
    ->take(5)
    ->get();

echo "🎬 YouTube Player Test URLs:\n\n";
echo "📺 Test Page: http://127.0.0.1:8000/youtube-test.html\n\n";

foreach ($sectionContents as $content) {
    $course = $content->courseSection->course;
    $url = "http://127.0.0.1:8000/course/{$course->slug}/preview/{$content->id}";
    
    echo "📝 {$content->name}\n";
    echo "   YouTube: {$content->youtube_url}\n";
    echo "   Video ID: " . $content->getYoutubeVideoId() . "\n";
    echo "   Preview URL: {$url}\n\n";
}

echo "🔧 Debug Mode: " . (config('app.debug') ? 'ENABLED' : 'DISABLED') . "\n";
echo "🌐 App URL: " . config('app.url') . "\n";
echo "📊 Total YouTube Videos: " . $sectionContents->count() . "\n";