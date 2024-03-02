<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('meta::manager', [
        'title' => 'One-Time Password (OTP) Registration Form - Secure Your Account',
        'description' =>
            'Register securely using our One-Time Password (OTP) system. Ensure your account is protected with the latest in security technology.',
        'image' => asset('favicon/otp.png'), // Update the image path accordingly
        'keywords' => 'OTP, One-Time Password, Secure Registration, Account Security, Laravel',
        'author' => 'Your Company or Your Name',
        'url' => url()->current(),
        'canonical' => url()->current(),
        'robots' => 'index, follow',
        'og_type' => 'website',
        'og_title' => 'One-Time Password (OTP) Registration Form - Secure Your Account',
        'og_description' =>
            'Register securely using our One-Time Password (OTP) system. Ensure your account is protected with the latest in security technology.',
        'og_image' => asset('favicon/otp.png'), // Repeat the image path here for social media
        'og_url' => url()->current(),
        'og_site_name' => 'Your Site Name',
        'og_locale' => 'en_US',
        'twitter_card' => 'summary_large_image',
        'twitter_creator' => '@YourTwitterHandle', // Your or your company's Twitter handle
        'twitter_title' => 'One-Time Password (OTP) Registration Form - Secure Your Account',
        'twitter_description' =>
            'Register securely using our One-Time Password (OTP) system. Ensure your account is protected with the latest in security technology.',
        'twitter_image' => asset('favicon/otp.png'), // Repeat the image path here for Twitter cards
        'favicon' => asset('favicon/favicon.ico'), // Assuming the favicon is in the public/favicon directory
        // Additional meta tags
        'viewport' => 'width=device-width, initial-scale=1, shrink-to-fit=no',
        'charset' => 'utf-8',
        // Social media specific tags could be added here as well
        // Custom tags for additional needs can be included too
    ])


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="icon" href="{{ asset('favicon/otp.png') }}">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <style>
        @media(prefers-color-scheme:light) {
            body {
                background-color: white;
            }

            h2 {
                color: white;
            }

            .card {
                color: white
            }
        }

        @media(prefers-color-scheme:dark) {
            body {
                background-color: rgb(0, 0, 0);
            }

            h2 {
                color: rgb(0, 0, 0);
            }

            .card {
                color: rgb(0, 0, 0)
            }
        }
    </style>
</head>

<body>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center bg-primary text-white">
                        <h2>Sign-Up Form with OTP Verification</h2>
                    </div>
                    <div class="card-body">
                        <form id="signupForm">
                            <div class="mb-3 input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Enter your name" required>
                            </div>
                            <div class="mb-3 input-group">
                                <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                <select class="form-select" id="country" name="country" required>
                                    <option value="" disabled selected>Select your country</option>
                                </select>
                            </div>
                            <div class="mb-3 input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <select class="form-select" id="countryCode" name="countryCode" required>
                                    <option value="" disabled selected>Select your country code</option>
                                </select>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                    placeholder="Enter your phone number" required>
                                <button type="button" class="btn btn-primary" id="verifyPhone">Verify</button>
                            </div>
                            <div class="mb-3 input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Enter your Email" required>
                                <button type="button" class="btn btn-primary" id="verifyEmail">Verify</button>
                                <button type="button" id="showbtn" class="btn btn-warning d-none"
                                    data-bs-toggle="modal" data-bs-target="#otpModal"><b>Show</b></button>
                            </div>
                            <div class="mb-3 input-group">
                                <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="" disabled selected>Select your gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="text-center">
                                <button type="button" disabled class="btn btn-info" id="resendOTP">
                                    <i class="fas fa-redo"></i> Resend OTP
                                </button>
                                <button type="submit" class="btn btn-success" id="signupBtn">
                                    <i class="fas fa-user-plus"></i> Sign-Up
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="otpModalLabel"><i class="fas fa-key"></i> Enter OTP</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="otpForm">
                        <div class="form-group">
                            <label for="emailOtp"><i class="fas fa-envelope"></i> OTP</label>
                            <input type="number" class="form-control" id="emailOtp" placeholder="Enter OTP"
                                autofocus>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-block mt-2"><i
                                    class="fas fa-check-circle"></i> Verify OTP</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <p class="text-muted">Please check your email for the OTP.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('jquery.js') }}"></script>
</body>

</html>
