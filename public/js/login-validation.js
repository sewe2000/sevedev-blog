import { checkIfEmpty, validateFormWithDataBase } from './common-form-validation-functions.js';

const form = document.querySelector('form');
form.addEventListener('submit', event => {
    event.preventDefault();
    if(!checkIfEmpty(form)) {
	validateFormWithDataBase(form, 'login', true);
    }
});

