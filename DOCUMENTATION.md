# Documentación Técnica: Auto Redirect 404 to Similar Post

## Visión General
Este plugin intercepta las peticiones que resultan en un error 404, analiza la URL solicitada, extrae palabras clave y busca contenido similar (Posts, Taxonomías) en la base de datos para redirigir al usuario automáticamente mediante una redirección 301.

## Flujo de Ejecución

Todo el proceso comienza en la clase `WP_404_Auto_Redirect`.

1.  **Inicialización (`init`)**:
    - Se registra el hook `template_redirect` con prioridad 999 (o configurable).
    - Se cargan las clases de ayudantes y lógica.

2.  **Intercepción (`run`)**:
    - Se verifica si es una página 404 (`is_404()`).
    - Se ignora si es una petición AJAX, Admin, o XMLRPC.
    - Se sanea la URL solicitada.

3.  **Análisis de Petición (`request`)**:
    - Se descompone la URL para extraer "Keywords" (palabras clave).
    - Se detectan posibles variables de WP Query implícitas (Post Type, Taxonomía).
    - Se prepara un array `$query` masivo que contiene todo el contexto.

4.  **Búsqueda (`search`)**:
    - Se obtienen los **Grupos** de búsqueda activos.
    - Se itera sobre cada grupo y sus **Motores** configurados.
    - Cada motor devuelve un resultado con: `url`, `score` (puntuación), y `why` (razón).
    - **Lógica de Ganador**: Se escoge el resultado con el `score` más alto. Si un motor marcado como `primary` devuelve un resultado positivo, se detiene la búsqueda inmediatamente.

5.  **Redirección (`redirect`)**:
    - Si se encontró un destino (o fallback), se ejecuta la redirección.
    - Se añaden cabeceras HTTP personalizadas de depuración si el usuario es administrador (`Auto-Redirect-404-Score`, etc.).

## Motores de Búsqueda (Engines)

El plugin viene con varios motores por defecto definidos en `class-engines.php`:

### 1. Default: Fix URL (`default_fix_url`)
- **Propósito**: Corregir errores técnicos simples en la URL.
- **Lógica**: Detecta patrones de paginación rota o parámetros mal formados y los limpia.

### 2. Default: Direct Match (`default_direct`)
- **Propósito**: Encontrar un Post que coincida exactamente con el slug final de la URL.
- **Lógica**: Usa `get_page_by_path()` con la última parte de la URL.

### 3. Default: Search Post (`default_post`)
- **Propósito**: Buscar el contenido más similar basado en palabras clave.
- **Lógica**: Realiza una búsqueda (similar a la búsqueda nativa de WP) usando las palabras clave extraídas de la URL rota. Busca en los Post Types permitidos.

### 4. Default: Search Term (`default_term`)
- **Propósito**: Buscar términos de taxonomía (categorías, etiquetas) similares.
- **Lógica**: Similar al anterior, pero busca en las taxonomías permitidas.

### 5. Default: Post Fallback (`default_post_fallback`)
- **Propósito**: Si WP detectó un Post Type pero no encontró el post específico, redirigir al archivo (Archive) de ese Post Type.
