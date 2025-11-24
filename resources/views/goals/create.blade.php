<x-app-layout>
    <div class="max-w-3xl mx-auto px-6 py-10">

        <h2 class="text-3xl font-bold text-white-800 mb-6">Create Goal</h2>

        <div class="bg-white shadow-md rounded-xl p-6 space-y-6" style="color: black;">

            <form action="{{ route('goals.store') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Title -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Title</label>
                    <input type="text" name="title"
                        class="w-full border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-lg"
                        placeholder="e.g. Reach $1000 income per month" required>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-lg"
                        placeholder="Explain your goal..." required></textarea>
                </div>

                <!-- Target Amount -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Target Amount (optional)</label>
                    <input type="number" name="target_amount"
                        class="w-full border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-lg"
                        placeholder="Example: 1000" required>
                </div>

                <!-- Deadline -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Deadline</label>
                    <input type="date" name="deadline"
                        class="w-full border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-lg" required>
                </div>

                <!-- Save Button -->
                <div class="pt-4">
                    <button class="w-full bg-blue-600 text-white py-3 rounded-lg text-lg font-semibold
                                   hover:bg-blue-700 transition shadow">
                        Save Goal
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>