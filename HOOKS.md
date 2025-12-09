# Referencia de Hooks y Filtros

Este plugin es altamente extensible a través de la API de Hooks de WordPress. A continuación se listan los principales puntos de enganche.

## Filtros (Filters)

### Inicialización y Contexto
- **`ar404/init`**
  - **Argumentos**: `bool $init`, `array $query`
  - **Propósito**: Permite cancelar la ejecución del plugin retornando `false` antes de iniciar la búsqueda.

- **`ar404/search/query`**
  - **Argumentos**: `array $query`
  - **Propósito**: Modificar los datos de la consulta (keywords, settings, request data) antes de buscar.

### Configuración de Búsqueda
- **`ar404/search/group`**
  - **Argumentos**: `string $group_slug`, `array $query`
  - **Propósito**: Cambiar dinámicamente qué grupo de motores se debe de usar. Por defecto es `'default'`.

### Motores
- **`ar404/search/engine/{slug}`**
  - **Argumentos**: `false|array $result`, `array $query`, `array $group`
  - **Dinámico**: `{slug}` corresponde al slug del motor (ej. `default_post`).
  - **Propósito**: Interceptar o modificar el resultado de un motor específico. O implementar lógica personalizada para un motor propio.

- **`ar404/search/results`**
  - **Argumentos**: `array $search_results`, `array $query`
  - **Propósito**: Filtrar el array completo de resultados encontrados por todos los motores antes de decidir el ganador.

### Redirección
- **`ar404/search/redirect`**
  - **Argumentos**: `array $redirect`, `array $query`
  - **Propósito**: Modificar el resultado ganador final (o el fallback) justo antes de procesar la redirección.

- **`ar404/redirect`**
  - **Argumentos**: `array $query`
  - **Propósito**: Última oportunidad para alterar todo el objeto de consulta antes de realizar el salto HTTP.

## Acciones (Actions)

- **`ar404/search/init`**
  - **Argumentos**: `array $query`
  - **Propósito**: Se dispara justo antes de empezar a iterar por los motores. Útil para registrar motores o grupos dinámicamente.

- **`ar404/after_redirect`**
  - **Argumentos**: `array $query`
  - **Propósito**: Se ejecuta **después** de enviar las cabeceras de redirección, pero antes del `exit`. Útil para logging personalizado o tracking.
