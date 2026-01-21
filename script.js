// VelSHE JS

//  scrolling link
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});

//  validation helper
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        const inputs = form.querySelectorAll('input[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.style.borderColor = 'red';
            } else {
                input.style.borderColor = '';
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields');
        }
    });
}

// Image - loading
if ('loading' in HTMLImageElement.prototype) {
    const images = document.querySelectorAll('img[loading="lazy"]');
    images.forEach(img => {
        img.src = img.dataset.src;
    });
} else {
    // FBack brows no support lazy loading
    const script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js';
    document.body.appendChild(script);
}

// Add  cart anima
function addToCartAnimation(button) {
    button.textContent = 'Added!';
    button.style.backgroundColor = '#28a745';
    
    setTimeout(() => {
        button.textContent = 'Add to Cart';
        button.style.backgroundColor = '';
    }, 2000);
}

// Prod img zoom -detail page
document.querySelectorAll('.product-detail-image').forEach(img => {
    img.addEventListener('mouseenter', function() {
        this.style.transform = 'scale(1.05)';
        this.style.transition = 'transform 0.3s ease';
    });
    
    img.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1)';
    });
});

// Auto-hide suss mgs
setTimeout(() => {
    const messages = document.querySelectorAll('.success-message, .error-message');
    messages.forEach(msg => {
        msg.style.transition = 'opacity 0.5s';
        msg.style.opacity = '0';
        setTimeout(() => msg.remove(), 500);
    });
}, 5000);

// Mobile menu 
function toggleMobileMenu() {
    const nav = document.querySelector('.main-nav');
    if (nav) {
        nav.classList.toggle('mobile-active');
    }
}

// Search functionality
function searchProducts() {
    const searchInput = document.getElementById('search-input');
    if (!searchInput) return;
    
    const searchTerm = searchInput.value.toLowerCase();
    const products = document.querySelectorAll('.product-card');
    
    products.forEach(product => {
        const productName = product.querySelector('.product-name').textContent.toLowerCase();
        if (productName.includes(searchTerm)) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
}

// Price filter
function updatePriceRange() {
    const minPrice = document.getElementById('min-price');
    const maxPrice = document.getElementById('max-price');
    
    if (minPrice && maxPrice) {
        document.getElementById('min-value').textContent = '$' + minPrice.value;
        document.getElementById('max-value').textContent = '$' + maxPrice.value;
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    console.log('VelvetVogueSHE loaded successfully');
    
    // add any initialization 
    validateForm('checkout-form');
    validateForm('login-form');
});