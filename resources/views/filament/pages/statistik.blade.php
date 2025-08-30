<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h2 class="text-2xl font-bold mb-4">Statistik Dashboard</h2>
                <p class="text-gray-600 dark:text-gray-400">
                    Halaman ini akan menampilkan berbagai statistik dan analitik dari sistem.
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200">Total Users</h3>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ App\Models\User::count() }}</p>
                    </div>
                    
                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-green-800 dark:text-green-200">Total Courses</h3>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ App\Models\Course::count() }}</p>
                    </div>
                    
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200">Total Categories</h3>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ App\Models\Category::count() }}</p>
                    </div>
                    
                    <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-purple-800 dark:text-purple-200">Total Transactions</h3>
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ App\Models\Transaction::count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>