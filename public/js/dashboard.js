document.addEventListener("DOMContentLoaded", () => {
    const hamburger = document.querySelector(".sidebar-toggler");
    const sidebar = document.getElementById("sidebar");
    const content = document.querySelector(".content");
    const navbar  = document.querySelector(".navbar");

    if (hamburger && sidebar && content) {
        hamburger.addEventListener("click", (e) => {
            e.preventDefault();
            sidebar.classList.toggle('sidebar-open');
            content.classList.toggle('sidebar-open');
        });
    }

    function adjustSidebarPosition() {
        if (window.innerWidth <= 991.98) {
            const navHeight = navbar.offsetHeight;

            sidebar.style.top = navHeight + "px";
            sidebar.style.height = `calc(100vh - ${navHeight}px)`;
        } else {
            sidebar.style.top = "0";
            sidebar.style.height = "100vh";
        }
    }

    adjustSidebarPosition();
    window.addEventListener("resize", adjustSidebarPosition);
});
