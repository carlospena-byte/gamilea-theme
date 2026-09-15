(() => {
  const nav = document.querySelector('.legal-index nav');
  if (!nav) return;
  const headings = Array.from(document.querySelectorAll('.legal-content h2'));
  const links = headings.map((heading, index) => {
    if (!heading.id) heading.id = `seccion-${index + 1}`;
    const link = document.createElement('a');
    link.href = `#${heading.id}`;
    link.textContent = heading.textContent;
    nav.append(link);
    return link;
  });
  if (!links.length) return;

  let current = -1;
  const setActive = (index) => {
    if (index === current) return;
    if (links[current]) links[current].removeAttribute('aria-current');
    if (links[index]) links[index].setAttribute('aria-current', 'true');
    current = index;
  };

  const update = () => {
    const offset = 120;
    let index = 0;
    headings.forEach((heading, i) => {
      if (heading.getBoundingClientRect().top <= offset) index = i;
    });
    // La última sección se marca al llegar al final de la página aunque su título quede arriba.
    if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 2) index = headings.length - 1;
    setActive(index);
  };

  let ticking = false;
  const onScroll = () => {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(() => { ticking = false; update(); });
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onScroll, { passive: true });
  update();
})();
