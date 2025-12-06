;(function () {
  const portals = {
    user: {
      email: 'user@fixnepal.com',
      username: 'abc',
      password: '123',
      redirect: 'index.html',
      successMessage: 'Welcome to the citizen dashboard!'
    },
    admin: {
      email: 'admin@fixnepal.com',
      username: 'admin',
      password: '@#$',
      redirect: 'admin.html',
      successMessage: 'Admin access granted.'
    }
  };

  function ensureStyles() {
    if (document.getElementById('portal-popup-style')) return;
    const style = document.createElement('style');
    style.id = 'portal-popup-style';
    style.textContent = `
      .portal-popup {
        position: fixed;
        bottom: 24px;
        right: 24px;
        min-width: 240px;
        max-width: 320px;
        padding: 14px 18px;
        border-radius: 16px;
        color: #fff;
        font-weight: 600;
        box-shadow: 0 20px 40px rgba(15,23,42,.25);
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 400;
        animation: popup-slide 0.3s ease forwards;
      }
      .portal-popup.error { background: #dc2626; }
      .portal-popup.success { background: #059669; }
      .portal-popup button {
        border: none;
        background: transparent;
        color: inherit;
        cursor: pointer;
        font-size: 1.2rem;
      }
      @keyframes popup-slide {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
      }
    `;
    document.head.appendChild(style);
  }

  function showPortalPopup(message, success = false) {
    ensureStyles();
    const popup = document.createElement('div');
    popup.className = `portal-popup ${success ? 'success' : 'error'}`;
    popup.innerHTML = `<span>${message}</span><button aria-label="Close">&times;</button>`;
    popup.querySelector('button').addEventListener('click', () => popup.remove());
    document.body.appendChild(popup);
    setTimeout(() => popup.remove(), 3500);
  }

  function matchPortal({ email, username, password }) {
    return (
      Object.values(portals).find(
        portal =>
          portal.email === email &&
          portal.username === username &&
          portal.password === password
      ) || null
    );
  }

  function attachPortalLogin(formSelector = '#loginForm') {
    const form = document.querySelector(formSelector);
    if (!form) return;
    form.addEventListener('submit', event => {
      event.preventDefault();
      const email =
        form.querySelector('[name="email"]')?.value.trim().toLowerCase() ?? '';
      const username =
        form.querySelector('[name="username"]')?.value.trim().toLowerCase() ?? '';
      const password = form.querySelector('[name="password"]')?.value ?? '';

      if (!email || !username || !password) {
        showPortalPopup('All fields are required.');
        return;
      }

      const portal = matchPortal({ email, username, password });
      if (!portal) {
        showPortalPopup('Credentials do not match any Fix नेपाल portal account.');
        return;
      }

      showPortalPopup(portal.successMessage, true);
      setTimeout(() => {
        window.location.href = portal.redirect;
      }, 500);
    });
  }

  document.addEventListener('DOMContentLoaded', () => attachPortalLogin('#loginForm'));

  window.AuthPortal = {
    attachPortalLogin
  };
})();
