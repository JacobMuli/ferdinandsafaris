<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800  leading-tight">
            {{ __('Account Settings') }}
        </h2>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800  leading-tight">
            {{ __('Account Settings') }}
        </h2>
    </x-slot>

    <div class="py-6" x-data="{
        activeTab: 'personal',
        loading: false,
        switchTab(tab) {
            this.loading = true;
            this.activeTab = tab;
            setTimeout(() => this.loading = false, 300); // Simulate load for skeleton feel
        }
    }">
        <div class="max-w-full px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row gap-6">

                <!-- Sidebar Navigation -->
                <div class="w-full md:w-64 flex-shrink-0">
                    <!-- Progress Bar -->
                    <div class="mb-6 bg-white  shadow rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 ">Profile Completion</span>
                            <span class="text-sm font-bold text-emerald-600 ">{{ $completionPercentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200  rounded-full h-2.5">
                            <div class="bg-emerald-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $completionPercentage }}%"></div>
                        </div>
                        @if($completionPercentage < 100)
                            <p class="text-xs text-gray-500  mt-2">Complete your profile to get verified!</p>
                        @endif
                    </div>

                    <nav class="space-y-1 bg-white  shadow rounded-lg p-2 sticky top-24">
                        <button @click="switchTab('personal')"
                                :class="{ 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 font-semibold transition-colors': activeTab === 'personal', 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors': activeTab !== 'personal' }"
                                class="w-full flex items-center px-4 py-3 text-sm rounded-r-md">
                            <i class="fas fa-user-circle w-5 h-5 mr-3" :class="{ 'text-emerald-500': activeTab === 'personal', 'text-gray-400': activeTab !== 'personal' }"></i>
                            {{ __('Personal Details') }}
                        </button>

                        <button @click="switchTab('account')"
                                :class="{ 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 font-semibold transition-colors': activeTab === 'account', 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors': activeTab !== 'account' }"
                                class="w-full flex items-center px-4 py-3 text-sm rounded-r-md">
                            <i class="fas fa-id-card w-5 h-5 mr-3" :class="{ 'text-emerald-500': activeTab === 'account', 'text-gray-400': activeTab !== 'account' }"></i>
                            {{ __('Account Info') }}
                        </button>

                        <button @click="switchTab('security')"
                                :class="{ 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 font-semibold transition-colors': activeTab === 'security', 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors': activeTab !== 'security' }"
                                class="w-full flex items-center px-4 py-3 text-sm rounded-r-md">
                            <i class="fas fa-shield-alt w-5 h-5 mr-3" :class="{ 'text-emerald-500': activeTab === 'security', 'text-gray-400': activeTab !== 'security' }"></i>
                            {{ __('Security') }}
                        </button>
                    </nav>
                </div>

                <!-- Content Area -->
                <div class="flex-1 space-y-6">

                    <!-- Skeleton Loader -->
                    <div x-show="loading" class="animate-pulse p-4 sm:p-8 bg-white  shadow sm:rounded-lg space-y-4">
                        <div class="h-6 bg-gray-200  rounded w-1/4"></div>
                        <div class="h-4 bg-gray-200  rounded w-1/2"></div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="h-10 bg-gray-200  rounded"></div>
                            <div class="h-10 bg-gray-200  rounded"></div>
                        </div>
                        <div class="h-32 bg-gray-200  rounded"></div>
                    </div>

                    <!-- Personal Details Tab -->
                    <div x-show="activeTab === 'personal' && !loading" x-cloak>
                        <div class="p-4 sm:p-8 bg-white  shadow sm:rounded-lg">
                            <div class="max-w-4xl">
                                @include('profile.partials.update-customer-information-form')
                            </div>
                        </div>
                    </div>

                    <!-- Account Tab -->
                    <div x-show="activeTab === 'account' && !loading" x-cloak>
                        <div class="p-4 sm:p-8 bg-white  shadow sm:rounded-lg">
                            <div class="max-w-xl">
                                @include('profile.partials.update-profile-information-form')
                            </div>
                        </div>
                    </div>



                    <!-- Security Tab -->
                    <div x-show="activeTab === 'security' && !loading" x-cloak class="space-y-6">
                        <div class="p-4 sm:p-8 bg-white  shadow sm:rounded-lg">
                            <div class="max-w-xl">
                                @include('profile.partials.update-password-form')
                            </div>
                        </div>

                            <div class="max-w-xl">
                                @include('profile.partials.delete-user-form')
                            </div>
                        </div>

                        {{-- Two Factor Authentication (All Users) --}}
                        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                            <div class="max-w-xl">
                                @include('profile.partials.two-factor-authentication-form')
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
