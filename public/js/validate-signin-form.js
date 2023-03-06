import {clearError, checkIfEmpty, validateFormWithDataBase, isLengthOK} from './common-form-validation-functions.js'

const form = document.querySelector('form');
const checkbox = document.querySelector('.notify-checkbox');

checkbox.addEventListener('change', () => {
  const input = document.querySelector('#email');
  const label = document.querySelector('label[for="email"]');
  input.disabled = !input.disabled; 
  label.disabled = !label.disabled; 
    clearError(input, form);
});
    
form.addEventListener('submit', event => {
    event.preventDefault();
    if(!checkIfEmpty(form) && isLengthOK(form, true)) { 
	validateFormWithDataBase(form, 'validate_signin_data', false);
    }
});
      
    
