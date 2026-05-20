<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Menu Variants
            </h2>

            <a href="{{ route('variants.create') }}"
               style="background:#2563eb;color:white;padding:10px 16px;border-radius:8px;text-decoration:none;">
                Add Variant
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-4 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow rounded-lg overflow-hidden">

                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">Menu Item</th>
                            <th class="px-6 py-3 text-left">Variant</th>
                            <th class="px-6 py-3 text-left">Price Adjustment</th>
                            <th class="px-6 py-3 text-left">Availability</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">

                        @forelse($variants as $variant)

                            <tr>

                                <td class="px-6 py-4">
                                    {{ $variant->menuItem->name }}
                                </td>

                                <td class="px-6 py-4 font-medium">
                                    {{ $variant->name }}
                                </td>

                                <td class="px-6 py-4">
                                    ${{ number_format($variant->price_adjustment, 2) }}
                                </td>

                                <td class="px-6 py-4">
                                    @if($variant->is_available)
                                        <span class="text-green-600 font-semibold">
                                            Available
                                        </span>
                                    @else
                                        <span class="text-red-600 font-semibold">
                                            Unavailable
                                        </span>
                                    @endif
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="4"
                                    class="px-6 py-4 text-center text-gray-500">
                                    No variants found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>
        </div>
    </div>
</x-app-layout>