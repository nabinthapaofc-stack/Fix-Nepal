const mockUsers = [
  { username: 'admin', password: 'FixNepal@123' },
  { username: 'nabin', password: '123' },
  { username: 'abhi', password: '123' }
];

function validateCredentials(username, password) {
  const match = mockUsers.find(user => user.username === username);
  if (!match || match.password !== password) {
    showAuthPopup('Invalid username or password. Please try again.');
    return false;
  }
  showAuthPopup(`Welcome back, ${username}!`, true);
  return true;
}

function showAuthPopup(message, success = false) {
  const popup = document.createElement('div');
  popup.className = `auth-popup ${success ? 'success' : 'error'}`;
  popup.innerHTML = `<span>${message}</span><button aria-label="Close">×</button>`;
  popup.querySelector('button').addEventListener('click', () => popup.remove());
  document.body.appendChild(popup);
  setTimeout(() => popup.remove(), 4000);
}

export { mockUsers, validateCredentials };
