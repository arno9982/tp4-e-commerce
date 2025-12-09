// Attend que tout le contenu HTML soit chargé avant d'exécuter le script
document.addEventListener("DOMContentLoaded", function() {
    
    // Configuration de l'Intersection Observer
    const observerOptions = {
        root: null, // Utilise le viewport (la fenêtre du navigateur) comme zone d'observation
        rootMargin: "0px",
        threshold: 0.1 // L'animation se déclenche quand 10% de l'élément est visible
    };

    // La fonction qui sera appelée quand un élément observé entre ou sort de la vue
    const observerCallback = (entries, observer) => {
        entries.forEach(entry => {
            // Si l'élément est en train d'entrer dans la vue
            if (entry.isIntersecting) {
                // On ajoute la classe "is-visible" pour déclencher l'animation CSS
                entry.target.classList.add("is-visible");
                
                // On cesse d'observer cet élément (l'animation ne se joue qu'une fois)
                observer.unobserve(entry.target);
            }
        });
    };

    // On crée l'observateur
    const observer = new IntersectionObserver(observerCallback, observerOptions);

    // On cible TOUTES les sections qu'on veut animer
    // On va leur ajouter la classe de "départ" (reveal-on-scroll)
    // et ensuite on demande à l'observer de les surveiller.
    const sectionsToReveal = document.querySelectorAll(
        '.about-section, .testimonial-section, .why-us-section, .page-title-section'
    );
    
    sectionsToReveal.forEach(section => {
        section.classList.add('reveal-on-scroll'); // Ajoute la classe CSS de "départ"
        observer.observe(section); // Demande à l'observer de surveiller cet élément
    });

});






