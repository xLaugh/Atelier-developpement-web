async function chargerCatalogue(categoryId) {
  const div = document.getElementById("catalogue");
  try {
    const url = categoryId
      ? `${API_BASE_URL}/api/outils?category_id=${encodeURIComponent(categoryId)}`
      : `${API_BASE_URL}/api/outils`;
    const res = await fetch(url);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();
    const outils = Array.isArray(data) ? data : data.items || [];

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
      chargerCatalogue(cid || undefined);
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

chargerCategories();
chargerCatalogue();
initCart();
checkAuth();
