<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom user list</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
        <h1>Manage Custom Users</h1>
        <form action="{{ route('custom-list.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="user_id" class="form-label">User</label>
                <select name="user_id" class="form-control">
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="domain" class="form-label">Host Domain</label>
                <input type="text" name="domain" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Add</button>
        </form>
        <hr>
        <h2>Custom User List</h2>
        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Host</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($customUsers as $customUser)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $customUser->user->name }}</td>
                    <td>{{ $customUser->host->domain }}</td>
                    <td>
                        <form action="{{ route('custom-list.destroy', $customUser->id) }}"
                              method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this custom user?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </body>
</html>
