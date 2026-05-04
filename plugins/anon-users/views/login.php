<div class="login-container">
    <h3>Login</h3>
    <?php if (isset($_GET['error'])): ?>
        <p style="color:red">Invalid credentials.</p>
    <?php endif; ?>
    <form action="app/auth.php?action=login" method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="index.php?page=register">Register here</a></p>
</div>
