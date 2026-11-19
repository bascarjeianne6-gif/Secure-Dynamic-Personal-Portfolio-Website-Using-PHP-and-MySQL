// main.js may intercept the contact form (client-side demo). This capture-phase
// listener stops that handler so the form posts to contact.php (server validates).
document.addEventListener('submit', function (e) {
  if (e.target && e.target.id === 'contactForm') e.stopPropagation();
}, true);
