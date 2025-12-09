// ==================== EAZYSHOP RESPONSIVE MENU - VERSION FINALE + HAMBURGER À GAUCHE ====================

document.addEventListener('DOMContentLoaded', () => {
    let isMenuOpen = false;

    function createMobileMenu() {
        // Éviter la création multiple
        if (document.getElementById('mobileMenuBtn')) return;

        // On insère le bouton hamburger DANS LE HEADER, tout à gauche
        const headerContainer = document.querySelector('.header-box .container');
        if (!headerContainer) return;

        // Création du bouton hamburger
        const hamburgerBtn = document.createElement('button');
        hamburgerBtn.type = 'button';
        hamburgerBtn.id = 'mobileMenuBtn';
        hamburgerBtn.className = 'mobile-menu-btn';
        hamburgerBtn.setAttribute('aria-label', 'Ouvrir le menu');
        hamburgerBtn.setAttribute('aria-expanded', 'false');
        hamburgerBtn.innerHTML = '<span></span><span></span><span></span>';

        // On l'insère en PREMIER enfant du header → il sera tout à gauche
        headerContainer.insertBefore(hamburgerBtn, headerContainer.firstChild);

        // Overlay
        const overlay = document.createElement('div');
        overlay.id = 'mobileMenuOverlay';
        overlay.className = 'mobile-menu-overlay';
        overlay.setAttribute('aria-hidden', 'true');
        document.body.appendChild(overlay);

        // Drawer (menu latéral)
        const drawer = document.createElement('aside');
        drawer.id = 'mobileMenuDrawer';
        drawer.className = 'mobile-menu-drawer';
        drawer.setAttribute('aria-hidden', 'true');
        drawer.innerHTML = getDrawerHTML();
        document.body.appendChild(drawer);

        // On initialise les événements maintenant que tout existe dans le DOM
        initMobileMenuEvents();
    }

    // === HTML du drawer (inchangé, juste plus propre) ===
    function getDrawerHTML() {
        // ... (ton code getDrawerHTML() reste exactement le même que dans ton message précédent)
        // Je le remets ici pour que le fichier soit complet :
        const navLinks = Array.from(document.querySelectorAll('.menu-first nav a'));
        const langSelect = document.querySelector('#lang');
        const currencySelect = document.querySelector('#currency');

        const navItems = navLinks.map(link => {
            const text = link.textContent.trim();
            const href = link.getAttribute('href') || '#';
            const isActive = link.classList.contains('active-link');

            const icons = {
                'accueil': 'fa-home', 'home': 'fa-home',
                'produits': 'fa-shopping-bag', 'products': 'fa-shopping-bag',
                'catégories': 'fa-th-large', 'categories': 'fa-th-large',
                'contact': 'fa-envelope',
                'à propos': 'fa-info-circle', 'about': 'fa-info-circle',
                'blog': 'fa-blog',
                'panier': 'fa-shopping-cart', 'cart': 'fa-shopping-cart',
                'compte': 'fa-user', 'account': 'fa-user'
            };

            let icon = 'fa-circle';
            Object.keys(icons).forEach(key => {
                if (text.toLowerCase().includes(key)) icon = icons[key];
            });

            return `
                <a href="${href}" class="${isActive ? 'active' : ''}">
                    <i class="fas ${icon}"></i>
                    <span>${text}</span>
                </a>
            `;
        }).join('');

        return `
            <div class="menu-header">
                <h3>Menu</h3>
                <button type="button" class="menu-close" id="menuClose" aria-label="Fermer le menu">×</button>
            </div>
            <div class="menu-content">
                <div class="menu-account">
                    <img src="${getImagePath('images/user.jpeg')}" alt="Photo de profil" onerror="this.src='https://via.placeholder.com/55/6a0dad/ffffff?text=User'">
                    <div class="menu-account-info">
                        <h4>Mon Compte</h4>
                        <p>Bienvenue sur EazyShop</p>
                    </div>
                </div>

                <div class="menu-section">
                    <div class="menu-section-title">Navigation</div>
                    <div class="menu-links">${navItems || '<a href="/"><i class="fas fa-home"></i> Accueil</a>'}</div>
                </div>

                <div class="menu-section">
                    <div class="menu-section-title">Paramètres</div>
                    <div class="menu-settings">
                        <div class="menu-setting-item">
                            <label><i class="fas fa-globe"></i> Langue</label>
                            <select id="mobileLang">${langSelect?.innerHTML || '<option value="fr">FR</option><option value="en">EN</option>'}</select>
                        </div>
                        <div class="menu-setting-item">
                            <label><i class="fas fa-dollar-sign"></i> Devise</label>
                            <select id="mobileCurrency">${currencySelect?.innerHTML || '<option value="eur">EUR</option><option value="usd">USD</option>'}</select>
                        </div>
                    </div>
                </div>

                <div class="menu-section">
                    <div class="menu-section-title">Mon Compte</div>
                    <div class="menu-links">
                        <a href="/profile"><i class="fas fa-user"></i> Mon Profil</a>
                        <a href="/orders"><i class="fas fa-box"></i> Mes Commandes</a>
                        <a href="/wishlist"><i class="fas fa-heart"></i> Favoris</a>
                        <a href="/settings"><i class="fas fa-cog"></i> Paramètres</a>
                        <a href="/logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                    </div>
                </div>
            </div>
        `;
    }

    // === Événements (identiques à ta version) ===
    function initMobileMenuEvents() {
        const btn = document.getElementById('mobileMenuBtn');
        const overlay = document.getElementById('mobileMenuOverlay');
        const drawer = document.getElementById('mobileMenuDrawer');
        const closeBtn = document.getElementById('menuClose');

        const openMenu = () => {
            isMenuOpen = true;
            btn?.classList.add('active');
            btn?.setAttribute('aria-expanded', 'true');
            overlay?.classList.add('active');
            overlay?.setAttribute('aria-hidden', 'false');
            drawer?.classList.add('active');
            drawer?.setAttribute('aria-hidden', 'false');
            document.body.classList.add('menu-open');
        };

        const closeMenu = () => {
            isMenuOpen = false;
            btn?.classList.remove('active');
            btn?.setAttribute('aria-expanded', 'false');
            overlay?.classList.remove('active');
            overlay?.setAttribute('aria-hidden', 'true');
            drawer?.classList.remove('active');
            drawer?.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('menu-open');
        };

        btn?.addEventListener('click', () => isMenuOpen ? closeMenu() : openMenu());
        closeBtn?.addEventListener('click', closeMenu);
        overlay?.addEventListener('click', closeMenu);

        drawer?.querySelectorAll('a[href]').forEach(link => {
            link.addEventListener('click', () => {
                if (link.getAttribute('href') !== '#') setTimeout(closeMenu, 300);
            });
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && isMenuOpen) closeMenu();
        });
    }

    // === Sync langue/devise + panier + resize (inchangés) ===
    function syncLanguageAndCurrency() { /* ... ton code ... */ }
    window.updateCartCount = function() { /* ... */ }
    function getImagePath(path) { return window.location.pathname.includes('/pages/') ? `../${path}` : path; }

    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            if (window.innerWidth > 768 && isMenuOpen) {
                document.querySelectorAll('.mobile-menu-btn, .mobile-menu-overlay, .mobile-menu-drawer')
                    .forEach(el => el?.classList.remove('active'));
                document.body.classList.remove('menu-open');
                isMenuOpen = false;
            }
        }, 200);
    });

    // === LANCEMENT ===
    createMobileMenu();
    syncLanguageAndCurrency();
    updateCartCount();
});