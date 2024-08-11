@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2 class="display-6 text-center fw-bold">Edit User</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('users.update',$user->id) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{$user->name}}" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{$user->email}}" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password (Minimum 6 characters)</label>
                    <input type="password" class="form-control" id="password" name="password"
                        value="{{$user->password}}" required>
                </div>
                <div class="mb-3">
                    <label for="password_confirm" class="form-label">Confirm Password (Minimum 6 characters)</label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirmation"
                        value="{{$user->password}}" required>
                </div>
                <div class="mb-3">
                    <label for="regions" class="form-label">Regions</label>
                    <select name="regions[]" id="regions" class="form-control" multiple>
                        @foreach($regions as $region)
                        <option value="{{$region}}" {{in_array($region,explode(',',$user->regions)) ? 'Selected' :
                            ''}}>{{$region}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    {{-- <input type="text" class="form-control" id="roles" name="roles" required> --}}
                    <select name="role" id="role" class="form-control" required>
                        @if(auth()->user()->role == 'super')
                        <option value="admin" {{$user->role == 'admin' ? 'Selected' : '' }}>Admin</option>
                        @endif
                        <option value="user" {{$user->role == 'user' ? 'Selected' : '' }}>User</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>
@endsection
