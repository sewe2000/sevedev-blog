const dropdown = document.querySelector('.dropdown');
const dropdownList = document.querySelector('.dropdown-list');

dropdown.addEventListener('mouseover', () => {
  dropdownList.style.display = '';
});

dropdown.addEventListener('mouseout', () => {
  dropdownList.style.display = 'none';
})
