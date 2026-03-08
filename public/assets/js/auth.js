function switchForm(target) {
  // Get all elements with class .auth-form (basically signup and form)
  // Add hidden class to both of them
  document.querySelectorAll(".auth-form").forEach((f) => {
    f.classList.add("hidden");
  });

  // Remove hidden class from target form to Display that form
  document.getElementById(target + "Form").classList.remove("hidden");
}

function togglePassword(inputId, eyeId) {
  let password = document.getElementById(inputId);

  if (password.type === "password") {
    // Make pass visible by changing type to text
    password.type = "text";

    // Change eye icon
    password.classList.remove("fa-eye");
    password.classList.add("fa-eye-slash");
  } else {
    password.type = "password";
    password.classList.remove("fa-eye-slash");
    password.classList.add("fa-eye");
  }
}

// Ensure DOM is fully loaded before attaching handlers
document.addEventListener("DOMContentLoaded", () => {

  // Login Form Handler
  const loginForm = document.getElementById("loginForm");
  if (loginForm) {
    loginForm.addEventListener("submit", async (e) => {
      e.preventDefault(); // Stop default form POST

      const formData = new FormData(loginForm);
      const data = Object.fromEntries(formData.entries());

      try {
        // The URL depends on how your local environment is set up.
        // It should match the API route mapped in routes/api.php
        const response = await fetch("/project/news/public/api/v1/login", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(data)
        });

        const result = await response.json();

        if (response.ok && result.success) {
          // Store the token in localStorage
          localStorage.setItem("auth_token", result.token);

          // Show success visually before redirecting
          alert(result.message || "Login successful!");

          // Redirect back to homepage
          window.location.href = "/project/news/public/";
        } else {
          alert("Login failed: " + (result.error || "Unknown error"));
        }
      } catch (error) {
        console.error("Login Error:", error);
        alert("An error occurred during login. Please ensure the server is running.");
      }
    });
  }

  // Signup Form Handler
  const signupForm = document.getElementById("signupForm");
  if (signupForm) {
    signupForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      const formData = new FormData(signupForm);
      const data = Object.fromEntries(formData.entries());

      try {
        const response = await fetch("/project/news/public/api/v1/register", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(data)
        });

        const result = await response.json();

        if (response.ok && result.success) {
          alert(result.message || "Registration successful! You may now log in.");
          // After successful signup, switch back to the login view automatically
          switchForm('login');
          signupForm.reset();
        } else {
          alert("Registration failed: " + (result.error || "Unknown error"));
        }
      } catch (error) {
        console.error("Signup Error:", error);
        alert("An error occurred during registration. Please check the network console.");
      }
    });
  }
});

