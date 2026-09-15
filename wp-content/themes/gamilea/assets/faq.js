/** Filtrado de preguntas por categoría. Sin JS la página muestra todas, que es el estado por defecto. */
(function () {
    var tabs = document.querySelectorAll('.faq-tab');
    var items = document.querySelectorAll('.faq-item');
    var empty = document.querySelector('.faq-empty');
    if (!items.length) { return; }

    /**
     * La exclusividad del acordeón la da el atributo name de <details>, que es nativo.
     * Este respaldo solo entra en navegadores que aún no lo soportan.
     */
    if (!('name' in document.createElement('details'))) {
        items.forEach(function (item) {
            item.addEventListener('toggle', function () {
                if (!item.open) { return; }
                items.forEach(function (other) { if (other !== item) { other.open = false; } });
            });
        });
    }

    /**
     * Toda la tarjeta abre y cierra, no solo el <summary>. Es un extra para ratón: el
     * summary sigue siendo el control real, así que el teclado y los lectores de
     * pantalla no cambian. Se ignoran los clics sobre el propio summary (ya alterna
     * solo), sobre enlaces de la respuesta, y los que terminan una selección de texto.
     */
    items.forEach(function (item) {
        item.addEventListener('click', function (event) {
            if (event.target.closest('summary, a, button')) { return; }
            if (String(window.getSelection())) { return; }
            item.open = !item.open;
        });
    });

    if (!tabs.length) { return; }

    function apply(category) {
        var visible = 0;
        items.forEach(function (item) {
            var match = !category || (item.dataset.categories || '').split(' ').indexOf(category) !== -1;
            item.hidden = !match;
            if (match) { visible++; }
        });
        if (empty) { empty.hidden = visible > 0; }
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (other) { other.setAttribute('aria-pressed', String(other === tab)); });
            apply(tab.dataset.category);
        });
    });
})();
