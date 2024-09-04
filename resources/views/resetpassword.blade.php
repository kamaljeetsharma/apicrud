<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            background-color: #f4f4f4;
        }

        .container {
            max-width: 100%;
            width: 100%;
            max-width: 400px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            box-sizing: border-box;
            position: relative;
        }

        h1 {
            margin-bottom: 20px;
            font-size: 24px;
            text-align: center;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        .input-wrapper {
            position: relative;
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            box-sizing: border-box;
        }

        button:hover {
            background-color: #0056b3;
        }

        .alert {
            padding: 8px 12px;
            border-radius: 4px;
            color: #fff;
            display: none;
            font-size: 0.875rem;    
            box-sizing: border-box;
            width: 100%;
            margin-bottom: 15px; /* Space between alert and other elements */
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
            font-size: 18px;
            margin-left: 10px;
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
            width: 40px;
            height: 40px;
            border: 4px solid rgba(0,0,0,0.1);
            border-radius: 50%;
            border-top: 4px solid #007bff;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 600px) {
            .container {
                padding: 15px;
                margin: 10px;
            }

            h1 {
                font-size: 20px;
            }

            input, button {
                padding: 12px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Response message container -->
        <div id="response-message" class="alert"></div>

        <h1>Reset Password</h1>
        <form id="otp-form">
            <div class="input-wrapper">
                <label for="password">New Password:</label>
                <input type="password" id="password" name="password" required>
                <span id="password-error" class="validation-message"></span>
            </div>

            <div class="input-wrapper">
                <label for="password_confirmation">Confirm Password:</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
                <span id="password_confirmation-error" class="validation-message"></span>
            </div>

            <button type="submit" formnovalidate id="resetButton">Reset Password</button>
        </form>

        <!-- Spinner element -->
        <div class="spinner">
            <div></div>
        </div>
    </div>

    <script>
            $(document).ready(function () {
            const token = localStorage.getItem('token');

            if (!token) {
                // If no token, redirect to login
                console.log('No token found, redirecting to login');
                window.location.href = '/signin';
                return;
            }



            // Real-time validation for password
            $('#password').on('input', function() {
                validatePasswordField();
            });

            $('#password_confirmation').on('input', function() {
                validatePasswordField();
            });

            // Form submission handler
            $('#otp-form').on('submit', function(event) {
                event.preventDefault(); // Prevent the default form submission

                let isValid = true;

                // Validate all fields
                if (!validatePasswordField()) isValid = false;

                if (!isValid) {
                    return; // Prevent form submission if validation fails
                }

                // Clear previous messages and show spinner
                $('.alert').hide();
                $('.validation-message').text('');
                $('.spinner').show();

                const email = localStorage.getItem('reset_email'); // Retrieve the stored email
                const formData = {
                    email: email, // Use the stored email from localStorage
                    password: $('#password').val(),
                    password_confirmation: $('#password_confirmation').val(),
                };

                $.ajax({
                    url: '/api/april', // Ensure this URL is correct
                    type: 'POST',
                    data: JSON.stringify(formData),
                    contentType: 'application/json',
                    success: function(response) {
                        showAlert($('#response-message'), response.message || 'Password reset successful!', 'success');
                        $('.spinner').hide(); // Hide spinner on success
                        setTimeout(function() {
                            localStorage.removeItem('reset_email');
                            window.location.href = "/signin"; // Redirect to login page
                        }, 2000);
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors || {};
                        const generalMessage = xhr.responseJSON?.message || 'An error occurred';

                        // Handle specific field errors
                        if (errors.password) {
                            $('#password-error').text(errors.password[0]);
                        }
                        if (errors.password_confirmation) {
                            $('#password_confirmation-error').text(errors.password_confirmation[0]);
                        }

                        // Show general error message if no specific field errors
                        if (!errors.password && !errors.password_confirmation) {
                            showAlert($('#response-message'), generalMessage, 'error');
                        }

                        $('.spinner').hide(); // Hide spinner on error
                    }
                });

               
            });

            function validatePasswordField() {
                const password = $('#password').val();
                const passwordError = $('#password-error');
                const passwordConfirmation = $('#password_confirmation').val();
                const passwordConfirmationError = $('#password_confirmation-error');
                let isValid = true;

                if (!password) {
                    passwordError.text('Password is required');
                    return false;
                } else if (password.length < 8) { // Example validation
                    passwordError.text('Password must be at least 8 characters');
                    isValid = false;
                } else {
                    passwordError.text('');
                }

                if (!passwordConfirmation) {
                    passwordConfirmationError.text('Password confirmation is required');
                    return false;
                } else if (password !== passwordConfirmation) {
                    passwordConfirmationError.text('Passwords do not match');
                    isValid = false;
                } else {
                    passwordConfirmationError.text('');
                }

                return isValid;
            }

            function showAlert(element, message, type) {
                element.removeClass('success error').addClass(`alert ${type}`);
                element.html(`<span>${message}</span><span class="alert-close" onclick="hideAlert('${element.attr('id')}')">×</span>`);
                element.show();
            }

            function hideAlert(id) {
                $(`#${id}`).fadeOut();
            }
        });
    </script>
</body>
</html>
