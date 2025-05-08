<footer class="bg-success text-white text-center py-4 mt-5">
    <p class="mb-0">&copy; <?= date('Y'); ?> Grains Wholesale. All rights reserved.</p>
</footer>

<!-- ✅ Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/ecommerce/js/main.js"></script>
<script>
    // Wishlist script (same as before)
    document.querySelectorAll('.wishlist-btn').forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.getAttribute('data-product-id');
            const action = this.getAttribute('data-action');

            fetch('index.php', {
                method: 'POST',
                body: new URLSearchParams({
                    action: action,
                    product_id: productId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('wishlistToastBody').textContent = 
                        action === 'add' ? 'Added to Wishlist' : 'Removed from Wishlist';
                    let toast = new bootstrap.Toast(document.getElementById('wishlistToast'));
                    toast.show();

                    this.querySelector('i').classList.toggle('bi-heart', action === 'remove');
                    this.querySelector('i').classList.toggle('bi-heart-fill', action === 'add');
                    this.setAttribute('data-action', action === 'add' ? 'remove' : 'add');
                }
            });
        });
    });
</script>
