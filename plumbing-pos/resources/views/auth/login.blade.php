
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Plumbing POS</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: white;
            padding: 32px;
            border: 1px solid var(--border);
            border-radius: 10px;
        }

        .login-card h1 {
            color: var(--primary);
            margin-top: 0;
        }

        .login-card button {
            width: 100%;
            margin-top: 20px;
        }

        .field-error {
            color: var(--danger);
            font-size: 14px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: normal;
        }

        .remember input {
            width: auto;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h1>Plumbing POS</h1>
    <p class="text-muted">Sign in to manage your shop.</p>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}">
        @csrf

        <label for="email">Email Address</label>
        <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email') }}"
            autocomplete="username"
            required
            autofocus
        >

        <label for="password">Password</label>
        <input
            id="password"
            type="password"
            name="password"
            autocomplete="current-password"
            required
        >

        <label class="remember">
            <input type="checkbox" name="remember" value="1">
            Remember me
        </label>

        <button type="submit">Sign In</button>
    </form>
</div>

</body>
</html>