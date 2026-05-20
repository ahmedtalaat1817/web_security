<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create Variant
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-lg p-6">

                <form method="POST"
                      action="{{ route('variants.store') }}">

                    @csrf

                    <div class="mb-6">
                        <label class="block font-medium mb-2">
                            Menu Item
                        </label>

                        <select name="menu_item_id"
                                class="w-full border-gray-300 rounded-lg shadow-sm"
                                required>

                            <option value="">
                                Select Menu Item
                            </option>

                            @foreach($menuItems as $item)

                                <option value="{{ $item->id }}">
                                    {{ $item->name }}
                                </option>

                            @endforeach

                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block font-medium mb-2">
                            Variant Name
                        </label>

                        <input type="text"
                               name="name"
                               class="w-full border-gray-300 rounded-lg shadow-sm"
                               placeholder="Small / Medium / Large"
                               required>
                    </div>

                    <div class="mb-6">
                        <label class="block font-medium mb-2">
                            Price Adjustment
                        </label>

                        <input type="number"
                               step="0.01"
                               name="price_adjustment"
                               class="w-full border-gray-300 rounded-lg shadow-sm"
                               placeholder="e.g. 2.50"
                               required>
                    </div>

                    <div class="mb-6 flex items-center gap-2">
                        <input type="checkbox"
                               name="is_available"
                               checked>

                        <label>
                            Available
                        </label>
                    </div>

                    <button type="submit"
                            style="background:#2563eb;color:white;padding:10px 16px;border-radius:8px;border:none;">
                        Create Variant
                    </button>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>