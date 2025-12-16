/**
 * 2. Valider le Formulaire de Contact
 */

document.addEventListener('DOMContentLoaded', () => {
    
   


    // 1. Sélectionner le formulaire et le bouton
    const contactForm = document.querySelector('.contact-form');
    // Si le formulaire n'existe pas sur la page (par exemple, on est sur la page d'accueil), on s'arrête.
    if (!contactForm) return; 

    // Références à la modale et ses éléments
    const modal = document.getElementById('contact-modal');
    const modalContent = modal.querySelector('.modal-content');
    const modalTitle = document.getElementById('modal-title');
    const modalMessage = document.getElementById('modal-message');
    const closeButton = modal.querySelector('.close-button');
    const okButton = document.getElementById('modal-ok-button');

    // Fonction pour afficher la modale
    const showModal = (title, message, type) => {
        modalTitle.textContent = title;
        modalMessage.textContent = message;

        // Gérer le style (success ou error)
        modalContent.classList.remove('success', 'error');
        modalContent.classList.add(type);

        modal.style.display = 'block'; // Afficher la modale
    };

    // Fonction pour fermer la modale
    const closeModal = () => {
        modal.style.display = 'none';
    };

    // Fermer la modale en cliquant sur le X ou le bouton OK
    closeButton.onclick = closeModal;
    okButton.onclick = closeModal;

    // Fermer la modale en cliquant en dehors
    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    };

    // 2. Fonction utilitaire pour la validation d'email simple
    const isValidEmail = (email) => {
        // Regex simple pour vérifier le format de base d'un email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    };

    // 3. Écouter l'événement de soumission du formulaire
    contactForm.addEventListener('submit', function(event) {
        // Empêcher l'envoi par défaut du formulaire pour faire la validation
        event.preventDefault(); 
        
        // Récupérer les champs
        const nameInput = this.querySelector('input[placeholder="Your Name"]');
        const emailInput = this.querySelector('input[placeholder="Your Email"]');
        const messageTextarea = this.querySelector('textarea');
        
        let isValid = true;

        // Réinitialiser les indicateurs visuels d'erreur 
        nameInput.classList.remove('error');
        emailInput.classList.remove('error');
        messageTextarea.classList.remove('error');

        // Validation du Nom
        if (nameInput.value.trim() === '') {
            nameInput.classList.add('error');
            isValid = false;
        }
        
        // Validation de l'Email (obligatoire ET format)
        if (emailInput.value.trim() === '' || !isValidEmail(emailInput.value.trim())) {
            emailInput.classList.add('error');
            isValid = false;
        }

        // Validation du Message
        if (messageTextarea.value.trim() === '' || messageTextarea.value.trim().length < 10) {
            // J'ajoute une contrainte de minimum 10 caractères pour un message significatif
            messageTextarea.classList.add('error');
            isValid = false;
        }

        // Si tout est valide, on peut soumettre le formulaire 
        if (isValid) {
            console.log('Formulaire valide !');
            
            // AFFICHAGE DE LA MODALE DE SUCCÈS
            showModal(
                'Message Envoyé ! ✅', 
                'Merci de nous avoir contactés. Nous vous répondrons dans les plus brefs délais.', 
                'success'
            );
            
            contactForm.reset(); 
            // On ferme la modale après un délai pour montrer le message 
            // (optionnel, mais l'utilisateur peut la fermer lui-même)
            // setTimeout(closeModal, 5000); 

        } else {
            console.log('Validation échouée.');

            // AFFICHAGE DE LA MODALE D'ERREUR
            showModal(
                'Erreur de Validation ❌', 
                'Veuillez vérifier les champs marqués en rouge et vous assurer que toutes les informations obligatoires sont correctes.', 
                'error'
            );
        }
    });
    // 1. Sélectionner les éléments
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const body = document.body;
    
    // SVG de l'icône de la Lune (pour le mode sombre)
    const moonIconPath = 'M480-120q-150 0-255-105t-105-255q0-150 105-255t255-105q17 0 30.5 3.5t25.5 13.5q-10 4-19 9.5t-21 12.5q-86 52-137 141.5T360-480q0 103 50.5 186t137.5 141q10 6 18.5 10t19 11q-9 8-19 12t-27 12q-17 4-30 4Zm40-280v-80h200v80H520Zm0 240v-80h80v80h-80Zm-240-240v-80h80v80h-80Zm0 240v-80h80v80h-80Zm480 0v-80h80v80h-80Zm-80-200q-21 0-35.5-14.5T660-480q0-21 14.5-35.5T710-530q21 0 35.5 14.5T760-480q0 21-14.5 35.5T710-430Zm-230-80q-50 0-85-35t-35-85q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35Zm0 80q83 0 141.5-58.5T680-480q0-83-58.5-141.5T480-680q-83 0-141.5 58.5T280-480q0 83 58.5 141.5T480-280Z';
    // SVG de l'icône du Soleil (pour le mode clair) - L'icône est déjà présente dans votre HTML
    const sunIconPath = 'm480-360q50 0 85-35t35-85q0-50-35-85t-85-35q-50 0-85 35t-35 85q0 50 35 85t85 35Zm0 80q-83 0-141.5-58.5T280-480q0-83 58.5-141.5T480-680q83 0 141.5 58.5T680-480q0 83-58.5 141.5T480-280ZM200-440H40v-80h160v80Zm720 0H760v-80h160v80ZM440-760v-160h80v160h-80Zm0 720v-160h80v160h-80ZM256-650l-101-97 57-59 96 100-52 56Zm492 496-97-101 53-55 101 97-57 59Zm-98-550 97-101 59 57-100 96-56-52ZM154-212l101-97 55 53-97 101-59-57Zm326-268Z';


    // Fonction principale pour basculer le thème
    const toggleTheme = () => {
        // 1. Basculer la classe 'dark-mode' sur le body
        const isDarkMode = body.classList.toggle('dark-mode'); 

        // 2. Mettre à jour l'icône (Soleil si clair, Lune si sombre)
        if (themeIcon) { 
            themeIcon.querySelector('path').setAttribute('d', isDarkMode ? moonIconPath : sunIconPath);
        }

        // 3. Stocker la préférence de l'utilisateur
        localStorage.setItem('theme', isDarkMode ? 'dark' : 'light');
    };

    // 4. Appliquer le thème au chargement de la page
    const storedTheme = localStorage.getItem('theme');
    if (storedTheme === 'dark') {
        // Si le thème sombre est stocké, on l'applique
        body.classList.add('dark-mode');
        if (themeIcon) {
            themeIcon.querySelector('path').setAttribute('d', moonIconPath);
        }
    } 
    // Si 'light' est stocké ou s'il n'y a rien, le mode clair par défaut s'applique

    // 5. Attacher l'écouteur d'événement au bouton
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
    }

    
});


