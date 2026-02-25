setTimeout(() => {
    document.getElementById('splashScreen').classList.add('hide');
    setTimeout(() => {
        window.location.href = 'index.php';
    }, 1000);
}, 5000);