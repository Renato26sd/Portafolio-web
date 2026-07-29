// premium.js - funciones premium simuladas (modo oscuro, más categorías, ilimitados)
(function(){
  const LS_PREMIUM = 'np_premium';
  const LS_THEME = 'np_theme';

  function isPremium(){ return localStorage.getItem(LS_PREMIUM)==='1'; }
  function setPremium(v){ localStorage.setItem(LS_PREMIUM, v ? '1':'0'); renderBadge('#premiumBadge'); }

  function toggleTheme(){
    const current = document.documentElement.getAttribute('data-theme') || 'light';
    const next = current==='light' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem(LS_THEME, next);
  }
  function applyThemeFromStorage(){
    const t = localStorage.getItem(LS_THEME) || 'light';
    document.documentElement.setAttribute('data-theme', t);
  }

  function renderBadge(sel){
    const el = document.querySelector(sel); if(!el) return;
    el.classList.toggle('d-none', !isPremium());
  }

  function saveLimit(){ return isPremium()? Infinity : 20; }

  window.Premium = { isPremium, setPremium, toggleTheme, applyThemeFromStorage, renderBadge, saveLimit };
})();
