    <header>
        <h2 class="text-lg font-medium text-gray-900 flex items-center gap-2">
            <i class="fas fa-user-circle text-emerald-600"></i>
            {{ __('Customer Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 ml-7">
            {{ __("Update your personal and contact details for bookings.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.customer.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Avatar Upload with Preview -->
            <div class="md:col-span-2" x-data="{ photoName: null, photoPreview: null }">
                <!-- Profile Photo File Input -->
                <input type="file" class="hidden"
                            x-ref="photo"
                            name="avatar"
                            x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

                <x-input-label for="photo" :value="__('Profile Photo')" class="" />

                <div class="mt-2 flex items-center gap-6">
                    <!-- Current Profile Photo -->
                    <div class="mt-2 shrink-0" x-show="!photoPreview">
                        @if($customer->avatar)
                            <img src="{{ Storage::url($customer->avatar) }}" alt="{{ $customer->first_name }}" class="rounded-full h-24 w-24 object-cover ring-4 ring-white  shadow-md">
                        @else
                            <div class="rounded-full h-24 w-24 bg-emerald-100  flex items-center justify-center text-emerald-600  text-3xl font-bold ring-4 ring-white  shadow-md">
                                {{ strtoupper(substr($customer->first_name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <!-- New Profile Photo Preview -->
                    <div class="mt-2 shrink-0" x-show="photoPreview" style="display: none;">
                        <span class="block rounded-full w-24 h-24 bg-cover bg-no-repeat bg-center ring-4 ring-white  shadow-md"
                              x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                        </span>
                    </div>

                    <div class="flex flex-col gap-2">
                         <button type="button" class="inline-flex items-center px-4 py-2 bg-white  border border-gray-300  rounded-md font-semibold text-xs text-gray-700  uppercase tracking-widest shadow-sm hover:bg-gray-50  focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2  disabled:opacity-25 transition ease-in-out duration-150"
                                x-on:click.prevent="$refs.photo.click()">
                            {{ __('Select a New Photo') }}
                        </button>
                        <p class="text-xs text-gray-500 ">JPG, GIF or PNG. 1MB Max.</p>
                    </div>

                </div>
                <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
            </div>

            <!-- First Name -->
            <div>
                <x-input-label for="first_name" :value="__('First Name')" class="" />
                <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full     " :value="old('first_name', $customer->first_name)" required autofocus autocomplete="given-name" />
                <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
            </div>

            <!-- Last Name -->
            <div>
                <x-input-label for="last_name" :value="__('Last Name')" class="" />
                <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full     " :value="old('last_name', $customer->last_name)" required autocomplete="family-name" />
                <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
            </div>

            <!-- Phone -->
            <div>
                <x-input-label for="phone" :value="__('Phone Number')" class="" />
                <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full     " :value="old('phone', $customer->phone)" autocomplete="tel" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>

            <!-- Country -->
            <div>
                <x-input-label for="country" :value="__('Country')" class="" />
                <x-inputs.country-select id="country" name="country" class="mt-1 block w-full" :value="old('country', $customer->country)" :countries="$countries" />
                <x-input-error class="mt-2" :messages="$errors->get('country')" />
            </div>

            <!-- Company Name -->
            <div>
                <x-input-label for="company_name" :value="__('Company Name (Optional)')" class="" />
                <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full     " :value="old('company_name', $customer->company_name)" autocomplete="organization" />
                <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
            </div>

            <!-- Tax ID -->
            <div>
                <x-input-label for="tax_id" :value="__('Tax ID / VAT Number (Optional)')" class="" />
                <x-text-input id="tax_id" name="tax_id" type="text" class="mt-1 block w-full     " :value="old('tax_id', $customer->tax_id)" />
                <x-input-error class="mt-2" :messages="$errors->get('tax_id')" />
            </div>

            <!-- Emergency Contact Name -->
            <div>
                <x-input-label for="emergency_contact_name" :value="__('Emergency Contact Name')" class="" />
                <x-text-input id="emergency_contact_name" name="emergency_contact_name" type="text" class="mt-1 block w-full     " :value="old('emergency_contact_name', $customer->emergency_contact_name)" />
                <x-input-error class="mt-2" :messages="$errors->get('emergency_contact_name')" />
            </div>

            <!-- Emergency Contact Phone -->
            <div>
                <x-input-label for="emergency_contact_phone" :value="__('Emergency Contact Phone')" class="" />
                <x-text-input id="emergency_contact_phone" name="emergency_contact_phone" type="tel" class="mt-1 block w-full     " :value="old('emergency_contact_phone', $customer->emergency_contact_phone)" />
                <x-input-error class="mt-2" :messages="$errors->get('emergency_contact_phone')" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save Details') }}</x-primary-button>

            @if (session('status') === 'customer-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 "
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
