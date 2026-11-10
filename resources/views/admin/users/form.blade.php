@php $editing = $item->exists; @endphp
<div class="grid gap-5 sm:grid-cols-2">
    <x-admin.input name="name" label="Name" :value="$item->name" required/>
    <x-admin.input name="email" label="Email (used to sign in)" type="email" :value="$item->email" required autocomplete="off"/>
    <x-admin.input name="password" label="{{ $editing ? 'New password' : 'Password' }}" type="password" :required="! $editing" autocomplete="new-password" :hint="$editing ? 'Leave empty to keep the current password.' : 'At least 10 characters.'"/>
    <x-admin.input name="password_confirmation" label="Confirm password" type="password" autocomplete="new-password"/>
</div>
