async function chargerCatalogue() {
  const res = await fetch("http://localhost:8080/api/outils");
  const outils = await res.json();

  const div = document.getElementById("catalogue");
  div.innerHTML = outils.map(o => `
    <div class="outil">
      <h3>${o.name}</h3>
      <p>Catégorie : ${o.category}</p>
      <p>Marque : ${o.brand ?? "Non spécifiée"}</p>
      <p>Exemplaires : ${o.exemplaires}</p>
    </div>
  `).join("");
}

chargerCatalogue();
