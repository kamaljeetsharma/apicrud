<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        /* Basic reset for margins and padding */
        body, ul {
            margin: 0;
            padding: 0;
        }

        /* Navbar styling */
        .navbar {
            background-color: #007bff; /* Background color of the navbar */
            color: white; /* Text color */
            padding: 10px 20px; /* Padding around the navbar */
            display: flex; /* Flexbox layout */
            justify-content: space-between; /* Space between links */
            align-items: center; /* Center items vertically */
        }

        /* List styling for navbar links */
        .nav-links {
            list-style-type: none; /* Remove bullets from list */
            display: flex; /* Align links horizontally */
            padding: 0;
        }

        /* List items styling */
        .nav-links li {
            margin: 0 15px; /* Space between links */
        }

        /* Link styling */
        .nav-links a {
            color: white; /* Text color */
            text-decoration: none; /* Remove underline from links */
            font-size: 16px; /* Font size of the links */
            padding: 5px 10px; /* Padding for clickable area */
            border-radius: 4px; /* Rounded corners */
            transition: background-color 0.3s, color 0.3s; /* Smooth transitions */
        }

        /* Change background color and text color on hover */
        .nav-links a:hover {
            background-color: #0056b3; /* Darker background color on hover */
            color: #e0e0e0; /* Light color on hover */
        }

        /* Container for the main content */
        .content {
            padding: 20px; /* Padding around the content */
        }

        /* Styling for the name */
        #name {
            font-size: 24px; /* Font size for the name */
            font-weight: bold; /* Make name bold */
            margin-top: 20px; /* Space above the name */
        }

        /* Responsive design for smaller screens */
        @media (max-width: 768px) {
            .nav-links {
                flex-direction: column; /* Stack links vertically */
                align-items: center; /* Center links */
            }

            .nav-links li {
                margin: 10px 0; /* Space between links */
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <ul class="nav-links">
            <li><a href="/home">Home</a></li>
            <li><a href="/about">About</a></li>
            <li><a href="/kamal">Forgot Password</a></li>
            <li><a id="logout">Logout</a></li>
        </ul>
    </nav>
    <div class="content">
        <h1>Welcome to the Dashboard</h1>
        <h2 id="name"></h2>
    </div>

    <script>
        $(document).ready(function () {
            const token = localStorage.getItem('token');

            if (!token) {
                // If no token, redirect to login
                console.log('No token found, redirecting to login');
                window.location.href = '/login';
                return;
            }

            // Validate token on the server
            $.ajax({
                url: '/api/dashboard',  // Endpoint to validate token
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`
                },
                success: function (response) {
                    console.log(response.userDetails);

                    // Ensure response.userDetails is not null and has a name property
                    if (response.userDetails) {
                        document.getElementById('name').innerHTML = response.userDetails;
                    } else {
                        console.log('User details are not available or do not contain a name');
                    }

                    console.log('Token is valid');
                },
                error: function (response) {
                    console.log(response);
                    console.log('Invalid token');
                    // If token is invalid, clear storage and redirect to login
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                }
            });

            $('#logout').on('click', function () {
                // Remove the token and redirect to login
                localStorage.removeItem('token');
                console.log('Token removed, redirecting to login');
                window.location.href = '/login';
            });
        });
    </script>
</body>
</html>
