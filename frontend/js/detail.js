async function chargerDetail() {
  const div = document.getElementById('detail');
  const id = new URLSearchParams(window.location.search).get('id');
  if (!id) {
    div.innerHTML = '<p>ID manquant</p>';
    return;
  }
  try {
    const res = await fetch(`${API_BASE_URL}/api/outils/${encodeURIComponent(id)}`);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const o = await res.json();
    const today = new Date().toISOString().split('T')[0];
    
    div.innerHTML = `
      <div class="space-y-4">
        <h1 class="text-2xl sm:text-3xl font-bold">${o.name}</h1>
        <div class="rounded-lg overflow-hidden bg-white border border-slate-200">
          <img src="${o.image_url || 'https://via.placeholder.com/800x400?text=Outil'}" alt="${o.name}" class="w-full max-h-[420px] object-contain bg-white" />
        </div>
        <div class="text-sm space-y-1">
          <p><span class="font-semibold">Catégorie:</span> ${o.category}</p>
          <p><span class="font-semibold">Marque:</span> ${o.brand ?? 'Non spécifiée'}</p>
          <p><span class="font-semibold">Exemplaires:</span> ${o.exemplaires}</p>
          <p><span class="font-semibold">Prix/jour:</span> ${o.price_per_day ? o.price_per_day + ' €' : 'N/A'}</p>
          <p>${o.description ?? ''}</p>
        </div>
      </div>
      <div class="sticky top-6 self-start">
        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
          <h3 class="text-lg font-semibold mb-3">Réserver cet outil</h3>
          <form id="reservationForm" class="space-y-3">
            <div>
              <label for="date" class="block text-sm mb-1">Date de location :</label>
              <input type="date" id="date" name="date" min="${today}" required class="w-full rounded-md border-slate-300">
            </div>
            <div>
              <label for="quantity" class="block text-sm mb-1">Quantité :</label>
              <input type="number" id="quantity" name="quantity" min="1" max="${o.exemplaires}" value="1" required class="w-full rounded-md border-slate-300">
            </div>
            <button type="submit" class="w-full inline-flex items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-white font-medium hover:bg-emerald-700 transition">Ajouter au panier</button>
          </form>
        </div>
      </div>
    `;

    document.getElementById('reservationForm').addEventListener('submit', (e) => {
      e.preventDefault();
      ajouterAuPanier(o, document.getElementById('date').value, parseInt(document.getElementById('quantity').value));
    });
    
  } catch (e) {
    div.innerHTML = `<p>Erreur: ${e.message}</p>`;
  }
}

function ajouterAuPanier(outil, date, quantite) {
  let panier = JSON.parse(localStorage.getItem('panier') || '[]');
  const existingIndex = panier.findIndex(item => 
    item.outil.id === outil.id && item.date === date
  );
  
  if (existingIndex >= 0) {
    panier[existingIndex].quantite += quantite;
  } else {
    panier.push({
      outil: outil,
      date: date,
      quantite: quantite,
      prixUnitaire: outil.price_per_day || 0
    });
  }
  
  localStorage.setItem('panier', JSON.stringify(panier));
  alert(`Ajouté au panier : ${quantite}x ${outil.name} pour le ${new Date(date).toLocaleDateString('fr-FR')}`);
  updateCartCounter();
}

chargerDetail();


