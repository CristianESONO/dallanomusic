document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mise à jour du compteur du panier
                    document.querySelector('.shop-button span').textContent = data.totalItems;
                    
                    // Feedback visuel
                    const button = this.querySelector('.add-to-cart-btn');
                    button.classList.add('added');
                    setTimeout(() => button.classList.remove('added'), 1000);
                }
            })
            .catch(error => console.error('Erreur:', error));
        });
    });

    // Gestion de la diminution de quantité
    document.querySelectorAll('.decrease-quantity').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            fetch(this.href, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mise à jour du compteur global du panier
                    document.querySelector('.shop-button span').textContent = data.totalItems;
                    
                    // Récupérer la ligne du produit
                    const productRow = this.closest('tr');
                    const quantityDisplay = productRow.querySelector('.quantity-display');
                    const currentQuantity = parseInt(quantityDisplay.textContent);
                    
                    if (currentQuantity > 1) {
                        // Mettre à jour l'affichage de la quantité
                        quantityDisplay.textContent = currentQuantity - 1;
                        
                        // Mettre à jour le prix total de la ligne
                        const unitPrice = parseFloat(productRow.querySelector('td:nth-child(3)').textContent);
                        const totalCell = productRow.querySelector('td:nth-child(5)');
                        totalCell.textContent = `${(unitPrice * (currentQuantity - 1)).toFixed(2)} FCFA`;
                    } else {
                        // Supprimer la ligne si la quantité atteint 0
                        productRow.remove();
                        
                        // Si c'était le dernier produit, afficher le message "panier vide"
                        if (document.querySelectorAll('tbody tr').length === 0) {
                            location.reload();
                        }
                    }
                    
                    // Mettre à jour le total général
                    updateCartTotal();
                }
            })
            .catch(error => console.error('Erreur:', error));
        });
    });
});

// Fonction pour mettre à jour le total du panier
function updateCartTotal() {
    const rows = document.querySelectorAll('tbody tr');
    let total = 0;
    
    rows.forEach(row => {
        const price = parseFloat(row.querySelector('td:nth-child(5)').textContent);
        total += price;
    });
    
    const totalElement = document.querySelector('h4 strong');
    if (totalElement) {
        totalElement.nextSibling.textContent = ` ${total.toFixed(2)} FCFA`;
    }
}
