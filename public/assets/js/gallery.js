document.addEventListener('DOMContentLoaded', function() {
    console.log('Script chargé'); // Pour débugger

    const likeForms = document.querySelectorAll('.like-form');
    console.log('Nombre de formulaires trouvés:', likeForms.length); // Pour débugger

    likeForms.forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            console.log('Formulaire soumis'); // Pour débugger

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                console.log('Réponse reçue:', data); // Pour débugger

                if (data.success) {
                    const likesCountElement = this.closest('.gallery-item-footer').querySelector('.likes-count');
                    const likeButton = this.querySelector('.btn-like');

                    likesCountElement.textContent = data.likes;
                    likeButton.classList.toggle('liked');

                    const heartIcon = likeButton.querySelector('i');
                    heartIcon.style.animation = 'none';
                    heartIcon.offsetHeight;
                    heartIcon.style.animation = 'likeEffect 0.4s ease';
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        });
    });
});
