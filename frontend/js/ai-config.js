const AI_CONFIG = {
    OPENAI_API_KEY: 'OPENAI_API_KEY',
    MODEL: 'gpt-3.5-turbo',

    CHATBOT: {
        enabled: true,
        maxHistoryMessages: 10,
        temperature: 0.7,
        maxTokens: 500,
        useProxy: true
    },

    RECOMMENDATIONS: {
        enabled: true,
        maxRecommendations: 6,
        useAIEnhancement: true
    },
    MESSAGES: {
        apiKeyMissing: 'Configura OPENAI_API_KEY en el servidor para activar IA.',
        error: 'Hubo un error al procesar tu solicitud. Por favor intenta de nuevo.',
        contactInfo: 'Puedes contactarnos directamente:\nTelefono: +506 8325 6836\nEmail: lucaszv2006@gmail.com'
    }
};

function initializeAI() {
    const proxyEnabled = AI_CONFIG.CHATBOT.useProxy === true;
    const isClientKeyConfigured = AI_CONFIG.OPENAI_API_KEY &&
        AI_CONFIG.OPENAI_API_KEY !== 'TU_API_KEY_AQUI';

    if (!proxyEnabled && !isClientKeyConfigured) {
        console.warn('API key de OpenAI no configurada y proxy desactivado.');
    }

    const chatbotPageEnabled = window.LH_CHATBOT_ENABLED === true;
    const authState = window.LH_AUTH || { loggedIn: false };

    if (AI_CONFIG.CHATBOT.enabled && chatbotPageEnabled && authState.loggedIn === true) {
        try {
            window.chatbot = new AIChatbot(AI_CONFIG.OPENAI_API_KEY, {
                useProxy: proxyEnabled,
                chatEndpoint: window.CHATBOT_API_ENDPOINT || './ai-chatbot-proxy.php',
                persistHistory: true,
                storageKey: authState.userKey ? `lh_chat_index_${authState.userKey}` : 'lh_chat_index_guest',
                maxStoredMessages: AI_CONFIG.CHATBOT.maxHistoryMessages ? Math.max(10, AI_CONFIG.CHATBOT.maxHistoryMessages * 4) : 40
            });
            console.log('Chatbot de IA inicializado');
        } catch (error) {
            console.error('Error al inicializar chatbot:', error);
        }
    } else if (AI_CONFIG.CHATBOT.enabled && chatbotPageEnabled && authState.loggedIn !== true) {
        try {
            const lockedHTML = `
                <div id="ai-chatbot-locked" style="position:fixed;bottom:20px;right:20px;z-index:9999;font-family:'Poppins',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
                    <button type="button" aria-label="Inicia sesion para usar el chat" style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border:none;color:#fff;cursor:pointer;box-shadow:0 4px 12px rgba(102,126,234,.4);display:flex;align-items:center;justify-content:center;">
                        <span style="font-size:22px;">🔒</span>
                    </button>
                </div>
            `;
            if (!document.getElementById('ai-chatbot-container') && !document.getElementById('ai-chatbot-locked')) {
                document.body.insertAdjacentHTML('beforeend', lockedHTML);
                document.getElementById('ai-chatbot-locked')?.addEventListener('click', () => {
                    window.location.href = 'panel-de-acceso.php';
                });
            }
        } catch (error) {
            console.error('Error al renderizar acceso bloqueado de chatbot:', error);
        }
    }

    if (AI_CONFIG.RECOMMENDATIONS.enabled) {
        try {
            window.aiRecommendations = new AIRecommendations(AI_CONFIG.OPENAI_API_KEY);
            console.log('Sistema de recomendaciones inicializado');
        } catch (error) {
            console.error('Error al inicializar recomendaciones:', error);
        }
    }

    return proxyEnabled || isClientKeyConfigured;
}

function getAIStatus() {
    const proxyEnabled = AI_CONFIG.CHATBOT.useProxy === true;
    const isClientKeyConfigured = AI_CONFIG.OPENAI_API_KEY &&
        AI_CONFIG.OPENAI_API_KEY !== 'TU_API_KEY_AQUI';

    return {
        configured: proxyEnabled || isClientKeyConfigured,
        proxyEnabled: proxyEnabled,
        chatbotEnabled: AI_CONFIG.CHATBOT.enabled,
        recommendationsEnabled: AI_CONFIG.RECOMMENDATIONS.enabled,
        model: AI_CONFIG.MODEL
    };
}

function showAISetupHelp() {
    console.log('Para activar IA en servidor define OPENAI_API_KEY y usa el proxy ai-chatbot-proxy.php');
}

window.AI_CONFIG = AI_CONFIG;
window.initializeAI = initializeAI;
window.getAIStatus = getAIStatus;
window.showAISetupHelp = showAISetupHelp;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeAI);
} else {
    initializeAI();
}
