<div>
    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
        <span class="bg-emerald-100 text-emerald-600 w-8 h-8 rounded-full flex items-center justify-center text-sm mr-2">4</span>
        <i class="fas fa-address-card text-emerald-600 mr-2"></i>
        Your Details
    </h3>

    <div class="grid md:grid-cols-2 gap-6 mb-4">
        <div>
            <x-input-label for="first_name" :value="__('First Name')" class="mb-2" />
            <x-text-input
                    type="text"
                    id="first_name"
                    name="first_name"
                    required
                    x-model="bookingFirstName"
                    class="w-full focus:border-emerald-500 focus:ring-emerald-500" />
        </div>

        <div>
            <x-input-label for="last_name" :value="__('Last Name')" class="mb-2" />
            <x-text-input
                    type="text"
                    id="last_name"
                    name="last_name"
                    required
                    x-model="bookingLastName"
                    class="w-full focus:border-emerald-500 focus:ring-emerald-500" />
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6 mb-4">
        <div>
            <x-input-label for="email" :value="__('Email')" class="mb-2" />
            <x-text-input
                    type="email"
                    id="email"
                    name="email"
                    required
                    x-model="bookingEmail"
                    class="w-full focus:border-emerald-500 focus:ring-emerald-500" />
        </div>

        <div>
            <x-input-label for="phone" :value="__('Phone')" class="mb-2" />
            <x-text-input
                    type="tel"
                    id="phone"
                    name="phone"
                    required
                    x-model="bookingPhone"
                    class="w-full focus:border-emerald-500 focus:ring-emerald-500" />
        </div>
    </div>

    <!-- Additional Required Details -->
    <div class="grid md:grid-cols-2 gap-6 mb-4">
        <div>
            <x-input-label for="country" :value="__('Country of Residence')" class="mb-2" />
            <x-inputs.country-select name="country" class="w-full" :value="old('country', optional(auth()->user()?->customer)->country ?? '')" :countries="$countries" @country-changed="bookingCountry = $event.detail"/>
        </div>
    </div>

    <h4 class="text-sm font-semibold text-gray-700 mb-2 mt-4">Emergency Contact</h4>
    <div class="grid md:grid-cols-2 gap-6 mb-4">
        <div>
            <x-input-label for="emergency_contact_name" :value="__('Contact Name')" class="mb-2" />
            <x-text-input
                    type="text"
                    id="emergency_contact_name"
                    name="emergency_contact_name"
                    required
                    x-model="emergencyContactName"
                    class="w-full focus:border-emerald-500 focus:ring-emerald-500" />
        </div>
        <div>
            <x-input-label for="emergency_contact_phone" :value="__('Contact Phone')" class="mb-2" />
            <x-text-input
                    type="tel"
                    id="emergency_contact_phone"
                    name="emergency_contact_phone"
                    required
                    x-model="emergencyContactPhone"
                    class="w-full focus:border-emerald-500 focus:ring-emerald-500" />
        </div>
    </div>

    <!-- Corporate -->
    <div x-show="customerType === 'corporate'" x-transition>
        <h4 class="text-sm font-semibold text-gray-700 mb-2">Company Information</h4>

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                 <x-text-input
                       type="text"
                       name="company_name"
                       x-model="companyName"
                       placeholder="Company Name"
                       class="w-full focus:border-emerald-500 focus:ring-emerald-500" />
            </div>
            <div>
                 <x-text-input
                       type="text"
                       name="tax_id"
                       placeholder="Tax ID (Optional)"
                       class="w-full focus:border-emerald-500 focus:ring-emerald-500" />
            </div>
        </div>
    </div>
</div>
