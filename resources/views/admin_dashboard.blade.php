<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">
                <!-- Upload CSV Section -->
                <div class="bg-gray-100 p-4 rounded-md">
                    <h3 class="text-lg font-medium">Upload CSV File</h3>
                    <form action="{{ route('import.upload') }}" method="POST" enctype="multipart/form-data" class="mt-4">
                        @csrf
                        <label for="csv_file" class="block text-sm font-medium text-gray-700">
                            Select CSV File:
                        </label>
                        <input type="file" name="csv_file" id="csv_file" accept=".csv"
                               class="mt-1 block w-full sm:text-sm border-gray-300 rounded-md">
                        <button type="submit" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Upload
                        </button>
                    </form>
                </div>

                <!-- Export Users Section -->
                <div class="bg-gray-100 p-4 rounded-md">
                    <h3 class="text-lg font-medium">Export Users</h3>
                    <form action="{{ route('export.users') }}" method="GET" class="mt-4">
                        <label for="limit" class="block text-sm font-medium text-gray-700">
                            Number of Users to Export (max 1000):
                        </label>
                        <input type="number" name="limit" id="limit" value="1000" min="1" max="1000"
                               class="mt-1 block w-full sm:text-sm border-gray-300 rounded-md">
                        <button type="submit" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Export
                        </button>
                    </form>
                </div>

                <!-- Import History Section -->
                <div class="bg-gray-100 p-4 rounded-md">
                    <h3 class="text-lg font-medium">Import History</h3>
                    <table class="table-auto w-full mt-4">
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
    </div>
</x-app-layout>
