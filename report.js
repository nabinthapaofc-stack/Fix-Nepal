const openBtn = document.getElementById('openReport');
const panel = document.getElementById('panel');
const form = document.getElementById('reportForm');
const reports = document.getElementById('reports');
const cancelBtn = document.getElementById('cancelBtn');

function openPanel(){ panel.classList.add('open'); panel.setAttribute('aria-hidden','false'); document.getElementById('title').focus(); }
function closePanel(){ panel.classList.remove('open'); panel.setAttribute('aria-hidden','true'); }

openBtn.addEventListener('click', () => {
  if(panel.classList.contains('open')) closePanel(); else openPanel();
});

cancelBtn.addEventListener('click', (e)=>{ e.preventDefault(); closePanel(); });

form.addEventListener('submit', (e)=>{
  e.preventDefault();
  const title = form.title.value.trim();
  const desc = form.description.value.trim();
  if(!title) return;

  const card = document.createElement('article');
  card.className = 'report-card';
  const h = document.createElement('h3'); h.textContent = title;
  const p = document.createElement('p'); p.textContent = desc;
  const meta = document.createElement('div'); meta.className = 'report-meta';
  const now = new Date(); meta.textContent = now.toLocaleString();
  card.appendChild(h); card.appendChild(p); card.appendChild(meta);
  reports.prepend(card);

  form.reset();
  closePanel();
});

document.addEventListener('keydown', (e)=>{
  if(e.key === 'Escape' && panel.classList.contains('open')) {
    closePanel();
  }
});
