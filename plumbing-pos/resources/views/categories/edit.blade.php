
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Category - Plumbing POS</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f4f6f9; }
        .container { max-width: 650px; margin: auto; background: white; padding: 25px; border-radius: 8px; }
        label { display: block; margin: 15px 0 6px; font-weight: bold; }
        input, textarea { width: 100%; box-sizing: border-box; padding: 10px; }
        button { margin-top: 18px; padding: 11px 16px; background: #176b45; color: white; border: 0; cursor: pointer; }
        .error { color: #b42318; }
    </style>
</head>
<body>
<div class="container">
    <h1>Edit Category</h1>

    @if($errors->any())
        <div class="error">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('categories.update', $category) }}" method="POST">
        @csrf
        @method('PUT')

        <label for="name">Category Name *</label>
        <input id="name" name="name" value="{{ old('name', $category->name) }}"
               required maxlength="255">

        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4"
                  maxlength="1000">{{ old('description', $category->description) }}</textarea>

        <button type="submit">Update Category</button>
        <a href="{{ route('categories.index') }}">Cancel</a>
    </form>
</div>
</body>
</html>