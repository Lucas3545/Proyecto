# Inteligencia Artificial - Luke's House Casa Tranquila

## Resumen Rápido

Tu sitio web ahora cuenta con **dos funcionalidades principales de IA**:

### 1. Chatbot Inteligente
Un asistente virtual que aparece en la esquina inferior derecha de todas las páginas donde esté integrado.

**Funciones:**
- Responde preguntas sobre la propiedad
- Ayuda con el proceso de reserva
- Proporciona información turística de La Fortuna
- Mantiene contexto de conversación
- Disponible 24/7

### 2. Sistema de Recomendaciones
Un sistema inteligente que sugiere actividades y crea itinerarios personalizados.

**Funciones:**
- Recomendaciones basadas en preferencias
- Filtros por dificultad y tipo de actividad
- Generación de itinerarios multi-día
- Enriquecimiento con IA (opcional)

---

## Inicio Rápido (5 minutos)

### Paso 1: Obtener API Key
1. Ve a https://platform.openai.com/api-keys
2. Crea una cuenta o inicia sesión
3. Crea una nueva API key
4. Copia la key (empieza con `sk-...`)

### Paso 2: Configurar
1. Abre [`js/ai-config.js`](js/ai-config.js)
2. Reemplaza `'TU_API_KEY_AQUI'` con tu API key real
3. Guarda el archivo

### Paso 3: ¡Listo!
Recarga tu página y verás:
- Un botón de chat flotante en la esquina inferior derecha
- El sistema de recomendaciones funcionando con IA

---

## Archivos Creados

```
Proyecto/
├── js/
│   ├── ai-chatbot.js           # Chatbot principal
│   ├── ai-recommendations.js   # Sistema de recomendaciones
│   └── ai-config.js           # Configuración (EDITA AQUÍ)
├── css/
│   ├── ai-chatbot.css         # Estilos del chatbot
│   └── ai-recommendations.css # Estilos de recomendaciones
├── recomendaciones.html       # Página de ejemplo
├── AI_INTEGRATION_GUIDE.md    # Guía completa
└── AI_README.md              # Este archivo
```

---

## Páginas Actualizadas

### [`index.html`](index.html)
Ya integrado con el chatbot
- El chatbot aparece automáticamente
- Listo para usar

### [`recomendaciones.html`](recomendaciones.html) (NUEVO)
Página dedicada a recomendaciones
- Widget interactivo completo
- Ejemplo de uso del sistema

---

## Cómo Usar

### Chatbot
El chatbot se inicializa automáticamente. Los usuarios pueden:
1. Hacer clic en el botón flotante
2. Escribir sus preguntas
3. Usar botones de acción rápida
4. Recibir respuestas inteligentes

### Recomendaciones
Dos formas de usar:

**Opción 1: Página Dedicada**
- Visita [`recomendaciones.html`](recomendaciones.html)
- Usa el widget interactivo

**Opción 2: Integrar en Cualquier Página**
```html
<!-- En tu HTML -->
<div id="mi-widget"></div>

<!-- En tu JavaScript -->
<script>
window.aiRecommendations.createRecommendationWidget('mi-widget');
</script>
```

---

## Configuración Avanzada

### Cambiar Modelo de IA
En [`js/ai-config.js`](js/ai-config.js:13):
```javascript
MODEL: 'gpt-3.5-turbo',  // Económico
// MODEL: 'gpt-4',       // Más potente
```

### Ajustar Creatividad
```javascript
CHATBOT: {
    temperature: 0.7,  // 0 = preciso, 1 = creativo
}
```

### Desactivar Funciones
```javascript
CHATBOT: {
    enabled: false,  // Desactiva chatbot
}
```

---

## Costos Estimados

### Con GPT-3.5-Turbo (Recomendado)
- **Chatbot**: ~$0.001 por conversación
- **Recomendaciones**: ~$0.0006 por solicitud
- **1000 interacciones**: ~$1 USD

### Consejos para Ahorrar
1. Usa GPT-3.5 en lugar de GPT-4
2. Las funciones básicas funcionan sin API key
3. Implementa caché para preguntas frecuentes

---

## Seguridad

### IMPORTANTE para Producción

**Nunca expongas tu API key en el código del cliente.**

Para producción, crea un proxy en PHP:

1. Crea [`PHP/ai-proxy.php`](PHP/ai-proxy.php)
2. Guarda tu API key en el servidor
3. Modifica [`js/ai-chatbot.js`](js/ai-chatbot.js) para usar el proxy

Ver detalles en [`AI_INTEGRATION_GUIDE.md`](AI_INTEGRATION_GUIDE.md#seguridad)

---

## Solución de Problemas

### El chatbot no aparece
```javascript
// En la consola del navegador (F12):
getAIStatus()
```

### Ver guía de configuración
```javascript
// En la consola:
showAISetupHelp()
```

### Errores comunes
- **401 Unauthorized**: API key inválida
- **429 Rate Limit**: Demasiadas solicitudes
- **No aparece nada**: Verifica que los archivos CSS/JS estén enlazados

---

## Documentación Completa

Para información detallada, consulta:
- [`AI_INTEGRATION_GUIDE.md`](AI_INTEGRATION_GUIDE.md) - Guía completa
- [Documentación OpenAI](https://platform.openai.com/docs)

---

## Próximos Pasos

### Sin API Key (Funcionalidad Básica)
 El chatbot muestra información de contacto
 Las recomendaciones funcionan con filtros locales
 Todo funciona sin costos

### Con API Key (Funcionalidad Completa)
 Chatbot con respuestas inteligentes
 Recomendaciones enriquecidas con IA
 Conversaciones contextuales
 Análisis personalizado

---

## Soporte

¿Necesitas ayuda?
- **Email**: lucaszv2006@gmail.com
- **Teléfono**: +506 8325 6836

---

## Características Destacadas

### Chatbot
- Interfaz moderna y responsive
- Conversaciones contextuales
- Respuestas instantáneas
- Compatible con móviles
-  Totalmente personalizable

### Recomendaciones
-  Filtros inteligentes
-  Itinerarios personalizados
-  Tarjetas visuales atractivas
-  Base de datos de atracciones
-  Enriquecimiento con IA

---

**¡Disfruta de tu sitio web potenciado con IA! **

Para comenzar, simplemente configura tu API key en [`js/ai-config.js`](js/ai-config.js) y recarga la página.
