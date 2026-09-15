/* Keep the original selects as the source of form values and WooCommerce events. */
(() => {
  document.querySelectorAll('.gamilea-category-filter select, .woocommerce-ordering select').forEach((select, index) => {
    const wrapper = document.createElement('div');
    wrapper.className = 'shop-select';
    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = 'shop-select-trigger';
    trigger.id = `shop-select-trigger-${index}`;
    trigger.setAttribute('aria-haspopup', 'listbox');
    trigger.setAttribute('aria-expanded', 'false');
    const list = document.createElement('ul');
    list.className = 'shop-select-options';
    list.id = `shop-select-options-${index}`;
    list.setAttribute('role', 'listbox');
    list.setAttribute('aria-labelledby', trigger.id);
    list.hidden = true;
    trigger.setAttribute('aria-controls', list.id);
    const label = select.labels?.[0]?.textContent.trim() || select.getAttribute('aria-label') || 'Ordenar productos';
    const options = Array.from(select.options).map(option => {
      const item = document.createElement('li');
      item.setAttribute('role', 'option');
      item.setAttribute('aria-disabled', String(option.disabled));
      item.tabIndex = -1;
      item.textContent = option.text;
      item.addEventListener('click', () => {
        if (option.disabled) return;
        select.value = option.value;
        sync();
        close();
        // WooCommerce listens for change through jQuery to submit sorting.
        window.jQuery(select).trigger('change');
      });
      list.append(item);
      return item;
    });
    const sync = () => {
      trigger.textContent = select.options[select.selectedIndex]?.text || label;
      trigger.setAttribute('aria-label', `${label}: ${trigger.textContent}`);
      options.forEach((item, i) => item.setAttribute('aria-selected', String(i === select.selectedIndex)));
    };
    const close = (restoreFocus = true) => {
      list.hidden = true;
      trigger.setAttribute('aria-expanded', 'false');
      if (restoreFocus) trigger.focus();
    };
    const open = () => {
      list.hidden = false;
      trigger.setAttribute('aria-expanded', 'true');
      (options[select.selectedIndex] || options[0])?.focus();
    };
    trigger.addEventListener('click', () => list.hidden ? open() : close());
    trigger.addEventListener('keydown', event => {
      if (['ArrowDown', 'ArrowUp'].includes(event.key)) { event.preventDefault(); open(); }
    });
    let query = '';
    let queryTimer;
    list.addEventListener('keydown', event => {
      const enabled = options.filter(item => item.getAttribute('aria-disabled') !== 'true');
      const current = enabled.indexOf(document.activeElement);
      let next;
      if (event.key === 'ArrowDown') next = enabled[(current + 1) % enabled.length];
      if (event.key === 'ArrowUp') next = enabled[(current - 1 + enabled.length) % enabled.length];
      if (event.key === 'Home') next = enabled[0];
      if (event.key === 'End') next = enabled[enabled.length - 1];
      if (next) { event.preventDefault(); next.focus(); }
      if (event.key === 'Escape') { event.preventDefault(); close(); }
      if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); document.activeElement.click(); }
      if (event.key === 'Tab') close();
      if (event.key.length === 1 && event.key !== ' ' && !event.ctrlKey && !event.metaKey && !event.altKey) {
        query += event.key.toLocaleLowerCase();
        clearTimeout(queryTimer);
        queryTimer = setTimeout(() => { query = ''; }, 500);
        enabled.find(item => item.textContent.toLocaleLowerCase().startsWith(query))?.focus();
      }
    });
    document.addEventListener('pointerdown', event => { if (!wrapper.contains(event.target)) close(false); });
    wrapper.addEventListener('focusout', event => { if (!wrapper.contains(event.relatedTarget)) close(false); });
    select.addEventListener('change', sync);
    select.form?.addEventListener('reset', () => setTimeout(sync, 0));
    select.before(wrapper);
    wrapper.append(select, trigger, list);
    select.hidden = true;
    select.labels?.forEach(element => element.addEventListener('click', event => { event.preventDefault(); trigger.focus(); }));
    sync();
  });
})();
