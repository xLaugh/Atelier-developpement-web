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
              <label for="startDate">Date de début :</label>
              <input type="date" id="startDate" name="startDate" min="${today}" required>
            </div>
            <div class="form-group">
              <label for="endDate">Date de fin :</label>
              <input type="date" id="endDate" name="endDate" min="${today}" required>
            </div>
            <div class="form-group">
              <label for="quantity">Quantité (Stock disponible: ${o.exemplaires}) :</label>
              <input type="number" id="quantity" name="quantity" min="1" max="${o.exemplaires}" value="1" required>
              <small class="text-muted">Vous pouvez réserver jusqu'à ${o.exemplaires} exemplaire${o.exemplaires > 1 ? 's' : ''}</small>
            </div>
            <div class="form-group">
              <label for="duration">Durée (jours) :</label>
              <input type="number" id="duration" readonly>
            </div>
            <div class="form-group">
              <label for="totalPrice">Prix total :</label>
              <input type="text" id="totalPrice" readonly>
            </div>
            <div class="form-group">
              <div id="availability-status"></div>
            </div>
            <button type="submit" class="btn-add-cart">Ajouter au panier</button>
          </form>
        </div>
      </div>
    `;

    async function checkAvailability() {
      const startDate = document.getElementById('startDate').value;
      const endDate = document.getElementById('endDate').value;
      const quantity = parseInt(document.getElementById('quantity').value) || 1;
      
      if (!startDate || !endDate) return;
      
      try {
        const response = await fetch(`${API_BASE_URL}/api/availability?model_id=${o.id}&start_date=${startDate}&end_date=${endDate}&quantity=${quantity}`);
        const data = await response.json();
        
        if (data.available) {
          document.getElementById('quantity').style.borderColor = '';
          document.getElementById('availability-status').innerHTML = `<span style="color: green;">✓ Disponible (${data.available_for_period} exemplaires libres)</span>`;
        } else {
          document.getElementById('quantity').style.borderColor = 'red';
          document.getElementById('availability-status').innerHTML = `<span style="color: red;">✗ Non disponible (${data.available_for_period} exemplaires libres, ${quantity} demandés)</span>`;
        }
      } catch (error) {
        console.error('Erreur lors de la vérification de disponibilité:', error);
      }
    }

    function updateDurationAndPrice() {
      const startDate = document.getElementById('startDate').value;
      const endDate = document.getElementById('endDate').value;
      const quantity = parseInt(document.getElementById('quantity').value) || 1;
      if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        const duration = Math.max(1, Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1);
        document.getElementById('duration').value = duration;
        if (o.price_per_day) {
          const totalPrice = duration * quantity * o.price_per_day;
          document.getElementById('totalPrice').value = totalPrice.toFixed(2) + ' €';
        }
        
        // Vérifier la disponibilité
        checkAvailability();
      }
    }
    document.getElementById('startDate').addEventListener('change', updateDurationAndPrice);
    document.getElementById('endDate').addEventListener('change', updateDurationAndPrice);
    document.getElementById('quantity').addEventListener('input', updateDurationAndPrice);

    document.getElementById('reservationForm').addEventListener('submit', (e) => {
      e.preventDefault();
      const startDate = document.getElementById('startDate').value;
      const endDate = document.getElementById('endDate').value;
      const quantity = parseInt(document.getElementById('quantity').value);
      
      if (!startDate || !endDate) { 
        alert('Veuillez sélectionner les dates'); 
        return; 
      }
      
      if (new Date(endDate) < new Date(startDate)) { 
        alert('La date de fin doit être postérieure à la date de début'); 
        return; 
      }
      
      if (quantity > o.exemplaires) {
        alert(`Quantité demandée (${quantity}) supérieure au stock disponible (${o.exemplaires})`);
        return;
      }
      
      if (quantity < 1) {
        alert('La quantité doit être d\'au moins 1');
        return;
      }
      
      ajouterAuPanier(o, startDate, endDate, quantity);
    });
    
  } catch (e) {
    div.innerHTML = `<p>Erreur: ${e.message}</p>`;
  }
}

function ajouterAuPanier(outil, startDate, endDate, quantite) {
  let panier = JSON.parse(localStorage.getItem('panier') || '[]');
  const start = new Date(startDate);
  const end = new Date(endDate);
  const duration = Math.max(1, Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1);
  const prixUnitaire = outil.price_per_day || 0;
  const prixTotal = duration * quantite * prixUnitaire;
  const existingIndex = panier.findIndex(item => item.outil.id === outil.id && item.startDate === startDate && item.endDate === endDate);
  if (existingIndex >= 0) {
    panier[existingIndex].quantite += quantite;
    panier[existingIndex].prixTotal = panier[existingIndex].duration * panier[existingIndex].quantite * prixUnitaire;
  } else {
    panier.push({ outil: outil, startDate, endDate, quantite, duration, prixUnitaire, prixTotal });
  }
  localStorage.setItem('panier', JSON.stringify(panier));
  alert(`Ajouté au panier : ${quantite}x ${outil.name} du ${new Date(startDate).toLocaleDateString('fr-FR')} au ${new Date(endDate).toLocaleDateString('fr-FR')} (${duration} jour${duration>1?'s':''})`);
  updateCartCounter();
}
chargerDetail();


