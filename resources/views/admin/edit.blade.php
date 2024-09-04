@extends('admin.layouts.app')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Update User Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/sahil">Add user </a></li>
                    <!--<li class="breadcrumb-item"><a href="/users">User List</a></li>-->
                    <li class="breadcrumb-item active">Update User</li>
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
                        <h3 class="card-title">Update User Details</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="updateprofile">
                            <div id="response-message" class="alert" style="display:none;"></div>

                            <div class="form-group">
                                <label for="inputName">Name</label>
                                <input type="text" id="inputName" name="name" class="form-control" value="">
                                <span id="name-error" class="text-danger"></span>
                            </div>

                            <div class="form-group">
                                <label for="inputEmail">Email</label>
                                <input type="email" id="inputEmail" name="email" class="form-control" value="">
                                <span id="email-error" class="text-danger"></span>
                            </div>

                            <div class="form-group">
                                <label for="inputMobile">Mobile Number</label>
                                <input type="text" id="inputMobile" name="mobile_number" class="form-control" value="">
                                <span id="mobile_number-error" class="text-danger"></span>
                            </div>

                            <div class="form-group">
                                <label for="inputGender">Gender</label>
                                <select id="inputGender" name="gender" class="form-control custom-select">
                                    <option disabled selected>Select one</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="others">Others</option>
                                </select>
                                <span id="gender-error" class="text-danger"></span>
                            </div>

                            <div class="form-group">
                                <label for="inputAddress">Address</label>
                                <textarea id="inputAddress" name="address" class="form-control" rows="4"></textarea>
                                <span id="address-error" class="text-danger"></span>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-success float-right">Save Changes</button>
                                <div class="spinner-border text-primary" style="display:none;" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include AdminLTE JS -->
<script src="{{ asset('path/to/adminlte.min.js') }}"></script>

<script>
$(document).ready(function() {
    const token = localStorage.getItem('token');

    // Real-time validation
    $('#inputName').on('input', validateName);
    $('#inputEmail').on('input', validateEmailField);
    $('#inputMobile').on('input', validateMobileNumberField);
    $('#inputGender').on('change', validateGender);
    $('#inputAddress').on('input', validateAddress);

    $('#updateprofile').on('submit', function(event) {
        event.preventDefault();

        let isValid = true;

        // Validate all fields
        isValid &= validateName();
        isValid &= validateEmailField();
        isValid &= validateMobileNumberField();
        isValid &= validateGender();
        isValid &= validateAddress();

        if (!isValid) {
            return;
        }

        $('#response-message').hide();
        $('.spinner-border').show();

        var formData = new FormData(this);

        $.ajax({
            url: '/api/update',
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`
            },
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('.spinner-border').hide();
                showAlert($('#response-message'), response.message || 'Profile updated successfully!', 'success');
                setTimeout(function() {
                    window.location.href = '/dashboard';
                }, 1000);
            },
            error: function(xhr) {
                $('.spinner-border').hide();
                const errors = xhr.responseJSON?.errors || {};
                const generalMessage = xhr.responseJSON?.message || 'An error occurred';

                if (xhr.status === 422) {
                    $('#name-error').text(errors.name?.[0] || '');
                    $('#email-error').text(errors.email?.[0] || '');
                    $('#mobile_number-error').text(errors.mobile_number?.[0] || '');
                    $('#gender-error').text(errors.gender?.[0] || '');
                    $('#address-error').text(errors.address?.[0] || '');
                } else {
                    showAlert($('#response-message'), generalMessage, 'error');
                }
            }
        });
    });

    function validateName() {
        const name = $('#inputName').val();
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
        const email = $('#inputEmail').val();
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
        const mobileNumber = $('#inputMobile').val();
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

    function validateGender() {
        const gender = $('#inputGender').val();
        const genderError = $('#gender-error');
        if (!gender) {
            genderError.text('Gender is required');
            return false;
        } else {
            genderError.text('');
            return true;
        }
    }

    function validateAddress() {
        const address = $('#inputAddress').val();
        const addressError = $('#address-error');
        if (!address) {
            addressError.text('Address is required');
            return false;
        } else {
            addressError.text('');
            return true;
        }
    }

    function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function validateMobileNumber(mobileNumber) {
        const mobileNumberRegex = /^\d{10}$/;
        return mobileNumberRegex.test(mobileNumber);
    }

    function showAlert(element, message, type) {
        element.removeClass('alert-success alert-error').addClass(`alert alert-${type}`);
        element.html(`<span>${message}</span><span class="alert-close" onclick="hideAlert('${element.attr('id')}')">×</span>`);
        element.show();
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
