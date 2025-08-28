<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Fix active configuration conflicts
\App\Models\MidtransSetting::where('id', '!=', 1)->update(['is_active' => false]);

echo "✅ Fixed active configuration conflicts - only ID 1 is now active\n";