function chargerPanier() {
  const panier = JSON.parse(localStorage.getItem('panier') || '[]');
  const cartContent = document.getElementById('cart-content');
  
  if (panier.length === 0) {
    cartContent.innerHTML = `
      <div class="empty-cart">
        <h2>Panier vide</h2>
        <p>Ajouter des outils à votre panier</p>
        <a href="../index.html">Voir le catalogue</a>
      </div>
    `;
    return;
  }
  
  // Calculer le total
  const total = panier.reduce((sum, item) => sum + (item.prixUnitaire * item.quantite), 0);
  
  cartContent.innerHTML = `
    <div class="cart-items">
      ${panier.map((item, index) => `
        <div class="cart-item">
          <div class="item-info">
            <h3>${item.outil.name}</h3>
            <p><strong>Date:</strong> ${new Date(item.date).toLocaleDateString('fr-FR')}</p>
            <p><strong>Quantité:</strong> ${item.quantite}</p>
            <p><strong>Prix unitaire:</strong> ${item.prixUnitaire}€/jour</p>
            <p><strong>Sous-total:</strong> ${(item.prixUnitaire * item.quantite).toFixed(2)}€</p>
          </div>
          <button class="btn-remove" onclick="supprimerArticle(${index})">Supprimer</button>
        </div>
      `).join('')}
    </div>
    
    <div class="cart-summary">
      <div class="total">
        <h2>Total: ${total.toFixed(2)}€</h2>
      </div>
      <div class="cart-actions">
        <button class="btn-confirm" onclick="confirmerReservation()">Confirmer la réservation</button>
        <button class="btn-clear" onclick="viderPanier()">Vider le panier</button>
      </div>
    </div>
  `;
}

window.supprimerArticle = function(index) {
  let panier = JSON.parse(localStorage.getItem('panier') || '[]');
  
  if (index >= 0 && index < panier.length) {
    panier.splice(index, 1);
    localStorage.setItem('panier', JSON.stringify(panier));
    chargerPanier();
    if (typeof updateCartCounter === 'function') {
      updateCartCounter();
    }
  }
}

window.viderPanier = function() {
  if (confirm('Êtes-vous sûr de vouloir vider le panier ?')) {
    localStorage.removeItem('panier');
    chargerPanier();
    if (typeof updateCartCounter === 'function') {
      updateCartCounter();
    }
  }
}

window.confirmerReservation = async function() {
  const panier = JSON.parse(localStorage.getItem('panier') || '[]');
  const token = localStorage.getItem('token');
  
  if (!token) {
    alert('Vous devez être connecté pour confirmer une réservation');
    window.location.href = 'auth.html';
    return;
  }
  
  if (panier.length === 0) {
    alert('Votre panier est vide');
    return;
  }
  
  if (!confirm('Confirmer la réservation ? Cette action est définitive.')) {
    return;
  }
  
  try {
    // Préparer les données de réservation
    const reservationData = {
      items: panier.map(item => ({
        outil_id: item.outil.id,
        date: item.date,
        quantite: item.quantite
      }))
    };
    
    const response = await fetch('http://localhost:8080/api/reservations', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify(reservationData)
    });
    
    if (response.ok) {
      const result = await response.json();
      alert('Réservation confirmée avec succès !');
      localStorage.removeItem('panier');
      chargerPanier();
      if (typeof updateCartCounter === 'function') {
        updateCartCounter();
      }
    } else {
      const error = await response.json();
      alert(`Erreur: ${error.message || 'Impossible de confirmer la réservation'}`);
    }
  } catch (error) {
    alert('Erreur de connexion. Veuillez réessayer.');
    console.error('Erreur:', error);
  }
}

chargerPanier();