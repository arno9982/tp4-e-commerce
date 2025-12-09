const themeButton = document.querySelector('.header-third button:first-child');

// Charger le thème précédent si défini
if(localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-mode');
}

// Activer/désactiver le mode sombre
themeButton.addEventListener('click', () => {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('theme', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
});