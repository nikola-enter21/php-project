document.addEventListener("DOMContentLoaded", () => {
  const changePasswordForm = document.querySelector(".change-password-form");
  const popupContainer = document.getElementById("popup-container");
  const popupText = document.getElementById("popup-text");
  const popupClose = document.getElementById("popup-close");
  const errorContainer = document.createElement("div");
  errorContainer.className = "error-container hidden";
  changePasswordForm.appendChild(errorContainer);

  if (changePasswordForm) {
    changePasswordForm.addEventListener("submit", async (event) => {
      event.preventDefault();

      const formData = new FormData(changePasswordForm);
      const oldPassword = formData.get("oldPassword");
      const newPassword = formData.get("newPassword");
      const confirmPassword = formData.get("confirmPassword");

      try {
        const response = await fetch(changePasswordForm.action, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            oldPassword,
            newPassword,
            confirmPassword,
          }),
        });

        // Parse the response as JSON
        const result = await response.json();

        if (response.ok) {
          popupText.textContent = result.message || "Password changed successfully!";
          popupContainer.classList.remove("hidden");

          // Clear the form
          changePasswordForm.reset();

          setTimeout(() => {
            location.reload();
          }, 2000);
        } else {
          errorContainer.textContent = result.message || "Failed to change password.";
          errorContainer.classList.remove("hidden");
        }
      } catch (error) {
        popupText.textContent = "An error occurred while changing the password.";
        popupContainer.classList.remove("hidden");
        console.error("Error:", error);
      }
    });
  }

  popupClose.addEventListener("click", () => {
    popupContainer.classList.add("hidden");
  });
});
