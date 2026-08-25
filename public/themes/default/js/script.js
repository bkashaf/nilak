// === نیلَک Theme Scripts ===

// اسکرول نرم برای لینک‌های داخلی
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        if (this.getAttribute('href') === '#') {
            return;
        }

        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (!target) {
            return;
        }

        target.scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// نمایش پیام ساده در کنسول برای تست
console.log("Theme NILAK loaded successfully.");
