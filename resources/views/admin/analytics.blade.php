@extends('layouts.admin')

@section('title', 'AI Analytics Deep Dive')

@section('content')
<div class="space-y-8">
    <!-- Header & Controls -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-700 to-emerald-600">
                AI Analytics Intelligence
            </h1>
            <p class="text-gray-500 mt-1">
                Deep dive analysis powered by OpenAI
            </p>
        </div>

        <form action="{{ route('admin.analytics') }}" method="GET" class="flex items-center space-x-2">
            <select name="period" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                <option value="7" {{ $period == 7 ? 'selected' : '' }}>Last 7 Days</option>
                <option value="30" {{ $period == 30 ? 'selected' : '' }}>Last 30 Days</option>
                <option value="90" {{ $period == 90 ? 'selected' : '' }}>Last Quarter</option>
                <option value="365" {{ $period == 365 ? 'selected' : '' }}>Last Year</option>
            </select>
        </form>
    </div>

    <!-- AI Executive Summary -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-robot text-indigo-500 mr-2"></i> AI-Generated Strategies & Insights
            </h2>
            <span class="text-xs font-semibold text-indigo-400 bg-indigo-50 border border-indigo-100 px-2 py-1 rounded shadow-sm">
                Powered by Watt Evolve
            </span>
        </div>

        <!-- Strategies Grid -->
        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Strategic Recommendations</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($aiInsights['strategies'] ?? [] as $strategy)
                <div class="bg-gradient-to-br from-indigo-50 to-white   border border-indigo-100  p-6 rounded-xl shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-indigo-500/5 rounded-full -mr-8 -mt-8"></div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 p-2 bg-indigo-100 rounded-lg text-indigo-600 mr-4">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <p class="text-gray-800  font-medium leading-relaxed">{{ $strategy }}</p>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center text-gray-500 py-4">No strategies available yet.</div>
            @endforelse
        </div>

        <!-- Insights & Warnings Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Key Insights -->
            <div class="lg:col-span-2 space-y-4">
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Market Insights</h3>
                <div class="bg-white  border border-gray-200  rounded-xl p-6 shadow-sm">
                    <ul class="space-y-4">
                        @forelse($aiInsights['insights'] ?? [] as $insight)
                            <li class="flex items-start">
                                <i class="fas fa-chart-line text-emerald-500 mt-1 mr-3"></i>
                                <span class="text-gray-700 ">{{ $insight }}</span>
                            </li>
                        @empty
                            <li class="text-gray-500">No insights available.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Warnings -->
            <div class="space-y-4">
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Risk Radar</h3>
                @forelse($aiInsights['warnings'] ?? [] as $warning)
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700 ">{{ $warning }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
                        <div class="flex">
                             <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">No critical risks detected.</p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Deep Dive Metrics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Revenue Breakdown -->
        <div class="bg-white  p-6 rounded-xl shadow-sm border border-gray-100 ">
            <h3 class="text-lg font-bold text-gray-800  mb-4">Top Revenue Drivers</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 ">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Tour</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500  uppercase">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 ">
                        @foreach($data['revenue_by_tour'] as $tour)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900  font-medium">{{ $tour->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700  text-right">${{ number_format($tour->revenue) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Customer Demographics -->
        <div class="bg-white  p-6 rounded-xl shadow-sm border border-gray-100 ">
            <h3 class="text-lg font-bold text-gray-800  mb-4">Customer Demographics</h3>
             @if(count($data['customer_demographics']) > 0)
                <div class="space-y-4">
                    @foreach($data['customer_demographics'] as $type => $count)
                        @php
                            $total = array_sum($data['customer_demographics']->toArray());
                            $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700 ">{{ ucfirst($type) }}</span>
                                <span class="text-gray-500 ">{{ $count }} customers</span>
                            </div>
                            <div class="w-full bg-gray-200  rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No demographic data available.</p>
            @endif
        </div>
    </div>
</div>
@endsection
