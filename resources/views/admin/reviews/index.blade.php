@extends('layouts.admin')

@section('title', 'Manage Reviews')

@section('content')
    <div class="bg-white  overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 ">

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 ">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50  text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 bg-gray-50  text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Tour</th>
                            <th class="px-6 py-3 bg-gray-50  text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Rating</th>
                            <th class="px-6 py-3 bg-gray-50  text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Comment</th>
                            <th class="px-6 py-3 bg-gray-50  text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 bg-gray-50  text-right text-xs font-medium text-gray-500  uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white  divide-y divide-gray-200 ">
                        @forelse($reviews as $review)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 ">
                                    {{ $review->customer->first_name ?? 'N/A' }} {{ $review->customer->last_name ?? '' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 ">
                                    {{ $review->tour->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-500">
                                    {{ str_repeat('★', $review->rating) }}
                                    <span class="text-gray-400 text-xs">({{ $review->rating }}/5)</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500  max-w-xs truncate">
                                    {{ $review->comment }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $review->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $review->is_approved ? 'Approved' : 'Pending' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <form action="{{ route('admin.reviews.toggle-status', $review) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="text-blue-600  hover:text-blue-900 mr-3">
                                            {{ $review->is_approved ? 'Reject' : 'Approve' }}
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600  hover:text-red-900">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500 ">
                                    No reviews found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $reviews->links() }}
            </div>

        </div>
    </div>
@endsection
