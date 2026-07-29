(function(){
  const LS_USERS = 'np_users';
  const LS_SESSION = 'np_session';

  function getUsers(){
    return JSON.parse(localStorage.getItem(LS_USERS) || '[]');
  }
  function saveUsers(users){
    localStorage.setItem(LS_USERS, JSON.stringify(users));
  }
  function hash(str){
    // Hash simple (no seguro, solo demo)
    let h = 0; for (let i=0;i<str.length;i++){ h = (h<<5)-h + str.charCodeAt(i); h|=0; }
    return String(h);
  }

  function register(username, password){
    username = (username||'').trim();
    if(!username || !password) throw new Error('Usuario y contraseña requeridos');
    const users = getUsers();
    if(users.find(u=>u.username===username)) throw new Error('Usuario ya existe');
    users.push({username, passwordHash: hash(password)});
    saveUsers(users);
    login(username, password);
  }

  function login(username, password){
    const users = getUsers();
    const u = users.find(u=>u.username===username);
    if(!u || u.passwordHash!==hash(password)) throw new Error('Credenciales inválidas');
    localStorage.setItem(LS_SESSION, JSON.stringify({username}));
    return {username};
  }

  function logout(){
    localStorage.removeItem(LS_SESSION);
  }

  function current(){
    return JSON.parse(localStorage.getItem(LS_SESSION) || 'null');
  }

  function requireAuth(){
    if(!current()) throw new Error('Requiere inicio de sesión');
  }

  function renderAuthArea(containerSel){
    const el = document.querySelector(containerSel);
    if(!el) return;
    const me = current();
    el.innerHTML = '';
    if(me){
      el.innerHTML = `
        <span class="small">Hola, <strong>${me.username}</strong></span>
        <button class="btn btn-sm btn-outline-danger" id="btnLogout"><i class="bi bi-box-arrow-right me-1"></i>Salir</button>
      `;
      el.querySelector('#btnLogout').addEventListener('click', ()=>{
        logout();
        location.reload();
      });
    }else{
      const grp = document.createElement('div');
      grp.className='input-group input-group-sm';
      grp.innerHTML = `
        <input id="authUser" class="form-control" placeholder="Usuario">
        <input id="authPass" class="form-control" placeholder="Contraseña" type="password">
        <button class="btn btn-success" id="btnLogin">Login</button>
        <button class="btn btn-outline-primary" id="btnRegister">Registro</button>
      `;
      el.appendChild(grp);
      const userEl = grp.querySelector('#authUser');
      const passEl = grp.querySelector('#authPass');
      grp.querySelector('#btnLogin').addEventListener('click', ()=>{
        try{ login(userEl.value, passEl.value); location.reload(); }catch(e){ alert(e.message); }
      });
      grp.querySelector('#btnRegister').addEventListener('click', ()=>{
        try{ register(userEl.value, passEl.value); location.reload(); }catch(e){ alert(e.message); }
      });
    }
  }

  window.Auth = { register, login, logout, current, requireAuth, renderAuthArea };
})();
