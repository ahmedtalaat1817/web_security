<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Restaurant Dashboard
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h1 class="text-2xl font-bold mb-2">
                    {{ $restaurant->name }}
                </h1>

                <p class="text-gray-600">
                    Welcome back, {{ auth()->user()->name }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">
                        Categories
                    </h3>

                    <p class="text-3xl font-bold mt-2">
                        {{ $categoriesCount }}
                    </p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">
                        Menu Items
                    </h3>

                    <p class="text-3xl font-bold mt-2">
                        {{ $menuItemsCount }}
                    </p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">
                        Reviews
                    </h3>

                    <p class="text-3xl font-bold mt-2">
                        {{ $reviewsCount }}
                    </p>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>