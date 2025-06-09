document.addEventListener("DOMContentLoaded", () => {
  const deleteLogButtons = document.querySelectorAll(".delete-log-btn");
  const clearLogsButton = document.getElementById("clear-logs-btn");
  const promptContainer = document.getElementById("custom-prompt-container");
  const promptMessage = document.getElementById("custom-prompt-message");
  const promptYesButton = document.getElementById("custom-prompt-yes");
  const promptNoButton = document.getElementById("custom-prompt-no");
  const popupContainer = document.getElementById("popup-container");
  const popupText = document.getElementById("popup-text");
  const popupCloseButton = document.getElementById("popup-close");
  const layoutContainer = document.querySelector(".layout-container");
  let currentAction = null;
  let currentLogId = null;

  // Handle individual log deletion
  deleteLogButtons.forEach((button) => {
    button.addEventListener("click", () => {
      currentLogId = button.dataset.logId;
      currentAction = "delete-log";
      promptMessage.textContent = `Are you sure you want to delete this log?`;
      promptContainer.classList.remove("hidden");
    });
  });

  // Handle clearing all logs
  clearLogsButton.addEventListener("click", () => {
    currentAction = "clear-logs";
    promptMessage.textContent = `Are you sure you want to clear all logs?`;
    promptContainer.classList.remove("hidden");
  });

  // Handle confirmation
  promptYesButton.addEventListener("click", async () => {
    if (!currentAction) return;

    try {
      if (currentAction === "delete-log") {
        const response = await fetch(`/admin/logs/${currentLogId}`, {
          method: "DELETE",
          headers: {
            "Content-Type": "application/json",
          },
        });

        const result = await response.json();

        if (response.ok) {
          popupText.textContent = result.message || "Log deleted successfully!";
          document.querySelector(`[data-log-id="${currentLogId}"]`).closest("tr").remove();
        } else {
          popupText.textContent = result.message || "Failed to delete log.";
        }
      } else if (currentAction === "clear-logs") {
        const response = await fetch(`/admin/logs`, {
          method: "DELETE",
          headers: {
            "Content-Type": "application/json",
          },
        });

        const result = await response.json();

        if (response.ok) {
          popupText.textContent = result.message || "All logs cleared successfully!";
          document.querySelector(".logs-table tbody").innerHTML =
            '<tr><td colspan="5">No logs found.</td></tr>';
        } else {
          popupText.textContent = result.message || "Failed to clear logs.";
        }
      }
    } catch (error) {
      console.error("Error:", error);
      popupText.textContent = "An error occurred. Please try again.";
    } finally {
      currentLogId = null;
      currentAction = null;
      promptContainer.classList.add("hidden");
      popupContainer.classList.remove("hidden");
      layoutContainer.classList.add("popup-active"); // Add blur effect
    }
  });

  // Handle cancellation
  promptNoButton.addEventListener("click", () => {
    promptContainer.classList.add("hidden");
    currentLogId = null;
    currentAction = null;
  });

  // Handle popup close
  popupCloseButton.addEventListener("click", () => {
    popupContainer.classList.add("hidden");
    layoutContainer.classList.remove("popup-active"); // Remove blur effect
  });
});
