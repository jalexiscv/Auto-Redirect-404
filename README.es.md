# Auto Redirect 404

[![Read in English](https://img.shields.io/badge/Read_in-English-blue)](README.md)
![Licencia](https://img.shields.io/badge/license-GPLv2-blue.svg)
![Versión](https://img.shields.io/badge/version-1.0.6-green.svg)

**Redirecciona automáticamente cualquier página 404 a una entrada similar basándose en el Título, Tipo de Post y Taxonomías usando redirecciones 301 o 302.**

---

## Descripción

¡Bienvenido a **Auto Redirect 404**!

Este plugin redirecciona automáticamente las páginas de error 404 a publicaciones similares basándose en el Título, Tipos de Post y Taxonomías. Si no se encuentra nada similar, los visitantes serán redirigidos a la página de inicio o a una URL personalizada.

### Características
*   **Detección Automática de 404**: Captura los errores 404 inmediatamente.
*   **Búsqueda Inteligente**: Encuentra publicaciones similares basándose en:
    *   Título
    *   Tipo de Post (Post Type)
    *   Taxonomía
*   **Comportamiento por Defecto (Fallback)**: Si no se encuentra coincidencia:
    *   Redirigir al Inicio (Homepage)
    *   Redirigir a una URL personalizada
    *   Mostrar la página 404 por defecto
*   **Estado de Redirección**: Elige entre redirecciones **301 (Permanente)** o **302 (Temporal)**.
*   **Exclusiones**:
    *   Excluir Tipos de Post específicos.
    *   Excluir Taxonomías específicas.
    *   Excluir Entradas/Términos mediante meta campos personalizados.
*   **Depuración y Vista Previa**:
    *   Consola de Depuración (para Administradores).
    *   Vista previa de redirecciones desde el Panel de Administración.

### Nuevas Características (v1.0+)
*   **Motores y Grupos**: Personaliza tu propia lógica de búsqueda y coincidencia.
*   **Registro (Logging)**: Registra redirecciones en `/wp-content/debug.log`.
*   **Cabeceras**: Expone cabeceras `Auto-Redirect-404` para depuración.

---

## Instalación

1.  Sube los archivos del plugin al directorio `/wp-content/plugins/auto-redirect-404-similar-post`, o instálalo directamente desde la pantalla de plugins de WordPress.
2.  Activa el plugin desde la pantalla 'Plugins' en WordPress.
3.  Ve a **Ajustes > Auto Redirect 404** para configurar tus preferencias.
4.  ¡Listo! Intenta acceder a una URL inexistente para probarlo.

---

## API para Desarrolladores

Auto Redirect 404 utiliza lógica de cachés y grupos para extensibilidad.

### Crear un Grupo Personalizado
```php
add_action('ar404/search/init', 'my_404_group');
function my_404_group($query){
    ar404_register_group(array(
        'name' => 'Mi Grupo',
        'slug' => 'my_group',
        'engines' => array('default_post', 'default_fix_url')
    ));
}
```

### Crear un Motor Personalizado
```php
add_action('ar404/search/init', 'my_404_group_engine');
function my_404_group_engine($query){
    ar404_register_engine(array(
        'name' => 'Mi Motor',
        'slug' => 'my_engine',
        'weight' => 100,
        'primary' => true
    ));
    // Lógica de implementación vía filtro 'ar404/search/engine/my_engine'...
}
```

---

## Preguntas Frecuentes

**¿Es compatible con otros plugins de redirección?**
¡Sí! Funciona junto con Redirection, RankMath, Yoast, etc. Si no se encuentra una redirección manual, Auto Redirect 404 toma el control.

---

## Registro de Cambios (Changelog)
**1.0.6**
*   Corrección: Escapado del valor de prioridad en ajustes.
*   Corrección: Deprecación de creación de propiedades dinámicas en PHP 8.3.
*   Actualización: Versión probada hasta WordPress 6.8.

*(Ver registro completo en `readme.txt`)*
