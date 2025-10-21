function updateCartCounter() {
  const panier = JSON.parse(localStorage.getItem('panier') || '[]');
  const totalItems = panier.reduce((sum, item) => sum + item.quantite, 0);
  
  // Creation du panier 
  let cartCounter = document.getElementById('cart-counter');
  if (!cartCounter) {
    cartCounter = document.createElement('div');
    cartCounter.id = 'cart-counter';
    cartCounter.className = 'cart-counter';
    document.body.appendChild(cartCounter);
  }
  
  // Afficher "0" quand le panier est vide, sinon affiche n nombre n outils 
  if (totalItems === 0) {
    cartCounter.textContent = '0';
    cartCounter.className = 'cart-counter';
  } else {
    cartCounter.textContent = `${totalItems}` ;
    cartCounter.className = 'cart-counter clickable';
  }
  
  // possible d'aller sur le panier
  cartCounter.onclick = () => {
    const currentPath = window.location.pathname;
    if (currentPath.includes('/page/')) {
      window.location.href = 'panier.html';
    } else {
      window.location.href = 'page/panier.html';
    }
  };
}

document.addEventListener('DOMContentLoaded', updateCartCounter);
window.addEventListener('storage', updateCartCounter);
try { updateCartCounter(); } catch (_) {}
