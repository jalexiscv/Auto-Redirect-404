# Auto Redirect 404

[![Read in English](https://img.shields.io/badge/Read_in-English-blue)](README.md)
![Licencia](https://img.shields.io/badge/license-GPLv2-blue.svg)
![Versión](https://img.shields.io/badge/version-1.0.6-green.svg)
![Probado hasta](https://img.shields.io/badge/tested_up_to-WordPress_6.8-brightgreen.svg)

**La solución definitiva para gestionar errores 404 en WordPress. Redirecciona automáticamente a los visitantes perdidos hacia contenido similar utilizando algoritmos de coincidencia inteligente.**

---

## 📖 Introducción

**Auto Redirect 404** es un plugin de WordPress robusto y orientado al rendimiento, diseñado para mejorar la Experiencia de Usuario (UX) y el Posicionamiento en Buscadores (SEO). Cuando un visitante encuentra un error de "Página no encontrada" (404), este plugin analiza la URL solicitada y redirige inteligentemente al usuario hacia el contenido existente más relevante de su sitio.

En lugar de perder un visitante en un callejón sin salida, Auto Redirect 404 lo guía suavemente hacia la entrada, página o término que probablemente estaba buscando, reduciendo la tasa de rebote y reteniendo el tráfico.

---

## 🚀 Características Clave

### 🧠 Motor de Coincidencia Inteligente
El núcleo de Auto Redirect 404 es su avanzado algoritmo de búsqueda, que considera:
*   **Análisis de Título**: Escanea sus publicaciones en busca de palabras clave encontradas en la URL 404.
*   **Contexto de Tipo de Post**: Detecta si la URL sigue una estructura de tipo de publicación específica.
*   **Lógica de Taxonomía**: Identifica categorías o etiquetas potenciales para encontrar contenido relacionado.

### ⚙️ Comportamiento Personalizable (Fallback)
Usted tiene control total sobre qué sucede cuando no se encuentra contenido similar:
*   **Redirigir al Inicio**: Envíe a los visitantes a su página principal.
*   **URL Personalizada**: Defina una página de destino específica (ej. una página de búsqueda personalizada o mapa del sitio).
*   **404 Predeterminado**: Mantenga el comportamiento estándar si lo prefiere.

### 🛠️ Capacidades Técnicas
*   **Códigos de Estado**: Elija entre **301 (Movido Permanentemente)** para valor SEO o **302 (Encontrado)** para cambios temporales.
*   **Reglas de Exclusión**: Evite que Tipos de Post o Taxonomías específicas sean objetivos de redirección.
*   **Control Meta**: Use el campo meta `ar404_no_redirect` para excluir entradas o términos específicos individualmente.
*   **Registro (Logging)**: Mantenga un rastro de auditoría detallado de cada redirección en su archivo `/wp-content/debug.log`.
*   **No Intrusivo**: Optimizado para velocidad, no guarda **ningún** dato inútil en sus tablas de base de datos.

---

## 💾 Instalación y Configuración

1.  **Descargar e Instalar**: 
    *   Suba la carpeta del plugin a `/wp-content/plugins/auto-redirect-404-similar-post`.
    *   O instálelo directamente a través del panel de Plugins de WordPress.
2.  **Activar**: Habilite el plugin.
3.  **Configurar**: Navegue a **Ajustes > Auto Redirect 404**.
    *   Establezca su comportamiento de "Fallback" preferido.
    *   Revise la configuración de exclusiones si es necesario.
4.  **Probar**: Visite una URL inexistente (ej. `sudominio.com/probando-pagina-perdida`) para ver la magia en acción.

---

## 💻 API para Desarrolladores

Para usuarios avanzados y desarrolladores, Auto Redirect 404 ofrece una API completa para engancharse (hooks) a su lógica, crear motores de búsqueda personalizados o modificar comportamientos de redirección programáticamente.

👉 **[Leer la Documentación Completa para Desarrolladores](docs/DEVELOPER.es.md)**

*   Crear Grupos de Búsqueda Personalizados
*   Registrar Nuevos Motores de Búsqueda
*   Modificar Secuencias de Ejecución
*   Hooks para Eventos de Redirección

---

## 🤝 Soporte y Contribuciones

¡Damos la bienvenida a contribuciones para mejorar Auto Redirect 404!
Por favor revise nuestras **[Guías de Contribución](CONTRIBUTING.md)** (Próximamente).

Si encuentra algún problema, por favor revise los [Foros de Soporte](https://wordpress.org/support/plugin/auto-redirect-404-similar-post/) o abra un issue en GitHub.

---

## 👨‍💻 Autor

**Jose Alexis Correa Valencia**  
*Full Stack Developer & Software Architect*

*   **GitHub**: [@jalexiscv](https://github.com/jalexiscv)
*   **Email**: jalexiscv@gmail.com
*   **Ubicación**: Colombia

---

## ❤️ Donaciones

Si este plugin le ha ayudado a usted o a su negocio, por favor considere hacer una pequeña donación para apoyar su desarrollo continuo y mantenimiento.

| Método | Detalles |
| :--- | :--- |
| **PayPal** | [jalexiscv@gmail.com](https://paypal.me/jalexiscv) |
| **Nequi (Colombia)** | `3117977281` |

*¡Gracias por su apoyo!*
