<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        /* Basic styles for form */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #6f86d6, #48c6ef);
        }

        .container {
            max-width: 100%;
            width: 100%;
            max-width: 400px;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            position: relative;
        }

        h1 {
            margin-bottom: 20px;
            font-size: 28px;
            text-align: center;
            color: #333;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
            color: #555;
        }

        .input-wrapper {
            position: relative;
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 0;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            box-sizing: border-box;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .alert {
            padding: 10px 15px;
            border-radius: 6px;
            color: #fff;
            display: none;
            font-size: 0.875rem;
            box-sizing: border-box;
            width: 100%;
            margin-bottom: 15px;
        }

        .alert.error {
            background-color: #f44336;
        }

        .alert.success {
            background-color: #4CAF50;
        }

        .alert-close {
            cursor: pointer;
            font-weight: bold;
            color: #fff;
            font-size: 20px;
            margin-left: 10px;
            position: absolute;
            top: 10px;
            right: 15px;
        }

        .validation-message {
            color: #f44336;
            font-size: 0.875rem;
            margin-top: 5px;
        }

        /* Spinner styles */
        .spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: none;
        }

        .spinner div {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            border-top: 5px solid #007bff;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
                margin: 10px;
            }

            h1 {
                font-size: 24px;
            }

            input,
            button {
                padding: 15px;
                font-size: 16px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Response message container -->
        <div id="response-message" class="alert"></div>

        <h1>Login</h1>
        <form id="login-form">
            <div class="input-wrapper">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <span id="email-error" class="validation-message"></span>
            </div>

            <div class="input-wrapper">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <span id="password-error" class="validation-message"></span>
            </div>

            <button type="submit" formnovalidate>Submit</button>
            <div class="form-footer" style="text-align: center; margin-top: 20px;">
                <span><a href="kamal" style="color: #007bff;">Forgot password?</a> | <a href="register" style="color: #007bff;">Register</a></span>
            </div>
        </form>

        <!-- Spinner element -->
        <div class="spinner">
            <div></div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Real-time validation for email
            $('#email').on('input', function () {
                validateEmailField();
            });

            // Real-time validation for password
            $('#password').on('input', function () {
                validatePasswordField();
            });

            // Form submission handler
            $('#login-form').on('submit', function (event) {
                event.preventDefault(); // Prevent the default form submission

                let isValid = true;

                // Validate fields
                if (!validateEmailField()) isValid = false;
                if (!validatePasswordField()) isValid = false;

                if (!isValid) {
                    return; // Prevent form submission if validation fails
                }

                // Clear previous messages and show spinner
                $('.alert').hide();
                $('.validation-message').text('');
                $('.spinner').show();

                const formData = {
                    email: $('#email').val(),
                    password: $('#password').val(),
                };

                $.ajax({
                    url: '/api/login',
                    type: 'post',
                    data: JSON.stringify(formData),
                    contentType: 'application/json',
                    success: function (response) {
                        console.log('Login successful:', response.token);

                        // Save the token in localStorage
                        localStorage.setItem('token',  response.token);

                        // Optionally, you can save the token type as well
                        localStorage.setItem('token_type', response.token_type);

                        // Show a success message
                        showAlert($('#response-message'), response.message || 'Login successful!', 'success');
                        $('.spinner').hide();

                        // Redirect to the dashboard page after a short delay
                        setTimeout(function () {
                            window.location.href = '/admin-page'; // Redirect to dashboard page
                        }, 1000); // Delay redirection to show success message
                    },
                    error: function (xhr) {
                        console.log('Login error:', xhr.responseJSON);
                        const errors = xhr.responseJSON?.errors || {};
                        const generalMessage = xhr.responseJSON?.message || 'An error occurred';

                        // Handle specific field errors
                        if (errors.email) {
                            $('#email-error').text('Email is incorrect or not registered');
                        }
                        if (errors.password) {
                            $('#password-error').text('Password is incorrect');
                        }

                        // Show general error message if no specific field errors
                        if (!errors.email && !errors.password) {
                            showAlert($('#response-message'), generalMessage, 'error');
                        }

                        $('.spinner').hide(); // Hide spinner on error
                    }
                });
            });

            function validateEmailField() {
                const email = $('#email').val();
                const emailError = $('#email-error');
                if (!email) {
                    emailError.text('Email is required');
                    return false;
                } else if (!validateEmail(email)) {
                    emailError.text('Invalid email format');
                    return false;
                } else {
                    emailError.text('');
                    return true;
                }
            }

            function validatePasswordField() {
    const password = $('#password').val();
    const passwordError = $('#password-error');
    const passwordRegex = /^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/; // Regex for password validation

    if (!password) {
        passwordError.text('Password is required');
        return false;
    } else if (!passwordRegex.test(password)) {
        passwordError.text('Password must be at least 8 characters long, contain at least one uppercase letter, and one number.');
        return false;
    } else {
        passwordError.text('');
        return true;
    }
}


            function validateEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

        });

        function showAlert(element, message, type) {
            element.removeClass('success error').addClass(`alert ${type}`);
            element.html(`<span>${message}</span><span class="alert-close" onclick="hideAlert('${element.attr('id')}')">×</span>`);
            element.show();
        }

        function hideAlert(id) {
            $(`#${id}`).fadeOut();
        }
    </script>
</body>

</html>
