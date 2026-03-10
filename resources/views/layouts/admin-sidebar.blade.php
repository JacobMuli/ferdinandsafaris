<div class="flex flex-col h-full">
    <!-- Navigation -->
    @php
        $activeGroup = null;
        if(request()->routeIs('admin.bookings.*')) {
            $activeGroup = 'operations';
        } elseif(request()->routeIs('admin.tours.*') || request()->routeIs('admin.locations.*') || request()->routeIs('admin.accommodations.*') || request()->routeIs('admin.vehicle-types.*') || request()->routeIs('admin.vehicles.*') || request()->routeIs('admin.guides.*')) {
            $activeGroup = 'tours';
        } elseif(request()->routeIs('admin.cms-pages.*') || request()->routeIs('admin.reviews.*') || request()->routeIs('admin.testimonials.*')) {
            $activeGroup = 'content';
        } elseif(request()->routeIs('admin.users.*')) {
            $activeGroup = 'users';
        } elseif(request()->routeIs('admin.billing.*') || request()->routeIs('admin.security.*')) {
            $activeGroup = 'system';
        }
    @endphp

    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto" x-data="{ activeGroup: '{{ $activeGroup }}' }">
        <!-- Dashboard -->
        <x-admin-nav-link
            :href="route('admin.dashboard')"
            :active="request()->routeIs('admin.dashboard')"
            icon="fas fa-chart-line"
            label="Dashboard"
        />

        <x-admin-nav-link
            :href="route('admin.messages.index')"
            :active="request()->routeIs('admin.messages.*')"
            icon="fas fa-comments"
            label="Messages"
            :badge="$unreadMessagesCount ?? 0"
        />

        <!-- Operations Group -->
        <x-admin-nav-group label="Operations" icon="fas fa-tasks" key="operations">
            <x-admin-nav-link
                :href="route('admin.bookings.index')"
                :active="request()->routeIs('admin.bookings.index')"
                label="All Bookings"
            />
            <x-admin-nav-link
                :href="route('admin.bookings.calendar')"
                :active="request()->routeIs('admin.bookings.calendar')"
                label="Calendar"
            />
        </x-admin-nav-group>

        <!-- Tours Management Group -->
        <x-admin-nav-group label="Tours & Logistics" icon="fas fa-route" key="tours">
            <x-admin-nav-link
                :href="route('admin.tours.index')"
                :active="request()->routeIs('admin.tours.*')"
                label="Tours"
            />
            <x-admin-nav-link
                :href="route('admin.locations.index')"
                :active="request()->routeIs('admin.locations.*')"
                label="Park Locations"
            />
            <x-admin-nav-link
                :href="route('admin.accommodations.index')"
                :active="request()->routeIs('admin.accommodations.*')"
                label="Accommodations"
            />
            <x-admin-nav-link
                :href="route('admin.vehicle-types.index')"
                :active="request()->routeIs('admin.vehicle-types.*')"
                label="Vehicle Types"
            />
            <x-admin-nav-link
                :href="route('admin.vehicles.index')"
                :active="request()->routeIs('admin.vehicles.*')"
                label="Vehicles"
            />
            <x-admin-nav-link
                :href="route('admin.guides.index')"
                :active="request()->routeIs('admin.guides.*')"
                label="Tour Guides"
            />
        </x-admin-nav-group>

        <!-- Content Management Group -->
        <x-admin-nav-group label="Content & Media" icon="fas fa-photo-video" key="content">
            <x-admin-nav-link
                :href="route('admin.cms-pages.index')"
                :active="request()->routeIs('admin.cms-pages.*')"
                label="Website Pages"
            />
            <x-admin-nav-link
                :href="route('admin.reviews.index')"
                :active="request()->routeIs('admin.reviews.*')"
                label="Reviews"
            />
            <x-admin-nav-link
                :href="route('admin.testimonials.index')"
                :active="request()->routeIs('admin.testimonials.*')"
                label="Testimonials"
            />
        </x-admin-nav-group>

        <!-- User Management Group -->
        <x-admin-nav-group label="User Management" icon="fas fa-users" key="users">
            <x-admin-nav-link
                :href="route('admin.users.customers')"
                :active="request()->routeIs('admin.users.customers')"
                label="Customers"
            />
            <x-admin-nav-link
                :href="route('admin.users.admins')"
                :active="request()->routeIs('admin.users.admins')"
                label="Administrators"
            />
        </x-admin-nav-group>

        <!-- Analytics -->
        <x-admin-nav-link
            :href="route('admin.analytics')"
            :active="request()->routeIs('admin.analytics')"
            icon="fas fa-chart-pie"
            label="Analytics"
        />

        <!-- System & Settings Group -->
        <x-admin-nav-group label="System & Settings" icon="fas fa-cog" key="system">

            <x-admin-nav-link
                :href="route('admin.billing.index')"
                :active="request()->routeIs('admin.billing.*')"
                label="Billing"
            />
            <x-admin-nav-link
                :href="route('admin.security.index')"
                :active="request()->routeIs('admin.security.*')"
                label="Security"
            />
            <x-admin-nav-link
                :href="route('admin.newsletter.index')"
                :active="request()->routeIs('admin.newsletter.*')"
                label="Newsletter Subs"
            />
            <x-admin-nav-link
                :href="route('admin.community-stories.index')"
                :active="request()->routeIs('admin.community-stories.*')"
                label="Guest Stories"
            />
        </x-admin-nav-group>

        <div class="border-t border-white/10 my-4"></div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="w-full flex items-center gap-3 px-4 py-3
                       text-white/70 hover:text-white hover:bg-white/10
                       rounded-lg transition">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </button>
        </form>
    </nav>
</aside>