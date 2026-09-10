# GA·MI·LEA · WordPress + WooCommerce

Entorno local Docker con WordPress (PHP 8.3 + Apache), MariaDB 11.4, WP-CLI y el tema propio **GA·MI·LEA**. Requiere Docker Desktop con Docker Compose y OpenSSL.

El tema requiere **WooCommerce** y **Advanced Custom Fields PRO** (incluido en `wp-content/plugins/advanced-custom-fields-pro`, montado igual que el tema). Sin ambos activos, el admin verá un aviso en el escritorio de WordPress y los bloques de la portada no funcionarán.

## Iniciar

```sh
./scripts/setup.sh
```

El script crea `.env` con contraseñas aleatorias, arranca los servicios, instala WordPress en español, instala y activa WooCommerce, activa Advanced Custom Fields PRO y el tema, y crea las páginas de tienda, carrito, checkout y cuenta. Puede ejecutarse otra vez sin reinstalar la base de datos ni cambiar contraseñas existentes. La primera ejecución requiere conexión para descargar imágenes, plugin y traducciones.

- Tienda: http://localhost:8080
- Administración: http://localhost:8080/wp-admin/
- Usuario y contraseña: `WP_ADMIN_USER` y `WP_ADMIN_PASSWORD` en `.env`.

Para personalizar nombre, correo o puerto antes de instalar, copia `.env.example` a `.env` y sustituye las tres contraseñas de ejemplo. Si cambias `WP_PORT`, ajusta también `WP_URL`. Las variables de instalación no actualizan un sitio ya instalado.

## Desarrollo

Edita `wp-content/themes/gamilea/`: los cambios se reflejan directamente, sin compilación. El tema incluye portada, navegación, plantillas generales y soporte para catálogo, galerías y páginas de WooCommerce. Usa las plantillas del plugin para evitar copias que queden obsoletas. Para una portada editable, crea una página en WordPress y selecciónala en Ajustes → Lectura. El logo y el menú se configuran desde Apariencia.

```sh
docker compose up -d               # Arrancar después de la instalación
docker compose stop               # Detener conservando datos
docker compose down               # Quitar contenedores conservando volúmenes
docker compose logs -f wordpress  # Ver registros
docker compose run --rm cli wp plugin list
```

El núcleo, plugins y subidas persisten en `wordpress_data`; la base de datos en `db_data`. El código del tema vive en el repositorio. **`docker compose down -v` elimina los datos de la tienda.**

## Versiones y puesta en marcha comercial

Los tags de WordPress siguen las actualizaciones de PHP 8.3 y WooCommerce se instala en su versión disponible al primer arranque (`WC_VERSION=latest`). Puedes fijar `WC_VERSION` antes de instalar; ejecutar setup de nuevo no actualiza un plugin existente. Para versiones reproducibles fija los tags/digests de las imágenes y una versión concreta del plugin después de validar la combinación.

Antes de vender configura país, moneda, impuestos, envíos y métodos de pago desde WooCommerce. No se crean productos ficticios ni se conectan pagos automáticamente. Este Compose es para desarrollo local: para producción prepara dominio, HTTPS, secretos, copias de seguridad, correo y desactiva depuración.

Referencias: [imagen oficial de WordPress](https://hub.docker.com/_/wordpress), [requisitos de WooCommerce](https://woocommerce.com/document/server-requirements/).

## Home y catálogo de demostración

El home reproduce la estructura del mockup: hero fotográfico, categorías, colecciones con pestañas, productos, pasos de compra, marcas, newsletter y footer. Incluye estilos para móvil, búsqueda WooCommerce, carrito AJAX y favoritos persistidos en el navegador. Los productos se administran desde Productos en WordPress.

Para cargar el catálogo en otra instalación local:

```sh
docker compose run --rm -v "$PWD/scripts/seed-demo.php:/tmp/seed-demo.php:ro" cli wp eval-file /tmp/seed-demo.php
```

El seed crea 12 productos identificados por SKU `DEMO-*`, cinco categorías y páginas informativas. Si se repite, actualiza esos productos y restablece su stock a 25; no toca productos con otros SKU. Configura envío gratuito en la zona general. Los productos, marcas asociadas y textos comerciales son ficticios. No habilita pasarelas de pago. La colección “Más vendidos” usa el orden editorial `_tienda_order` del catálogo demo, sin inventar ventas ni reseñas.

Las suscripciones se guardan localmente en opciones `tienda_subscriber_*`, con consentimiento y fecha; no se envía correo. Antes de producción debe conectarse el proveedor de campañas y completar los textos y canales legales. Las imágenes generadas están en `wp-content/themes/gamilea/assets/images/`; sus prompts están en `assets/image-prompts.md`.

## Diseño GA·MI·LEA (Figma)

El theme usa el archivo Figma `ZQzLKl6Kr2424FDjcvKX0h`: portada de escritorio `32:16` (1440 px) y móvil `40:2` (390 px), y las referencias de catálogo, producto, carrito, checkout y cuenta de sus páginas Desktop/Mobile.

- `assets/gamilea.css`: medidas, colores, tipografías y adaptación responsive de GA·MI·LEA.
- `assets/figma/`: imágenes y SVG originales descargados de Figma. Los recortes CSS conservan las transformaciones del diseño y no dependen de URLs temporales.
- `woocommerce/content-product.php`: tarjeta compartida entre portada y catálogo. `inc/gamilea-commerce.php` adapta presentación y filtros conservando el procesamiento de WooCommerce.
- Los productos demo usan los recortes fotográficos de Figma. Los productos comerciales conservan las imágenes administradas en WordPress. Precios, disponibilidad y reseñas proceden del catálogo; no se simulan las valoraciones del mockup.
- Carrito y checkout siguen usando los bloques oficiales, incluidos métodos de envío/pago y extensiones instaladas. La composición de estos bloques y los estados de cuenta dependen de su configuración y de los datos de la sesión; no son copias estáticas de los ejemplos Figma.
- Se conserva el consentimiento del newsletter. Los iconos sociales reproducen el diseño; no tienen destinos configurados.

La marca visible del encabezado y pie es GA·MI·LEA. El tema se llama GA·MI·LEA, su autor es Carlos Peña y su identificador técnico es `gamilea`. Las fuentes DM Sans y Marcellus se cargan desde Google Fonts.

Validación realizada: PHP sin errores de sintaxis; JavaScript válido; revisión visual de escritorio, móvil y tablet; búsqueda, filtro de categoría, pestañas, menú móvil, favoritos y agregado AJAX al carrito. Se retiró el producto agregado durante la prueba. A 390 px de contenido, las alturas de hero (582), categorías (709), productos (699), pasos (589), marcas (240) y banner de newsletter (379) coinciden con los frames de Figma. El checkout se verificó hasta la pantalla de compra: no hay métodos de pago habilitados y no se creó ningún pedido.
