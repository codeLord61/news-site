/**
 * Switch between login and signup panels.
 *
 * Input example: "login" or "signup".
 * Output: no return value; updates DOM classes so one form is visible.
 */
function switchForm(target) {
  // Get all elements with class .auth-form (basically signup and form)
  // Add hidden class to both of them
  document.querySelectorAll(".auth-form").forEach((f) => {
    f.classList.add("hidden");
  });

  // Remove hidden class from target form to Display that form
  document.getElementById(target + "Form").classList.remove("hidden");
}

/**
 * Toggle password input visibility.
 *
 * Input:
 * - inputId: password input element id
 * - eyeId: eye icon element id (currently unused in this implementation)
 * Output: no return value; changes <input type> and icon classes.
 */
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
      // Transform FormData entries into plain object for JSON body.
      const data = Object.fromEntries(formData.entries());

      try {
        // Use relative path for API endpoint
        const response = await fetch((window.appBaseUrl || "") + "/api/v1/login", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(data)
        });

        const result = await response.json();

        if (response.ok && result.success) {
          // Store the token and role in localStorage
          localStorage.setItem("auth_token", result.token);
          if (result.role) {
            localStorage.setItem("user_role", result.role);
          }

          // Show success visually before redirecting
          alert(result.message || "Login successful!");

          // Redirect to dashboard or home
          window.location.href = (window.appBaseUrl || "") + "/dashboard";
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
      // Convert browser form fields into API-ready JSON object.
      const data = Object.fromEntries(formData.entries());

      try {
        const response = await fetch((window.appBaseUrl || "") + "/api/v1/register", {
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

