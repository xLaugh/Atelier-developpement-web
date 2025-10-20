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
  
  cartContent.innerHTML = panier.map((item, index) => `
    <div>
      <h3>${item.outil.name}</h3>
      <p>Date: ${new Date(item.date).toLocaleDateString('fr-FR')}</p>
      <p>Quantité: ${item.quantite}</p>
      <p>Prix: ${item.prixUnitaire}€/jour</p>
      <button onclick="supprimerArticle(${index})">Supprimer</button>
    </div>
  `).join('');
}

window.supprimerArticle = function(index) {
  let panier = JSON.parse(localStorage.getItem('panier') || '[]');
  
  if (index >= 0 && index < panier.length) {
    panier.splice(index, 1);
    localStorage.setItem('panier', JSON.stringify(panier));
    chargerPanier();
    updateCartCounter();
  }
}

function updateCartCounter() {
  const panier = JSON.parse(localStorage.getItem('panier') || '[]');
  const totalItems = panier.reduce((sum, item) => sum + item.quantite, 0);
  
  let cartCounter = document.getElementById('cart-counter');
  if (cartCounter) {
    cartCounter.textContent = totalItems;
    cartCounter.style.display = totalItems > 0 ? 'inline' : 'none';
  }
}

chargerPanier();