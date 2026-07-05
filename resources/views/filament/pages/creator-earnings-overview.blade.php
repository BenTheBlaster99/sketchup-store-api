<x-filament-panels::page>
    <div class="space-y-6">

        <div>
            <h2 class="text-lg font-semibold mb-4">{{ $month }}</h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-2xl border p-5">
                    <p class="text-sm text-gray-500 mb-1">Total subscription revenue</p>
                    <p class="text-2xl font-semibold">{{ number_format($totalRevenue) }} DZD</p>
                </div>
                <div class="rounded-2xl border p-5">
                    <p class="text-sm text-gray-500 mb-1">Creator pool ({{ $poolPercent }}%)</p>
                    <p class="text-2xl font-semibold text-blue-600">{{ number_format($creatorPool) }} DZD</p>
                    <p class="text-xs text-gray-400 mt-1">Split by download share below</p>
                </div>
                <div class="rounded-2xl border p-5">
                    <p class="text-sm text-gray-500 mb-1">Platform keeps ({{ 100 - $poolPercent }}%)</p>
                    <p class="text-2xl font-semibold text-green-600">{{ number_format($platformKeeps) }} DZD</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium">Creator</th>
                        <th class="text-left px-4 py-3 font-medium">PayPal</th>
                        <th class="text-right px-4 py-3 font-medium">Downloads</th>
                        <th class="text-right px-4 py-3 font-medium">Share</th>
                        <th class="text-right px-4 py-3 font-medium">Payout (DZD)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($creatorBreakdown as $creator)
                    <tr class="border-b last:border-0">
                        <td class="px-4 py-3">
                            <p class="font-medium">{{ $creator['name'] }}</p>
                            <p class="text-xs text-gray-400">{{ $creator['email'] }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $creator['paypal'] }}</td>
                        <td class="px-4 py-3 text-right">{{ $creator['downloads'] }}</td>
                        <td class="px-4 py-3 text-right">{{ $creator['share'] }}%</td>
                        <td class="px-4 py-3 text-right font-semibold">
                            {{ number_format($creator['payout_dzd']) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                            No creator activity this month yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($creatorBreakdown) > 0)
                <tfoot class="bg-gray-50 border-t">
                    <tr>
                        <td colspan="4" class="px-4 py-3 font-medium">Total creator payouts</td>
                        <td class="px-4 py-3 text-right font-semibold text-blue-600">
                            {{ number_format(array_sum(array_column($creatorBreakdown, 'payout_dzd'))) }} DZD
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <p class="text-xs text-gray-400">
            Payouts are estimated. Send via PayPal manually at month end.
            Revenue calculated from active subscriptions prorated to monthly equivalent.
        </p>
    </div>
</x-filament-panels::page>
