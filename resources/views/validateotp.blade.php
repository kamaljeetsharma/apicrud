<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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

        <h1>Verify OTP</h1>
        <form id="otp-form">
            <div class="input-wrapper">
                <label for="otp">OTP:</label>
                <input type="text" id="otp" name="otp" required>
                <span id="otp-error" class="validation-message"></span>
            </div>

            <button type="submit" formnovalidate>Verify OTP</button>

            <!-- Spinner element -->
            <div class="spinner">
                <div></div>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            // Real-time validation for OTP
            $('#otp').on('input', function() {
                validateOtp();
            });
    
            // Form submission handler
            $('#otp-form').on('submit', function(event) {
                event.preventDefault(); // Prevent the default form submission
    
                if (!validateOtp()) {
                    return; // Prevent form submission if validation fails
                }
    
                // Clear previous messages and show spinner
                $('.alert').hide();
                $('.validation-message').text('');
                $('.spinner').show();
    
                const otp = $('#otp').val();
    
                $.ajax({
                    url: '/api/may', // Adjust the URL if necessary
                    type: 'POST',
                    data: JSON.stringify({ otp: otp }),
                    contentType: 'application/json',
                    success: function(response) {
                        showAlert($('#response-message'), response.message || 'OTP verified successfully!', 'success');
                        $('.spinner').hide(); // Hide spinner on success
                        setTimeout(function() {
                            localStorage.setItem('reset_email', response.email);
                            window.location.href = "/password"; // Redirect to reset password page
                        }, 1000);
                    },
                    error: function(xhr) {
                        const errorMessage = xhr.responseJSON?.message || 'An error occurred';
                        showAlert($('#response-message'), errorMessage, 'error');
                        $('.spinner').hide(); // Hide spinner on error
                    }
                });
            });
    
            function validateOtp() {
                const otp = $('#otp').val();
                const otpError = $('#otp-error');
                if (!otp) {
                    otpError.text('OTP is required');
                    return false;
                } else if (!/^\d{6}$/.test(otp)) { // Check if OTP is exactly 6 digits
                    otpError.text('OTP must be a 6-digit number');
                    return false;
                } else {
                    otpError.text('');
                    return true;
                }
            }
    
            function showAlert(element, message, type) {
                element.removeClass('success error').addClass(`alert ${type}`);
                element.html(`<span>${message}</span><span class="alert-close" onclick="hideAlert('${element.attr('id')}')">×</span>`);
                element.show();
    
                // Hide alert after 10 seconds
                setTimeout(function() {
                    hideAlert(element.attr('id'));
                }, 10000); // 10 seconds
            }
    
            function hideAlert(id) {
                $(`#${id}`).fadeOut();
            }
        });
    </script>
</body>
</html>    