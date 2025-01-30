<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .login-container {
            width: 100%;
            max-width: 440px;
            padding: 2.5rem;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 24px;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(12px);
        }

        .software-name {
            font-size: 2.25rem;
            color: #212529;
            letter-spacing: -0.03em;
            margin-bottom: 2.5rem !important;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-control {
            height: 52px;
            padding: 0 1rem;
            border: 2px solid #dee2e6;
            border-radius: 12px;
            transition: all 0.2s ease;
            background: #fff;
        }

        .form-control:focus {
            border-color: #4dabf7;
            box-shadow: 0 0 0 3px rgba(77, 171, 247, 0.15);
        }

        .form-label {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: #fff;
            padding: 0 0.25rem;
            color: #868e96;
            transition: all 0.2s ease;
            pointer-events: none;
        }

        .form-control:focus~.form-label,
        .form-control:not(:placeholder-shown)~.form-label {
            transform: translateY(-180%);
            font-size: 0.875rem;
            color: #4dabf7;
        }

        .login-btn {
            width: 100%;
            padding: 0.875rem;
            border-radius: 12px;
            background: #212529;
            color: white;
            font-weight: 600;
            transition: all 0.2s ease;
            border: 2px solid transparent;
        }

        .login-btn:hover {
            background: #343a40;
            transform: translateY(-1px);
        }

        .form-check-input {
            width: 1.25em;
            height: 1.25em;
            margin-top: 0.15em;
            border: 2px solid #dee2e6;
        }

        .form-check-input:checked {
            background-color: #212529;
            border-color: #212529;
        }

        .additional-links {
            margin-top: 1.5rem;
            text-align: center;
        }

        .additional-links a {
            color: #868e96;
            text-decoration: none;
            font-size: 0.875rem;
        }
    </style>

</head>

<body>

    <div class="container">
        <h2 class="text-center software-name">{{ env('APP_NAME') }}</h2>
        <div class="login-container">
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <input type="email" name="email" id="email" class="form-control" required placeholder=" ">
                    <label for="email" class="form-label">Email</label>
                </div>
                <div class="form-group">
                    <input type="password" name="password" id="password" class="form-control" required placeholder=" ">
                    <label for="password" class="form-label">Password</label>
                </div>
                <div class="form-group form-check">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <button type="submit" class="btn login-btn">Login</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const email = this.querySelector('#email').value;
            const password = this.querySelector('#password').value;

            if (!email || !password) {
                e.preventDefault();
                alert('Please fill in all required fields');
            }
        });

        // Add input animations
        document.querySelectorAll('.form-group input').forEach(input => {
            input.addEventListener('focus', () => {
                input.parentNode.style.transform = 'scale(1.02)';
            });

            input.addEventListener('blur', () => {
                input.parentNode.style.transform = 'scale(1)';
            });
        });
    </script>
</body>

</html>
