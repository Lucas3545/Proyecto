# 🤖 Guía de Integración de Inteligencia Artificial
## Luke's House Casa Tranquila

Esta guía te ayudará a integrar y configurar las funcionalidades de IA en tu sitio web.

---

## 📋 Tabla de Contenidos

1. [Características de IA](#características-de-ia)
2. [Requisitos](#requisitos)
3. [Instalación](#instalación)
4. [Configuración](#configuración)
5. [Uso](#uso)
6. [Personalización](#personalización)
7. [Solución de Problemas](#solución-de-problemas)
8. [Costos](#costos)

---

## ✨ Características de IA

### 1. **Chatbot Inteligente**
- Asistencia 24/7 para visitantes
- Responde preguntas sobre la propiedad
- Ayuda con el proceso de reserva
- Proporciona información turística de La Fortuna
- Mantiene contexto de conversación
- Interfaz moderna y responsive

### 2. **Sistema de Recomendaciones Personalizadas**
- Sugiere actividades basadas en preferencias del usuario
- Filtra por nivel de dificultad y presupuesto
- Genera itinerarios personalizados
- Recomendaciones enriquecidas con IA
- Widget interactivo fácil de usar

---

## 📦 Requisitos

### Archivos Necesarios
Los siguientes archivos ya están creados en tu proyecto:

```
js/
├── ai-chatbot.js           # Lógica del chatbot
├── ai-recommendations.js   # Sistema de recomendaciones
└── ai-config.js           # Configuración central

css/
├── ai-chatbot.css         # Estilos del chatbot
└── ai-recommendations.css # Estilos de recomendaciones
```

### Dependencias Externas
- **OpenAI API**: Para las funcionalidades de IA
- **Font Awesome** (opcional): Para iconos mejorados

---

## 🚀 Instalación

### Paso 1: Obtener API Key de OpenAI

1. Ve a [OpenAI Platform](https://platform.openai.com/api-keys)
2. Crea una cuenta o inicia sesión
3. Navega a "API Keys"
4. Haz clic en "Create new secret key"
5. Copia la API key (empieza con `sk-...`)

⚠️ **IMPORTANTE**: Guarda tu API key en un lugar seguro. No la compartas públicamente.

### Paso 2: Configurar la API Key

Abre el archivo [`js/ai-config.js`](js/ai-config.js) y reemplaza:

```javascript
OPENAI_API_KEY: 'TU_API_KEY_AQUI',
```

Por:

```javascript
OPENAI_API_KEY: 'sk-tu-api-key-real-aqui',
```

### Paso 3: Integrar en tus Páginas HTML

Agrega estos enlaces en el `<head>` de tus páginas HTML:

```html
<!-- Estilos de IA -->
<link rel="stylesheet" href="./css/ai-chatbot.css">
<link rel="stylesheet" href="./css/ai-recommendations.css">

<!-- Font Awesome para iconos (opcional) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
```

Agrega estos scripts antes del cierre de `</body>`:

```html
<!-- Scripts de IA -->
<script src="./js/ai-chatbot.js"></script>
<script src="./js/ai-recommendations.js"></script>
<script src="./js/ai-config.js"></script>
```

---

## ⚙️ Configuración

### Configuración del Chatbot

En [`js/ai-config.js`](js/ai-config.js):

```javascript
CHATBOT: {
    enabled: true,              // Activar/desactivar chatbot
    maxHistoryMessages: 10,     // Mensajes en historial
    temperature: 0.7,           // Creatividad (0-1)
    maxTokens: 500             // Longitud de respuesta
}
```

### Configuración de Recomendaciones

```javascript
RECOMMENDATIONS: {
    enabled: true,              // Activar/desactivar recomendaciones
    maxRecommendations: 6,      // Número de recomendaciones
    useAIEnhancement: true     // Usar IA para mejorar
}
```

### Modelos Disponibles

- **gpt-3.5-turbo**: Más rápido y económico (recomendado)
- **gpt-4**: Más potente pero más costoso

```javascript
MODEL: 'gpt-3.5-turbo',
```

---

## 💻 Uso

### Chatbot

El chatbot se inicializa automáticamente y aparece como un botón flotante en la esquina inferior derecha.

**Características:**
- Clic en el botón para abrir/cerrar
- Escribe mensajes y presiona Enter o clic en enviar
- Botones de acción rápida para consultas comunes
- Historial de conversación mantenido

**Personalizar el contexto del chatbot:**

Edita en [`js/ai-chatbot.js`](js/ai-chatbot.js:21):

```javascript
this.systemContext = `Tu mensaje personalizado aquí...`;
```

### Sistema de Recomendaciones

#### Opción 1: Widget Automático

Agrega un contenedor en tu HTML:

```html
<div id="recommendations-widget"></div>
```

Inicializa el widget en tu JavaScript:

```javascript
// Después de que se cargue la página
document.addEventListener('DOMContentLoaded', function() {
    if (window.aiRecommendations) {
        window.aiRecommendations.createRecommendationWidget('recommendations-widget');
    }
});
```

#### Opción 2: Uso Programático

```javascript
// Obtener recomendaciones personalizadas
const preferences = {
    interests: ['aventura', 'naturaleza'],
    difficulty: 'media',
    budget: null
};

window.aiRecommendations.getPersonalizedRecommendations(preferences)
    .then(recommendations => {
        console.log(recommendations);
        // Renderizar recomendaciones
        window.aiRecommendations.renderRecommendations(
            recommendations, 
            'container-id'
        );
    });
```

#### Opción 3: Generar Itinerario

```javascript
// Generar itinerario de 3 días
const preferences = {
    interests: ['aventura', 'naturaleza', 'relajacion'],
    difficulty: 'media'
};

window.aiRecommendations.generateItinerary(3, preferences)
    .then(itinerary => {
        console.log(itinerary);
        // Mostrar itinerario
    });
```

---

## 🎨 Personalización

### Colores del Chatbot

Edita en [`css/ai-chatbot.css`](css/ai-chatbot.css):

```css
/* Cambiar colores del gradiente */
.chatbot-toggle {
    background: linear-gradient(135deg, #TU_COLOR_1 0%, #TU_COLOR_2 100%);
}
```

### Agregar Nuevas Atracciones

Edita en [`js/ai-recommendations.js`](js/ai-recommendations.js:15):

```javascript
this.attractions = {
    aventura: [
        {
            name: 'Nueva Atracción',
            description: 'Descripción aquí',
            difficulty: 'media',
            duration: '3-4 horas',
            price: '$40-60',
            image: './img/ruta/imagen.jpg'
        }
    ]
};
```

### Personalizar Mensajes

Edita en [`js/ai-config.js`](js/ai-config.js:24):

```javascript
MESSAGES: {
    apiKeyMissing: 'Tu mensaje personalizado',
    error: 'Tu mensaje de error',
    contactInfo: 'Tu información de contacto'
}
```

---

## 🔧 Solución de Problemas

### El chatbot no aparece

1. Verifica que los archivos CSS y JS estén correctamente enlazados
2. Abre la consola del navegador (F12) y busca errores
3. Verifica que [`ai-config.js`](js/ai-config.js) se cargue último

### La IA no responde

1. Verifica que tu API key esté correctamente configurada
2. Revisa la consola para errores de API
3. Verifica que tengas créditos en tu cuenta de OpenAI
4. Comprueba tu conexión a internet

### Error 401 (Unauthorized)

- Tu API key es inválida o ha expirado
- Genera una nueva API key en OpenAI

### Error 429 (Rate Limit)

- Has excedido tu límite de solicitudes
- Espera unos minutos o actualiza tu plan de OpenAI

### Las recomendaciones no se muestran

1. Verifica que el contenedor HTML exista
2. Revisa la consola para errores
3. Asegúrate de que las imágenes existan en las rutas especificadas

### Verificar Estado de la IA

Abre la consola del navegador y ejecuta:

```javascript
getAIStatus()
```

Para ver la guía de configuración:

```javascript
showAISetupHelp()
```

---

## 💰 Costos

### Precios de OpenAI (Aproximados)

**GPT-3.5-Turbo:**
- Input: $0.0015 por 1K tokens
- Output: $0.002 por 1K tokens

**GPT-4:**
- Input: $0.03 por 1K tokens
- Output: $0.06 por 1K tokens

### Estimación de Uso

**Chatbot:**
- Conversación promedio: ~500 tokens
- Costo por conversación (GPT-3.5): ~$0.001
- 1000 conversaciones: ~$1

**Recomendaciones:**
- Solicitud promedio: ~300 tokens
- Costo por solicitud (GPT-3.5): ~$0.0006
- 1000 solicitudes: ~$0.60

### Consejos para Reducir Costos

1. Usa GPT-3.5-Turbo en lugar de GPT-4
2. Limita el historial de conversación (`maxHistoryMessages`)
3. Reduce `maxTokens` para respuestas más cortas
4. Implementa caché para respuestas comunes
5. Usa filtros locales antes de llamar a la API

---

## 🔒 Seguridad

### Mejores Prácticas

1. **Nunca expongas tu API key en el código del cliente**
   - Para producción, usa un backend proxy
   - Implementa rate limiting
   - Valida solicitudes del lado del servidor

2. **Implementar Backend Proxy (Recomendado para Producción)**

Crea un archivo PHP [`PHP/ai-proxy.php`](PHP/ai-proxy.php):

```php
<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$apiKey = 'TU_API_KEY_AQUI'; // Guarda esto en un archivo de configuración seguro
$data = json_decode(file_get_contents('php://input'), true);

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);

$response = curl_exec($ch);
curl_close($ch);

echo $response;
?>
```

Luego modifica [`js/ai-chatbot.js`](js/ai-chatbot.js:165) para usar el proxy:

```javascript
this.apiEndpoint = './PHP/ai-proxy.php';
// Elimina el header de Authorization
```

---

## 📚 Recursos Adicionales

- [Documentación de OpenAI](https://platform.openai.com/docs)
- [Mejores prácticas de OpenAI](https://platform.openai.com/docs/guides/production-best-practices)
- [Límites de tasa de OpenAI](https://platform.openai.com/docs/guides/rate-limits)

---

## 🆘 Soporte

Si necesitas ayuda adicional:

- **Email**: lucaszv2006@gmail.com
- **Teléfono**: +506 8325 6836

---

## 📝 Notas Finales

- Las funcionalidades de IA funcionan sin API key pero con capacidades limitadas
- El chatbot mostrará información de contacto si no hay API key configurada
- Las recomendaciones funcionan con filtros locales sin necesidad de API
- Para mejor experiencia, configura la API key de OpenAI

---

**¡Disfruta de tu sitio web potenciado con IA! 🚀**
