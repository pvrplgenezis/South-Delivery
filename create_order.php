(function(){
  async function me(){
    const res = await fetch('api/me.php', {credentials:'same-origin'});
    const data = await res.json().catch(()=>({ok:false}));
    return (res.ok && data.ok) ? data.user : null;
  }
  async function requireRole(role){
    const user = await me();
    if (!user){ location.href = 'login.html'; throw new Error('unauthorized'); }
    if (role && user.role !== role){ location.href = user.role === 'admin' ? 'admin.html' : 'profile.html'; throw new Error('forbidden'); }
    return user;
  }
  async function logout(){
    await fetch('api/logout.php', {method:'POST'}).catch(()=>{});
  }
  async function enhanceHeader(){
    const user = await me();
    const links = document.querySelectorAll('.login-btn, .mobile-nav a[href="login.html"]');
    links.forEach(a => {
      if (user) {
        a.textContent = user.role === 'admin' ? 'Админка' : 'Личный кабинет';
        a.href = user.role === 'admin' ? 'admin.html' : 'profile.html';
      } else {
        a.textContent = 'Войти';
        a.href = 'login.html';
      }
    });
    const slot = document.getElementById('authIndicatorSlot') || document.querySelector('.header-top');
    if (!slot || !user) return;
    const wrap = document.createElement('div');
    wrap.className = 'auth-indicator';
    wrap.title = `Вы вошли как ${user.login}`;
    wrap.innerHTML = '<span class="auth-indicator__dot"></span><span class="auth-indicator__name"></span>';
    wrap.querySelector('.auth-indicator__name').textContent = user.login;
    wrap.addEventListener('click', () => location.href = user.role === 'admin' ? 'admin.html' : 'profile.html');
    if (slot.id === 'authIndicatorSlot') slot.appendChild(wrap); else slot.appendChild(wrap);
  }
  window.SDSession = { me, require: requireRole, logout };
  document.addEventListener('DOMContentLoaded', enhanceHeader);
})();
