export default function bookingForm(config) {
    return {
        tourId: config.tourId,
        calculatePriceUrl: config.calculatePriceUrl,
        checkAvailabilityUrl: config.checkAvailabilityUrl,

        customerType: 'individual',
        adults: 1,
        children: 0,
        vehicleTypeId: '',
        accommodationId: '',
        residentStatus: 'non_resident',
        tourDate: '',

        // Contact Info
        bookingFirstName: config.initial?.firstName || '',
        bookingLastName: config.initial?.lastName || '',
        bookingEmail: config.initial?.email || '',
        bookingPhone: config.initial?.phone || '',
        bookingCountry: config.initial?.country || '',
        emergencyContactName: config.initial?.emergencyContactName || '',
        emergencyContactPhone: config.initial?.emergencyContactPhone || '',
        companyName: '',

        pricing: {
            base_price: 0,
            discount: 0,
            final_price: 0,
            tax: 0,
            total: 0,
            breakdown: {}
        },

        availableSpots: null,
        loading: false,
        isValid: false,

        init() {
            this.$watch('customerType', () => {
                this.updatePrice();
                this.validate();
            });
            this.$watch('adults', () => this.updatePrice());
            this.$watch('children', () => this.updatePrice());
            this.$watch('vehicleTypeId', () => this.updatePrice());
            this.$watch('accommodationId', () => this.updatePrice());
            this.$watch('residentStatus', () => this.updatePrice());
            this.$watch('tourDate', () => this.checkAvailability());
            
            // Watch contact fields for validation
            this.$watch('bookingFirstName', () => this.validate());
            this.$watch('bookingLastName', () => this.validate());
            this.$watch('bookingEmail', () => this.validate());
            this.$watch('bookingPhone', () => this.validate());
            this.$watch('bookingCountry', () => this.validate());
            this.$watch('emergencyContactName', () => this.validate());
            this.$watch('emergencyContactPhone', () => this.validate());
            this.$watch('companyName', () => this.validate());

            this.updatePrice();
            this.validate();
        },

        async updatePrice() {
            if (this.adults < 1) return;

            try {
                const payload = {
                    customer_type: this.customerType,
                    adults_count: parseInt(this.adults) || 1,
                    children_count: parseInt(this.children) || 0,
                    vehicle_type_id: this.vehicleTypeId ? parseInt(this.vehicleTypeId) : null,
                    accommodation_id: this.accommodationId ? parseInt(this.accommodationId) : null,
                    resident_status: this.residentStatus
                };

                const response = await fetch(this.calculatePriceUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                if (response.ok) {
                    const data = await response.json();
                    this.pricing = data;
                    // Calculate tax and total based on final_price
                    this.pricing.tax = this.pricing.final_price * 0.16;
                    this.pricing.total = this.pricing.final_price + this.pricing.tax;
                } else {
                    const errorData = await response.json();
                    console.error('Price calculation validation failed:', errorData);
                }
            } catch (error) {
                console.error('Price calculation failed:', error);
            }

            this.validate();
        },

        async checkAvailability() {
            if (!this.tourDate) return;

            try {
                const response = await fetch(this.checkAvailabilityUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        date: this.tourDate
                    })
                });

                if (response.ok) {
                    const data = await response.json();
                    this.availableSpots = data.available_spots;
                }
            } catch (error) {
                console.error('Availability check failed:', error);
            }

            this.validate();
        },

        validate() {
            try {
                const hasBasicInfo = (this.bookingFirstName || '').toString().trim() !== '' &&
                                   (this.bookingLastName || '').toString().trim() !== '' &&
                                   (this.bookingEmail || '').toString().trim() !== '' &&
                                   (this.bookingPhone || '').toString().trim() !== '' &&
                                   (this.bookingCountry || '').toString().trim() !== '' &&
                                   (this.emergencyContactName || '').toString().trim() !== '' &&
                                   (this.emergencyContactPhone || '').toString().trim() !== '';

                const hasCorporateInfo = this.customerType !== 'corporate' || (this.companyName || '').toString().trim() !== '';

                this.isValid = (this.adults || 0) >= 1 && 
                               (this.tourDate || '') !== '' && 
                               (this.availableSpots === null || this.availableSpots >= ((this.adults || 0) + (this.children || 0))) &&
                               hasBasicInfo &&
                               hasCorporateInfo;
            } catch (error) {
                console.error('Validation error:', error);
                this.isValid = false;
            }
        }
    };
}
