@props([
    'empty' => null,
    'sticky' => true,
])

<div class="bg-white  border border-gray-200  rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 ">

            {{-- Table Head --}}
            <thead class="{{ $sticky ? 'sticky top-0 z-10' : '' }} bg-gray-50 ">
                {{ $head }}
            </thead>

            {{-- Table Body --}}
            <tbody class="bg-white  divide-y divide-gray-200 ">
                @if(trim($body ?? '') !== '')
                    {{ $body }}
                @else
                    <tr>
                        <td colspan="100%" class="px-6 py-14 text-center">
                            {{ $empty ?? 'No records found.' }}
                        </td>
                    </tr>
                @endif
            </tbody>

        </table>
    </div>

    {{-- Footer (pagination etc.) --}}
    @if(isset($footer))
        <div class="bg-gray-50  px-4 py-3 border-t border-gray-200 ">
            {{ $footer }}
        </div>
    @endif
</div>
