# Documentación de API para Desarrolladores

**Auto Redirect 404** está construido pensando en la extensibilidad. Utiliza una arquitectura modular de "Grupos y Motores", permitiendo a los desarrolladores inyectar lógica de búsqueda personalizada, priorizar motores específicos o alterar completamente el flujo de redirección.

---

## Conceptos Principales

### Motores (Engines)
Un **Motor** es una unidad individual de lógica de búsqueda. Por ejemplo, el motor `default_post` busca una Entrada de WordPress que coincida con las palabras clave en la URL.

### Grupos (Groups)
Un **Grupo** es una colección de Motores organizados en una "Secuencia de Ejecución" (Fire Sequence). Cuando ocurre un error 404, el plugin selecciona un Grupo (generalmente el grupo 'default') y ejecuta sus motores en orden hasta encontrar una coincidencia.

---

## Ejemplos de Código

### 1. Registrar un Grupo Personalizado

Puede definir un nuevo grupo con un conjunto específico de motores.

```php
add_action('ar404/search/init', 'register_my_custom_group');

function register_my_custom_group($query) {
    ar404_register_group(array(
        'name'    => 'Grupo E-commerce',
        'slug'    => 'ecommerce_group',
        'engines' => array(
            'product_sku_search', // Motor personalizado
            'default_post',       // Motor integrado
            'default_term'        // Motor integrado
        )
    ));
}
```

### 2. Registrar un Motor Personalizado

Cree unidades lógicas que busquen contenido de formas específicas (ej. buscar por SKU, buscar en una API externa, etc.).

```php
add_action('ar404/search/init', 'register_sku_engine');

function register_sku_engine($query) {
    ar404_register_engine(array(
        'name'    => 'Búsqueda por SKU',
        'slug'    => 'product_sku_search',
        'weight'  => 100, // Multiplicador de puntuación si se encuentra
        'primary' => true // Detener búsqueda si este motor encuentra algo
    ));
}

// Definir la lógica
add_filter('ar404/search/engine/product_sku_search', 'logic_sku_search', 10, 3);

function logic_sku_search($result, $query, $group) {
    // Extraer palabras clave de la URL
    $potential_sku = $query['request']['keywords']['all'];

    // Consulta personalizada a BD para encontrar producto por SKU
    $product_id = my_custom_find_product_by_sku($potential_sku);

    if ($product_id) {
        return array(
            'score' => 100,
            'url'   => get_permalink($product_id),
            'why'   => "Producto encontrado con SKU coincidente: $potential_sku"
        );
    }

    return false; // Nada encontrado
}
```

### 3. Disparar un Grupo Específico

Puede cambiar condicionalmente qué Grupo se utiliza basándose en la URL solicitada.

```php
add_filter('ar404/search/group', 'trigger_ecommerce_group', 10, 2);

function trigger_ecommerce_group($group, $query) {
    // Si la URL contiene '/shop/' o '/producto/', usar nuestro grupo personalizado
    if (strpos($query['request']['url'], '/shop/') !== false) {
        return 'ecommerce_group';
    }
    return $group; // Retornar por defecto
}
```

---

## Hooks Disponibles

### Acciones (Actions)

*   `ar404/search/init` - Se dispara durante la inicialización del plugin. Úselo para registrar grupos y motores.
*   `ar404/after_redirect` - Se dispara inmediatamente después de realizar una redirección (útil para seguimiento/analytics).

### Filtros (Filters)

*   `ar404/init` - Retorne `false` para deshabilitar completamente el plugin para la solicitud actual.
*   `ar404/search/group` - Modifique qué Grupo es seleccionado para la solicitud actual.
*   `ar404/search/query` - Modifique los argumentos de consulta analizados antes de comenzar la búsqueda.
*   `ar404/search/results` - Filtre los resultados finales antes de elegir al ganador.
*   `ar404/search/redirect` - Última oportunidad para modificar la URL de redirección o las cabeceras.

---
[Volver al README](../README.es.md)
