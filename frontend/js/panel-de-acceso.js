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
  if (e.key === 'Escape' && overlay.style.display === 'flex') {
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

formLogin.addEventListener('submit', async (e) => {
  e.preventDefault();
  clearMessages();

  const email = formLogin['login-email'].value.trim();
  const password = formLogin['login-password'].value;

  if (!email || !password) {
    loginMessage.textContent = 'Por favor, completa todos los campos.';
    return;
  }

  const body = new URLSearchParams();
  body.append('login-email', email);
  body.append('login-password', password);

  try {
    const response = await fetch('./login.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body.toString()
    });

    const data = await response.json();

    if (data.success) {
      loginMessage.style.color = 'green';
      loginMessage.textContent = data.mensaje || 'Inicio de sesion exitoso.';
      setTimeout(() => {
        closePanel();
        window.location.href = 'index.php';
      }, 500);
      return;
    }

    loginMessage.textContent = data.mensaje || 'Credenciales invalidas.';
  } catch (error) {
    loginMessage.textContent = 'No se pudo conectar con el servidor.';
  }
});

formRegister.addEventListener('submit', async (e) => {
  e.preventDefault();
  clearMessages();

  const username = formRegister['username'].value.trim();
  const name = formRegister['register-name'].value.trim();
  const email = formRegister['register-email'].value.trim();
  const password = formRegister['register-password'].value;
  const confirmPassword = formRegister['register-confirm-password'].value;

  if (!username || !name || !email || !password || !confirmPassword) {
    registerMessage.textContent = 'Por favor, completa todos los campos.';
    return;
  }

  if (password.length < 6) {
    registerMessage.textContent = 'La contrasena debe tener al menos 6 caracteres.';
    return;
  }

  if (password !== confirmPassword) {
    registerMessage.textContent = 'Las contrasenas no coinciden.';
    return;
  }

  const body = new URLSearchParams();
  body.append('username', username);
  body.append('register-name', name);
  body.append('register-email', email);
  body.append('register-password', password);

  try {
    const response = await fetch('./registro.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body.toString()
    });

    const data = await response.json();

    if (data.success) {
      registerMessage.style.color = 'green';
      registerMessage.textContent = data.mensaje || 'Registro exitoso.';
      formRegister.reset();
      setTimeout(() => {
        closePanel();
        window.location.href = 'index.php';
      }, 500);
      return;
    }

    registerMessage.textContent = data.mensaje || 'No se pudo registrar el usuario.';
  } catch (error) {
    registerMessage.textContent = 'No se pudo conectar con el servidor.';
  }
});
