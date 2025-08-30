<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h2 class="text-2xl font-bold mb-4">Data Management</h2>
                <p class="text-gray-600 dark:text-gray-400">
                    Halaman ini akan menampilkan berbagai data dan informasi dari sistem.
                </p>
                
                <div class="mt-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-3">Recent Users</h3>
                            <div class="space-y-2">
                                @foreach(App\Models\User::latest()->take(5)->get() as $user)
                                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-600">
                                        <span class="font-medium">{{ $user->name }}</span>
                                        <span class="text-sm text-gray-500">{{ $user->email }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-3">Recent Courses</h3>
                            <div class="space-y-2">
                                @foreach(App\Models\Course::latest()->take(5)->get() as $course)
                                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-600">
                                        <span class="font-medium">{{ $course->title }}</span>
                                        <span class="text-sm text-gray-500">{{ $course->created_at->format('d M Y') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>