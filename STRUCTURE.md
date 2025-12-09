# Estructura de Archivos del Plugin

Este documento detalla la organización de archivos y directorios del plugin **Auto Redirect 404 to Similar Post**.

## Raíz del Plugin

- **`auto-redirect-404.php`**: Archivo principal del plugin.
  - Define la clase core `WP_404_Auto_Redirect`.
  - Inicializa el plugin, carga dependencias, y se engancha a `template_redirect` para interceptar errores 404.
  - Orquesta el flujo de ejecución: `request()` -> `search()` -> `redirect()`.

## Directorio `class/` (Lógica de Negocio)

Este directorio contiene las clases que definen las reglas y el comportamiento del motor de redirección.

- **`class-engines.php`** (`WP_404_Auto_Redirect_Engines`):
  - Registra y gestiona los algoritmos de búsqueda ("Motores").
  - Implementa lógica como:
    - `default_fix_url`: Corrige URLs mal formadas o paginación.
    - `default_direct`: Coincidencia exacta de slug.
    - `default_post`: Búsqueda de posts similares por título/palabras clave.
    - `default_term`: Búsqueda de términos de taxonomía similares.

- **`class-groups.php`** (`WP_404_Auto_Redirect_Groups`):
  - Organiza los motores en grupos ordenados.
  - Por defecto existe el grupo 'Default' con una lista priorizada de motores.

- **`class-search.php`** (`WP_404_Auto_Redirect_Search`):
  - Ejecuta las consultas de búsqueda reales.

- **`class-settings.php`** (`WP_404_Auto_Redirect_Settings`):
  - Gestiona la configuración global del plugin.

## Directorio `includes/` (Funciones Auxiliares)

- **`admin.php`**: Funcionalidad del panel de administración WP.
- **`ajax.php`**: Gestión de peticiones AJAX (ej. vista previa).
- **`debug.php`**: Funciones de logging y depuración.
- **`helpers.php`**: Funciones de utilidad global (`ar404_...`).
