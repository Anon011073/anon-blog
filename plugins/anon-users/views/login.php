<div class="anon-users-form">
    <?php if (isset($_SESSION['anon_error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['anon_error']; unset($_SESSION['anon_error']); ?></p>
    <?php endif; ?>
    <h3>Sign In</h3>
    <form action="" method="POST">
        <input type="hidden" name="anon_action" value="login">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>
