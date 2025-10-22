// Chargement du dashboard
function chargerDashboard() {
  const token = localStorage.getItem('token');
  const user = localStorage.getItem('user');
  
  if (!token || !user) {
    document.getElementById('dashboard-content').innerHTML = '<p>Vous devez être connecté.</p>';
    return;
  }

  const userData = JSON.parse(user);
  if (userData.role !== 'admin') {
    document.getElementById('dashboard-content').innerHTML = '<p>Accès refusé. Droits administrateur requis.</p>';
    return;
  }

  document.getElementById('total-users').textContent = '12';
  document.getElementById('total-outils').textContent = '24';
  document.getElementById('total-reservations').textContent = '8';
  
  chargerCategories();
  chargerModels();
}

async function chargerCategories() {
  try {
    const response = await fetch(`${API_BASE_URL}/api/categories`);
    const payload = await response.json();
    const categories = Array.isArray(payload) ? payload : (payload.items || []);
    
    const select = document.getElementById('category-select');
    select.innerHTML = '<option value="">Sélectionner une catégorie</option>';
    categories.forEach(category => {
      const option = document.createElement('option');
      option.value = category.id;
      option.textContent = category.name;
      select.appendChild(option);
    });
  } catch (error) {
    console.error('Erreur lors du chargement des catégories:', error);
  }
}

async function chargerModels() {
  try {
    const response = await fetch(`${API_BASE_URL}/api/models`);
    const payload = await response.json();
    const models = Array.isArray(payload) ? payload : (payload.items || []);
    
    const select = document.getElementById('model-select');
    select.innerHTML = '<option value="">Sélectionner un modèle</option>';
    models.forEach(model => {
      const option = document.createElement('option');
      option.value = model.id;
      option.textContent = model.name;
      select.appendChild(option);
    });
  } catch (error) {
    console.error('Erreur lors du chargement des modèles:', error);
  }
}

function bindForms() {
  document.getElementById('create-category-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    
    try {
      const response = await fetch(`${API_BASE_URL}/api/admin/categories`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });
      const result = await response.json();
      
      if (result.success) {
        alert('Catégorie créée avec succès');
        e.target.reset();
        chargerCategories();
      } else {
        alert(result.message || 'Erreur lors de la création');
      }
    } catch (error) {
      alert('Erreur lors de la création de la catégorie');
    }
  });

  document.getElementById('create-model-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    
    try {
      const response = await fetch(`${API_BASE_URL}/api/admin/models`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });
      const result = await response.json();
      
      if (result.success) {
        alert('Modèle créé avec succès');
        e.target.reset();
        chargerModels();
      } else {
        alert(result.message || 'Erreur lors de la création');
      }
    } catch (error) {
      alert('Erreur lors de la création du modèle');
    }
  });

  document.getElementById('create-outil-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    
    try {
      const response = await fetch(`${API_BASE_URL}/api/admin/outils`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });
      const result = await response.json();
      
      if (result.success) {
        alert('Outil créé avec succès');
        e.target.reset();
      } else {
        alert(result.message || 'Erreur lors de la création');
      }
    } catch (error) {
      alert('Erreur lors de la création de l\'outil');
    }
  });
}

// Bootstrap
document.addEventListener('DOMContentLoaded', function () {
  chargerDashboard();
  bindForms();
});
