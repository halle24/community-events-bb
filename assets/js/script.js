document.addEventListener('DOMContentLoaded', function() {
    const regForm = document.querySelector('form');

    let errors = [];
    
    if (regForm) {
        regForm.addEventListener('submit', function(e) {
            const password = document.querySelector('input[name="password"]').value;
            const username = document.querySelector('input[name="username"]').value;
            const specialChars = /[@!$%^&*()?<>, \'\";:\/`~-\[\]]/;

            // Client-side check for special characters
            if (specialChars.test(username)) {
                alert("Username cannot contain special characters.");
                e.preventDefault(); // Stops the form from submitting to PHP
                return;
            }
            if (username.length < 2) {
                errors.push("Username must be at least 3 characters.");
                e.preventDefault();
            }

           // 3. Password Strength Check
            const hasLetter = /[A-Za-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            if (password.length < 8 || !hasLetter || !hasNumber) {
                alert("Password must be at least 8 characters long and contain both letters and numbers.");
                e.preventDefault();
                return;
            }
        });
    }
});