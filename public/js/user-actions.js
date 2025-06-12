document.addEventListener("DOMContentLoaded", () => {
  const updateRoleButtons = document.querySelectorAll(".btn-secondary");
  const deleteUserButtons = document.querySelectorAll(".delete-user-btn");
  const promptContainer = document.getElementById("custom-prompt-container");
  const promptMessage = document.getElementById("custom-prompt-message");
  const promptYesButton = document.getElementById("custom-prompt-yes");
  const promptNoButton = document.getElementById("custom-prompt-no");
  const popupContainer = document.getElementById("popup-container");
  const popupText = document.getElementById("popup-text");
  const popupCloseButton = document.getElementById("popup-close");
  const layoutContainer = document.querySelector(".layout-container");
  let currentAction = null;
  let currentUserId = null;
  let currentRole = null;
  let currentUserName = null;

  // Handle role updates
  updateRoleButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
      event.preventDefault();
      const form = button.closest("form");
      const userCard = form.closest(".user-card");
      currentUserId = form.action.split("/").slice(-2)[0];
      currentRole = form.querySelector(".styled-select").value;
      currentUserName = userCard.querySelector(".user-name").textContent;

      currentAction = "update-role";
      promptMessage.textContent = `Are you sure you want to set '${currentUserName}' role to '${currentRole}'?`;
      promptContainer.classList.remove("hidden");
    });
  });

  // Handle user deletion
  deleteUserButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const userCard = button.closest(".user-card");
      currentUserId = button.dataset.userId;
      currentUserName = userCard.querySelector(".user-name").textContent;

      currentAction = "delete-user";
      promptMessage.textContent = `Are you sure you want to delete user '${currentUserName}'?`;
      promptContainer.classList.remove("hidden");
    });
  });

  // Handle confirmation
  promptYesButton.addEventListener("click", async () => {
    if (!currentUserId || !currentAction) return;

    try {
      if (currentAction === "delete-user") {
        const response = await fetch(`?path=/users/${currentUserId}`, {
          method: "DELETE",
          headers: {
            "Content-Type": "application/json",
          },
        });

        const result = await response.json();

        if (response.ok) {
          popupText.textContent = result.message || "User deleted successfully!";
          document
            .querySelector(`[data-user-id="${currentUserId}"]`)
            .closest(".user-card")
            .remove();
        } else {
          popupText.textContent = result.message || "Failed to delete user.";
        }
      } else if (currentAction === "update-role") {
        const response = await fetch(`?path=/admin/users/${currentUserId}/role`, {
          method: "PATCH",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ role: currentRole }),
        });

        const result = await response.json();

        if (response.ok) {
          popupText.textContent = result.message || "User role updated successfully!";
        } else {
          popupText.textContent = result.message || "Failed to update user role.";
        }
      }
    } catch (error) {
      console.error("Error:", error);
      popupText.textContent = "An error occurred. Please try again.";
    } finally {
      currentUserId = null;
      currentAction = null;
      currentRole = null;
      currentUserName = null;
      promptContainer.classList.add("hidden");
      popupContainer.classList.remove("hidden");
      layoutContainer.classList.add("popup-active"); // Add blur effect
    }
  });

  // Handle cancellation
  promptNoButton.addEventListener("click", () => {
    promptContainer.classList.add("hidden");
    currentUserId = null;
    currentAction = null;
    currentRole = null;
    currentUserName = null;
  });

  // Handle popup close
  popupCloseButton.addEventListener("click", () => {
    popupContainer.classList.add("hidden");
    layoutContainer.classList.remove("popup-active"); // Remove blur effect
    location.reload(); // Refresh the page
  });
});
