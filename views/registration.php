<h1>Create Account</h1>
<p class="subtitle">Join us today and get started!</p>

<div id="message"></div>

<form id="registrationForm" method="POST" action="/register">
    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($data['csrfToken'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    
    <div class="form-group">
        <label for="email">Email Address</label>
        <input 
            type="email" 
            id="email" 
            name="email" 
            placeholder="you@example.com"
            required
            autocomplete="email"
        >
    </div>
    
    <div class="form-group">
        <label for="password">Password</label>
        <input 
            type="password" 
            id="password" 
            name="password" 
            placeholder="At least 8 characters"
            required
            autocomplete="new-password"
        >
    </div>
    
    <div class="form-group">
        <label for="password2">Confirm Password</label>
        <input 
            type="password" 
            id="password2" 
            name="password2" 
            placeholder="Confirm your password"
            required
            autocomplete="new-password"
        >
    </div>
    
    <button type="submit">Create Account</button>
</form>

<script>
document.getElementById('registrationForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const form = e.target;
    const messageDiv = document.getElementById('message');
    const button = form.querySelector('button');
    
    // Get form data
    const formData = new FormData(form);
    
    // Add loading state
    button.textContent = 'Creating Account...';
    form.classList.add('loading');
    messageDiv.innerHTML = '';
    
    try {
        const response = await fetch('/register', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Non-JSON response received from server:', text);
            messageDiv.innerHTML = `<div class="error">
                <strong>Server Error:</strong><br>
                ${text ? text.substring(0, 500) : 'Empty response from server'}
            </div>`;
            return;
        }
        
        const data = await response.json();
        
        if (data.success) {
            messageDiv.innerHTML = '<div class="success">Account created successfully! Redirecting...</div>';
            setTimeout(() => {
                window.location.href = '/dashboard';
            }, 1500);
        } else {
            // Handle different error types
            let errorMessage = 'Registration failed. Please try again.';
            
            switch(data.error) {
                case 'email':
                    errorMessage = 'Please provide a valid email address.';
                    break;
                case 'email_format':
                    errorMessage = 'Invalid email format.';
                    break;
                case 'password':
                    errorMessage = 'Password must be at least 8 characters long.';
                    break;
                case 'password_mismatch':
                    errorMessage = 'Passwords do not match.';
                    break;
                case 'email_exists':
                    errorMessage = 'This email is already registered.';
                    break;
                case 'fraud_detected':
                    errorMessage = 'Registration blocked due to security concerns. Please contact support.';
                    break;
                case 'method_not_allowed':
                    errorMessage = 'Invalid request method.';
                    break;
                case 'csrf_token_invalid':
                    errorMessage = 'Session expired. Please refresh the page and try again.';
                    break;
                case 'server_error':
                    errorMessage = 'An internal error occurred. Please try again later.';
                    break;
                default:
                    errorMessage = `Registration failed: ${data.error}`;
            }
            
            messageDiv.innerHTML = `<div class="error">${errorMessage}</div>`;
        }
    } catch (error) {
        console.error('Fetch error:', error);
        messageDiv.innerHTML = `<div class="error">
            <strong>Connection Error:</strong><br>
            ${error.message}<br>
            <small style="display: block; margin-top: 8px;">Check the browser console (F12) for more details.</small>
        </div>`;
    } finally {
        button.textContent = 'Create Account';
        form.classList.remove('loading');
    }
});
</script>
