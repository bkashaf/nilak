// === نیلَک Theme Scripts ===

// اسکرول نرم برای لینک‌های داخلی
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// نمایش پیام ساده در کنسول برای تست
console.log("Theme NILAK loaded successfully.");
