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
  chargerOutils();
  afficherCategories();
  afficherModels();
  afficherOutils();
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

async function chargerOutils() {
  try {
    const response = await fetch(`${API_BASE_URL}/api/outils`);
    const payload = await response.json();
    const outils = Array.isArray(payload) ? payload : (payload.items || []);
    return outils;
  } catch (error) {
    console.error('Erreur lors du chargement des outils:', error);
    return [];
  }
}

async function afficherCategories() {
  try {
    const response = await fetch(`${API_BASE_URL}/api/categories`);
    const payload = await response.json();
    const categories = Array.isArray(payload) ? payload : (payload.items || []);
    
    const container = document.getElementById('categories-list');
    container.innerHTML = '';
    
    categories.forEach(category => {
      const div = document.createElement('div');
      div.className = 'admin-item';
      div.innerHTML = `
        <div class="item-info">
          <strong>${category.name}</strong>
          <span>ID: ${category.id}</span>
        </div>
        <div class="item-actions">
          <button onclick="editerCategorie(${category.id}, '${category.name}')">Modifier</button>
        </div>
      `;
      container.appendChild(div);
    });
  } catch (error) {
    console.error('Erreur lors du chargement des catégories:', error);
    document.getElementById('categories-list').innerHTML = '<p>Erreur lors du chargement</p>';
  }
}

async function afficherModels() {
  try {
    const response = await fetch(`${API_BASE_URL}/api/models`);
    const payload = await response.json();
    const models = Array.isArray(payload) ? payload : (payload.items || []);
    
    const container = document.getElementById('models-list');
    container.innerHTML = '';
    
    models.forEach(model => {
      const div = document.createElement('div');
      div.className = 'admin-item';
      div.innerHTML = `
        <div class="item-info">
          <strong>${model.name}</strong>
          <span>ID: ${model.id}</span>
        </div>
        <div class="item-actions">
          <button onclick="editerModel(${model.id}, '${model.name}')">Modifier</button>
        </div>
      `;
      container.appendChild(div);
    });
  } catch (error) {
    console.error('Erreur lors du chargement des modèles:', error);
    document.getElementById('models-list').innerHTML = '<p>Erreur lors du chargement</p>';
  }
}

async function afficherOutils() {
  try {
    const outils = await chargerOutils();
    
    const container = document.getElementById('outils-list');
    container.innerHTML = '';
    
    outils.forEach(outil => {
      const div = document.createElement('div');
      div.className = 'admin-item';
      div.innerHTML = `
        <div class="item-info">
          <strong>${outil.name}</strong>
          <span>ID: ${outil.id}</span>
          <span>Catégorie: ${outil.category_id}</span>
        </div>
        <div class="item-actions">
          <button onclick="editerOutil(${outil.id}, '${outil.name}', '${outil.description}', ${outil.category_id}, ${outil.model_id})">Modifier</button>
        </div>
      `;
      container.appendChild(div);
    });
  } catch (error) {
    console.error('Erreur lors du chargement des outils:', error);
    document.getElementById('outils-list').innerHTML = '<p>Erreur lors du chargement</p>';
  }
}

function editerCategorie(id, name) {
  const nouveauNom = prompt('Nouveau nom de la catégorie:', name);
  if (nouveauNom && nouveauNom !== name) {
    modifierCategorie(id, nouveauNom);
  }
}

function editerModel(id, name) {
  const nouveauNom = prompt('Nouveau nom du modèle:', name);
  if (nouveauNom && nouveauNom !== name) {
    modifierModel(id, nouveauNom);
  }
}

function editerOutil(id, name, description, categoryId, modelId) {
  const nouveauNom = prompt('Nouveau nom de l\'outil:', name);
  if (nouveauNom && nouveauNom !== name) {
    const nouvelleDescription = prompt('Nouvelle description:', description);
    if (nouvelleDescription && nouvelleDescription !== description) {
      modifierOutil(id, nouveauNom, nouvelleDescription, categoryId, modelId);
    }
  }
}

async function modifierCategorie(id, name) {
  try {
    const response = await fetch(`${API_BASE_URL}/api/admin/categories/${id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name })
    });
    const result = await response.json();
    
    if (result.success) {
      alert('Catégorie modifiée avec succès');
      afficherCategories();
      chargerCategories(); // Recharger les selects
    } else {
      alert(result.message || 'Erreur lors de la modification');
    }
  } catch (error) {
    alert('Erreur lors de la modification de la catégorie');
  }
}

async function modifierModel(id, name) {
  try {
    const response = await fetch(`${API_BASE_URL}/api/admin/models/${id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name })
    });
    const result = await response.json();
    
    if (result.success) {
      alert('Modèle modifié avec succès');
      afficherModels();
      chargerModels(); // Recharger les selects
    } else {
      alert(result.message || 'Erreur lors de la modification');
    }
  } catch (error) {
    alert('Erreur lors de la modification du modèle');
  }
}

async function modifierOutil(id, name, description, categoryId, modelId) {
  try {
    const response = await fetch(`${API_BASE_URL}/api/admin/outils/${id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name, description, category_id: categoryId, model_id: modelId })
    });
    const result = await response.json();
    
    if (result.success) {
      alert('Outil modifié avec succès');
      afficherOutils();
    } else {
      alert(result.message || 'Erreur lors de la modification');
    }
  } catch (error) {
    alert('Erreur lors de la modification de l\'outil');
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
