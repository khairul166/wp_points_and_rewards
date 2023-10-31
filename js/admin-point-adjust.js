document.addEventListener('DOMContentLoaded', function () {
    // Declare response variable here to make it accessible in the event listener
    let response = null;

    // Check if the current page is the admin point settings page
    const isPointSettingsPage = window.location.href.indexOf('admin.php?page=points-rewards&tab=point-settings') !== -1;

    if (isPointSettingsPage) {
        const adminPointAdjustCheckbox = document.getElementById('admin_point_adjust');
        const adminPointAdjustModal = document.getElementById('adminPointAdjustModal');
        const verificationMessage = document.getElementById('verificationMessage');
        const verificationForm = document.getElementById('verificationForm');
        const sendEmailButton = document.getElementById('sendEmailButton');
        const closeModalButton = document.getElementById('closeModal');
        const verifyOTPButton = document.getElementById('verify_otp_button');
        const verificationCodeInput = document.getElementById('verification_code');
        const mailsendreportElement = document.getElementById('mailSendReport');
        const verificationCodePHP = document.getElementById('verification_code_php');

        function showAdminPointAdjustModal() {
            adminPointAdjustModal.style.display = 'block';
        }

        function hideAdminPointAdjustModal() {
            adminPointAdjustModal.style.display = 'none';
            adminPointAdjustCheckbox.checked = false;
        }

        if (adminPointAdjustCheckbox) {
            adminPointAdjustCheckbox.addEventListener('change', function () {
                if (this.checked) {
                    showAdminPointAdjustModal();
                } else {
                    hideAdminPointAdjustModal();
                }
            });
        }

        if (closeModalButton) {
            closeModalButton.addEventListener('click', function () {
                hideAdminPointAdjustModal();
            });
        }

        if (sendEmailButton) {
            sendEmailButton.addEventListener('click', function () {
                verificationMessage.style.display = 'none';
                verificationForm.style.display = 'block';
                // AJAX request to send the email
                jQuery.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'send_email',
                    },
                    success: function (ajaxResponse) {
                        // Assign the response to the outer response variable
                        response = ajaxResponse;
                        if (response.success) {
                            // Email sent successfully
                            mailsendreportElement.innerHTML = 'An email hasbeen sent to admin email with six digit code.';
                            //verificationCodePHP.value = response.newVerificationCode;
                        } else {
                            // Email sending failed
                            mailsendreportElement.innerHTML = 'Email could not be sent.';
                        }
                    },
                    error: function (xhr, status, error) {
                        mailsendreportElement.innerHTML = 'AJAX error occurred';
                    },
                });
            });
        }

        if (verifyOTPButton) {
            verifyOTPButton.addEventListener('click', function () {
                const enteredCode = verificationCodeInput.value;
                const storedCode = response.newVerificationCode; // Use the value from the response
                if (enteredCode === storedCode.toString()) {
                    mailsendreportElement.innerHTML = 'Verification was successful';
                    adminPointAdjustCheckbox.checked = true;
                    verificationCodeInput.style.display = 'none';
                    verifyOTPButton.style.display = 'none';
                    mailsendreportElement.style.color = 'green';
                    mailsendreportElement.style.fontSize = '2em';
                    mailsendreportElement.style.padding = '10px;';
    
                    setTimeout(function () {
                        adminPointAdjustModal.style.display = 'none';
                    }, 2000); // 2000 milliseconds (2 seconds)
                } else {
                    mailsendreportElement.innerHTML = 'Incorrect Code.';
                    mailsendreportElement.style.color = 'red';
                }
            });
        }
    }



});




