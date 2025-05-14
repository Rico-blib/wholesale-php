<footer class="bg-success text-white text-center py-4 mt-5">
    <p class="mb-0">&copy; <?= date('Y'); ?> Grains Wholesale. All rights reserved.</p>
</footer>
<!-- 🔵 New Contact & Social Section (Dark Background) -->
<section class="bg-dark text-white py-4">
    <div class="container">
        <div class="row">
            <!-- Contact Info -->
            <div class="col-md-4 mb-3">
                <h5>Contact Us</h5>
                <p><i class="fas fa-map-marker-alt me-2"></i> Nairobi, Kenya</p>
                <p><i class="fas fa-phone me-2"></i> +254 712 345678</p>
                <p><i class="fas fa-envelope me-2"></i> support@sekowholesale.com</p>
                <a href="/ecommerce/contact.php" class="text-white">Contact Us</a>
            </div>

            <!-- Useful Links -->
            <div class="col-md-4 mb-3">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="/ecommerce/index.php" class="text-white">Home</a></li>
                    <li><a href="/ecommerce/products/category.php" class="text-white">Shop</a></li>
                    <li><a href="/ecommerce/auth/login.php" class="text-white">Login</a></li>
                    <li><a href="/ecommerce/auth/register.php" class="text-white">Register</a></li>
                </ul>
            </div>

            <!-- Social Media -->
            <div class="col-md-4 mb-3">
                <h5>Follow Us</h5>
                <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
    </div>
</section>

<!-- ✅ Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/ecommerce/js/main.js"></script>
<script>
    // Wishlist script (same as before)
    document.querySelectorAll('.wishlist-btn').forEach(button => {
        button.addEventListener('click', function() {
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
<!-- Font Awesome for social icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
