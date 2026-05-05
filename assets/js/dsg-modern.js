(function(){
  function ready(fn){document.readyState==='loading'?document.addEventListener('DOMContentLoaded',fn):fn();}
  function setThemeIcon(btn){btn.innerHTML=document.body.classList.contains('dsg-dark')?'☀️ Light':'🌙 Dark';}
  function setPanelIcon(btn){btn.innerHTML=document.body.classList.contains('dsg-sidebar-hidden')?'☰ Tampilkan Panel':'☰ Sembunyikan Panel';}
  ready(function(){
    document.querySelectorAll('table').forEach(function(t){
      if(!t.parentElement.classList.contains('table-responsive')){
        var w=document.createElement('div');w.className='table-responsive';t.parentNode.insertBefore(w,t);w.appendChild(t);
      }
      t.classList.add('table-hover');
    });

    if(localStorage.getItem('dsgTheme')==='dark'){document.body.classList.add('dsg-dark');}
    if(localStorage.getItem('dsgSidebar')==='hidden'){document.body.classList.add('dsg-sidebar-hidden');}

    if(document.body.classList.contains('dsg-login-body')){ return; }

    if(!document.querySelector('.dsg-top-tools')){
      var bar=document.createElement('div');
      bar.className='dsg-top-tools';

      var tutorial=document.createElement('a');
      tutorial.href='/admin_tutorial.php';
      tutorial.className='btn dsg-top-tool-btn';
      tutorial.innerHTML='💡 Tutorial';

      var theme=document.createElement('button');
      theme.type='button';
      theme.className='btn dsg-top-tool-btn';
      theme.title='Toggle dark mode';
      setThemeIcon(theme);
      theme.onclick=function(){
        document.body.classList.toggle('dsg-dark');
        localStorage.setItem('dsgTheme',document.body.classList.contains('dsg-dark')?'dark':'light');
        setThemeIcon(theme);
      };

      var panel=document.createElement('button');
      panel.type='button';
      panel.className='btn dsg-top-tool-btn';
      panel.title='Sembunyikan / tampilkan sidebar panel';
      setPanelIcon(panel);
      panel.onclick=function(){
        document.body.classList.toggle('dsg-sidebar-hidden');
        localStorage.setItem('dsgSidebar',document.body.classList.contains('dsg-sidebar-hidden')?'hidden':'shown');
        setPanelIcon(panel);
      };

      bar.appendChild(tutorial);
      bar.appendChild(theme);
      bar.appendChild(panel);
      document.body.insertBefore(bar, document.body.firstChild);
    }

    document.querySelectorAll('input[type="text"],input[type="password"],input[type="number"],input[type="date"],select,textarea').forEach(function(e){e.classList.add('form-control');});
  });
})();
