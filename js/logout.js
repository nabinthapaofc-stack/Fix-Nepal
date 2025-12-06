function confirmLogout(target = 'index1.html') {
  const confirmed = window.confirm('Are you sure you want to logout?');
  if (confirmed) {
    window.location.href = target;
  }
}
