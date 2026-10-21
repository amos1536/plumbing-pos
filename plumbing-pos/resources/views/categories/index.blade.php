
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Categories - Plumbing POS</title>
    <style>
        body {
             font-family: Arial, sans-serif; 
             margin: 30px; 
             background: #f4f6f9;
             }
        .container {
             max-width: 1000px; 
             margin: auto; 
            }
        .header {
             display: flex; 
             justify-content: space-between; 
             align-items: center; 
            }
        .btn {
             background: #176b45; 
             color: white; 
             padding: 10px 14px; 
             text-decoration: none; 
             border-radius: 5px; 
            }
        table {
             width: 100%; 
             
             border-collapse: collapse; 
             background: white; 
             margin-top: 20px;
             }
        th, td {
             padding: 12px; 
             border-bottom: 1px solid #ddd; 
             text-align: left;
             }
        th {
             background: #e9eef2;
             }
        .success {
             background: #d9f4e4; 
             padding: 12px;
              margin-top: 15px;
             }
        .error {
             background: #fde8e8; 
             padding: 12px; 
             margin-top: 15px;
             }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Product Categories</h1>
        <a class="btn" href="{{ route('categories.create') }}">+ Add Category</a>
    </div>

    <p>
        <a href="{{ route('products.index') }}">Products</a>
    </p>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="error">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <table>
        <thead>
        <tr>
            <th>Category Name</th>
            <th>Description</th>
            <th>Products</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse($categories as $category)
            <tr>
                <td>{{ $category->name }}</td>
                <td>{{ $category->description ?: '—' }}</td>
                <td>{{ $category->products_count }}</td>
                <td>
                    <a href="{{ route('categories.edit', $category) }}">Edit</a>

                    <form action="{{ route('categories.destroy', $category) }}"
                          method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('Delete this category?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">No categories have been created yet.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{ $categories->links() }}
</div>
</body>
</html>