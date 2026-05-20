<div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

    <x-nav-link
        :href="route('dashboard')"
        :active="request()->routeIs('dashboard')">

        Dashboard

    </x-nav-link>

    <x-nav-link
        :href="route('categories.index')"
        :active="request()->routeIs('categories.*')">

        Categories

    </x-nav-link>

    <x-nav-link
        :href="route('menu-items.index')"
        :active="request()->routeIs('menu-items.*')">

        Menu Items

    </x-nav-link>

    <x-nav-link
        :href="route('variants.index')"
        :active="request()->routeIs('variants.*')">

        Variants

    </x-nav-link>

    <x-nav-link
        :href="route('reviews.index')"
        :active="request()->routeIs('reviews.*')">

        Reviews

    </x-nav-link>

</div>