@extends('layouts.admin')

@section('title', 'Billing & Invoices')

@section('subtitle', 'Manage financial records and invoice settings')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Stat Card -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Total Revenue</h3>
            <div class="p-2 bg-emerald-100 rounded-lg text-emerald-600">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>
        <div class="text-3xl font-bold text-gray-900">$0.00</div>
        <div class="mt-2 text-sm text-gray-500">Year to date</div>
    </div>

    <!-- Stat Card -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Pending Invoices</h3>
            <div class="p-2 bg-yellow-100 rounded-lg text-yellow-600">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
        </div>
        <div class="text-3xl font-bold text-gray-900">0</div>
        <div class="mt-2 text-sm text-gray-500">Needs attention</div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-900">Recent Transactions</h3>
        <button class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Export Report</button>
    </div>
    <div class="p-8 text-center text-gray-500">
        <div class="mb-3">
             <i class="fas fa-receipt text-4xl text-gray-300"></i>
        </div>
        <p>No transaction history available yet.</p>
    </div>
</div>
@endsection
