<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 ">
            {{ __('Payment Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 ">
            {{ __("View your recent payment history and manage saved payment methods.") }}
        </p>
    </header>

    <div class="mt-6 space-y-6">
        <!-- Saved Methods Mockup -->
        <div class="bg-gray-50  p-4 rounded-lg border border-gray-200 ">
            <h3 class="text-sm font-semibold text-gray-700  mb-3">{{ __('Saved Payment Methods') }}</h3>
            <div class="flex items-center justify-between bg-white  p-3 rounded border border-gray-200  shadow-sm">
                <div class="flex items-center space-x-3">
                    <i class="fab fa-cc-visa text-2xl text-blue-600 "></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900 ">Visa ending in 4242</p>
                        <p class="text-xs text-gray-500 ">Expires 12/28</p>
                    </div>
                </div>
                <button class="text-red-600  text-sm hover:underline">{{ __('Remove') }}</button>
            </div>
            <button class="mt-3 text-sm text-emerald-600  hover:text-emerald-700  font-medium">
                <i class="fas fa-plus mr-1"></i> {{ __('Add New Card') }}
            </button>
        </div>

        <!-- Recent Payments Table -->
        <div>
            <h3 class="text-sm font-semibold text-gray-700  mb-3">{{ __('Recent Transactions') }}</h3>
            @if($payments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200  border border-gray-200  rounded-lg">
                        <thead class="bg-gray-50 ">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Date</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Transaction ID</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Amount</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white  divide-y divide-gray-200 ">
                            @foreach($payments as $payment)
                                <tr>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 ">
                                        {{ $payment->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm font-mono text-gray-500 ">
                                        {{ $payment->transaction_id }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm font-semibold text-gray-900 ">
                                        {{ $payment->currency }} {{ number_format($payment->amount, 2) }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm">
                                        @if($payment->status === 'completed')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100  text-green-800 ">
                                                Completed
                                            </span>
                                        @elseif($payment->status === 'pending')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100  text-yellow-800 ">
                                                Pending
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100  text-red-800 ">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-6 bg-gray-50  rounded border border-gray-200  border-dashed">
                    <p class="text-sm text-gray-500 ">{{ __('No payment history found.') }}</p>
                </div>
            @endif
        </div>
    </div>
</section>
