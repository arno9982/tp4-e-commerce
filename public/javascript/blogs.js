// Sélectionne toutes les cartes du blog
const blogCards = document.querySelectorAll('.blog-card');

// Fonction d'apparition au scroll
function showCardsOnScroll() {
  const triggerBottom = window.innerHeight * 0.85;

  blogCards.forEach(card => {
    const cardTop = card.getBoundingClientRect().top;
    if (cardTop < triggerBottom) {
      card.classList.add('visible');
    }
  });
}

// Événement au scroll
window.addEventListener('scroll', showCardsOnScroll);

// Appel initial
showCardsOnScroll();

const readMoreBtn=document.querySelector(".readMore");
const moreText=document.querySelector(".moreText");
let expanded=false;
readMoreBtn.addEventListener("click", function(event){event.preventDefault()  //empeche le rechargement de la page

    if (!expanded){
        moreText.computedStyleMap.display="inline";
        readMoreBtn.textContent="Lire moins";
        expanded=true;
    }else{
        moreText.computedStyleMap.display="none";
        readMoreBtn.textContent="Lire la suite";
        expanded=false;
    }

});

