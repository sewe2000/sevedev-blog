const links = document.querySelectorAll('.remove-link');

links.forEach(link => {
  link.addEventListener('click', function(event) {
    event.preventDefault();
    if(confirm('Czy na pewno chcesz usunąć ten post?')) {

      const formData = new FormData();
      formData.append('id', this.getAttribute('data-id'));

      fetch(this.href, {
	method: 'POST',
	body: formData
      }).then((response) => {
        if(response.ok)
          location.reload();
      });
    }
  })
})

const addCategoryButton = document.querySelector('.add-category');
addCategoryButton.addEventListener('click', event  => {
  event.preventDefault();
  const name = prompt('Podaj nazwę nowej kategorii', '');

  if(name) {

    const formData = new FormData();
    formData.append('name', name);

    fetch(addCategoryButton.href, {
      method: 'POST',
      body: formData
    }).then(response => {
      if(response.ok)
	alert('Pomyślnie dodano nową kategorię');
      else
	alert('Błąd podczas dodawania kategorii!');
    })
  }
});

