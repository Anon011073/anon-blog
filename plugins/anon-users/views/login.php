<div class="anon-users-login">
    <?php if (isset($_SESSION['anon_msg'])): ?>
        <div style="background: #dff0d8; color: #3c763d; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <?php echo $_SESSION['anon_msg']; unset($_SESSION['anon_msg']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['anon_error'])): ?>
        <div style="background: #f2dede; color: #a94442; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <?php echo $_SESSION['anon_error']; unset($_SESSION['anon_error']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" style="max-width: 400px; margin: 0 auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h3 style="margin-top: 0; text-align: center;">Sign In</h3>
        <input type="hidden" name="anon_action" value="login">
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Username</label>
            <input type="text" name="username" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
        </div>
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Password</label>
            <input type="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
        </div>
        <button type="submit" style="width: 100%; padding: 12px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; font-weight: bold;">Login</button>

        <p style="margin-top: 20px; text-align: center; font-size: 0.9rem;">
            Don't have an account? <a href="index.php?page=register" style="color: #007bff; text-decoration: none; font-weight: bold;">Register here</a>
        </p>
    </form>
</div>
