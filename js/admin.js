const requests = [
  {
    title: 'Water Leakage - New Baneshwor',
    status: 'pending',
    description: 'Pipe burst near ward office, lane flooded.',
    submitted: 'Dec 6, 2025',
    reporter: 'नागरिक'
  },
  {
    title: 'Street Light Repair - Ratna Park',
    status: 'in-progress',
    description: 'Three poles off near bus stop, commuters unsafe.',
    submitted: 'Dec 5, 2025',
    reporter: 'Hari'
  },
  {
    title: 'Garbage Overflow - Thamel',
    status: 'pending',
    description: 'Bins overflowing, needs pickup before festival rush.',
    submitted: 'Dec 4, 2025',
    reporter: 'Sita'
  },
  {
    title: 'Pothole - Pulchowk',
    status: 'completed',
    description: 'Deep pothole patched, awaiting verification.',
    submitted: 'Dec 3, 2025',
    reporter: 'Ramesh'
  }
];

const teamMembers = [
  { name: 'Kiran Shrestha', role: 'Field Lead', contact: '9876543210' },
  { name: 'Mina Gurung', role: 'Dispatcher', contact: '9812345678' }
];

const navButtons = document.querySelectorAll('.main-nav li');
const sections = document.querySelectorAll('[data-section-target]');
const filterButtons = document.querySelectorAll('.filter-btn');
const requestRows = document.getElementById('admin-request-rows');
const addMemberBtn = document.getElementById('add-member-btn');
const memberModal = document.getElementById('member-modal');
const memberForm = document.getElementById('member-form');
const memberCancel = document.getElementById('member-cancel');
const teamList = document.getElementById('team-list');
const heroPending = document.getElementById('hero-pending');
const heroDone = document.getElementById('hero-done');
const heroSla = document.getElementById('hero-sla');
const metricOpen = document.getElementById('metric-open');
const metricCompleted = document.getElementById('metric-completed');
const metricTeams = document.getElementById('metric-teams');
const metricTime = document.getElementById('metric-time');
const statusChart = document.getElementById('status-chart');
const volumeChart = document.getElementById('volume-chart');
const toast = document.getElementById('admin-toast');

function switchSection(target) {
  navButtons.forEach(li => li.classList.toggle('active', li.dataset.section === target));
  sections.forEach(section => {
    const isOverview = target === 'overview';
    section.classList.toggle('hidden', section.dataset.sectionTarget !== target && !isOverview);
  });
}

navButtons.forEach(li => {
  const btn = li.querySelector('button');
  if (btn) {
    btn.addEventListener('click', () => switchSection(li.dataset.section));
  }
});

function renderRequests(filter = 'all') {
  requestRows.innerHTML = '';
  requests
    .filter(r => filter === 'all' || r.status === filter)
    .forEach(r => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${r.title}</td>
        <td><span class="status-badge ${statusClass(r.status)}">${formatStatus(r.status)}</span></td>
        <td>${r.description}</td>
        <td>${r.submitted}</td>
        <td>
          <button class="admin-action" data-action="approve">Approve</button>
          <button class="admin-action danger" data-action="reject">Reject</button>
        </td>`;
      requestRows.appendChild(tr);
    });
}

function statusClass(status) {
  if (status === 'completed') return 'status-completed';
  if (status === 'pending') return 'status-pending';
  return 'status-in-progress';
}

function formatStatus(status) {
  if (status === 'completed') return 'Completed';
  if (status === 'pending') return 'Pending';
  return 'In Progress';
}

filterButtons.forEach(btn => {
  btn.addEventListener('click', () => {
    filterButtons.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    renderRequests(btn.dataset.filter);
  });
});

function renderTeam() {
  teamList.innerHTML = '';
  teamMembers.forEach(member => {
    const li = document.createElement('li');
    li.textContent = `${member.name} — ${member.role} (${member.contact})`;
    teamList.appendChild(li);
  });
}

addMemberBtn.addEventListener('click', () => memberModal.classList.remove('hidden'));
memberCancel.addEventListener('click', () => memberModal.classList.add('hidden'));
memberForm.addEventListener('submit', e => {
  e.preventDefault();
  const name = document.getElementById('member-name').value.trim();
  const role = document.getElementById('member-role').value.trim();
  const contact = document.getElementById('member-contact').value.trim();
  if (!name || !role || !contact) return;
  teamMembers.push({ name, role, contact });
  renderTeam();
  memberForm.reset();
  memberModal.classList.add('hidden');
  showToast(`${name} added to response roster.`);
});

function updateMetrics() {
  const pendingCount = requests.filter(r => r.status === 'pending').length;
  const completedCount = requests.filter(r => r.status === 'completed').length;
  heroPending.textContent = pendingCount;
  heroDone.textContent = completedCount;
  heroSla.textContent = `${Math.min(100, 78 + completedCount)}%`;
  metricOpen.textContent = pendingCount;
  metricCompleted.textContent = completedCount;
  metricTeams.textContent = teamMembers.length;
  metricTime.textContent = completedCount ? '9h' : '0h';

  statusChart.innerHTML = ['pending', 'in-progress', 'completed']
    .map(status => {
      const count = requests.filter(r => r.status === status).length;
      return `<div class="chart-bar">
        <span>${formatStatus(status)}</span>
        <div class="bar bar-${status}" style="--bar-value:${count * 20}px"></div>
        <strong>${count}</strong>
      </div>`;
    })
    .join('');

  volumeChart.innerHTML = Array.from({ length: 7 })
    .map((_, idx) => `<div class="spark-bar" style="--spark-value:${(idx + 3) * 8}px"></div>`)
    .join('');
}

function showToast(message) {
  if (!toast) return;
  toast.textContent = message;
  toast.classList.add('visible');
  setTimeout(() => toast.classList.remove('visible'), 3000);
}

switchSection('overview');
renderRequests();
renderTeam();
updateMetrics();
