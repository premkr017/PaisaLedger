<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            PaisaLedger Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- 3 Cards: Income, Expense, Balance -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

                <!-- Income Card -->
                <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-lg shadow">
                    <p class="text-sm text-gray-600">Total Income</p>
                    <p class="text-3xl font-bold text-green-600">₹{{ number_format($totalIncome, 2) }}</p>
                </div>

                <!-- Expense Card -->
                <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-lg shadow">
                    <p class="text-sm text-gray-600">Total Expense</p>
                    <p class="text-3xl font-bold text-red-600">₹{{ number_format($totalExpense, 2) }}</p>
                </div>

                <!-- Balance Card -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg shadow">
                    <p class="text-sm text-gray-600">Current Balance</p>
                    <p class="text-3xl font-bold text-blue-600">₹{{ number_format($balance, 2) }}</p>
                </div>
            </div>

            <!-- Last 5 Transactions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">Recent Transactions</h3>

                @php
                    $recentTransactions = \App\Models\Transaction::where('user_id', Auth::id())->latest()->take(5)->get();
                @endphp

                @if($recentTransactions->count() > 0)
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-gray-500">
                                <th>Date</th><th>Note</th><th>Type</th><th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTransactions as $tx)
                            <tr>
                                <td>{{ $tx->date }}</td>
                                <td>{{ $tx->note ?? '-' }}</td>
                                <td>
                                    <span class="{{ $tx->type == 'income' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ ucfirst($tx->type) }}
                                    </span>
                                </td>
                                <td class="font-bold">₹{{ $tx->amount }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-500">Abhi koi transaction nahi hai. Pehle transaction add karo.</p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
