@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2 class="display-6 text-center"><strong>Current Users</strong></h2>
        </div>
        <div class="card-body">
            <form action="admin.php" method="POST">
                <a href="{{route('users.create')}}" class='btn btn-primary mb-2'>Add New User</a>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center">
                        <thead class="table table-dark">
                            <th>User ID</th>
                            <th>User Name</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Role</th>
                            <th>Regions</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </thead>

                        @foreach($users as $user )
                        <!-- We don't want admins to be able to see and change other admins only super user can do that -->
                        <tr>
                            <td class="align-middle">
                                {{$user->id}}
                            </td>
                            <td class="align-middle">
                                {{$user->name}}
                            </td>
                            <td class="align-middle" style="word-wrap: break-word;min-width: 160px;max-width: 160px;">
                                {{$user->email}}
                            </td>
                            <td class="align-middle">
                                {{$user->password}}
                            </td>
                            <td class="align-middle">
                                {{$user->role}}
                            </td>
                            <td class="align-middle" style="word-wrap: break-word;min-width: 125px;max-width: 15px;">
                                {{$user->sorted_regions}}
                            </td>
                            <td class="align-middle">
                                <a href="{{route('users.edit',$user->id)}}" class="btn btn-primary btn-sm">Edit</a>
                            </td>
                            <td class="align-middle">

                                <form action="{{ route('users.destroy', $user->id)}}" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                                </form>
                            </td>

                        </tr>
                        @endforeach

                    </table>
                </div>
        </div>
    </div>
</div>
@endsection
