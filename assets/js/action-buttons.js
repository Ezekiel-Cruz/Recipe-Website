/**
 * Action Buttons Animation
 * Handles animations and hover effects for View, Edit, Delete buttons
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize action button animations
    initActionButtonAnimations();
});

/**
 * Initialize animations for action buttons
 */
function initActionButtonAnimations() {
    const actionButtons = document.querySelectorAll('.recipe-actions a');
    
    if (actionButtons.length === 0) return;
    
    // Apply initial styles
    actionButtons.forEach((button, index) => {
        // Make sure any conflicting styles are overridden
        button.style.transition = 'background-color 0.25s ease, color 0.25s ease, box-shadow 0.25s ease';
        
        // Set initial opacity and transform for staggered fade in
        button.style.opacity = '0';
        button.style.transform = 'translateY(10px)';
        
        // Create staggered animation effect
        setTimeout(() => {
            button.style.transition = 'opacity 0.3s ease, transform 0.3s ease, background-color 0.25s ease, color 0.25s ease, box-shadow 0.25s ease';
            button.style.opacity = '1';
            button.style.transform = 'translateY(0)';
        }, index * 100); // 100ms delay between each button
        
        // Add ripple effect container if it doesn't exist
        if (!button.querySelector('.ripple-container')) {
            const rippleContainer = document.createElement('span');
            rippleContainer.className = 'ripple-container';
            rippleContainer.style.position = 'absolute';
            rippleContainer.style.top = '0';
            rippleContainer.style.left = '0';
            rippleContainer.style.right = '0';
            rippleContainer.style.bottom = '0';
            rippleContainer.style.overflow = 'hidden';
            rippleContainer.style.pointerEvents = 'none';
            rippleContainer.style.zIndex = '1'; // Ensure ripple is above other content
            button.appendChild(rippleContainer);
        }
        
        // Add click handler for ripple effect
        button.addEventListener('click', createRippleEffect);
    });
}

/**
 * Create ripple effect on button click
 * @param {Event} e - Click event
 */
function createRippleEffect(e) {
    const button = e.currentTarget;
    
    // Get ripple container or create it
    let rippleContainer = button.querySelector('.ripple-container');
    if (!rippleContainer) {
        rippleContainer = document.createElement('span');
        rippleContainer.className = 'ripple-container';
        rippleContainer.style.position = 'absolute';
        rippleContainer.style.top = '0';
        rippleContainer.style.left = '0';
        rippleContainer.style.right = '0';
        rippleContainer.style.bottom = '0';
        rippleContainer.style.overflow = 'hidden';
        button.appendChild(rippleContainer);
    }
    
    // Remove existing ripples
    const existingRipples = rippleContainer.querySelectorAll('.ripple');
    existingRipples.forEach(ripple => ripple.remove());
    
    // Create new ripple
    const ripple = document.createElement('span');
    ripple.className = 'ripple';
    
    // Style the ripple
    ripple.style.position = 'absolute';
    ripple.style.transform = 'translate(-50%, -50%)';
    ripple.style.borderRadius = '50%';
    ripple.style.backgroundColor = 'rgba(255, 255, 255, 0.5)';
    ripple.style.pointerEvents = 'none';
    
    // Calculate position and size
    const rect = button.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height) * 2;
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    
    ripple.style.width = `${size}px`;
    ripple.style.height = `${size}px`;
    ripple.style.left = `${x}px`;
    ripple.style.top = `${y}px`;
    
    // Add animation
    ripple.style.animation = 'ripple-effect 0.6s ease-out forwards';
    
    // Add ripple to container
    rippleContainer.appendChild(ripple);
    
    // Clean up after animation
    setTimeout(() => {
        ripple.remove();
    }, 700);
}

// Add ripple animation to document
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple-effect {
        0% {
            transform: translate(-50%, -50%) scale(0);
            opacity: 1;
        }
        90% {
            opacity: 0;
        }
        100% {
            transform: translate(-50%, -50%) scale(1);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
