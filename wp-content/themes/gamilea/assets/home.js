(() => {
  const header = document.querySelector('.site-header');
  const navbar = header?.querySelector('.nav-shell');
  const topbar = header?.querySelector('.topbar');
  if (header && navbar && topbar) {
    const spacer = document.createElement('div');
    spacer.className = 'navbar-spacer';
    spacer.setAttribute('aria-hidden', 'true');
    navbar.before(spacer);
    let scheduled = false;
    const updateNavbar = () => {
      scheduled = false;
      const adminBar = document.getElementById('wpadminbar');
      const adminOffset = adminBar ? Math.max(0, adminBar.getBoundingClientRect().bottom) : 0;
      const offset = adminOffset + 12;
      const fixed = window.matchMedia('(min-width: 701px)').matches && topbar.offsetHeight > 0 && topbar.getBoundingClientRect().bottom <= offset;
      spacer.style.height = `${navbar.getBoundingClientRect().height}px`;
      header.style.setProperty('--navbar-top', `${offset}px`);
      header.classList.toggle('is-scrolled', fixed);
      document.documentElement.style.setProperty('--navigation-clearance', `${navbar.getBoundingClientRect().height + offset + 20}px`);
    };
    const scheduleNavbar = () => {
      if (!scheduled) { scheduled = true; requestAnimationFrame(updateNavbar); }
    };
    window.addEventListener('scroll', scheduleNavbar, { passive: true });
    window.addEventListener('resize', scheduleNavbar);
    new ResizeObserver(scheduleNavbar).observe(navbar);
    updateNavbar();
  }
  const menu = document.querySelector('.mobile-menu-toggle');
  menu?.addEventListener('click', () => { const open = menu.getAttribute('aria-expanded') !== 'true'; menu.setAttribute('aria-expanded', String(open)); document.querySelector('#primary-nav').classList.toggle('is-open', open); });
  const tabs = [...document.querySelectorAll('[data-collection]')];
  function selectCollection(name, updateURL = true) {
    if (!tabs.some(tab => tab.dataset.collection === name)) return;
    tabs.forEach(tab => { const active = tab.dataset.collection === name; tab.setAttribute('aria-selected', String(active)); tab.tabIndex = active ? 0 : -1; document.getElementById(tab.getAttribute('aria-controls')).hidden = !active; if(active) document.getElementById('collection-heading').textContent = tab.textContent; });
    if (updateURL) { const url = new URL(location.href); url.searchParams.set('collection', name); history.replaceState(null, '', url); }
  }
  tabs.forEach((tab, i) => { tab.addEventListener('click', () => selectCollection(tab.dataset.collection)); tab.addEventListener('keydown', e => { let next; if(e.key === 'ArrowRight') next = (i+1)%tabs.length; if(e.key === 'ArrowLeft') next = (i+tabs.length-1)%tabs.length; if(e.key === 'Home') next=0; if(e.key === 'End') next=tabs.length-1; if(next !== undefined) { e.preventDefault(); tabs[next].focus(); selectCollection(tabs[next].dataset.collection); } }); });
  selectCollection(new URLSearchParams(location.search).get('collection'), false);
  document.querySelectorAll('a[href*="collection="]').forEach(a => a.addEventListener('click', e => { const url=new URL(a.href); if(tabs.length && url.pathname===location.pathname) {e.preventDefault(); selectCollection(url.searchParams.get('collection')); document.getElementById('bestsellers').scrollIntoView({behavior:'smooth'});}}));
  let favorites = [];
  try { const saved = JSON.parse(localStorage.getItem('tienda-favorites') || '[]'); if(Array.isArray(saved)) favorites=saved.filter(x => x && typeof x.id === 'string' && typeof x.name === 'string' && typeof x.url === 'string' && typeof x.image === 'string' && x.url.startsWith(location.origin + '/') && x.image.startsWith(location.origin + '/')); } catch {}
  const status=document.getElementById('shop-status'); let timer;
  function announce(message) { status.textContent=message; status.classList.add('visible'); clearTimeout(timer); timer=setTimeout(()=>status.classList.remove('visible'),3200); }
  function syncFavorites() { document.querySelectorAll('.favorite-toggle').forEach(button => { const saved=favorites.some(p=>p.id===button.dataset.id); button.setAttribute('aria-pressed',String(saved)); }); try { localStorage.setItem('tienda-favorites', JSON.stringify(favorites)); } catch {} }
  document.addEventListener('click', e => { const button=e.target.closest('.favorite-toggle'); if(!button) return; const id=button.dataset.id; const exists=favorites.some(p=>p.id===id); if(exists) favorites=favorites.filter(p=>p.id!==id); else { const card=button.closest('.product-card'); favorites.push({id,name:card.dataset.productName || card.querySelector('h3').textContent,url:card.querySelector('h3 a').href,image:card.querySelector('.product-photo img').src,sprite:card.querySelector('.product-sprite')?.className || ''}); } syncFavorites(); announce(exists ? 'Producto eliminado de favoritos' : 'Producto guardado en favoritos'); });
  const dialog=document.getElementById('favorites-dialog');
  function renderFavorites() { const content=document.getElementById('favorites-content'); content.replaceChildren(); if(!favorites.length) {const p=document.createElement('p');p.textContent='Todavía no tienes favoritos. Toca el corazón de un producto para guardarlo.';content.append(p);} favorites.forEach(product=>{const row=document.createElement('div');row.className='favorite-item';const a=document.createElement('a');a.href=product.url;const img=document.createElement('img');img.src=product.image;img.alt='';const title=document.createElement('span');title.textContent=product.name;if(typeof product.sprite==='string' && /^product-sprite sprite-[0-4]$/.test(product.sprite)){const crop=document.createElement('span');crop.className=product.sprite;crop.append(img);a.append(crop,title);}else{a.append(img,title);}const remove=document.createElement('button');remove.textContent='Quitar';remove.addEventListener('click',()=>{favorites=favorites.filter(p=>p.id!==product.id);syncFavorites();renderFavorites();});row.append(a,remove);content.append(row);}); }
  document.querySelector('.open-favorites')?.addEventListener('click',()=>{renderFavorites();dialog.showModal();});
  document.querySelector('.close-favorites')?.addEventListener('click',()=>dialog.close());
  dialog?.addEventListener('click',e=>{if(e.target===dialog){const rect=dialog.getBoundingClientRect();if(e.clientX<rect.left||e.clientX>rect.right||e.clientY<rect.top||e.clientY>rect.bottom)dialog.close();}});
  syncFavorites();
  if(window.jQuery) window.jQuery(document.body).on('added_to_cart',()=>announce('Producto agregado al carrito'));
})();
