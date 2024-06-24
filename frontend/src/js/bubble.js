export function createBubbles(element) {
    const bubbleContainer = element.querySelector('.bubble-container');
    if (!bubbleContainer) return;

    function createBubble() {
        const bubble = document.createElement('div');
        bubble.classList.add('bubble');
        bubble.style.left = `${Math.random() * 100}%`;
        bubble.style.animationDuration = `${Math.random() * 5 + 5}s`;
        bubble.style.width = bubble.style.height = `${Math.random() * 30 + 10}px`;
        bubble.style.opacity = `${Math.random() * 0.5 + 0.2}`;

        bubbleContainer.appendChild(bubble);

        setTimeout(() => {
            bubble.remove();
        }, 10000);
    }

    setInterval(createBubble, 300);
}

