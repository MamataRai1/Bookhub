document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".dashboard-menu a").forEach(link => {
        link.addEventListener("click", function(event) {
            event.preventDefault();
            document.querySelector(this.getAttribute("href")).scrollIntoView({ behavior: "smooth" });
        });
    });

    document.querySelector(".edit-profile-btn").addEventListener("click", function() {
        alert("Profile editing feature coming soon!");
    });
});
