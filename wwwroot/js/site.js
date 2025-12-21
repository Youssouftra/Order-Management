document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
});

function updateCartCount() {
    fetch('/Cart/GetCartCount')
        .then(response => response.json())
        .then(count => {
            var badge = document.getElementById('cart-count');
            if (badge) {
                badge.textContent = count;
            }
        })
        .catch(error => console.log('Error:', error));
}
