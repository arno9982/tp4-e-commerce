/*
 * product-cart-checkout.js
 * JS pour: 
 * - Ouvrir une "fiche produit" (modal) au clic sur une carte produit.
 * - Ajouter au panier, incrémenter le badge du header, persister le panier (localStorage).
 * - Ouvrir le panier (modal), modifier quantités, supprimer un article, vider le panier, recalculer les totaux.
 * - Procéder au checkout (modal), valider et simuler un envoi.
 *
 * Hypothèses:
 * - Les cartes ont la structure montrée dans product-list (voir classes .product-card, .product-link, .card-details...).
 * - Il existe un bouton/icone pour ouvrir le panier: #btn-cart ou .btn-cart ou [data-open-cart].
 * - Le compteur du panier est: #cart-count ou .cart-count ou [data-cart-count].
 * - Les styles fournis dans la consigne ciblent les classes utilisées ci‑dessous.
 */
(function () {
  'use strict';

  // ==== Utils ==============================================================
  const qs = (sel, el = document) => el.querySelector(sel);
  const qsa = (sel, el = document) => Array.from(el.querySelectorAll(sel));

  const money = {
    parse(text) {
      // extrait les chiffres d'une chaîne (ex: "**50 000 FCFA**" -> 50000)
      const num = String(text || '').replace(/[^0-9.,]/g, '').replace(/,/g, '.');
      return Number(num || 0);
    },
    formatXAF(value) {
      const n = Number(value || 0);
      // XAF n'a pas de décimales usuelles. On affiche des milliers séparés par espace insécable
      return new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(n) + ' XAF';
    }
  };

  const dom = {
    elCartCount() {
      return qs('#cart-count') || qs('.cart-count') || qs('[data-cart-count]');
    },
    elCartOpenBtn() {
      return qs('#btn-cart') || qs('.btn-cart') || qs('[data-open-cart]');
    }
  };

  // ==== Store (localStorage) ==============================================
  const STORAGE_KEY = 'shop.cart.v1';

  const Cart = {
    _data: new Map(), // key: id (slug), value: { id, name, price, image, qty }

    load() {
      try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) return;
        const arr = JSON.parse(raw);
        this._data = new Map(arr.map(i => [i.id, i]));
      } catch (e) { console.error('Cart load error', e); }
    },
    save() {
      try {
        const arr = Array.from(this._data.values());
        localStorage.setItem(STORAGE_KEY, JSON.stringify(arr));
      } catch (e) { console.error('Cart save error', e); }
    },
    add(item, qty = 1) {
      const cur = this._data.get(item.id);
      if (cur) {
        cur.qty += qty;
      } else {
        this._data.set(item.id, { ...item, qty });
      }
      this.save();
      ui.updateCartCount();
    },
    setQty(id, qty) {
      const it = this._data.get(id);
      if (!it) return;
      it.qty = Math.max(1, qty | 0);
      this.save();
    },
    remove(id) {
      this._data.delete(id);
      this.save();
      ui.updateCartCount();
    },
    clear() {
      this._data.clear();
      this.save();
      ui.updateCartCount();
    },
    items() { return Array.from(this._data.values()); },
    stats() {
      const items = this.items();
      const nbProducts = items.length;
      const quantity = items.reduce((s, i) => s + i.qty, 0);
      const total = items.reduce((s, i) => s + i.qty * i.price, 0);
      return { nbProducts, quantity, total };
    }
  };

  // ==== Modal System =======================================================
  const Modal = {
    open(html, opts = {}) {
      const { onOpen, onClose, width = 'min(980px, 96vw)' } = opts;
      const overlay = document.createElement('div');
      overlay.className = 'modal-overlay';
      overlay.style.cssText = `position:fixed;inset:0;background:rgba(0,0,0,.55);display:flex;align-items:center;justify-content:center;z-index:9999;padding:2rem;`;
      const wrap = document.createElement('div');
      wrap.className = 'modal-wrap';
      wrap.style.cssText = `background:#fff;max-height:90vh;overflow:auto;width:${width};border-radius:12px;box-shadow:0 10px 40px rgba(0,0,0,.25);`;
      wrap.innerHTML = html;

      const close = () => {
        overlay.removeEventListener('click', onOverlay);
        document.removeEventListener('keydown', onEsc);
        overlay.remove();
        onClose && onClose();
      };
      const onOverlay = (e) => { if (e.target === overlay) close(); };
      const onEsc = (e) => { if (e.key === 'Escape') close(); };

      const btnClose = document.createElement('button');
      btnClose.setAttribute('aria-label', 'Fermer');
      btnClose.innerHTML = '&times;';
      btnClose.style.cssText = 'position:absolute;top:8px;right:16px;font-size:32px;background:transparent;border:0;cursor:pointer;opacity:.6;';
      btnClose.addEventListener('click', close);

      overlay.addEventListener('click', onOverlay);
      document.addEventListener('keydown', onEsc);

      overlay.appendChild(wrap);
      overlay.appendChild(btnClose);
      document.body.appendChild(overlay);

      onOpen && onOpen({ overlay, wrap, close });
      return { overlay, wrap, close };
    }
  };

  // ==== UI builders ========================================================
  const ui = {
    updateCartCount() {
      const badge = dom.elCartCount();
      const altBadge = document.querySelector('.btn-cart .nb-cart'); // support de ton bouton custom
      const { quantity } = Cart.stats();
      const text = quantity > 99 ? '99+' : String(quantity);

      if (badge) {
        badge.textContent = text;
        badge.style.display = quantity > 0 ? '' : 'none';
      }
      if (altBadge) {
        altBadge.textContent = text;
        altBadge.style.display = quantity > 0 ? 'inline-flex' : 'none';
      }
    },

    // Petit toast pour confirmer l'ajout
    toast(message = 'Produit ajouté au panier ✅') {
      const el = document.createElement('div');
      el.className = 'toast-added';
      el.style.cssText = 'position:fixed;right:16px;bottom:16px;background:#111;color:#fff;padding:12px 16px;border-radius:10px;box-shadow:0 6px 24px rgba(0,0,0,.25);z-index:10000;opacity:0;transform:translateY(8px);transition:opacity .2s, transform .2s';
      el.textContent = message;
      document.body.appendChild(el);
      requestAnimationFrame(() => { el.style.opacity = '1'; el.style.transform = 'translateY(0)'; });
      setTimeout(() => {
        el.style.opacity = '0'; el.style.transform = 'translateY(8px)';
        setTimeout(() => el.remove(), 250);
      }, 1500);
    },

    productModal(product) {
      const stars = ui.starIcons(product.rating || 0);
      const html = `
        <main>
          <div class="container">
            <div class="image-box">
              <img src="${product.image}" alt="${escapeHtml(product.name)}">
            </div>
            <div class="details-box">
              <h2>${escapeHtml(product.name)}</h2>
              <p class="stars">${stars}</p>
              <p class="description">${escapeHtml(product.description || '—')}</p>
              <p class="price">${money.formatXAF(product.price)}</p>
              <p class="in-stock">
                <svg viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg" fill="#000000"><path class="cls-1" d="M800,510a30,30,0,1,1,30-30A30,30,0,0,1,800,510Zm-16.986-23.235a3.484,3.484,0,0,1,0-4.9l1.766-1.756a3.185,3.185,0,0,1,4.574.051l3.12,3.237a1.592,1.592,0,0,0,2.311,0l15.9-16.39a3.187,3.187,0,0,1,4.6-.027L817,468.714a3.482,3.482,0,0,1,0,4.846l-21.109,21.451a3.185,3.185,0,0,1-4.552.03Z" transform="translate(-770 -450)"></path></svg>
                &nbsp;in stock
              </p>
              <p class="options">
                <button type="button" class="btn-add-cart">
                  <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24"><path d="M440-600v-120H320v-80h120v-120h80v120h120v80H520v120h-80ZM280-80q-33 0-56.5-23.5T200-160q0-33 23.5-56.5T280-240q33 0 56.5 23.5T360-160q0 33-23.5 56.5T280-80Zm400 0q-33 0-56.5-23.5T600-160q0-33 23.5-56.5T680-240q33 0 56.5 23.5T760-160q0 33-23.5 56.5T680-80ZM40-800v-80h131l170 360h280l156-280h91L692-482q-11 20-29.5 31T622-440H324l-44 80h480v80H280q-45 0-68.5-39t-1.5-79l54-98-144-304H40Z"/></svg>
                  <span>Add To Cart</span>
                </button>
                <button type="button" class="btn-catalog">
                  <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24"><path d="M360-200 80-480l280-280 56 56-183 184h647v80H233l184 184-57 56Z"/></svg>
                  <span>See the catalog</span>
                </button>
              </p>
            </div>
          </div>
        </main>`;

      const { wrap, close } = Modal.open(html, { width: 'min(1100px, 96vw)' });
      const btnAdd = qs('.btn-add-cart', wrap);
      btnAdd?.addEventListener('click', () => {
        Cart.add({ id: product.id, name: product.name, price: product.price, image: product.image });
        ui.toast('Produit ajouté au panier ✅');
      });
      qs('.btn-catalog', wrap)?.addEventListener('click', close);
    },

    starIcons(rating) {
      const full = Math.max(0, Math.min(5, Math.round(rating)));
      const empty = 5 - full;
      return `${'★'.repeat(full)}${'☆'.repeat(empty)}`;
    },

    cartModal() {
      const items = Cart.items();
      const { nbProducts, quantity, total } = Cart.stats();

      const rows = items.map((it, idx) => `
        <tr data-id="${it.id}">
          <td><img src="${it.image}" alt="${escapeHtml(it.name)}"></td>
          <td>${escapeHtml(it.name)}</td>
          <td class="unit">${money.formatXAF(it.price)}</td>
          <td><input type="number" min="1" value="${it.qty}" class="qty-input"></td>
          <td class="subtotal">${money.formatXAF(it.price * it.qty)}</td>
          <td>
            <button type="button" class="btn-delete-cart" aria-label="Supprimer">
              <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg>
            </button>
          </td>
        </tr>`).join('');

      const html = `
        <main>
          <div class="container">
            <h1>Shopping Cart</h1>
            <div class="cart-content">
              <div class="cart-box">
                <div class="cart-options">
                  <button type="button" class="btn-continue">Continue Shopping</button>
                  <button type="button" class="btn-clear">Clear Shopping Cart</button>
                </div>
                <table>
                  <thead>
                    <tr>
                      <th colspan="2">Item</th>
                      <th>Unit Price</th>
                      <th>Quantity</th>
                      <th>Subtotal</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    ${rows || '<tr><td colspan="6">Votre panier est vide.</td></tr>'}
                  </tbody>
                </table>
              </div>
              <div class="cart-summary">
                <h2>Summary</h2>
                <div class="summary-content">
                  <div class="total-products"><span class="total-label">Nb. Products</span><span class="total-value" data-sum-products>${nbProducts}</span></div>
                  <div class="total-quantity"><span class="total-label">Quantity</span><span class="total-value" data-sum-qty>${quantity}</span></div>
                  <div class="total-price"><span class="total-label">Total</span><span class="total-value" data-sum-total>${money.formatXAF(total)}</span></div>
                  <div class="options-box">
                    <button type="button" class="btn-proceed" ${nbProducts ? '' : 'disabled'}>Proceed to Checkout</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </main>`;

      const { wrap, close } = Modal.open(html, { width: 'min(1200px, 98vw)' });

      // Listeners
      qs('.btn-continue', wrap)?.addEventListener('click', close);
      qs('.btn-clear', wrap)?.addEventListener('click', () => { Cart.clear(); ui.refreshCartDom(wrap); });
      qsa('tbody .btn-delete-cart', wrap).forEach(btn => btn.addEventListener('click', (e) => {
        const tr = e.currentTarget.closest('tr');
        const id = tr?.getAttribute('data-id');
        if (!id) return;
        Cart.remove(id);
        tr.remove();
        ui.refreshCartDom(wrap);
      }));
      qsa('tbody .qty-input', wrap).forEach(inp => inp.addEventListener('input', (e) => {
        const input = e.currentTarget;
        const tr = input.closest('tr');
        const id = tr?.getAttribute('data-id');
        const qty = Math.max(1, parseInt(input.value || '1', 10));
        input.value = String(qty);
        Cart.setQty(id, qty);
        qs('.subtotal', tr).textContent = money.formatXAF(Cart._data.get(id).price * qty);
        ui.refreshCartDom(wrap);
      }));

      qs('.btn-proceed', wrap)?.addEventListener('click', () => {
        if (!Cart.items().length) return;
        ui.checkoutModal(close);
      });
    },

    refreshCartDom(cartWrap) {
      const { nbProducts, quantity, total } = Cart.stats();
      const sumProducts = qs('[data-sum-products]', cartWrap);
      const sumQty = qs('[data-sum-qty]', cartWrap);
      const sumTotal = qs('[data-sum-total]', cartWrap);
      if (sumProducts) sumProducts.textContent = nbProducts;
      if (sumQty) sumQty.textContent = quantity;
      if (sumTotal) sumTotal.textContent = money.formatXAF(total);
      ui.updateCartCount();
      const proceed = qs('.btn-proceed', cartWrap);
      if (proceed) proceed.disabled = nbProducts === 0;

      // Si tableau vide -> message
      const tbody = qs('tbody', cartWrap);
      if (tbody && !Cart.items().length) {
        tbody.innerHTML = '<tr><td colspan="6">Votre panier est vide.</td></tr>';
      }
    },

    checkoutModal(onCloseCart) {
      const html = `
        <main>
          <div class="container">
            <div class="checkout-first">
              <h1>Checkout</h1>
              <button type="button" class="btn-sign-in">Sign in</button>
            </div>
            <form action="#" class="checkout-form" novalidate>
              <div class="form-first">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
                <label for="first-name">First Name</label>
                <input type="text" name="first-name" id="first-name" required>
                <label for="last-name">Last Name</label>
                <input type="text" name="last-name" id="last-name" required>
                <label for="phone">Phone Number</label>
                <input type="text" name="phone" id="phone" required>
                <label for="street-address">Street Address</label>
                <input type="text" name="street-address" id="street-address" required>
                <label for="city">City</label>
                <input type="text" name="city" id="city" required>
              </div>
              <div class="form-second">
                <label for="region">Region</label>
                <select name="region" id="region" required>
                  <option value="">—</option>
                  <option value="adamaoua">Adamaoua</option>
                  <option value="center">Center</option>
                  <option value="east">East</option>
                  <option value="extreme-North">Extreme-North</option>
                  <option value="littoral">Littoral</option>
                  <option value="north">North</option>
                  <option value="north-west">North-West</option>
                  <option value="south">South</option>
                  <option value="south-west">South-West</option>
                  <option value="west">West</option>
                </select>
                <label for="postal">Postal Code</label>
                <input type="text" name="postal" id="postal" required>
                <label for="region">Payment &nbsp;(Select your payment method)</label>
                <div class="payment-methods">
                  ${['paypal','visa','mastercard','orange-money','momo'].map(id => `
                    <div class="payment-method">
                      <input type="radio" name="payment" id="${id}" value="${id}">
                      <label for="${id}"><img src="../images/payment/${id}.png" alt="${id}"></label>
                    </div>`).join('')}
                </div>
                <label for="identifier">Payment identifier</label>
                <input type="number" name="identifier" id="identifier" required>
                <button type="submit" class="btn-checkout">Validate</button>
                <button type="reset" class="btn-reset">Reset</button>
              </div>
            </form>
          </div>
        </main>`;

      const { wrap, close } = Modal.open(html, { width: 'min(1100px, 98vw)' });
      onCloseCart && onCloseCart();

      const form = qs('.checkout-form', wrap);
      form?.addEventListener('submit', (e) => {
        e.preventDefault();
        const data = new FormData(form);
        const required = ['email','first-name','last-name','phone','street-address','city','region','postal','identifier'];
        const missing = required.filter(k => !String(data.get(k) || '').trim());
        if (missing.length) {
          alert('Veuillez remplir tous les champs obligatoires.');
          return;
        }
        if (!data.get('payment')) {
          alert('Veuillez choisir un moyen de paiement.');
          return;
        }
        // Simulation d'envoi
        const recap = Cart.items().map(i => `${i.qty}× ${i.name} = ${money.formatXAF(i.qty * i.price)}`).join('\\n');
        alert('Commande envoyée !\\n\\nRécapitulatif:\\n' + recap + '\\n\\nTotal: ' + money.formatXAF(Cart.stats().total));
        Cart.clear();
        close();
      });
    }
  };

  // ==== Product extraction =================================================
  function productFromCard(card) {
    // id à partir du href (slug)
    const link = qs('a.product-link', card);
    const href = link?.getAttribute('href') || '#';
    const id = (href.split('/').filter(Boolean).pop() || '').toLowerCase();

    const img = qs('img', card);
    const image = img?.getAttribute('src') || '';

    const name = (qs('h3', card)?.textContent || '').trim();

    // rating depuis (x.y) s'il existe, sinon compte les .stars.full
    let rating = 0;
    const reviewTxt = (qs('.review-count', card)?.textContent || '').match(/([0-9]+(?:\\.[0-9]+)?)/);
    if (reviewTxt) {
      rating = parseFloat(reviewTxt[1]);
    } else {
      rating = qsa('.product-rating .stars.full', card).length;
    }

    // prix: extraire nombre
    const priceStr = (qs('.price', card)?.textContent || '').trim();
    const price = money.parse(priceStr);

    // description: pas présente dans la carte -> placeholder
    const description = (img?.getAttribute('alt') || '').trim();

    return { id, name, image, price, rating, description };
  }

  // ==== Wiring =============================================================
  function bindProductCards() {
    qsa('.product-card').forEach(card => {
      // Ouvrir modal au clic sur le <a> (et empêcher navigation)
      const link = qs('a.product-link', card) || card;
      link.addEventListener('click', (e) => {
        // si CTRL/Cmd click -> laisser le comportement natif
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        e.preventDefault();
        const product = productFromCard(card);
        ui.productModal(product);
      });
    });
  }

  function bindCartOpen() {
    const btn = dom.elCartOpenBtn();
    btn && btn.addEventListener('click', (e) => {
      e.preventDefault();
      ui.cartModal();
    });
  }

  function init() {
    Cart.load();
    ui.updateCartCount();
    bindProductCards();
    bindCartOpen();
  }

  // Petite fonction d'échappement HTML
  function escapeHtml(str) {
    return String(str).replace(/[&<>"']/g, (ch) => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    })[ch]);
  }

  // Init au DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();























// Variables globales
let allProducts = [];
let filteredProducts = [];
let currentPage = 1;
const productsPerPage = 12;

// État des filtres
let filters = {
    priceMax: 1000000, // Mettre un prix très élevé par défaut
    size: null,
    shoeSize: null
};

// Récupération de tous les produits depuis le HTML
function getAllProductsFromHTML() {
    const productCards = document.querySelectorAll('.product-card');
    const products = [];
    
    productCards.forEach((card, index) => {
        const category = card.querySelector('.category-tag').textContent.trim();
        const name = card.querySelector('h3').textContent.trim();
        const priceText = card.querySelector('.price').textContent;
        const price = parseInt(priceText.replace(/[^0-9]/g, ''));
        const ratingText = card.querySelector('.review-count').textContent;
        const rating = parseFloat(ratingText.replace(/[()]/g, ''));
        
        // Récupérer les tailles depuis le HTML si elles existent
        let sizes = [];
        const sizesContainer = card.querySelector('.product-sizes');
        if (sizesContainer) {
            const sizeBadges = sizesContainer.querySelectorAll('.size-badge');
            sizes = Array.from(sizeBadges).map(badge => badge.textContent.trim());
        }
        
        products.push({
            id: index + 1,
            element: card,
            category: category,
            name: name,
            price: price,
            rating: rating,
            sizes: sizes
        });
    });
    
    return products;
}

// Fonction pour afficher les produits
function displayProducts() {
    const startIndex = (currentPage - 1) * productsPerPage;
    const endIndex = startIndex + productsPerPage;
    const productsToShow = filteredProducts.slice(startIndex, endIndex);
    
    // Cacher tous les produits
    allProducts.forEach(product => {
        product.element.style.display = 'none';
    });
    
    // Afficher uniquement les produits filtrés de la page actuelle
    productsToShow.forEach(product => {
        product.element.style.display = 'block';
    });
    
    updatePagination();
}

// Appliquer les filtres
function applyFilters() {
    filteredProducts = allProducts.filter(product => {
        // Filtre de prix
        if (product.price > filters.priceMax) {
            return false;
        }
        
        // Filtre de taille (vêtements)
        if (filters.size && product.category === 'VÊTEMENTS') {
            if (!product.sizes.includes(filters.size.toUpperCase())) {
                return false;
            }
        }
        
        // Filtre de taille (chaussures)
        if (filters.shoeSize && product.category === 'CHAUSSURES') {
            if (!product.sizes.includes(filters.shoeSize)) {
                return false;
            }
        }
        
        return true;
    });
    
    currentPage = 1;
    displayProducts();
}

// Mettre à jour la pagination
function updatePagination() {
    const totalPages = Math.ceil(filteredProducts.length / productsPerPage);
    const pagination = document.querySelector('.pagination');
    
    if (!pagination) return;
    
    pagination.innerHTML = '';
    
    // Bouton précédent
    const prevButton = document.createElement('a');
    prevButton.href = '#';
    prevButton.innerHTML = '&laquo; Précédent';
    if (currentPage === 1) {
        prevButton.classList.add('disabled');
        prevButton.style.opacity = '0.5';
        prevButton.style.pointerEvents = 'none';
    }
    prevButton.addEventListener('click', (e) => {
        e.preventDefault();
        if (currentPage > 1) {
            currentPage--;
            displayProducts();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
    pagination.appendChild(prevButton);
    
    // Numéros de page
    const maxPagesToShow = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
    let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);
    
    if (endPage - startPage < maxPagesToShow - 1) {
        startPage = Math.max(1, endPage - maxPagesToShow + 1);
    }
    
    for (let i = startPage; i <= endPage; i++) {
        const pageLink = document.createElement('a');
        pageLink.href = '#';
        pageLink.textContent = i;
        if (i === currentPage) {
            pageLink.classList.add('active');
        }
        pageLink.addEventListener('click', (e) => {
            e.preventDefault();
            currentPage = i;
            displayProducts();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        pagination.appendChild(pageLink);
    }
    
    // Bouton suivant
    const nextButton = document.createElement('a');
    nextButton.href = '#';
    nextButton.innerHTML = 'Suivant &raquo;';
    if (currentPage === totalPages || totalPages === 0) {
        nextButton.classList.add('disabled');
        nextButton.style.opacity = '0.5';
        nextButton.style.pointerEvents = 'none';
    }
    nextButton.addEventListener('click', (e) => {
        e.preventDefault();
        if (currentPage < totalPages) {
            currentPage++;
            displayProducts();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
    pagination.appendChild(nextButton);
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    console.log('Page chargée, initialisation...');
    
    // Récupérer les produits depuis le HTML
    allProducts = getAllProductsFromHTML();
    filteredProducts = [...allProducts];
    
    console.log('Nombre de produits trouvés:', allProducts.length);
    
    // Afficher les produits initiaux
    displayProducts();
    
    // Filtre de prix
    const priceRange = document.querySelector('.price-range');
    if (priceRange) {
        // Mettre à jour l'affichage du prix
        const priceDisplay = document.createElement('div');
        priceDisplay.style.textAlign = 'center';
        priceDisplay.style.marginTop = '10px';
        priceDisplay.style.fontWeight = 'bold';
        priceRange.parentNode.insertBefore(priceDisplay, priceRange.nextSibling);
        
        const updatePriceDisplay = () => {
            const value = parseInt(priceRange.value);
            const price = value * 100; // Convertir en FCFA
            priceDisplay.textContent = `Max: ${price.toLocaleString()} FCFA`;
            filters.priceMax = price;
        };
        
        updatePriceDisplay();
        
        priceRange.addEventListener('input', updatePriceDisplay);
    }
    
    // Filtre de taille (vêtements)
    const sizeInputs = document.querySelectorAll('input[name="size"]');
    sizeInputs.forEach(input => {
        input.addEventListener('change', (e) => {
            if (e.target.checked) {
                filters.size = e.target.value;
                // Décocher les autres
                sizeInputs.forEach(otherInput => {
                    if (otherInput !== e.target) {
                        otherInput.checked = false;
                    }
                });
            } else {
                filters.size = null;
            }
        });
    });
    
    // Filtre de taille (chaussures)
    const shoeSizeInputs = document.querySelectorAll('input[name="shoe-size"]');
    shoeSizeInputs.forEach(input => {
        input.addEventListener('change', (e) => {
            if (e.target.checked) {
                filters.shoeSize = e.target.value;
                // Décocher les autres
                shoeSizeInputs.forEach(otherInput => {
                    if (otherInput !== e.target) {
                        otherInput.checked = false;
                    }
                });
            } else {
                filters.shoeSize = null;
            }
        });
    });
    
    // Bouton appliquer les filtres
    const applyButton = document.querySelector('.apply-filters-btn');
    if (applyButton) {
        applyButton.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('Application des filtres:', filters);
            applyFilters();
        });
    }
    
    // Filtrage par catégorie
    const categoryLinks = document.querySelectorAll('.category-list a');
    categoryLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const categoryText = e.target.textContent.trim().toUpperCase();
            
            console.log('Catégorie cliquée:', categoryText);
            
            // Mapper les catégories françaises vers les catégories anglaises
            const categoryMap = {
                'ROBES & JUPES': 'ROBES & JUPES',
                'T-SHIRTS & HAUTS': 'T-SHIRTS & HAUTS',
                'JEANS & PANTALONS': 'JEANS & PANTALONS',
                'VESTES & MANTEAUX': 'VESTES & MANTEAUX',
                'CHAUSSURES FEMME': 'CHAUSSURES FEMME',
                'CHAUSSURES HOMME': 'CHAUSSURES HOMME',
                'ACCESSOIRES': 'ACCESSOIRES'
            };
            
            const category = categoryMap[categoryText];
            
            if (category) {
                filteredProducts = allProducts.filter(p => p.category === category);
                currentPage = 1;
                displayProducts();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    });
    
    // Réinitialiser les filtres en cliquant sur le titre "Catégories"
    const categoryTitle = document.querySelector('.sidebar h2');
    if (categoryTitle) {
        categoryTitle.style.cursor = 'pointer';
        categoryTitle.title = 'Cliquez pour afficher tous les produits';
        categoryTitle.addEventListener('click', () => {
            console.log('Réinitialisation des filtres');
            
            // Réinitialiser les filtres
            filteredProducts = [...allProducts];
            filters = {
                priceMax: 1000000,
                size: null,
                shoeSize: null
            };
            
            // Réinitialiser les inputs
            document.querySelectorAll('input[name="size"]').forEach(input => input.checked = false);
            document.querySelectorAll('input[name="shoe-size"]').forEach(input => input.checked = false);
            
            const priceRange = document.querySelector('.price-range');
            if (priceRange) {
                priceRange.value = 150;
                const event = new Event('input');
                priceRange.dispatchEvent(event);
            }
            
            currentPage = 1;
            displayProducts();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
    
    console.log('Initialisation terminée');
});









