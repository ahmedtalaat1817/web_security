<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Category
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-lg p-6">

                <form method="POST"
                      action="{{ route('categories.update', $category) }}">

                    @csrf
                    @method('PUT')

                    <div class="mb-6">
                        <label class="block font-medium mb-2">
                            Category Name
                        </label>

                        <input type="text"
                               name="name"
                               value="{{ $category->name }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm"
                               placeholder="e.g. Appetizers"
                               required>
                    </div>

                    <div class="mb-6">
                        <label class="block font-medium mb-2">
                            Description
                        </label>

                        <textarea name="description"
                                  rows="4"
                                  class="w-full border-gray-300 rounded-lg shadow-sm"
                                  placeholder="Brief description of this category">{{ $category->description }}</textarea>
                    </div>

                    <button type="submit"
                            style="background:#2563eb;color:white;padding:10px 16px;border-radius:8px;border:none;">
                        Update Category
                    </button>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>