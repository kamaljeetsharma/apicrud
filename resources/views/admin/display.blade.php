@extends('admin.layouts.app')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Add User</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/edit">Update user</a></li>
                    <!--<li class="breadcrumb-item"><a href="/users">User List</a></li>-->
                    <li class="breadcrumb-item active">Add User</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header" style="background-color: #ffffff; color: #000000;">
                        <h3 class="card-title">Add User</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="response-message" class="alert" style="display:none; position: absolute; top: 0; width: 100%; z-index: 9999;"></div>
                        <form id="registration-form">
                            @csrf <!-- Important for Laravel forms -->
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" id="name" class="form-control" name="name">
                                <span id="name-error" class="text-danger"></span>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" class="form-control" name="email">
                                <span id="email-error" class="text-danger"></span>
                            </div>

                            <div class="form-group">
                                <label for="mobile_number">Mobile Number</label>
                                <input type="text" id="mobile_number" class="form-control" name="mobile_number">
                                <span id="mobile_number-error" class="text-danger"></span>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-success float-right">Save Changes</button>
                            </div>
                        </form>
                        <div class="spinner-border text-primary" style="display:none;" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
        $('#response-message').hide();
        $('.spinner-border').show();

        const formData = {
            _token: $('input[name="_token"]').val(), // Laravel CSRF token
            name: $('#name').val(),
            email: $('#email').val(),
            mobile_number: $('#mobile_number').val(),
        };

        $.ajax({
            url: '/api/signup', // Update this with your actual route
            type: 'POST',
            data: formData,
            success: function(response) {
                $('.spinner-border').hide(); // Hide spinner on success
                showAlert($('#response-message'), response.message || 'Registration successful!', 'success');
                setTimeout(function() {
                    window.location.href = "/signin"; // Redirect to login page
                }, 1000);
            },
            error: function(xhr) {
                $('.spinner-border').hide(); // Hide spinner on error
                const errors = xhr.responseJSON?.errors || {};
                const generalMessage = xhr.responseJSON?.message || 'An error occurred';

                // Handle specific field errors
                if (errors.email) {
                    $('#email-error').text(errors.email[0]);
                }
                if (errors.mobile_number) {
                    $('#mobile_number-error').text(errors.mobile_number[0]);
                }

                // Show general error message if no specific field errors
                if (!errors.email && !errors.mobile_number) {
                    showAlert($('#response-message'), generalMessage, 'error');
                }
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
        element.removeClass('alert-success alert-error').addClass(`alert alert-${type}`);
        element.html(`<span>${message}</span><span class="alert-close" onclick="hideAlert('${element.attr('id')}')">×</span>`);
        element.show();

        // Automatically hide alert after 10 seconds
        setTimeout(function() {
            hideAlert(element.attr('id'));
        }, 10000);
    }

    function hideAlert(id) {
        $(`#${id}`).fadeOut();
    }
});
</script>
@endsection
