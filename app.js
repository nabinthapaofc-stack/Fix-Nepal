document.addEventListener('submit', async function(e) {
  const form = e.target;
  if (!form || (form.id !== 'loginForm' && form.id !== 'regForm')) return;
  e.preventDefault();

  const out = form.id === 'loginForm' ? document.getElementById('error') : document.getElementById('regError');
  if (out) out.textContent = '';

  const action = form.action || (form.id === 'loginForm' ? 'login.php' : 'register.php');

  try {
    const res = await fetch(action, {
      method: 'POST',
      body: new FormData(form),
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });

    const text = await res.text();
    try {
      const json = JSON.parse(text);
      if (json.ok) {
        if (json.redirect) {
          window.location.href = json.redirect;
        } else {
          window.location.href = form.id === 'regForm' ? 'index.html' : 'dashboard.php';
        }
      } else {
        if (out) out.textContent = json.error || 'Operation failed';
      }
    } catch (err) {
      window.location.reload();
    }
  } catch (err) {
    if (out) out.textContent = 'Network error';
  }
});

// Ensure other fetch calls use endpoints like 'register.php', 'user.php', 'admin.php' as needed.