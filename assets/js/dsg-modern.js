(function(){
  function ready(fn){document.readyState==='loading'?document.addEventListener('DOMContentLoaded',fn):fn();}
  ready(function(){
    document.querySelectorAll('table').forEach(function(t){ if(!t.parentElement.classList.contains('table-responsive')){var w=document.createElement('div');w.className='table-responsive';t.parentNode.insertBefore(w,t);w.appendChild(t);} t.classList.add('table-hover'); });
    if(!document.querySelector('.dsg-fab-help')){var a=document.createElement('a');a.href='/admin_tutorial.php';a.className='btn dsg-fab-help';a.innerHTML='💡 Tutorial';document.body.appendChild(a);}
    if(!document.querySelector('.dsg-theme-toggle')){var b=document.createElement('button');b.type='button';b.className='btn dsg-theme-toggle';b.innerHTML='🌙';b.title='Toggle dark mode';b.onclick=function(){document.body.classList.toggle('dsg-dark');localStorage.setItem('dsgTheme',document.body.classList.contains('dsg-dark')?'dark':'light')};document.body.appendChild(b);if(localStorage.getItem('dsgTheme')==='dark'){document.body.classList.add('dsg-dark')}}
    document.querySelectorAll('input[type="text"],input[type="password"],input[type="number"],input[type="date"],select,textarea').forEach(function(e){e.classList.add('form-control');});
  });
})();
