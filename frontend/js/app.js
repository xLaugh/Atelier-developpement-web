async function chargerCatalogue(categoryId, page = 1, search = '') {
  const div = document.getElementById("catalogue");
  try {
    let url;
    if (search) {
      url = `${API_BASE_URL}/api/outils/search?q=${encodeURIComponent(search)}&page=${page}&limit=48`;
    } else {
      url = `${API_BASE_URL}/api/outils/paginated?page=${page}&limit=48`;
      if (categoryId) {
        url += `&category_id=${categoryId}`;
      }
    }
    
    const res = await fetch(url);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();
    const outils = data.items || [];

    div.innerHTML = outils
      .map(
        (o) => `
      <a class="outil" href="page/detail.html?id=${o.id}">
        <img src="${o.image_url || 'https://via.placeholder.com/300x200?text=Outil'}" alt="${o.name}" />
        <h3>${o.name}</h3>
      </a>
    `
      )
      .join("");
      
    // Mettre à jour la pagination
    if (data.pagination) {
      updatePagination(data.pagination);
    }
  } catch (e) {
    div.innerHTML = `<p>Erreur de chargement: ${e.message}</p>`;
  }
}

async function chargerCategories() {
  const nav = document.getElementById("categories");
  try {
    const res = await fetch(`${API_BASE_URL}/api/categories`);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();
    const cats = Array.isArray(data) ? data : data.items || [];
    nav.innerHTML = ['<button data-cat="">Tous</button>']
      .concat(cats.map((c) => `<button data-cat="${c.id}">${c.name}</button>`))
      .join(" ");
    nav.addEventListener("click", (e) => {
      const btn = e.target.closest("button[data-cat]");
      if (!btn) return;
      const cid = btn.getAttribute("data-cat");
      currentCategory = cid || null;
      currentPage = 1;
      currentSearch = '';
      chargerCatalogue(cid || undefined, 1);
    });
  } catch (e) {
    nav.innerHTML = `<p>Erreur catégories: ${e.message}</p>`;
  }
}

function initCart() {}

function checkAuth() {
  const token = localStorage.getItem("token");
  const user = localStorage.getItem("user");
  const headerRight = document.getElementById("header-right");

  if (token && user) {
    const userData = JSON.parse(user);
    headerRight.innerHTML = `
      <div class="user-menu">
        <button onclick="toggleUserMenu()" class="user-menu-btn">
          Bonjour ${userData.prenom} ${userData.nom}
          <span class="arrow">▼</span>
        </button>
        <div id="user-dropdown-menu" class="user-dropdown-content">
          <a href="page/profil.html">Mon Profil</a>
          <a href="page/parametres.html">Paramètres</a>
          ${
            userData.role === "admin"
              ? '<a href="page/dashboard.html">Dashboard</a>'
              : ""
          }
          <a href="#" onclick="logout()">Déconnexion</a>
        </div>
      </div>
      <div id="cart-counter" class="cart-counter clickable" onclick="window.location.href='page/panier.html'">0</div>
    `;
  }
}

function toggleUserMenu() {
  const menu = document.getElementById("user-dropdown-menu");
  const container = document.querySelector(".user-menu");
  const isOpen = menu.style.display === "block";

  document
    .querySelectorAll(".user-dropdown-content")
    .forEach((m) => (m.style.display = "none"));
  document
    .querySelectorAll(".user-menu")
    .forEach((u) => u.classList.remove("open"));

  if (!isOpen) {
    menu.style.display = "block";
    container.classList.add("open");
  }
}

document.addEventListener("click", (e) => {
  const menu = document.getElementById("user-dropdown-menu");
  const button = document.querySelector(".user-menu-btn");
  if (!menu || !button) return;

  if (!button.contains(e.target) && !menu.contains(e.target)) {
    menu.style.display = "none";
    document.querySelector(".user-menu")?.classList.remove("open");
  }
});

function logout() {
  localStorage.removeItem("token");
  localStorage.removeItem("user");
  location.reload();
}

window.logout = logout;

let currentPage = 1;
let currentCategory = null;
let currentSearch = '';

function initSearch() {
  const searchInput = document.getElementById('search-input');
  const searchBtn = document.getElementById('search-btn');
  
  if (searchInput && searchBtn) {
    searchBtn.addEventListener('click', performSearch);
    searchInput.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        performSearch();
      }
    });
  }
}

async function performSearch() {
  const searchInput = document.getElementById('search-input');
  const searchTerm = searchInput.value.trim();
  
  if (searchTerm) {
    currentSearch = searchTerm;
    currentCategory = null;
    currentPage = 1;
    await chargerCatalogue(null, 1, searchTerm);
  } else {
    currentSearch = '';
    currentPage = 1;
    await chargerCatalogue(currentCategory, 1);
  }
}

function initPagination() {
  const prevBtn = document.getElementById('prev-page');
  const nextBtn = document.getElementById('next-page');
  
  if (prevBtn && nextBtn) {
    prevBtn.addEventListener('click', () => {
      if (currentPage > 1) {
        currentPage--;
        chargerCatalogue(currentCategory, currentPage, currentSearch);
      }
    });
    
    nextBtn.addEventListener('click', () => {
      currentPage++;
      chargerCatalogue(currentCategory, currentPage, currentSearch);
    });
  }
}

function updatePagination(pagination) {
  const prevBtn = document.getElementById('prev-page');
  const nextBtn = document.getElementById('next-page');
  const pageInfo = document.getElementById('page-info');
  
  if (prevBtn) prevBtn.disabled = currentPage <= 1;
  if (nextBtn) nextBtn.disabled = currentPage >= pagination.total_pages;
  if (pageInfo) pageInfo.textContent = `Page ${currentPage} sur ${pagination.total_pages}`;
}

async function chargerCatalogue(categoryId, page = 1, search = '') {
  const div = document.getElementById("catalogue");
  try {
    let url;
    if (search) {
      url = `${API_BASE_URL}/api/outils/search?q=${encodeURIComponent(search)}&page=${page}&limit=48`;
    } else {
      url = `${API_BASE_URL}/api/outils/paginated?page=${page}&limit=48`;
      if (categoryId) {
        url += `&category_id=${categoryId}`;
      }
    }
    
    const res = await fetch(url);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();
    const outils = data.items || [];

    div.innerHTML = outils
      .map(
        (o) => `
      <a class="outil" href="page/detail.html?id=${o.id}">
        <img src="${o.image_url || 'https://via.placeholder.com/300x200?text=Outil'}" alt="${o.name}" />
        <h3>${o.name}</h3>
      </a>
    `
      )
      .join("");

    if (data.pagination) {
      updatePagination(data.pagination);
    }
  } catch (e) {
    div.innerHTML = `<p>Erreur de chargement: ${e.message}</p>`;
  }
}

chargerCategories();
chargerCatalogue(null, 1); // Charger la première page avec pagination
initCart();
checkAuth();
initSearch();
initPagination();
