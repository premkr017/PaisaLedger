<x-app-layout>
    <div class="py-10 max-w-5xl mx-auto">
        <div class="flex justify-between mb-6">
            <h2 class="text-2xl font-bold">All Transactions</h2>
            <a href="{{ route('transactions.create') }}" class="bg-green-600 text-white px-4 py-2 rounded">+ Add Transaction</a>
        </div>

        <table class="w-full bg-white shadow rounded">
            <thead><tr><th>Date</th><th>Category</th><th>Type</th><th>Amount</th><th>Action</th></tr></thead>
            <tbody>
            @foreach($transactions as $tx)
                <tr>
                    <td>{{ $tx->date }}</td>
                    <td>{{ $tx->category->name }}</td>
                    <td class="{{ $tx->type=='income' ? 'text-green-600' : 'text-red-600' }}">{{ $tx->type }}</td>
                    <td>₹{{ $tx->amount }}</td>
                    <td>
                        <form method="POST" action="{{ route('transactions.destroy', $tx) }}">
                            @csrf @method('DELETE')
                            <button class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $transactions->links() }}
    </div>
</x-app-layout>
