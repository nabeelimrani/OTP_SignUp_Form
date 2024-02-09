  // Fetch and populate countries
        $(document).ready(function() {
            // ...
            $.ajax({
                url: 'https://restcountries.com/v2/all',
                type: 'GET',
                success: function(data) {
                    for (var i = 0; i < data.length; i++) {
                        $('#country').append('<option value="' + data[i].name + '">' + data[i].name +
                            '</option>');
                    }
                },
                error: function() {
                    console.log('Error fetching countries');
                }
            });
            // Fetch country codes and populate dropdown
            $.ajax({
                url: 'https://restcountries.com/v3.1/all',
                type: 'GET',
                success: function(data) {
                    for (var i = 0; i < data.length; i++) {
                        var country = data[i];
                        var countryCode = country.idd.root + (country.idd.suffixes ? country.idd
                            .suffixes[0] : '');
                        var countryName = country.name.common;

                        // Append each country code to the dropdown
                        $('#countryCode').append(
                            `<option value="${countryCode}" data-country="${countryName}">${countryCode} (${countryName})</option>`
                        );
                    }
                },
                error: function(err) {
                    console.error('Error fetching country codes:', err);
                }
            });


            // Update country code when the country is selected
            $('#country').change(function() {
                var selectedCountry = $('#country').val();
                $('#countryCode option').removeAttr('selected');
                $('#countryCode option[data-country="' + selectedCountry + '"]').attr('selected',
                    'selected');

                var selectedCode = $('#countryCode option:selected').val();
                $('#phone').val('');

                $('#phone').attr('placeholder', '(' + selectedCode + ')-');

                $('#countryCode').prop('disabled', true);


            });

            $('#signupForm').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission


                // Collect form data
                var formData = $('#signupForm').serialize();

                // Optional: Validate the formData here
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                // Send the form data
                $.ajax({
                    type: 'POST',
                    url: 'user/submit', // Replace with your server endpoint
                    data: formData,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            html: 'Form Submit Successfully.',
                        });
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        Swal.fire({ // Using SweetAlert2 for better UI
                            icon: 'error',
                            title: 'Oops...',
                            html: xhr.responseJSON.message,
                        });

                    }
                });
            });
            $('#verifyEmail').click(function() {
                var email = $('#email').val();
                if (!email) { // Check if the email variable is an empty string, null, or undefined
                    // Display a SweetAlert2 error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please enter your email address.',
                    });
                } else {
                    // AJAX call to send OTP
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: '/sendemail/otp',
                        type: 'POST',
                        data: {
                            email: email
                        },
                        success: function(response) {
                            // Show OTP input and submit button upon success

                            // Show the modal upon success
                            $('#otpModal').modal(
                                'show'); // Using Bootstrap's modal method to show the modal

                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'OTP has been sent to your email.',
                            });
                            $('#showbtn').removeClass('d-none');
                            $('#verifyEmail').addClass('d-none');
                            $('#resendOTP').prop('disabled', false);

                        },
                        error: function(xhr) {
                            // Handle error response
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: xhr.responseJSON.message ? xhr.responseJSON
                                    .message : 'An error occurred. Please try again.',
                            });
                        }
                    });
                }
            });
            // Handle OTP form submission
            $('#otpForm').submit(function(e) {
                e.preventDefault(); // Prevent the form from submitting traditionally

                var otp = $('#emailOtp').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/verifyemail/otp', // Endpoint for verifying OTP
                    type: 'POST',
                    data: {
                        otp: otp
                    },
                    success: function(response) {
                        // Handle successful OTP verification
                        $('#otpModal').modal('hide'); // Hide the modal
                        Swal.fire({
                            icon: 'success',
                            title: 'Verified',
                            text: 'Your Email has been successfully verified.',
                        });
                        $('#verifyEmail')
                            .text('Verified')
                            .attr('disabled', true)
                            .removeClass('btn-primary')
                            .addClass('btn-success');
                        $('#email').attr('readonly', true);
                        $('#showbtn').addClass('d-none');
                        $('#verifyEmail').removeClass('d-none');




                    },
                    error: function(xhr) {
                        // Handle error response
                        Swal.fire({
                            icon: 'error',
                            title: 'Verification Failed',
                            text: xhr.responseJSON.message ? xhr.responseJSON.message :
                                'Failed to verify OTP. Please try again.',
                        });
                    }
                });
            });
            $('#verifyPhone').click(function() {
                var phone = $('#phone').val();
                var code = $('#countryCode').val();
                var phoneWithCode = code + phone;

                if (!phone) { // Check if the phone variable is an empty string, null, or undefined
                    // Display a SweetAlert2 error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please enter your Phone Number.',
                    });
                } else {
                    // AJAX call to send OTP
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: '/sendphone/otp',
                        type: 'POST',
                        data: {
                            phone: phoneWithCode,
                        },
                        success: function(response) {
                            // Show OTP input and submit button upon success

                            // Show the modal upon success
                            $('#phoneotpModal').modal(
                                'show'); // Using Bootstrap's modal method to show the modal

                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'OTP has been sent to your phone number.',
                            });
                        },
                        error: function(xhr) {
                            // Handle error response
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: xhr.responseJSON.message ? xhr.responseJSON
                                    .message : 'An error occurred. Please try again.',
                            });
                        }
                    });
                }
            });

            // Handle Resend OTP button click
            $('#resendOTP').click(function() {
                // Make an AJAX call to resend the OTP

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/resend/otp', // Adjust the URL to your backend endpoint
                    type: 'POST',
                    data: {
                        email: $('#email').val()
                    },
                    success: function(response) {
                        // Handle success, e.g., show a message to the user
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'OTP has been resent to your email.',
                        });
                        $('#showbtn').removeClass('d-none');
                        $('#verifyEmail').addClass('d-none');
                    },
                    error: function(error) {
                        // Handle error, e.g., show an error message to the user
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Failed to resend OTP. Please try again.',
                        });
                    }
                });
            });

        });
