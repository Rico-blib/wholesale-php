// js/main.js

document.addEventListener("DOMContentLoaded", () => {
    // Add to wishlist button
    const addToWishlistBtns = document.querySelectorAll(".add-to-wishlist");
    addToWishlistBtns.forEach(btn => {
        btn.addEventListener("click", function(event) {
            event.preventDefault();
            const productId = this.dataset.productId;
            
            // AJAX request to add item to wishlist
            fetch('ajax_handler.php', {
                method: 'POST',
                body: JSON.stringify({
                    action: 'add_to_wishlist',
                    product_id: productId
                }),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product added to wishlist!');
                } else {
                    alert(data.error || 'Something went wrong.');
                }
            });
        });
    });

    // Remove from wishlist button
    const removeFromWishlistBtns = document.querySelectorAll(".remove-from-wishlist");
    removeFromWishlistBtns.forEach(btn => {
        btn.addEventListener("click", function(event) {
            event.preventDefault();
            const productId = this.dataset.productId;
            
            // AJAX request to remove item from wishlist
            fetch('../wishlist/removewishlist.php', {
                method: 'POST',
                body: JSON.stringify({
                    product_id: productId
                }),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Show success message and remove item from DOM
                    alert(data.message);
                    this.closest('.card').remove();  // Remove the wishlist item from DOM
                } else {
                    alert(data.message || 'Something went wrong.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to remove product from wishlist.');
            });
        });
    });
});
