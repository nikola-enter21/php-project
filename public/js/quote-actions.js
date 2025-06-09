document.addEventListener("DOMContentLoaded", function () {
  const actionButtons = document.querySelectorAll(".action-icon");

  actionButtons.forEach((button) => {
    button.addEventListener("click", async function (e) {
      e.preventDefault();

      if (this.classList.contains("delete")) {
        return;
      }

      const quoteId = this.dataset.quoteId;

      let action;
      if (this.classList.contains("love")) {
        action = "like";
      } else if (this.classList.contains("save")) {
        action = "save";
      } else if (this.classList.contains("report")) {
        action = "report";
      } else if (this.classList.contains("add-to-collection")) {
        // Skip processing here for "Add to Collection" button
        return;
      }

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
          } else if (this.classList.contains("add-to-collection")) {
            // Skip processing here for "Add to Collection" button
            return;
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

  const addToCollectionButtons = document.querySelectorAll(".add-to-collection");
  const popup = document.getElementById("collection-popup");
  const collectionList = popup.querySelector(".collection-list");
  const closePopup = popup.querySelector(".close-popup");

  addToCollectionButtons.forEach((button) => {
    button.addEventListener("click", async function () {
      const quoteId = this.dataset.quoteId;

      popup.style.display = "block";
      collectionList.innerHTML = "";

      try {
        const response = await fetch("/collections/json");
        const data = await response.json();

        if (data.success && data.collections.length > 0) {
          data.collections.forEach((collection) => {
            const li = document.createElement("li");
            li.textContent = collection.name;
            li.dataset.collectionId = collection.id;
            li.addEventListener("click", async () => {
              try {
                const addResponse = await fetch(`/quotes/${quoteId}/add-to-collection`, {
                  method: "POST",
                  headers: { "Content-Type": "application/json" },
                  body: JSON.stringify({
                    collection_id: collection.id,
                    quote_id: quoteId,
                  }),
                });
                const addData = await addResponse.json();

                if (addData.success) {
                  alert(addData.message);
                  popup.style.display = "none";
                } else {
                  alert(addData.message);
                }
              } catch (error) {
                console.error("Error adding to collection:", error);
                alert(
                  "An error occurred while adding the quote to the collection. Please try again."
                );
              }
            });
            collectionList.appendChild(li);
          });
        } else {
          const noCollectionsMessage = document.createElement("p");
          noCollectionsMessage.textContent = "No collections available.";
          noCollectionsMessage.style.color = "#64748b";
          collectionList.appendChild(noCollectionsMessage);
        }
      } catch (error) {
        console.error("Error fetching collections:", error);
        alert("Failed to fetch collections. Please try again.");
      }
    });
  });

  closePopup.addEventListener("click", () => {
    popup.style.display = "none";
  });

  window.addEventListener("click", (event) => {
    if (event.target === popup) {
      popup.style.display = "none";
    }
  });
});
