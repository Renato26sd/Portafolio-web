// metrics.js - registro de uso local (vistas, tiempo promedio, categorías)
(function(){
  const LS_METRICS = 'np_metrics';

  function _load(){
    return JSON.parse(localStorage.getItem(LS_METRICS) || '{}');
  }
  function _save(m){ localStorage.setItem(LS_METRICS, JSON.stringify(m)); }

  // Inicializar sesión y temporizador de permanencia
  let start = Date.now();
  function beginSession(){
    const m = _load();
    m.sessions = (m.sessions||0) + 1;
    _save(m);
  }
  function endSession(){
    const m = _load();
    const deltaSec = Math.round((Date.now()-start)/1000);
    m.dwellTimeSec = (m.dwellTimeSec||0) + Math.max(0, deltaSec);
    _save(m);
  }

  // Vistas de artículo y categorías
  function viewedArticle(id, category){
    const m = _load();
    m.articleViews = m.articleViews || {}; m.articleViews[id] = (m.articleViews[id]||0)+1;
    m.categoryViews = m.categoryViews || {}; if(category){ m.categoryViews[category] = (m.categoryViews[category]||0)+1; }
    _save(m);
  }

  function getAll(){ return _load(); }

  // Eventos de visibilidad para un mejor conteo de tiempo
  document.addEventListener('visibilitychange', ()=>{
    if(document.hidden){ endSession(); } else { start = Date.now(); beginSession(); }
  });
  window.addEventListener('beforeunload', endSession);
  beginSession();

  window.Metrics = { viewedArticle, getAll };
})();
