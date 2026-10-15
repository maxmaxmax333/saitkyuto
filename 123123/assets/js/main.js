(function(){
  document.querySelectorAll('.mk-slider').forEach(function(s){
    var slides=s.querySelectorAll('.mk-slide'),dots=s.querySelectorAll('.mk-dots button'),i=0,t;
    if(!slides.length)return;
    function go(n){
      slides[i].classList.remove('is-active');if(dots[i])dots[i].classList.remove('is-active');
      i=(n+slides.length)%slides.length;
      slides[i].classList.add('is-active');if(dots[i])dots[i].classList.add('is-active');
    }
    function start(){stop();t=setInterval(function(){go(i+1)},6000)}
    function stop(){if(t)clearInterval(t)}
    var p=s.querySelector('.mk-prev'),n=s.querySelector('.mk-next');
    if(p)p.addEventListener('click',function(){go(i-1);start()});
    if(n)n.addEventListener('click',function(){go(i+1);start()});
    dots.forEach(function(d,k){d.addEventListener('click',function(){go(k);start()})});
    s.addEventListener('mouseenter',stop);s.addEventListener('mouseleave',start);
    start();
  });
  var b=document.querySelector('.mk-burger'),h=document.querySelector('.mk-header');
  if(b&&h)b.addEventListener('click',function(){h.classList.toggle('is-open');b.setAttribute('aria-expanded',h.classList.contains('is-open'))});
})();
