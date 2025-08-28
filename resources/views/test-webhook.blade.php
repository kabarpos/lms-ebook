<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Webhook Midtrans</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-6">Test Webhook Midtrans</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Webhook URLs -->
            <div class="bg-blue-50 p-4 rounded-lg">
                <h2 class="text-lg font-semibold mb-3">Webhook URLs</h2>
                <div class="space-y-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Production Webhook URL:</label>
                        <input type="text" readonly value="{{ url('/booking/payment/midtrans/notification') }}" 
                               class="w-full mt-1 p-2 border rounded bg-gray-50 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Test Webhook URL:</label>
                        <input type="text" readonly value="{{ url('/test-webhook-receiver') }}" 
                               class="w-full mt-1 p-2 border rounded bg-gray-50 text-sm">
                    </div>
                </div>
            </div>
            
            <!-- Recent Transactions -->
            <div class="bg-green-50 p-4 rounded-lg">
                <h2 class="text-lg font-semibold mb-3">Recent Transactions</h2>
                <div class="space-y-2 text-sm">
                    @php
                        $recentTransactions = \App\Models\Transaction::orderBy('created_at', 'desc')->take(5)->get();
                    @endphp
                    
                    @if($recentTransactions->count() > 0)
                        @foreach($recentTransactions as $trans)
                            <div class="flex justify-between items-center p-2 bg-white rounded border">
                                <span class="font-mono">{{ $trans->booking_trx_id }}</span>
                                <span class="text-xs text-gray-500">{{ $trans->created_at->format('H:i:s') }}</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-500">No transactions found</p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Test Form -->
        <div class="mt-6 bg-yellow-50 p-4 rounded-lg">
            <h2 class="text-lg font-semibold mb-3">Test Webhook Manually</h2>
            <form action="{{ route('test.webhook.receiver') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="transaction_status" placeholder="settlement" class="p-2 border rounded">
                    <input type="text" name="order_id" placeholder="DC1234" class="p-2 border rounded">
                    <input type="text" name="gross_amount" placeholder="109890" class="p-2 border rounded">
                    <input type="text" name="custom_field1" placeholder="16" class="p-2 border rounded">
                    <input type="text" name="custom_field2" placeholder="1" class="p-2 border rounded">
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Send Test Webhook
                </button>
            </form>
        </div>
        
        <!-- Instructions -->
        <div class="mt-6 bg-red-50 p-4 rounded-lg">
            <h2 class="text-lg font-semibold mb-3 text-red-800">Setup Instructions</h2>
            <ol class="list-decimal list-inside space-y-2 text-sm">
                <li>Login ke <a href="https://dashboard.sandbox.midtrans.com" target="_blank" class="text-blue-600 underline">Midtrans Sandbox Dashboard</a></li>
                <li>Pilih merchant Anda</li>
                <li>Masuk ke menu <strong>Settings > Configuration</strong></li>
                <li>Set Payment Notification URL ke: <code class="bg-gray-200 px-1 rounded">{{ url('/booking/payment/midtrans/notification') }}</code></li>
                <li>Set Finish/Unfinish/Error Redirect URL ke halaman yang sesuai</li>
                <li>Save konfigurasi</li>
                <li>Test pembayaran dengan simulasi</li>
            </ol>
        </div>
        
        <!-- Auto Refresh -->
        <div class="mt-4 text-center">
            <button onclick="location.reload()" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                Refresh Page
            </button>
        </div>
    </div>
</body>
</html>