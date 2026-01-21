  <!-- Reg form -->
        <div class="auth-box">
            <h2>Register</h2>
            
            <?php if($error && isset($_POST['register'])): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" class="auth-form">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" required>
                </div>
                
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="reg_username" required>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="reg_email" required>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="reg_password" require>
                
        <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </div>
            
            <button type="submit" name="register" class="submit-btn">Register</button>
        </form>
    </div>
</div>