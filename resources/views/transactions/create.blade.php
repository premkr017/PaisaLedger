<x-app-layout>
    <div class="mx-auto max-w-2xl">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold">Add New Transaction</h1>
            <a href="{{ route('transactions.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">Back to transactions</a>
        </div>
        <form method="POST" action="{{ route('transactions.store') }}" class="space-y-4 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            @csrf
            <div>
                <label for="type" class="mb-1 block text-sm font-medium text-slate-700">Type</label>
                <select id="type" name="type" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                </select>
            </div>
            <div>
                <label for="category_id" class="mb-1 block text-sm font-medium text-slate-700">Category</label>
                <select id="category_id" name="category_id" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="amount" class="mb-1 block text-sm font-medium text-slate-700">Amount (₹)</label>
                <input id="amount" type="number" name="amount" step="0.01" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-indigo-500 focus:ring-indigo-500" required>
            </div>
            <div>
                <label for="date" class="mb-1 block text-sm font-medium text-slate-700">Date</label>
                <input id="date" type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-indigo-500 focus:ring-indigo-500" required>
            </div>
            <div>
                <label for="note" class="mb-1 block text-sm font-medium text-slate-700">Note</label>
                <input id="note" type="text" name="note" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <button class="rounded-lg bg-indigo-600 px-6 py-2.5 font-semibold text-white transition hover:bg-indigo-700">Save Transaction</button>
        </form>
    </div>
</x-app-layout>
