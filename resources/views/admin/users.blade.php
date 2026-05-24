@extends('layouts.admin')

@section('title', 'Users')
@section('page-title', 'User Management')

@section('content')
    <div class="rounded-3xl bg-white p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="text-xs uppercase tracking-wider text-slate-500"><tr><th class="py-3">User</th><th>Email</th><th>Role</th><th>Joined</th><th class="text-right">Actions</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $user)
                        <tr>
                            <td class="py-4 font-black">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex gap-2">
                                    @csrf @method('PUT')
                                    <select name="role" class="rounded-xl border border-slate-200 px-3 py-2">
                                        <option value="user" @selected($user->role === 'user')>User</option>
                                        <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                    </select>
                                    <button class="rounded-xl bg-slate-950 px-3 py-2 font-bold text-white">Save</button>
                                </form>
                            </td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="text-right">
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}">@csrf @method('DELETE')<button class="rounded-xl bg-rose-50 px-3 py-2 font-bold text-rose-700" onclick="return confirm('Delete this user?')">Delete</button></form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-5">{{ $users->links() }}</div>
    </div>
@endsection
