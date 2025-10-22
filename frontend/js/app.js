async function chargerCatalogue(categoryId) {
  const div = document.getElementById("catalogue");
  try {
    const url = categoryId ? `${API_BASE_URL}/api/outils?category_id=${encodeURIComponent(categoryId)}` : `${API_BASE_URL}/api/outils`;
    const res = await fetch(url);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();
    const outils = Array.isArray(data) ? data : (data.items || []);

    div.innerHTML = outils.map(o => `
      <a class="group block rounded-lg overflow-hidden border border-slate-200 bg-white shadow-sm hover:shadow-md transition" href="page/detail.html?id=${o.id}">
        <div class="aspect-[3/2] overflow-hidden bg-slate-100">
          <img src="${o.image_url || 'https://via.placeholder.com/300x200?text=Outil'}" alt="${o.name}" class="w-full h-full object-cover group-hover:scale-105 transition" />
        </div>
        <div class="p-3">
          <h3 class="text-sm font-semibold line-clamp-2">${o.name}</h3>
        </div>
      </a>
    `).join("");
  } catch (e) {
    div.innerHTML = `<p class="text-sm text-red-600">Erreur de chargement: ${e.message}</p>`;
  }
}

async function chargerCategories() {
  const nav = document.getElementById('categories');
  try {
    const res = await fetch(`${API_BASE_URL}/api/categories`);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();
    const cats = Array.isArray(data) ? data : (data.items || []);
    nav.innerHTML = ['<button data-cat="" class="btn-cat px-3 py-1.5 rounded-md border border-slate-300 bg-white text-sm hover:bg-slate-100 transition">Tous</button>']
      .concat(cats.map(c => `<button data-cat="${c.id}" class="btn-cat px-3 py-1.5 rounded-md border border-slate-300 bg-white text-sm hover:bg-slate-100 transition">${c.name}</button>`))
      .join(' ');

    const setActive = (cid) => {
      nav.querySelectorAll('.btn-cat').forEach(b => {
        b.classList.remove('bg-emerald-600','text-white','border-emerald-600');
        b.classList.add('bg-white','text-slate-800','border-slate-300');
      });
      const active = nav.querySelector(`.btn-cat[data-cat="${cid ?? ''}"]`);
      if (active) {
        active.classList.remove('bg-white','text-slate-800','border-slate-300');
        active.classList.add('bg-emerald-600','text-white','border-emerald-600');
      }
    };

    setActive('');

    nav.addEventListener('click', (e) => {
      const btn = e.target.closest('button[data-cat]');
      if (!btn) return;
      const cid = btn.getAttribute('data-cat');
      setActive(cid || '');
      chargerCatalogue(cid || undefined);
    });
  } catch (e) {
    nav.innerHTML = `<p class="text-sm text-red-600">Erreur catégories: ${e.message}</p>`;
  }
}

function initCart() {
  const headerRight = document.getElementById('auth-buttons');
  const existingCartLink = document.querySelector('a[href*="panier"]');
  
  if (!existingCartLink) {
    const cartLink = document.createElement('a');
    cartLink.href = 'page/panier.html';
    cartLink.innerHTML = 'Panier';
    cartLink.className = 'inline-flex items-center justify-center rounded-md border border-slate-300 px-3 py-1.5 text-sm hover:bg-slate-100 transition';
    headerRight?.appendChild(cartLink);
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
