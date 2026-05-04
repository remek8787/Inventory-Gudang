(function(){
  function ready(fn){document.readyState==='loading'?document.addEventListener('DOMContentLoaded',fn):fn();}
  ready(function(){
    document.querySelectorAll('table').forEach(function(t){ if(!t.parentElement.classList.contains('table-responsive')){var w=document.createElement('div');w.className='table-responsive';t.parentNode.insertBefore(w,t);w.appendChild(t);} t.classList.add('table-hover'); });
    if(!document.querySelector('.dsg-fab-help')){var a=document.createElement('a');a.href=(location.pathname.includes('/')?'':'')+'/admin_tutorial.php';a.className='btn dsg-fab-help';a.innerHTML='💡 Tutorial Admin';document.body.appendChild(a);}
    document.querySelectorAll('input[type="text"],input[type="password"],input[type="number"],input[type="date"],select,textarea').forEach(function(e){e.classList.add('form-control');});
  });
})();
