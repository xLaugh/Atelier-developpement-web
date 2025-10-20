async function chargerCatalogue() {
  const div = document.getElementById("catalogue");
  try {
    const res = await fetch("http://localhost:8080/api/outils");
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();
    const outils = Array.isArray(data) ? data : (data.items || []);

    div.innerHTML = outils.map(o => `
      <div class="outil">
        <h3>${o.name}</h3>
        <p>Catégorie : ${o.category}</p>
        <p>Marque : ${o.brand ?? "Non spécifiée"}</p>
        <p>Exemplaires : ${o.exemplaires}</p>
      </div>
    `).join("");
  } catch (e) {
    div.innerHTML = `<p>Erreur de chargement: ${e.message}</p>`;
  }
}

chargerCatalogue();
