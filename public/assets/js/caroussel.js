let currentScrollPosition = 0;
const carousel = document.querySelector('.carousel');
const itemWidth = document.querySelector('.carousel-item').offsetWidth;
const visibleItems = 5;

function scrollCarousel(direction) {
    const maxScroll = (carousel.children.length - visibleItems) * itemWidth;
    currentScrollPosition += direction * itemWidth;
    
    if (currentScrollPosition < 0) {
        currentScrollPosition = 0;
    } else if (currentScrollPosition > maxScroll) {
        currentScrollPosition = maxScroll;
    }

    carousel.style.transform = `translateX(-${currentScrollPosition}px)`;
}
