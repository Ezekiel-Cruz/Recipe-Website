/**
 * Pagination JavaScript for Recipe Website
 * Enhanced for better performance and UX
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize pagination if the container exists
    const paginationContainer = document.querySelector('.pagination-container');
    if (paginationContainer) {
        initPagination();
        initAnimations();
    }
});

/**
 * Initialize pagination functionality
 */
function initPagination() {
    // Get current URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const currentPage = parseInt(urlParams.get('page')) || 1;
    
    // Get pagination links
    const paginationLinks = document.querySelectorAll('.pagination a');
    
    paginationLinks.forEach(link => {
        // Fix previous and next button links if they don't have proper data-page attributes
        if (link.closest('.previous') && !link.hasAttribute('data-page')) {
            link.setAttribute('data-page', Math.max(1, currentPage - 1));
        }
        
        if (link.closest('.next') && !link.hasAttribute('data-page')) {
            const totalPages = getTotalPages();
            link.setAttribute('data-page', Math.min(totalPages, currentPage + 1));
        }
        
        link.addEventListener('click', function(e) {
            // Prevent default for all pagination links to handle properly
            e.preventDefault();
            
            // Extract page number from the link
            const pageNumber = link.getAttribute('data-page');
            
            if (pageNumber) {
                // Show loading indicator
                showLoadingState();
                
                // Create a new URL with updated page parameter
                urlParams.set('page', pageNumber);
                const newUrl = `${window.location.pathname}?${urlParams.toString()}`;
                
                // Navigate to the new URL
                window.location.href = newUrl;
            }        });
    });
}

/**
 * Initialize animations for recipe cards
 */
function initAnimations() {
    const recipeCards = document.querySelectorAll('.recipe-card');
    
    // Make sure all cards have animation applied
    recipeCards.forEach((card, index) => {
        if (!card.style.animation) {
            const delay = index * 0.03;
            card.style.animation = `fadeInUp 0.35s ease-out forwards ${delay}s`;
        }
    });
}

/**
 * Get total pages from pagination
 */
function getTotalPages() {
    const pagination = document.querySelector('.pagination');
    if (!pagination) return 1;
    
    // Find the last page number by checking numeric pagination items
    let lastPage = 1;
    const pageItems = pagination.querySelectorAll('li:not(.previous):not(.next)');
    pageItems.forEach(item => {
        const pageNum = parseInt(item.textContent.trim());
        if (!isNaN(pageNum) && pageNum > lastPage) {
            lastPage = pageNum;
        }
    });
    
    return lastPage;
}

/**
 * Show loading state while page changes
 */
function showLoadingState() {
    const recipeGrid = document.querySelector('.recipe-grid');
    if (recipeGrid) {
        recipeGrid.style.opacity = '0.6';
        recipeGrid.style.transition = 'opacity 0.2s';
    }
}