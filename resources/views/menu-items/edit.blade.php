<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Menu Item
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-lg p-6">

                <form method="POST"
                      action="{{ route('menu-items.update', $menuItem) }}">

                    @csrf
                    @method('PUT')

                    <div class="mb-6">
                        <label class="block font-medium mb-2">
                            Category
                        </label>

                        <select name="category_id"
                                class="w-full border-gray-300 rounded-lg shadow-sm"
                                required>

                            @foreach($categories as $category)

                                <option value="{{ $category->id }}"
                                    @selected($menuItem->category_id == $category->id)>

                                    {{ $category->name }}

                                </option>

                            @endforeach

                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block font-medium mb-2">
                            Item Name
                        </label>

                        <input type="text"
                               name="name"
                               value="{{ $menuItem->name }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm"
                               placeholder="e.g. Margherita Pizza"
                               required>
                    </div>

                    <div class="mb-6">
                        <label class="block font-medium mb-2">
                            Description
                        </label>

                        <textarea name="description"
                                  rows="4"
                                  class="w-full border-gray-300 rounded-lg shadow-sm"
                                  placeholder="Describe the item...">{{ $menuItem->description }}</textarea>
                    </div>

                    <div class="mb-6">
                        <label class="block font-medium mb-2">
                            Price
                        </label>

                        <input type="number"
                               step="0.01"
                               name="price"
                               value="{{ $menuItem->price }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm"
                               placeholder="e.g. 12.99"
                               required>
                    </div>

                    <div class="mb-6 flex items-center gap-2">
                        <input type="checkbox"
                               name="is_available"
                               @checked($menuItem->is_available)>

                        <label>
                            Available
                        </label>
                    </div>

                    <button type="submit"
                            style="background:#2563eb;color:white;padding:10px 16px;border-radius:8px;border:none;">
                        Update Menu Item
                    </button>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>