(function(){
  function ready(fn){document.readyState==='loading'?document.addEventListener('DOMContentLoaded',fn):fn();}
  function debounce(fn, wait){var t;return function(){var ctx=this,args=arguments;clearTimeout(t);t=setTimeout(function(){fn.apply(ctx,args)},wait||350)}}
  function serialize(form){var data=new FormData(form);data.set('ajax','1');return new URLSearchParams(data).toString();}
  function setBusy(form,busy){form.classList.toggle('dsg-search-busy',busy);var btn=form.querySelector('button[type="submit"]');if(btn){btn.disabled=busy;btn.dataset.original=btn.dataset.original||btn.textContent;btn.textContent=busy?'Mencari...':btn.dataset.original;}}
  ready(function(){
    document.querySelectorAll('form.dsg-ajax-search').forEach(function(form){
      var target=document.querySelector(form.getAttribute('data-target'));
      var counter=document.querySelector(form.getAttribute('data-counter'));
      if(!target) return;
      var run=debounce(function(push){
        setBusy(form,true);
        var url=form.getAttribute('action')||location.pathname;
        var qs=serialize(form);
        fetch(url+'?'+qs,{headers:{'X-Requested-With':'XMLHttpRequest'}})
          .then(function(r){return r.json()})
          .then(function(data){
            if(!data.ok) throw new Error('Search failed');
            target.innerHTML=data.html;
            if(counter) counter.textContent='Menampilkan '+data.total+' data barang';
            if(push!==false){history.replaceState(null,'',url+'?'+new URLSearchParams(new FormData(form)).toString());}
          })
          .catch(function(){ if(counter) counter.textContent='Search gagal dimuat, coba tekan tombol Cari.'; })
          .finally(function(){setBusy(form,false)});
      },300);
      form.addEventListener('submit',function(e){e.preventDefault();run(true);});
      form.querySelectorAll('input[type="text"],input[type="date"],select').forEach(function(el){el.addEventListener('input',function(){run(true)});el.addEventListener('change',function(){run(true)});});
    });
  });
})();
