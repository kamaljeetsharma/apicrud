@extends('admin.layouts.app')

@section('content')
    <section class="content">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">View Profile</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="new-page">Home</a></li>
                            <li class="breadcrumb-item active">My Profile</li>
                            <li class="breadcrumb-item"><a href="users">User List</a></li>

                        </ol>
                    </div>
                </div>
                <div class="row">
                    <section class="col-lg-7 connectedSortable">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Profile</h3>
                            </div>
                            <div class="card-body">
                                <div class="tab-content p-0">
                                    <div class="media d-flex flex-column align-items-center">
                                        <img src="admin/dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-100 img-circle">
                                        <button type="button" class="btn btn-primary mt-3">Change</button>
                                    </div>
                                    
                                    <form id="registerForm" method="POST" action="/updateProfile" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" id="name" name="name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="mobile_number">Mobile Number</label>
                                            <input type="text" class="form-control" id="mobile_number" name="mobile_number" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="gender">Gender</label>
                                            <select class="form-control" id="gender" name="gender" required>
                                                <option value="">Select Gender</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="col-lg-5 connectedSortable">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Change Password</h3>
                            </div>
                            <div class="card-body">
                                <div class="tab-content p-0">
                                    <form id="PasswordresetForm" method="POST" action="/updatePassword">
                                        @csrf
                                        <div class="form-group">
                                            <label for="old_password">Old Password</label>
                                            <input type="password" class="form-control" id="old_password" name="old_password" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="new_password">New Password</label>
                                            <input type="password" class="form-control" id="new_password" name="password" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="password_confirmation">Confirm Password</label>
                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                        </div>
                                        <div class="form-group mb-0">
                                            <button type="submit" class="btn btn-primary">Reset Password</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>

    <!-- jQuery is loaded once here -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
      const token = localStorage.getItem('token');
        $('#registerForm, #PasswordresetForm').on('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission

            // Gather the form data
            var formData = new FormData(this);
            var actionUrl = $(this).attr('action'); // Get form action

            // Make the AJAX request
            $.ajax({
                url: actionUrl, 
                method: 'POST',
                headers: {
                     'Authorization': `Bearer ${token}`
                },
                data: formData,
                contentType: false, // Required for FormData
                processData: false, // Required for FormData
                success: function(response) {
                    if (response.status) {
                        alert('Operation completed successfully!');
                        // Optionally, redirect or update the UI
                    } else {
                        alert('Operation failed: ' + response.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessage = 'Validation errors:\n';
                        $.each(errors, function(key, value) {
                            errorMessage += value[0] + '\n'; // Display the first error message for each field
                        });
                        alert(errorMessage);
                    } else if (xhr.status === 401) {
                        alert('Unauthorized: ' + xhr.responseJSON.message);
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                }
            });
        });
    });
    </script>
@endsection
