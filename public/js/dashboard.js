document.addEventListener("DOMContentLoaded", () => {
    const hamburger = document.querySelector(".sidebar-toggler");
    const sidebar = document.getElementById("sidebar");
    const content = document.querySelector(".content");

    if (hamburger && sidebar && content) {
        hamburger.addEventListener("click", (e) => {
            e.preventDefault();
            sidebar.classList.toggle('sidebar-open');
            content.classList.toggle('sidebar-open');
        });
    }
});
