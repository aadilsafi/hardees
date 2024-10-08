@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2 class="display-6 text-center fw-bold">Profile</h2>
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
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>
@endsection
