<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
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
            max-width: 400px;
            width: 100%;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        h1 {
            margin-bottom: 20px;
            color: #333;
            font-size: 24px;
            text-align: center;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            color: #555;
        }
        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input.error {
            border-color: #dc3545;
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
        }
        button:hover {
            background-color: #0056b3;
        }
        .alert {
            position: absolute;
            top: -70px; /* Position alert above the form */
            left: 0;
            width: calc(100% - 40px);
            padding: 15px;
            border-radius: 4px;
            color: white;
            text-align: center;
            font-weight: bold;
            display: none;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.4s ease-out, transform 0.4s ease-out;
            transform: translateY(-10px);
        }
        .alert.success {
            background-color: #28a745;
        }
        .alert.error {
            background-color: #dc3545;
        }
        .alert .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            color: white;
            background: none;
            border: none;
        }
        .validation-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 5px;
            display: none; /* Initially hidden */
        }
        .spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: none; /* Hidden initially */
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
        <div id="response-alert" class="alert">
            <button class="close-btn" id="close-alert">&times;</button>
            <span id="alert-message"></span>
        </div>
        <div class="spinner">
            <div></div>
        </div>
        <h1>Forgot Password</h1>
        <form id="forgotpassword">
            @csrf
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required placeholder="Enter your email">
            <span id="email-error" class="validation-message"></span>
            <button type="submit" formnovalidate>Send OTP</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            // Set CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Real-time validation for email
            $('#email').on('input', function() {
                validateEmailField();
            });

            $("#forgotpassword").on('submit', function(event) {
                event.preventDefault(); // Prevent the default form submission

                // Check if there are validation errors before proceeding
                if ($('#email-error').is(':visible')) {
                    return; // Stop if there are errors
                }

                // Show spinner
                $('.spinner').show();

                $.ajax({
                    url: '/api/forgotpassword',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        email: $("#email").val()
                    }),
                    success: function(response) {
                        if (response.status) {
                            showAlert('OTP sent successfully!', 'success');
                            // Redirect after showing the alert message
                            setTimeout(function() {
                                window.location.href = "/otp"; // Redirect to reset password page
                            }, 2000); // Delay before redirection to allow user to see the message
                        } else {
                            showAlert('Failed to send OTP. Please try again.', 'error');
                        }
                    },
                    error: function(xhr) {
                        const errorMessage = xhr.responseJSON?.message || 'An error occurred';
                        showAlert(errorMessage, 'error');
                    },
                    complete: function() {
                        $('.spinner').hide(); // Hide spinner on completion
                    }
                });
            });

            function validateEmailField() {
                const email = $('#email').val();
                const emailError = $('#email-error');
                if (!email) {
                    emailError.text('Email is required').show();
                    $('#email').addClass('error');
                } else if (!validateEmail(email)) {
                    emailError.text('Invalid email format').show();
                    $('#email').addClass('error');
                } else {
                    emailError.text('').hide();
                    $('#email').removeClass('error');
                }
            }

            function validateEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            function showAlert(message, type) {
                const alertBox = $('#response-alert');
                $('#alert-message').text(message);
                alertBox.removeClass('success error').addClass(type).fadeIn(200).css({
                    'opacity': 1,
                    'transform': 'translateY(0)' // Reset transform to visible position
                });
                setTimeout(function() {
                    if (alertBox.is(':visible')) {
                        alertBox.fadeOut(200, function() {
                            alertBox.css('opacity', 0);
                            alertBox.css('transform', 'translateY(-10px)'); // Apply transform for exit animation
                        });
                    }
                }, 2000); // Show the alert for 2 seconds
            }

            $('#close-alert').on('click', function() {
                $('#response-alert').fadeOut(200, function() {
                    $('#response-alert').css('opacity', 0);
                    $('#response-alert').css('transform', 'translateY(-10px)');
                });
            });
        });
    </script>
</body>
</html>
