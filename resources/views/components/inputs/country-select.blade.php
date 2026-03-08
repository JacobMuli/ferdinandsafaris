@props(['name', 'value' => '', 'class' => '', 'countries' => []])

<div x-data="countrySelect('{{ $value }}', {{ json_encode($countries) }})" class="relative {{ $class }}">


    <!-- Hidden Input for Form Submission -->
    <input type="hidden" name="{{ $name }}" :value="selected">

    <!-- Visible Search Input -->
    <div class="relative">
        <input type="text"
               x-model="search"
               @click="open = true"
               @click.away="open = false"
               @keydown.escape="open = false"
               placeholder="Select a country..."
               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
               autocomplete="off">

        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
        </div>
    </div>

    <!-- Dropdown List -->
    <div x-show="open && filteredCountries.length > 0"
         x-transition
         class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
        <ul>
            <template x-for="country in filteredCountries" :key="country">
                <li @click="select(country)"
                    class="px-4 py-2 hover:bg-emerald-50 cursor-pointer text-sm text-gray-700 hover:text-emerald-700"
                    :class="{'bg-emerald-50 text-emerald-700 font-medium': selected === country}">
                    <span x-text="country"></span>
                </li>
            </template>
        </ul>
    </div>

    <!-- No Results -->
    <div x-show="open && filteredCountries.length === 0"
         class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg px-4 py-2 text-sm text-gray-500">
        No countries found.
    </div>

</div>

<script>
    function countrySelect(initialValue, countriesList) {
        return {
            search: initialValue,
            selected: initialValue,
            open: false,
            countries: countriesList,

            get filteredCountries() {
                if (this.search === '') {
                    return this.countries;
                }
                return this.countries.filter(country =>
                    country.toLowerCase().includes(this.search.toLowerCase())
                );
            },

            select(country) {
                this.selected = country;
                this.search = country;
                this.open = false;
                this.$el.dispatchEvent(new CustomEvent('country-changed', {
                    detail: country,
                    bubbles: true
                }));
            }
        }
    }
</script>
