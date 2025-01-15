<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Import History') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium">Import History</h3>

                    <!-- Form Export Users -->
                    <form action="{{ route('export.users') }}" method="GET" class="flex items-center space-x-4">
                        <label for="limit" class="text-sm font-medium text-gray-700">
                            Number of Users to Export (max 1000):
                        </label>
                        <input type="number" name="limit" id="limit" value="1000" min="1" max="1000"
                               class="w-20 px-2 py-1 border-gray-300 rounded-md text-sm">
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                            Export
                        </button>
                    </form>
                </div>

                <!-- Table of Import History -->
                <table class="table-auto w-full mt-6">
                    <thead>
                    <tr>
                        <th class="px-4 py-2">File Name</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Success Count</th>
                        <th class="px-4 py-2">Error Count</th>
                        <th class="px-4 py-2">Created At</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($imports as $import)
                        <tr>
                            <td class="border px-4 py-2">{{ $import->file_name }}</td>
                            <td class="border px-4 py-2">{{ $import->status }}</td>
                            <td class="border px-4 py-2">{{ $import->success_count }}</td>
                            <td class="border px-4 py-2">{{ $import->error_count }}</td>
                            <td class="border px-4 py-2">{{ $import->created_at }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{ $imports->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
