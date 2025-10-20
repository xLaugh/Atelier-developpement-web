async function chargerCatalogue(categoryId) {
  const div = document.getElementById("catalogue");
  try {
    const url = categoryId ? `http://localhost:8080/api/outils?category_id=${encodeURIComponent(categoryId)}` : `http://localhost:8080/api/outils`;
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
    const res = await fetch('http://localhost:8080/api/categories');
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

chargerCategories();
chargerCatalogue();
