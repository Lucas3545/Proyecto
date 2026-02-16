setTimeout(() => {
    document.getElementById('splashScreen').classList.add('hide');
    setTimeout(() => {
        window.location.href = 'index.html';
    }, 1000);
}, 5000);