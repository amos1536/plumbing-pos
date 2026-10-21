
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Supplier | Plumbing POS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 650px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        h1 { color: #1e3a5f; }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button, .back {
            display: inline-block;
            margin-top: 20px;
            padding: 11px 16px;
            border: none;
            border-radius: 5px;
            background: #1e3a5f;
            color: white;
            text-decoration: none;
            cursor: pointer;
        }

        .error { color: #c62828; }
    </style>
</head>
<body>
<div class="container">
    <h1>Register Supplier</h1>
    <p>Enter the plumbing supplier's details below.</p>

    @if ($errors->any())
        <div class="error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('suppliers.store') }}" method="POST">
        @csrf

        <label for="name">Supplier Name *</label>
        <input id="name" type="text" name="name"
               value="{{ old('name') }}" required>

        <label for="phone">Phone Number</label>
        <input id="phone" type="text" name="phone"
               value="{{ old('phone') }}">

        <label for="email">Email Address</label>
        <input id="email" type="email" name="email"
               value="{{ old('email') }}">

        <label for="address">Physical Address</label>
        <input id="address" type="text" name="address"
               value="{{ old('address') }}">

        <label for="kra_pin">KRA PIN</label>
        <input id="kra_pin" type="text" name="kra_pin"
               value="{{ old('kra_pin') }}">

        <button type="submit">Save Supplier</button>
        <a class="back" href="{{ route('suppliers.index') }}">
            Back to Suppliers
        </a>
    </form>
</div>
</body>
</html>