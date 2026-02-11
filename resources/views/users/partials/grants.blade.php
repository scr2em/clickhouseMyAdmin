<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    @if(!empty($grants))
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Grant</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($grants as $grant)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 font-mono text-sm text-gray-900">{{ $grant }}</td>
                <td class="px-4 py-2 text-right">
                    <form method="POST" action="{{ route('users.revoke', $user) }}"
                          hx-post="{{ route('users.revoke', $user) }}"
                          hx-target="#grants-list"
                          hx-swap="innerHTML"
                          hx-boost="false"
                          onsubmit="if(!this.hasAttribute('data-hx-post')){return confirm('Revoke this grant?');}">
                        @csrf
                        <input type="hidden" name="grant_statement" value="{{ $grant }}">
                        <button type="submit" class="text-sm text-red-500 hover:text-red-700">Revoke</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="px-4 py-6 text-center text-gray-400">No grants found</div>
    @endif
</div>
