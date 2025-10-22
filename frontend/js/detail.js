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
      <div class="detail-container">
        <div class="detail-info">
          <h1>${o.name}</h1>
          <img src="${o.image_url || 'https://via.placeholder.com/800x400?text=Outil'}" alt="${o.name}">
          <p><strong>Catégorie:</strong> ${o.category ?? 'N/A'}</p>
          <p><strong>Marque:</strong> ${o.brand ?? 'Non spécifiée'}</p>
          <p><strong>Exemplaires disponibles:</strong> ${o.exemplaires}</p>
          <p><strong>Prix/jour:</strong> ${o.price_per_day ? o.price_per_day + ' €' : 'N/A'}</p>
          <p>${o.description ?? ''}</p>
        </div>
        
        <div class="reservation-form">
          <h3>Réserver cet outil</h3>
          <form id="reservationForm">
            <div class="form-group">
              <label for="date">Date de location :</label>
              <input type="date" id="date" name="date" min="${today}" required>
            </div>
            <div class="form-group">
              <label for="quantity">Quantité :</label>
              <input type="number" id="quantity" name="quantity" min="1" max="${o.exemplaires}" value="1" required>
            </div>
            <button type="submit" class="btn-add-cart">Ajouter au panier</button>
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

// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', chargerDetail);


