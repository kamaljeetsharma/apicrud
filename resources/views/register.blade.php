<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
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

        <h1>Register</h1>
        <form id="registration-form">
            <div class="input-wrapper">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
                <span id="name-error" class="validation-message"></span>
            </div>

            <div class="input-wrapper">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <span id="email-error" class="validation-message"></span>
            </div>

            <div class="input-wrapper">
                <label for="mobile_number">Mobile Number:</label>
                <input type="text" id="mobile_number" name="mobile_number" required>
                <span id="mobile_number-error" class="validation-message"></span>
            </div>

            <button type="submit" formnovalidate>Submit</button>
            <div class="form-footer" style="text-align: center; margin-top: 15px;">
                <span>Already have an account? <a href="signin">Login</a></span>
            </div>
        </form>

        <!-- Spinner element -->
        <div class="spinner">
            <div></div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Real-time validation for name
            $('#name').on('input', function() {
                validateName();
            });

            // Real-time validation for email
            $('#email').on('input', function() {
                validateEmailField();
            });

            // Real-time validation for mobile number
            $('#mobile_number').on('input', function() {
                validateMobileNumberField();
            });

            // Form submission handler
            $('#registration-form').on('submit', function(event) {
                event.preventDefault(); // Prevent the default form submission

                let isValid = true;

                // Validate all fields
                if (!validateName()) isValid = false;
                if (!validateEmailField()) isValid = false;
                if (!validateMobileNumberField()) isValid = false;

                if (!isValid) {
                    return; // Prevent form submission if validation fails
                }

                // Clear previous messages and show spinner
                $('.alert').hide();
                $('.validation-message').text('');
                $('.spinner').show();

                const formData = {
                    name: $('#name').val(),
                    email: $('#email').val(),
                    mobile_number: $('#mobile_number').val(),
                };

                $.ajax({
                    url: '/api/signup', // Adjust the URL if necessary
                    type: 'POST',
                    data: JSON.stringify(formData),
                    contentType: 'application/json',
                    success: function(response) {
                        showAlert($('#response-message'), response.message || 'Registration successful!', 'success');
                        $('.spinner').hide(); // Hide spinner on success
                        setTimeout(function() {
                            window.location.href = "/login"; // Redirect to login page
                        }, 1000);
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors || {};
                        const generalMessage = xhr.responseJSON?.message || 'An error occurred';

                        // Handle specific field errors
                        if (errors.email) {
                            $('#email-error').text('Email is already taken');
                        }
                        if (errors.mobile_number) {
                            $('#mobile_number-error').text('Mobile number is already taken');
                        }

                        // Show general error message if no specific field errors
                        if (!errors.email && !errors.mobile_number) {
                            showAlert($('#response-message'), generalMessage, 'error');
                        }

                        $('.spinner').hide(); // Hide spinner on error
                    }
                });
            });

            function validateName() {
                const name = $('#name').val();
                const nameError = $('#name-error');
                if (!name) {
                    nameError.text('Name is required');
                    return false;
                } else {
                    nameError.text('');
                    return true;
                }
            }

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

            function validateMobileNumberField() {
                const mobileNumber = $('#mobile_number').val();
                const mobileNumberError = $('#mobile_number-error');
                if (!mobileNumber) {
                    mobileNumberError.text('Mobile number is required');
                    return false;
                } else if (!validateMobileNumber(mobileNumber)) {
                    mobileNumberError.text('Invalid mobile number format');
                    return false;
                } else {
                    mobileNumberError.text('');
                    return true;
                }
            }

            function validateEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            function validateMobileNumber(mobileNumber) {
                // Example validation: Ensure it's a 10-digit number
                const mobileNumberRegex = /^\d{10}$/;
                return mobileNumberRegex.test(mobileNumber);
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
