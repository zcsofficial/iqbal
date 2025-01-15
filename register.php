<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Syncgo</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7f8f9;
        }
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            text-align: center;
            color: #003f2f;
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 10px;
        }
        .btn-custom {
            background-color: #00695c;
            color: white;
            border-radius: 25px;
            padding: 10px 20px;
        }
        .btn-custom:hover {
            background-color: #004d40;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: #00695c;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        .destination-list {
            margin-top: 10px;
        }
        .destination-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            position: relative;
        }
        .destination-item input {
            flex-grow: 1;
            margin-right: 10px;
        }
        .autocomplete-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1000;
            background: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            max-height: 150px;
            overflow-y: auto;
        }
        .autocomplete-suggestion {
            padding: 8px 12px;
            cursor: pointer;
        }
        .autocomplete-suggestion:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <h2><i class="fas fa-user-plus"></i> Register</h2>
        <form id="registerForm" method="POST" action="register_action.php">
            <div class="mb-3">
                <label for="firstName" class="form-label">First Name</label>
                <input type="text" class="form-control" id="firstName" name="first_name" placeholder="Enter your first name" required>
            </div>
            <div class="mb-3">
                <label for="lastName" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="lastName" name="last_name" placeholder="Enter your last name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="mb-3">
                <label for="contactNumber" class="form-label">Contact Number</label>
                <input type="text" class="form-control" id="contactNumber" name="contact_number" placeholder="Enter your contact number" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="mb-3">
                <label for="place" class="form-label">Place</label>
                <input type="text" class="form-control" id="place" name="place" placeholder="Enter your place" required>
            </div>
            <div class="mb-3">
                <label for="state" class="form-label">State</label>
                <input type="text" class="form-control" id="state" name="state" placeholder="Enter your state" required>
            </div>
            <div class="mb-3">
                <label for="age" class="form-label">Age</label>
                <input type="number" class="form-control" id="age" name="age" placeholder="Enter your age" required>
            </div>
            <div class="mb-3">
                <label for="destinations" class="form-label">Preferred Destinations (Max 5)</label>
                <div id="destinationContainer" class="destination-list"></div>
                <button type="button" id="addDestination" class="btn btn-sm btn-outline-success mt-2"><i class="fas fa-plus"></i> Add Destination</button>
            </div>
            <button type="submit" class="btn btn-custom w-100">Register</button>
        </form>
        <div class="login-link">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Dynamic Destination Input
    const destinationContainer = document.getElementById('destinationContainer');
    const addDestinationButton = document.getElementById('addDestination');

    let destinationCount = 0;
    const maxDestinations = 5;

    addDestinationButton.addEventListener('click', () => {
        if (destinationCount < maxDestinations) {
            const div = document.createElement('div');
            div.classList.add('destination-item');
            div.innerHTML = `
                <input type="text" class="form-control destination-input" name="destinations[]" placeholder="Enter destination" required autocomplete="off">
                <button type="button" class="btn btn-danger btn-sm remove-destination"><i class="fas fa-trash"></i></button>
                <div class="autocomplete-suggestions"></div>
            `;
            destinationContainer.appendChild(div);
            destinationCount++;

            const input = div.querySelector('.destination-input');
            const suggestionsBox = div.querySelector('.autocomplete-suggestions');

            // Fetch suggestions
            input.addEventListener('input', () => {
                const query = input.value.trim();
                if (query.length > 0) {
                    fetch(`fetch_destinations.php?query=${query}`)
                        .then(response => response.json())
                        .then(data => {
                            suggestionsBox.innerHTML = '';
                            data.forEach(destination => {
                                const suggestion = document.createElement('div');
                                suggestion.classList.add('autocomplete-suggestion');
                                suggestion.textContent = destination;
                                suggestion.addEventListener('click', () => {
                                    input.value = destination;
                                    suggestionsBox.innerHTML = '';
                                });
                                suggestionsBox.appendChild(suggestion);
                            });
                        });
                } else {
                    suggestionsBox.innerHTML = '';
                }
            });

            // Remove Destination
            div.querySelector('.remove-destination').addEventListener('click', () => {
                destinationContainer.removeChild(div);
                destinationCount--;
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Limit Reached',
                text: 'You can only add up to 5 destinations.',
            });
        }
    });

    // SweetAlert for form submission
    document.getElementById('registerForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('register_action.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Registration Successful',
                    text: data.message,
                }).then(() => {
                    window.location.href = 'login.php';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Registration Failed',
                    text: data.message,
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Something went wrong!',
            });
        });
    });
</script>
</body>
</html>
