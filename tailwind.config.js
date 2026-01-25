module.exports = {
    content: [
        './application/views/layouts/booking_layout.php',
        './application/views/components/booking_*.php',
        './application/views/pages/customer_*.php',
        './assets/js/layouts/booking_layout.js',
        './assets/js/pages/booking.js',
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    DEFAULT: '#439a82',
                    dark: '#024225',
                },
            },
            fontFamily: {
                brand: ['"Libre Baskerville"', 'Baskerville', 'serif'],
            },
        },
    },
    plugins: [],
};
