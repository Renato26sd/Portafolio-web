// main.js - funciones generales y renderizado
(function(){
  const LS_SAVED = 'np_saved';
  const LS_RECS = 'np_recs';
  const LS_NOTIFS = 'np_notifications';

  document.addEventListener('DOMContentLoaded', init);

  function init(){
    // Año footer
    const y = document.getElementById('year'); if(y) y.textContent = new Date().getFullYear();

    // Theme & premium
    window.Premium?.applyThemeFromStorage();
    window.Premium?.renderBadge('#premiumBadge');
    bindHeader();

    // Auth UI
    window.Auth?.renderAuthArea('#authArea');

    // Load settings to UI
    const s = window.News.getSettings();
    setSelect('#providerSelect', s.provider||'demo');
    setSelect('#settingsProvider', s.provider||'demo');
    setInput('#settingsApiKey', s.apiKey||'');
    document.getElementById('settingsPremium').checked = window.Premium.isPremium();

    // Events
    document.getElementById('btnRefresh').addEventListener('click', loadAndRender);
    document.getElementById('providerSelect').addEventListener('change', (e)=>{ window.News.setProvider(e.target.value); syncSettingsUI(); loadAndRender(); });
    document.getElementById('categorySelect').addEventListener('change', loadAndRender);
    document.getElementById('btnSaveSettings').addEventListener('click', saveSettingsFromModal);
    document.getElementById('settingsPremium').addEventListener('change', (e)=>{ window.Premium.setPremium(e.target.checked); });
    document.getElementById('btnClearRecs').addEventListener('click', ()=>{ localStorage.removeItem(LS_RECS); renderRecs(); });

    // Ads & notifications
    renderAds();
    updateNotifCount();

    // First load
    loadAndRender();

    // Saved side panel
    renderSaved();
  }

  function bindHeader(){
    const btnTheme = document.getElementById('btnToggleTheme');
    if(btnTheme) btnTheme.addEventListener('click', ()=>{ window.Premium.toggleTheme(); });

    const btnNotif = document.getElementById('btnNotifications');
    if(btnNotif) btnNotif.addEventListener('click', ()=>{
      showRecentNewsModal();
    });

    const btnSaved = document.getElementById('btnSaved');
    if(btnSaved) btnSaved.addEventListener('click', ()=>{
      document.getElementById('savedList').scrollIntoView({behavior:'smooth'});
    });
  }

  // Mostrar modal con noticias recientes
  async function showRecentNewsModal(){
    try {
      // Obtener noticias de todas las categorías (general por defecto)
      const recentNews = await window.News.fetchNews({category: 'general'});
      
      // Tomar las primeras 5 noticias más recientes
      const topRecent = recentNews.slice(0, 5);
      
      // Renderizar en el modal
      const container = document.getElementById('recentNewsList');
      container.innerHTML = '';
      
      topRecent.forEach((item, index) => {
        const newsItem = document.createElement('div');
        newsItem.className = 'recent-news-item';
        newsItem.style.cssText = `
          display: flex;
          gap: 1rem;
          padding: 1rem;
          background: var(--bg-card);
          border-radius: 12px;
          border: 2px solid rgba(102, 126, 234, 0.1);
          transition: all 0.3s ease;
          cursor: pointer;
          opacity: 0;
          transform: translateY(20px);
        `;
        
        newsItem.innerHTML = `
          <img src="${item.image || 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=200&h=150&fit=crop'}" 
               alt="${item.title}" 
               style="width: 120px; height: 90px; object-fit: cover; border-radius: 8px; flex-shrink: 0;">
          <div style="flex: 1; min-width: 0;">
            <div style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">
              <span class="category-badge-modern" style="font-size: 0.75rem;">${item.category || 'general'}</span>
              ${window.News.trustedLabel(item) ? '<span class="verified-badge" style="font-size: 0.75rem;"><i class="bi bi-patch-check-fill me-1"></i>Verificado</span>' : ''}
            </div>
            <h6 style="margin: 0 0 0.5rem 0; font-weight: 700; color: var(--text-primary); font-size: 0.95rem; line-height: 1.3;">
              ${item.title}
            </h6>
            <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
              ${item.description || ''}
            </p>
          </div>
        `;
        
        newsItem.addEventListener('mouseenter', () => {
          newsItem.style.borderColor = 'var(--color-primary)';
          newsItem.style.transform = 'translateX(5px)';
          newsItem.style.boxShadow = 'var(--shadow-md)';
        });
        
        newsItem.addEventListener('mouseleave', () => {
          newsItem.style.borderColor = 'rgba(102, 126, 234, 0.1)';
          newsItem.style.transform = 'translateX(0)';
          newsItem.style.boxShadow = 'none';
        });
        
        newsItem.addEventListener('click', () => {
          window.open(item.url, '_blank');
          window.Metrics?.viewedArticle(item.id, item.category || 'general');
        });
        
        container.appendChild(newsItem);
        
        // Animación de entrada
        setTimeout(() => {
          newsItem.style.transition = 'all 0.5s ease';
          newsItem.style.opacity = '1';
          newsItem.style.transform = 'translateY(0)';
        }, index * 100);
      });
      
      // Mostrar el modal
      const modal = new bootstrap.Modal(document.getElementById('notificationsModal'));
      modal.show();
      
      // Registrar notificación
      addNotification('Has visto las últimas noticias.');
      
    } catch(e) {
      alert('Error al cargar noticias recientes: ' + e.message);
    }
  }

  async function loadAndRender(){
    const provider = getSelect('#providerSelect');
    const category = getSelect('#categorySelect');
    try{
      const list = await window.News.fetchNews({category});
      renderNews(list);
      storeRecsFrom(list);
    }catch(e){
      alert(e.message);
    }
  }

  function renderNews(items){
    const grid = document.getElementById('newsList');
    grid.innerHTML = '';
    const tpl = document.getElementById('tplNewsCard');

    items.forEach((item, index) =>{
      const n = tpl.content.cloneNode(true);
      const card = n.querySelector('.news-card');
      const img = n.querySelector('.news-card-image');
      const title = n.querySelector('.news-card-title');
      const desc = n.querySelector('.news-card-description');
      const meta = n.querySelector('.news-card-meta');
      const aRead = n.querySelector('.btn-action-primary');
      const btnShare = n.querySelector('[data-action="share"]');
      const btnSave = n.querySelector('[data-action="save"]');
      const badgeCat = n.querySelector('.category-badge-modern');
      const fact = n.querySelector('[data-fact="badge"]');

      // Imagen con fallback a Unsplash si no hay imagen
      if(img) {
        img.src = item.image || `https://images.unsplash.com/photo-${1500000000000 + index}?w=800&h=600&fit=crop&auto=format`;
        img.alt = item.title || 'Noticia';
      }
      
      title.textContent = item.title || 'Sin título';
      desc.textContent = item.description || 'Sin descripción disponible.';
      
      // Formato de fecha más amigable
      const date = new Date(item.publishedAt || Date.now());
      const timeAgo = getTimeAgo(date);
      meta.textContent = `${item.source || 'Fuente desconocida'} • ${timeAgo}`;
      
      // Cambiar comportamiento: "Leer más" abre modal con contenido completo
      aRead.href = '#';
      aRead.addEventListener('click', (e)=>{
        e.preventDefault();
        showFullNewsModal(item);
        window.Metrics?.viewedArticle(item.id, item.category||'general');
      });
      
      btnShare.addEventListener('click', ()=>onShare(item));
      btnSave.addEventListener('click', ()=>onSave(item));
      badgeCat.textContent = (item.category||'general');

      // Fact-checking simulado
      if(window.News.trustedLabel(item)){
        fact.classList.remove('d-none');
      }

      // Animación de entrada escalonada
      const col = n.querySelector('.col-12');
      if(col) {
        col.style.opacity = '0';
        col.style.transform = 'translateY(20px)';
        setTimeout(() => {
          col.style.transition = 'all 0.5s ease';
          col.style.opacity = '1';
          col.style.transform = 'translateY(0)';
        }, index * 100);
      }

      grid.appendChild(n);
    });
  }

  // Mostrar modal con el contenido completo de la noticia específica
  function showFullNewsModal(item) {
    const modal = document.getElementById('fullNewsModal');
    const titleEl = document.getElementById('fullNewsTitle');
    const imageEl = document.getElementById('fullNewsImage').querySelector('img');
    const badgesEl = document.getElementById('fullNewsBadges');
    const metaEl = document.getElementById('fullNewsMeta');
    const contentEl = document.getElementById('fullNewsContent');
    const linkEl = document.getElementById('fullNewsLink');

    // Título
    titleEl.innerHTML = `<i class="bi bi-newspaper me-2"></i>${item.title}`;

    // Imagen
    imageEl.src = item.image || 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=600&fit=crop';
    imageEl.alt = item.title;

    // Badges
    badgesEl.innerHTML = `
      <span class="category-badge-modern">${item.category || 'general'}</span>
      ${window.News.trustedLabel(item) ? '<span class="verified-badge"><i class="bi bi-patch-check-fill me-1"></i>Verificado</span>' : ''}
    `;

    // Meta
    const date = new Date(item.publishedAt || Date.now());
    const timeAgo = getTimeAgo(date);
    metaEl.innerHTML = `<i class="bi bi-building me-2"></i>${item.source || 'Fuente desconocida'} • <i class="bi bi-clock me-2"></i>${timeAgo}`;

    // Contenido completo
    const fullContent = item.fullContent || item.description || 'No hay contenido completo disponible para esta noticia.';
    contentEl.innerHTML = `<p>${fullContent}</p>`;

    // Enlace a fuente original
    linkEl.href = item.url;

    // Mostrar modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
  }

  // Función helper para mostrar tiempo relativo
  function getTimeAgo(date) {
    const seconds = Math.floor((new Date() - date) / 1000);
    const intervals = {
      año: 31536000,
      mes: 2592000,
      semana: 604800,
      día: 86400,
      hora: 3600,
      minuto: 60
    };

    for (const [name, secondsInInterval] of Object.entries(intervals)) {
      const interval = Math.floor(seconds / secondsInInterval);
      if (interval >= 1) {
        return `Hace ${interval} ${name}${interval > 1 ? (name === 'mes' ? 'es' : 's') : ''}`;
      }
    }
    return 'Hace un momento';
  }

  async function onShare(item){
    try{
      window.Auth?.requireAuth();
      const link = item.url;
      if(navigator.clipboard){ await navigator.clipboard.writeText(link); alert('Enlace copiado al portapapeles'); }
      else { window.open(link, '_blank'); }
      addNotification('Has compartido una noticia.');
    }catch(e){ alert('Solo usuarios registrados pueden compartir.'); }
  }

  function onSave(item){
    try{
      window.Auth?.requireAuth();
      const saved = JSON.parse(localStorage.getItem(LS_SAVED)||'[]');
      if(saved.length >= window.Premium.saveLimit()){
        alert('Has alcanzado el límite de guardados. Activa Premium para ilimitados.');
        return;
      }
      if(!saved.find(s=>s.id===item.id)){
        saved.push({id:item.id, title:item.title, url:item.url});
        localStorage.setItem(LS_SAVED, JSON.stringify(saved));
        renderSaved();
        addNotification('Noticia guardada.');
      }
    }catch(e){ alert('Solo usuarios registrados pueden guardar.'); }
  }

  function renderSaved(){
    const ul = document.getElementById('savedList'); if(!ul) return;
    const saved = JSON.parse(localStorage.getItem(LS_SAVED)||'[]');
    ul.innerHTML = '';
    saved.forEach(it=>{
      const li = document.createElement('li');
      li.className='list-group-item d-flex justify-content-between align-items-center';
      li.innerHTML = `<a href="${it.url}" target="_blank" class="text-decoration-none">${it.title}</a>`;
      ul.appendChild(li);
    });
    if(saved.length===0){ ul.innerHTML = '<li class="list-group-item text-muted">Nada guardado aún</li>'; }
  }

  // Recomendaciones: mantener últimas vistas/sugeridas
  function storeRecsFrom(items){
    // Guardar top 5 títulos de la carga actual; en real, usaría historial de vistas/guardados
    const recs = JSON.parse(localStorage.getItem(LS_RECS)||'[]');
    const add = items.slice(0,5).map(i=>({title:i.title, url:i.url}));
    const merged = [...add, ...recs].slice(0,10);
    localStorage.setItem(LS_RECS, JSON.stringify(merged));
    renderRecs();
  }
  function renderRecs(){
    const ul = document.getElementById('recsList'); if(!ul) return; ul.innerHTML='';
    const recs = JSON.parse(localStorage.getItem(LS_RECS)||'[]');
    recs.forEach(r=>{
      const li = document.createElement('li');
      li.className='list-group-item';
      li.innerHTML = `<a href="${r.url}" target="_blank" class="text-decoration-none">${r.title}</a>`;
      ul.appendChild(li);
    });
    if(recs.length===0){ ul.innerHTML = '<li class="list-group-item text-muted">Sin recomendaciones</li>'; }
  }

  // Notificaciones con límite diario 5
  function addNotification(text){
    const obj = JSON.parse(localStorage.getItem(LS_NOTIFS)||'{}');
    const today = new Date().toISOString().slice(0,10);
    obj[today] = obj[today] || {count:0, items:[]};
    if(obj[today].count >= 5){ updateNotifCount(obj); return; }
    obj[today].count += 1;
    obj[today].items.push({text, at: Date.now()});
    localStorage.setItem(LS_NOTIFS, JSON.stringify(obj));
    updateNotifCount(obj);
  }
  function updateNotifCount(pre){
    const obj = pre || JSON.parse(localStorage.getItem(LS_NOTIFS)||'{}');
    const today = new Date().toISOString().slice(0,10);
    const used = obj[today]?.count || 0;
    const el = document.getElementById('notifCount'); if(el) el.textContent = `${used}/5`;
  }
  function notify(text){ addNotification(text); }

  // Ads minimalista
  function renderAds(){
    const strip = document.getElementById('adsStrip'); if(!strip) return;
    const items = [
      {text:'Suscríbete a Premium y obtén modo oscuro', tag:'general'},
      {text:'Ofertas en gadgets tecnológicos', tag:'technology'},
      {text:'Apuesta responsable — deportes', tag:'sports'},
    ];
    strip.innerHTML='';
    items.forEach(it=>{
      const span = document.createElement('span');
      span.className='ad-pill';
      span.textContent = it.text;
      strip.appendChild(span);
    });
  }

  // Helpers
  function setSelect(sel, val){ const el = document.querySelector(sel); if(el) el.value = val; }
  function getSelect(sel){ const el = document.querySelector(sel); return el? el.value: ''; }
  function setInput(sel, val){ const el = document.querySelector(sel); if(el) el.value = val; }

  function syncSettingsUI(){
    const s = window.News.getSettings();
    setSelect('#settingsProvider', s.provider||'demo');
  }

  function saveSettingsFromModal(){
    const provider = getSelect('#settingsProvider');
    const apiKey = document.getElementById('settingsApiKey').value;
    window.News.setProvider(provider);
    window.News.setApiKey(apiKey);
    alert('Guardado.');
  }

  // Evitar eliminación sin verificación: no hay UI de borrado; cualquier futura eliminación debe confirmarse
  window.safeDelete = function(cb){ if(confirm('¿Confirmas? Esto no se puede deshacer.')) cb(); };
})();
