function getParam(name) {
  const url = new URL(location.href);
  return url.searchParams.get(name);
}

async function chargerDetail() {
  const div = document.getElementById('detail');
  const id = getParam('id');
  if (!id) {
    div.innerHTML = '<p>ID manquant</p>';
    return;
  }
  try {
    const res = await fetch(`http://localhost:8080/api/outils/${encodeURIComponent(id)}`);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const o = await res.json();
    div.innerHTML = `
      <h1>${o.name}</h1>
      <img src="${o.image_url || 'https://via.placeholder.com/800x400?text=Outil'}" alt="${o.name}">
      <p><strong>Catégorie:</strong> ${o.category}</p>
      <p><strong>Marque:</strong> ${o.brand ?? 'Non spécifiée'}</p>
      <p><strong>Prix/jour:</strong> ${o.price_per_day ? o.price_per_day + ' €' : 'N/A'}</p>
      <p>${o.description ?? ''}</p>
    `;
  } catch (e) {
    div.innerHTML = `<p>Erreur: ${e.message}</p>`;
  }
}

chargerDetail();


