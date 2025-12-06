document.addEventListener('submit', async function(e){
  const form = e.target;
  if (form.id !== 'loginForm' && form.id !== 'regForm') return;
  e.preventDefault();
  const out = form.id === 'loginForm' ? document.getElementById('error') : document.getElementById('regError');
  out && (out.textContent = '');
  try {
    const res = await fetch(form.action, {
      method: 'POST',
      body: new FormData(form),
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const json = await res.json();
    if (json.ok) {
      if (form.id === 'regForm') window.location = 'index.html';
      else window.location = json.redirect || 'dashboard.php';
    } else {
      out && (out.textContent = json.error || 'Failed');
    }
  } catch {
    out && (out.textContent = 'Network error');
  }
}); 