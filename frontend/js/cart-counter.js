function updateCartCounter() {
  const panier = JSON.parse(localStorage.getItem('panier') || '[]');
  const totalItems = panier.reduce((sum, item) => sum + item.quantite, 0);
  
  let cartCounter = document.getElementById('cart-counter');
  if (!cartCounter) {
    cartCounter = document.createElement('div');
    cartCounter.id = 'cart-counter';
    cartCounter.className = 'cart-counter';
    document.body.appendChild(cartCounter);
  }
  
  cartCounter.textContent = totalItems;
  cartCounter.className = totalItems > 0 ? 'cart-counter clickable' : 'cart-counter hidden';
  
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
