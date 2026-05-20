<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Categories
            </h2>

            <a href="{{ route('categories.create') }}"
               style="background:#2563eb;color:white;padding:10px 16px;border-radius:8px;text-decoration:none;">
                Add Category
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
                            <th class="px-6 py-3 text-left">Name</th>
                            <th class="px-6 py-3 text-left">Description</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">

                        @forelse($categories as $category)

                            <tr>

                                <td class="px-6 py-4 font-medium">
                                    {{ $category->name }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $category->description }}
                                </td>

                                <td class="px-6 py-4">
                                    @if($category->is_active)
                                        <span class="text-green-600 font-semibold">
                                            Active
                                        </span>
                                    @else
                                        <span class="text-red-600 font-semibold">
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 flex gap-2">

                                    <a href="{{ route('categories.edit', $category) }}"
                                       style="background:#f59e0b;color:white;padding:8px 12px;border-radius:6px;text-decoration:none;">
                                        Edit
                                    </a>

                                    <form action="{{ route('categories.destroy', $category) }}"
                                          method="POST">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                onclick="return confirm('Delete this category?')"
                                                style="background:#dc2626;color:white;padding:8px 12px;border-radius:6px;border:none;">
                                            Delete
                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="4"
                                    class="px-6 py-4 text-center text-gray-500">
                                    No categories found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>
        </div>
    </div>
</x-app-layout>