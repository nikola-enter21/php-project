document.addEventListener("DOMContentLoaded", function () {
  const actionButtons = document.querySelectorAll(".action-icon");

  actionButtons.forEach((button) => {
    button.addEventListener("click", async function (e) {
      e.preventDefault();

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
});
