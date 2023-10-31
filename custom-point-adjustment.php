<?php 

// Include necessary WordPress functions and other libraries here
// Start the PHP session to enable session variables
session_start();

// Initialize the $mailsendreport variable
$mailsendreport = '';

// Check if we are on the admin point settings page
$is_point_settings_page = isset($_GET['page']) && $_GET['page'] === 'points-rewards' && isset($_GET['tab']) && $_GET['tab'] === 'point-settings';

if (isset($_POST['send_email_button'])) {
    // Generate a new verification code
    //$new_verification_code = rand(100000, 999999);
    
    // Store the new verification code temporarily in a session variable
    $_SESSION['verification_code'] = $new_verification_code;
    
    // Prepare the email content
    $to = get_bloginfo('admin_email');
    $subject = 'Verification Code for Admin Point Adjustment';
    $message = 'Your verification code is: ' . $new_verification_code;
    
    // Send the email using wp_mail
    if (wp_mail($to, $subject, $message)) {
        $mailsendreport = 'Email sent successfully';
    } else {
        $mailsendreport = 'Email could not be sent. Error: ' . print_r(error_get_last(), true);
    }
}
?>
<?php if ($is_point_settings_page): ?>
    <div id="adminPointAdjustModal" class="modal">
        <div class="modal-content">
            <div class="modal-header-area">
            
            <div class="modal-header-text">Admin Point Adjustment Verification
            <span class="close" id="closeModal">&times;</span>
            </div>    
            </div>
            <div class="modal-otp-input" id="verificationMessage">
                <!-- Display the initial message when the modal opens -->
                <div>An email will be sent to the admin email for verification.
                </div>
                <!-- Send Email Button -->
                <button type="button" name="send_email_button" id="sendEmailButton" class="ptn-submit send-mail-btn">Send Email</button>
            </div>
            <div class="modal-otp-input" id="verificationForm" style="display: none;">
                <div id="mailSendReport">
                    <?php //echo $mailsendreport; ?>
                    Email Sending..
                </div>
                <!-- Verification Code Field -->
                <input type="text" id="verification_code" placeholder="Enter verification code">
                <!-- Verify OTP Button -->
                <button type="button" id="verify_otp_button" class="ptn-submit">Verify OTP</button>
            </div>
        </div>
    </div>
<?php endif; ?>
