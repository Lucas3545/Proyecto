/**
 * Configuración de IA para Luke's House Casa Tranquila
 * 
 * IMPORTANTE: Este archivo contiene la configuración para las funcionalidades de IA.
 * Para activar las funciones de IA, necesitas obtener una API key de OpenAI.
 */

// Configuración de la API de OpenAI
const AI_CONFIG = {
    // Reemplaza 'TU_API_KEY_AQUI' con tu API key de OpenAI
    // Puedes obtener una en: https://platform.openai.com/api-keys
    OPENAI_API_KEY: 'TU_API_KEY_AQUI',
    
    // Modelo a utilizar (gpt-3.5-turbo es más económico, gpt-4 es más potente)
    MODEL: 'gpt-3.5-turbo',
    
    // Configuración del chatbot
    CHATBOT: {
        enabled: true,
        maxHistoryMessages: 10, // Número máximo de mensajes en el historial
        temperature: 0.7, // Creatividad de las respuestas (0-1)
        maxTokens: 500 // Longitud máxima de respuesta
    },
    
    // Configuración de recomendaciones
    RECOMMENDATIONS: {
        enabled: true,
        maxRecommendations: 6, // Número máximo de recomendaciones a mostrar
        useAIEnhancement: true // Usar IA para mejorar recomendaciones
    },
    
    // Mensajes del sistema
    MESSAGES: {
        apiKeyMissing: 'Para activar las funciones de IA, configura tu API key de OpenAI en js/ai-config.js',
        error: 'Hubo un error al procesar tu solicitud. Por favor, intenta de nuevo.',
        contactInfo: 'Puedes contactarnos directamente:\n📞 Teléfono: +506 8325 6836\n📧 Email: lucaszv2006@gmail.com'
    }
};

/**
 * Inicializa los servicios de IA
 */
function initializeAI() {
    // Verificar si la API key está configurada
    const isConfigured = AI_CONFIG.OPENAI_API_KEY && 
                        AI_CONFIG.OPENAI_API_KEY !== 'TU_API_KEY_AQUI';
    
    if (!isConfigured) {
        console.warn('⚠️ API key de OpenAI no configurada. Las funciones de IA estarán limitadas.');
        console.info('📖 Para configurar, edita js/ai-config.js y agrega tu API key de OpenAI.');
        console.info('🔗 Obtén tu API key en: https://platform.openai.com/api-keys');
    }
    
    // Inicializar chatbot si está habilitado
    if (AI_CONFIG.CHATBOT.enabled) {
        try {
            window.chatbot = new AIChatbot(AI_CONFIG.OPENAI_API_KEY);
            console.log('✅ Chatbot de IA inicializado');
        } catch (error) {
            console.error('❌ Error al inicializar chatbot:', error);
        }
    }
    
    // Inicializar sistema de recomendaciones si está habilitado
    if (AI_CONFIG.RECOMMENDATIONS.enabled) {
        try {
            window.aiRecommendations = new AIRecommendations(AI_CONFIG.OPENAI_API_KEY);
            console.log('✅ Sistema de recomendaciones inicializado');
        } catch (error) {
            console.error('❌ Error al inicializar recomendaciones:', error);
        }
    }
    
    return isConfigured;
}

/**
 * Obtiene el estado de configuración de la IA
 */
function getAIStatus() {
    const isConfigured = AI_CONFIG.OPENAI_API_KEY && 
                        AI_CONFIG.OPENAI_API_KEY !== 'TU_API_KEY_AQUI';
    
    return {
        configured: isConfigured,
        chatbotEnabled: AI_CONFIG.CHATBOT.enabled,
        recommendationsEnabled: AI_CONFIG.RECOMMENDATIONS.enabled,
        model: AI_CONFIG.MODEL
    };
}

/**
 * Muestra información de ayuda para configurar la IA
 */
function showAISetupHelp() {
    console.log(`
╔════════════════════════════════════════════════════════════════╗
║           CONFIGURACIÓN DE IA - LUKE'S HOUSE                   ║
╚════════════════════════════════════════════════════════════════╝

📋 PASOS PARA ACTIVAR LA IA:

1. Obtén una API key de OpenAI:
   🔗 https://platform.openai.com/api-keys
   
2. Abre el archivo: js/ai-config.js

3. Reemplaza 'TU_API_KEY_AQUI' con tu API key:
   OPENAI_API_KEY: 'sk-...'

4. Guarda el archivo y recarga la página

💡 FUNCIONALIDADES DISPONIBLES:

✨ Chatbot Inteligente
   - Responde preguntas sobre la propiedad
   - Ayuda con reservas
   - Proporciona información turística

🎯 Recomendaciones Personalizadas
   - Sugiere actividades basadas en preferencias
   - Crea itinerarios personalizados
   - Recomendaciones según el clima

📞 CONTACTO DIRECTO:
   Teléfono: +506 8325 6836
   Email: lucaszv2006@gmail.com

════════════════════════════════════════════════════════════════
    `);
}

// Exportar configuración
window.AI_CONFIG = AI_CONFIG;
window.initializeAI = initializeAI;
window.getAIStatus = getAIStatus;
window.showAISetupHelp = showAISetupHelp;

// Auto-inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeAI);
} else {
    initializeAI();
}
