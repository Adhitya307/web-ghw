// Splash screen functionality
document.addEventListener('DOMContentLoaded', function() {
    const splashScreen = document.getElementById('splash-screen');
    const mainContent = document.getElementById('main-content');
    
    // Set timer untuk menyembunyikan splash screen setelah 5 detik
    setTimeout(function() {
        splashScreen.style.opacity = '0';
        splashScreen.style.transition = 'opacity 0.5s ease';
        
        setTimeout(function() {
            splashScreen.style.display = 'none';
            mainContent.style.display = 'block';
            
            // Inisialisasi fungsi lainnya setelah splash screen hilang
            initializePage();
        }, 500);
    }, 5000);

    // Skip splash screen jika diklik
    splashScreen.addEventListener('click', function() {
        splashScreen.style.display = 'none';
        mainContent.style.display = 'block';
        initializePage();
    });

    // Skip dengan tombol keyboard
    document.addEventListener('keydown', function() {
        splashScreen.style.display = 'none';
        mainContent.style.display = 'block';
        initializePage();
    });
});

function initializePage() {
    // Fungsi untuk inisialisasi halaman setelah splash screen
    document.getElementById('current-year').textContent = new Date().getFullYear();

    // Inisialisasi AOS (Animate On Scroll)
    AOS.init({
        duration: 800,
        once: true,
        offset: 100,
        easing: 'ease-out-cubic'
    });
    
    // Setup parallax effect
    setupParallax();
    
    // Animasi tambahan untuk elemen spesifik
    animateElements();
    
    // Smooth scroll dengan offset untuk header fixed dan trigger animasi
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.getAttribute('href').startsWith('#')) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    const headerHeight = document.querySelector('.main-header').offsetHeight;
                    const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                    
                    // Trigger animasi untuk section yang dituju
                    setTimeout(() => {
                        triggerSectionAnimation(targetId);
                    }, 600);
                    
                    history.pushState(null, null, '#' + targetId);
                }
            }
        });
    });

    // Update active nav link berdasarkan scroll position
    window.addEventListener('scroll', function() {
        const sections = document.querySelectorAll('section');
        const navLinks = document.querySelectorAll('.nav-link');
        const headerHeight = document.querySelector('.main-header').offsetHeight;
        
        let current = '';
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            
            if (pageYOffset >= (sectionTop - headerHeight - 50)) {
                current = section.getAttribute('id');
            }
        });
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + current) {
                link.classList.add('active');
            }
        });
        
        // Trigger animasi berdasarkan scroll
        triggerScrollAnimations();
    });
    
    // Trigger animasi awal
    setTimeout(() => {
        triggerScrollAnimations();
    }, 1000);
}

function setupParallax() {
    const heroBg = document.querySelector('.hero-bg');
    if (heroBg) {
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            heroBg.style.transform = `translate3d(0px, ${rate}px, 0px)`;
        });
    }
}

function animateElements() {
    // Animasi untuk badge
    const badges = document.querySelectorAll('.badge-animate');
    badges.forEach((badge, index) => {
        badge.style.animationDelay = `${index * 0.2}s`;
    });
    
    // Animasi untuk contact items
    const contactItems = document.querySelectorAll('.contact-item');
    contactItems.forEach((item, index) => {
        item.style.animationDelay = `${index * 0.1}s`;
    });
    
    // Animasi typing untuk hero text
    const typingText = document.querySelector('.typing-text');
    if (typingText) {
        // Reset animation untuk memastikan berjalan setiap kali halaman dimuat
        typingText.style.animation = 'none';
        setTimeout(() => {
            typingText.style.animation = 'typing 3.5s steps(40, end), blink-caret .75s step-end infinite';
        }, 100);
    }
}

function triggerSectionAnimation(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        // Reset dan trigger animasi AOS
        AOS.refresh();
        
        // Tambah class active untuk trigger animasi custom
        section.classList.add('active');
        
        // Trigger animasi untuk elemen dalam section
        const animatedElements = section.querySelectorAll('[data-aos]');
        animatedElements.forEach(el => {
            el.classList.add('aos-animate');
        });
        
        // Animasi khusus untuk list items di visi-misi
        if (sectionId === 'visi-misi') {
            const missionItems = section.querySelectorAll('.mission-list li');
            missionItems.forEach((item, index) => {
                setTimeout(() => {
                    item.classList.add('animate-in');
                }, index * 200);
            });
        }
    }
}

function triggerScrollAnimations() {
    // Refresh AOS untuk elemen yang masuk viewport
    AOS.refresh();
    
    // Animasi khusus untuk mission list
    const missionSection = document.getElementById('visi-misi');
    if (missionSection && isElementInViewport(missionSection)) {
        const missionItems = missionSection.querySelectorAll('.mission-list li');
        missionItems.forEach((item, index) => {
            setTimeout(() => {
                item.classList.add('animate-in');
            }, index * 200);
        });
    }
}

// Helper function untuk cek elemen dalam viewport
function isElementInViewport(el) {
    const rect = el.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

// Refresh AOS ketika window di-resize
window.addEventListener('resize', function() {
    AOS.refresh();
});