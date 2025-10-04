<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - LMS DripCourse</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-gray-900 text-center mb-2">Oops! Something went wrong</h1>
            <p class="text-gray-600 text-center mb-6">{{ $message ?? 'An unexpected error occurred while processing your request.' }}</p>
            
            @if(isset($error) && config('app.debug'))
            <div class="bg-gray-100 p-3 rounded-md mb-4">
                <p class="text-xs text-gray-700 font-mono">{{ $error }}</p>
            </div>
            @endif
            
            <div class="flex flex-col space-y-3">
                <a href="{{ url()->previous() }}" class="w-full bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                    Try Again
                </a>
                <a href="{{ route('front.index') }}" class="w-full bg-gray-200 text-gray-800 text-center py-2 px-4 rounded-md hover:bg-gray-300 transition-colors">
                    Go Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>