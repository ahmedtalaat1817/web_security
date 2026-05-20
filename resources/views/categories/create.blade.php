<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create Category
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-lg p-6">

                <form method="POST" action="{{ route('categories.store') }}">

                    @csrf

                    <div class="mb-6">
                        <label class="block font-medium mb-2">
                            Category Name
                        </label>

                        <input type="text"
                               name="name"
                               class="w-full border-gray-300 rounded-lg shadow-sm"
                               placeholder="e.g. Appetizers"
                               required>

                        @error('name')
                            <p class="text-red-500 text-sm mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block font-medium mb-2">
                            Description
                        </label>

                        <textarea name="description"
                                  rows="4"
                                  class="w-full border-gray-300 rounded-lg shadow-sm"
                                  placeholder="Brief description of this category"></textarea>
                    </div>

                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                        Create Category
                    </button>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>