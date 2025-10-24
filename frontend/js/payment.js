document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("paymentForm");
  const message = document.getElementById("formMessages");
  const cancelBtn = document.getElementById("cancelBtn");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    message.textContent = "Traitement du paiement...";

    // Récupérer les données du formulaire
    const formData = Object.fromEntries(new FormData(form).entries());

    // Génération d'un token factice (simulation de paiement)
    const paymentToken = "tok_" + Math.floor(Math.random() * 1000000);

    // Récupère le panier depuis le localStorage
    const panier = JSON.parse(localStorage.getItem("panier") || "[]");

    const reservationData = {
      items: panier.map((item) => ({
        outil_id: item.outil.id,
        start_date: item.startDate || item.date,
        end_date: item.endDate || item.date,
        quantite: item.quantite,
        duration: item.duration || 1,
        prix_total: item.prixTotal || item.prixUnitaire * item.quantite,
      })),
      payment_token: paymentToken,
    };

    // Récupération du token JWT si utilisé pour l'authentification
    const tokenJWT = localStorage.getItem("jwt") || "";

    try {
      const res = await fetch(`${API_BASE_URL}/api/reservations/period`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: "Bearer " + tokenJWT,
        },
        body: JSON.stringify(reservationData),
      });

      const data = await res.json();

      if (res.ok) {
        // ✅ Succès
        alert("✅ Paiement réussi ! Votre réservation a bien été enregistrée.");
        localStorage.removeItem("panier"); // on vide le panier
        window.location.href = "../index.html"; // redirection vers l'accueil
      } else {
        // ❌ Erreur renvoyée par le serveur
        alert("❌ Erreur : " + (data.message || data.error || "Paiement refusé."));
        message.innerHTML = `<p class="error">Erreur : ${data.message || data.error}</p>`;
      }
    } catch (err) {
      console.error(err);
      alert("⚠️ Erreur de communication avec le serveur : " + err.message);
      message.innerHTML = `<p class="error">Erreur : ${err.message}</p>`;
    }
  });

  cancelBtn.addEventListener("click", () => {
    form.reset();
    message.textContent = "";
  });
});
