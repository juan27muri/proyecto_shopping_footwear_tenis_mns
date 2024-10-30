const container = document.getElementById('container');
const loginButton = document.getElementById('login');
const registerButton = document.getElementById('register');

loginButton.addEventListener('click', () => {
    container.classList.remove('active');
});

registerButton.addEventListener('click', () => {
    container.classList.add('active');
});
