const navigationBar = document.querySelector('.mobile-navigation-bar');
const closeButton = document.querySelector('.close');

navigationBar.addEventListener('click', showNavigationBar);
closeButton.addEventListener('click', hideNavigationBar);

function showNavigationBar() {
    const content = document.querySelector('.topics-pane');
    content.style.margin = 0;
    content.style.display = 'flex';
    content.style.flexDirection = 'column';
    content.style.position = 'fixed';
    content.style.top = 0;
    content.style.left = 0;
}

function hideNavigationBar() {
        const content = document.querySelector('.topics-pane');
        content.style.display = 'none';
}
