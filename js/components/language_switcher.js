document.addEventListener("DOMContentLoaded", () => {
    const carousel = document.getElementById("languageCarousel");
    if (!carousel) return;

    const items = Array.from(carousel.children);
    const activeItem = carousel.querySelector(".language-item.active");
    let currentIndex = items.indexOf(activeItem);

    function scrollToActive() {
        if (activeItem) {
            const container = carousel.parentElement;
            const scrollLeft = activeItem.offsetLeft - (container.offsetWidth / 2) + (activeItem.offsetWidth / 2);
            container.scrollLeft = scrollLeft;
        }
    }

    // Initial scroll to center the active item
    scrollToActive();

    carousel.addEventListener("click", (event) => {
        const target = event.target.closest(".language-item");
        if (target) {
            const lang = target.dataset.lang;
            // Redirect to update the language
            window.location.href = `?lang=${lang}`;
        }
    });
});