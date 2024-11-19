<!-- Sign up modal -->
<div id="myModalSignup" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="followUsTitle">Sign up
            <div class="close">X</div>
        </div>
        <form id="signupForm" action="index.php" method="POST">
            <input type="hidden" name="form_type" value="registration">
            <label>First Name<span class="requiredField">*</span></label>
            <input class="formField" type="text" name="first_name" placeholder="First name" required>
            <label>Last Name<span class="requiredField">*</span></label>
            <input class="formField" type="text" name="last_name" placeholder="Last name" required>
            <label>Email<span class="requiredField">*</span></label>
            <input class="formField" type="email" name="email" placeholder="Enter email address" required>
            <label>Password<span class="requiredField">*</span></label>
            <input class="formField" type="password" name="password" placeholder="Enter password" required>
            <label>Confirm Password<span class="requiredField">*</span></label>
            <input class="formField" type="password" name="confirm_password" placeholder="Confirm password" required>
            <button class="followUs_subscribe" type="submit">SIGN UP</button>
            <div class="SignUpLink">Already have an account? <a href="#" id="switchToLogin">Log In</a></div>
        </form>
    </div>
</div>

<!-- Log in modal -->
<div id="myModalLogin" class="modal">
    <div class="modal-content">
        <div class="followUsTitle">Log in
            <div class="close">X</div>
        </div>
        <form id="contactForm" action="index.php" method="POST">
            <label>Email<span class="requiredField">*</span></label>
            <input class="formField" type="email" name="login" placeholder="Enter email address" required>
            <label>Password<span class="requiredField">*</span></label>
            <input class="formField" type="password" name="password" placeholder="Enter password" required>
            <button class="followUs_subscribe" type="submit">LOG IN</button>
            <div class="SignUpLink">Don't have an account? <a href="#" id="switchToSignUp">Sign Up</a></div>
        </form>
    </div>
</div>