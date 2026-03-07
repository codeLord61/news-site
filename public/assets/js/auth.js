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
