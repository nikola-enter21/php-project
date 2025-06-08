document.addEventListener("DOMContentLoaded", function () {
  const actionButtons = document.querySelectorAll(".action-icon");

  actionButtons.forEach((button) => {
    button.addEventListener("click", async function (e) {
      e.preventDefault();

      if (this.classList.contains("delete")) {
        return;
      }

      const quoteId = this.dataset.quoteId;
      const action = this.classList.contains("love")
        ? "like"
        : this.classList.contains("save")
        ? "save"
        : "report";

      try {
        const response = await fetch(`/quotes/${quoteId}/${action}`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
        });

        const data = await response.json();
        if (response.status === 401 || response.status === 403) {
          window.location.href = "/login";
          return;
        }

        if (data.success) {
          // Update the count
          const countSpan = this.querySelector(".count");
          if (action === "like") {
            countSpan.textContent = data.likes_count;
            this.classList.toggle("active", data.is_liked);
          } else if (action === "save") {
            countSpan.textContent = data.saves_count;
            this.classList.toggle("active", data.is_saved);
          } else if (action === "report") {
            countSpan.textContent = data.reports_count;
            this.classList.toggle("active", data.is_reported);
          }
        }
      } catch (error) {
        console.error("Error:", error);
        alert("An error occurred. Please try again.");
      }
    });
  });

  const promptContainer = document.getElementById("custom-prompt-container");
  const promptYesButton = document.getElementById("custom-prompt-yes");
  const promptNoButton = document.getElementById("custom-prompt-no");
  const deleteButtons = document.querySelectorAll(".action-icon.delete");
  let currentQuoteId = null;

  deleteButtons.forEach((button) => {
    button.addEventListener("click", async () => {
      console.log("Delete button clicked");
      promptContainer.classList.remove("hidden");
      currentQuoteId = button.dataset.quoteId;
    });
  });

  promptYesButton.addEventListener("click", async () => {
    if (!currentQuoteId) return;

    try {
      const response = await fetch(`/quotes/${currentQuoteId}`, {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
        },
      });

      if (response.ok) {
        document
          .querySelector(`[data-quote-id="${currentQuoteId}"]`)
          .closest(".quote-card")
          .remove();
        promptContainer.classList.add("hidden");
      } else {
        const result = await response.json();
        console.error(result.message || "Failed to delete quote.");
      }
    } catch (error) {
      console.error("Error:", error);
    } finally {
      currentQuoteId = null;
    }
  });

  promptNoButton.addEventListener("click", () => {
    // Hide the prompt without deleting
    promptContainer.classList.add("hidden");
    currentQuoteId = null;
  });
});
