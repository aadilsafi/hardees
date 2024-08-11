@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2 class="display-6 text-center fw-bold">Add New User</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password (Minimum 6 characters)</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="password_confirm" class="form-label">Confirm Password (Minimum 6 characters)</label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirmation"
                        value="" required>
                </div>
                <div class="mb-3">
                    <label for="regions" class="form-label">Regions</label>
                    <select name="regions[]" id="regions" class="form-control" multiple>
                        @foreach($regions as $region)
                        <option value="{{$region}}">{{$region}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" id="" class="form-control">
                        @if(auth()->user()->role == 'super')
                        <option value="admin">Admin</option>
                        @endif
                        <option value="user">User</option>

                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>
@endsection
