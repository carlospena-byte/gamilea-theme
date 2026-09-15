(() => {
  const nav = document.querySelector('.legal-index nav');
  if (!nav) return;
  document.querySelectorAll('.legal-content h2').forEach((heading, index) => {
    if (!heading.id) heading.id = `seccion-${index + 1}`;
    const link = document.createElement('a');
    link.href = `#${heading.id}`;
    link.textContent = heading.textContent;
    nav.append(link);
  });
})();
