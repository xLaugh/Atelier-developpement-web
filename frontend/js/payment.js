document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("paymentForm");
  const message = document.getElementById("formMessages");
  const cancelBtn = document.getElementById("cancelBtn");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    message.textContent = "Traitement du paiement...";

    try {
      // Récupérer les données du formulaire
      const formData = Object.fromEntries(new FormData(form).entries());

      // Récupère le panier depuis le localStorage pour calculer le montant
      const panier = JSON.parse(localStorage.getItem("panier") || "[]");
      const totalAmount = panier.reduce((total, item) => {
        return total + (item.prixTotal || item.prixUnitaire * item.quantite);
      }, 0);

      // Étape 1: Traitement du paiement via l'architecture
      const paymentData = {
        cardholder: formData.cardholder,
        cardNumber: formData.cardNumber,
        expiry: formData.expiry,
        cvc: formData.cvc,
        amount: totalAmount
      };

      const paymentRes = await fetch(`${API_BASE_URL}/api/payment/process`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(paymentData),
      });

      const paymentResult = await paymentRes.json();

      if (!paymentRes.ok) {
        throw new Error(paymentResult.message || "Erreur de paiement");
      }

      // Étape 2: Si paiement réussi, créer la réservation
      const reservationData = {
        items: panier.map((item) => ({
          outil_id: item.outil.id,
          start_date: item.startDate || item.date,
          end_date: item.endDate || item.date,
          quantite: item.quantite,
          duration: item.duration || 1,
          prix_total: item.prixTotal || item.prixUnitaire * item.quantite,
        })),
        payment_token: paymentResult.token,
      };

      // Récupération du token JWT si utilisé pour l'authentification
      const tokenJWT = localStorage.getItem("jwt") || "";

      const reservationRes = await fetch(`${API_BASE_URL}/api/reservations/period`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: "Bearer " + tokenJWT,
        },
        body: JSON.stringify(reservationData),
      });

      const reservationResult = await reservationRes.json();

      if (reservationRes.ok) {
        alert("✅ Paiement réussi ! Votre réservation a bien été enregistrée.");
        localStorage.removeItem("panier"); // on vide le panier
        window.location.href = "../index.html"; // redirection vers l'accueil
      } else {
        alert("❌ Paiement validé mais erreur lors de la réservation : " + (reservationResult.message || reservationResult.error));
        message.innerHTML = `<p class="error">Erreur réservation : ${reservationResult.message || reservationResult.error}</p>`;
      }
    } catch (err) {
      console.error(err);
      alert("⚠️ Erreur : " + err.message);
      message.innerHTML = `<p class="error">Erreur : ${err.message}</p>`;
    }
  });

  cancelBtn.addEventListener("click", () => {
    form.reset();
    message.textContent = "";
  });
});
