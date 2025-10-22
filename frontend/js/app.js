// Détection automatique de l'environnement
const API_BASE_URL = "localhost:13013";


async function chargerCatalogue(categoryId) {
  const div = document.getElementById("catalogue");
  try {
    const url = categoryId ? `${API_BASE_URL}/api/outils?category_id=${encodeURIComponent(categoryId)}` : `${API_BASE_URL}/api/outils`;
    const res = await fetch(url);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();
    const outils = Array.isArray(data) ? data : (data.items || []);

    div.innerHTML = outils.map(o => `
      <a class="outil" href="page/detail.html?id=${o.id}">
        <img src="${o.image_url || 'https://via.placeholder.com/300x200?text=Outil'}" alt="${o.name}" style="width:100%;height:auto;display:block;border-radius:6px;" />
        <h3>${o.name}</h3>
      </a>
    `).join("");
  } catch (e) {
    div.innerHTML = `<p>Erreur de chargement: ${e.message}</p>`;
  }
}

async function chargerCategories() {
  const nav = document.getElementById('categories');
  try {
    const res = await fetch(`${API_BASE_URL}/api/categories`);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();
    const cats = Array.isArray(data) ? data : (data.items || []);
    nav.innerHTML = ['<button data-cat="">Tous</button>']
      .concat(cats.map(c => `<button data-cat="${c.id}">${c.name}</button>`))
      .join(' ');
    nav.addEventListener('click', (e) => {
      const btn = e.target.closest('button[data-cat]');
      if (!btn) return;
      const cid = btn.getAttribute('data-cat');
      chargerCatalogue(cid || undefined);
    });
  } catch (e) {
    nav.innerHTML = `<p>Erreur catégories: ${e.message}</p>`;
  }
}

function initCart() {
  const nav = document.querySelector('nav') || document.body;
  const existingCartLink = nav.querySelector('a[href*="panier"]');
  
  if (!existingCartLink) {
    const cartLink = document.createElement('a');
    cartLink.href = 'page/panier.html';
    cartLink.innerHTML = 'Panier';
    cartLink.className = 'btn-cart';
    nav.appendChild(cartLink);
  }
}

function checkAuth() {
  const token = localStorage.getItem('token');
  const user = localStorage.getItem('user');
  if (token && user) {
    const userData = JSON.parse(user);
    document.getElementById('login-btn').style.display = 'none';
    document.getElementById('user-info').style.display = 'block';
    document.getElementById('username').textContent = `Bonjour ${userData.prenom} ${userData.nom}`;
  }
}

function logout() {
  localStorage.removeItem('token');
  localStorage.removeItem('user');
  location.reload();
}

window.logout = logout;

chargerCategories();
chargerCatalogue();
initCart();
checkAuth();
