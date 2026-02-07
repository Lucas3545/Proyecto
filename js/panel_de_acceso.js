const openPanelBtn = document.getElementById('openPanelBtn');
  const overlay = document.getElementById('authPanel');
  const tabLogin = document.getElementById('tab-login');
  const tabRegister = document.getElementById('tab-register');
  const formLogin = document.getElementById('form-login');
  const formRegister = document.getElementById('form-register');
  const loginMessage = document.getElementById('login-message');
  const registerMessage = document.getElementById('register-message');

  openPanelBtn.addEventListener('click', () => {
    const expanded = openPanelBtn.getAttribute('aria-expanded') === 'true';
    if (expanded) {
      closePanel();
    } else {
      openPanel();
    }
  });

  function openPanel() {
    overlay.style.display = 'flex';
    openPanelBtn.setAttribute('aria-expanded', 'true');
    tabLogin.focus();
    document.body.style.overflow = 'hidden';
  }

  function closePanel() {
    overlay.style.display = 'none';
    openPanelBtn.setAttribute('aria-expanded', 'false');
    openPanelBtn.focus();
    document.body.style.overflow = ''; 
  }

  
  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) {
      closePanel();
    }
  });

  
  document.addEventListener('keydown', (e) => {
    if (e.key === "Escape" && overlay.style.display === 'flex') {
      closePanel();
    }
  });

  tabLogin.addEventListener('click', () => {
    selectTab('login');
  });

  tabRegister.addEventListener('click', () => {
    selectTab('register');
  });

  function selectTab(tab) {
    clearMessages();
    if (tab === 'login') {
      tabLogin.classList.add('active');
      tabLogin.setAttribute('aria-selected', 'true');
      tabLogin.tabIndex = 0;
      tabRegister.classList.remove('active');
      tabRegister.setAttribute('aria-selected', 'false');
      tabRegister.tabIndex = -1;

      formLogin.style.display = 'block';
      formLogin.setAttribute('aria-hidden', 'false');
      formRegister.style.display = 'none';
      formRegister.setAttribute('aria-hidden', 'true');
      formLogin.querySelector('input').focus();
    } else {
      tabRegister.classList.add('active');
      tabRegister.setAttribute('aria-selected', 'true');
      tabRegister.tabIndex = 0;
      tabLogin.classList.remove('active');
      tabLogin.setAttribute('aria-selected', 'false');
      tabLogin.tabIndex = -1;

      formRegister.style.display = 'block';
      formRegister.setAttribute('aria-hidden', 'false');
      formLogin.style.display = 'none';
      formLogin.setAttribute('aria-hidden', 'true');
      formRegister.querySelector('input').focus();
    }
  }

  function clearMessages() {
    loginMessage.textContent = '';
    loginMessage.style.color = 'red';
    registerMessage.textContent = '';
    registerMessage.style.color = 'red';
  }

  formLogin.addEventListener('submit', e => {
    e.preventDefault();
    clearMessages();

    const email = formLogin['login-email'].value.trim();
    const password = formLogin['login-password'].value;

    if (!email || !password) {
      loginMessage.textContent = 'Por favor, completa todos los campos.';
      return;
    }

    loginMessage.style.color = 'green';
    loginMessage.textContent = '¡Inicio de sesión exitoso! (simulado)';
  });

  formRegister.addEventListener('submit', e => {
    e.preventDefault();
    clearMessages();

    const name = formRegister['register-name'].value.trim();
    const email = formRegister['register-email'].value.trim();
    const password = formRegister['register-password'].value;
    const confirmPassword = formRegister['register-confirm-password'].value;

    if (!name || !email || !password || !confirmPassword) {
      registerMessage.textContent = 'Por favor, completa todos los campos.';
      return;
    }
    if (password.length < 6) {
      registerMessage.textContent = 'La contraseña debe tener al menos 6 caracteres.';
      return;
    }
    if (password !== confirmPassword) {
      registerMessage.textContent = 'Las contraseñas no coinciden.';
      return;
    }

    registerMessage.style.color = 'green';
    registerMessage.textContent = '¡Registro exitoso!';
    formRegister.reset();
  });
    formLogin.addEventListener('submit', closePanel);
    formLogin.addEventListener('submit', () => {
      window.location.href = 'index.html';
    });