<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Reviews Dashboard
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-lg overflow-hidden">

                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                Customer
                            </th>

                            <th class="px-6 py-3 text-left">
                                Rating
                            </th>

                            <th class="px-6 py-3 text-left">
                                Comment
                            </th>

                            <th class="px-6 py-3 text-left">
                                Date
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">

                        @forelse($reviews as $review)

                            <tr>

                                <td class="px-6 py-4">
                                    {{ $review->user->name }}
                                </td>

                                <td class="px-6 py-4">
                                    ⭐ {{ $review->rating }}/5
                                </td>

                                <td class="px-6 py-4">
                                    {{ $review->comment }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $review->created_at->format('Y-m-d') }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="4"
                                    class="px-6 py-4 text-center text-gray-500">

                                    No reviews found.

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>
        </div>
    </div>
</x-app-layout>