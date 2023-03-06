const navbarToggle = document.querySelector('.nav-toggle-label');
navbarToggle.addEventListener('click', () => {
  const ariaExpanded = navbarToggle.getAttribute('aria-expanded');
  navbarToggle.setAttribute('aria-expanded', !(ariaExpanded === 'true'));
})
