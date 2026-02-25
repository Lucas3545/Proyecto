class AIChatbot {
    constructor(apiKey, options = {}) {
        this.apiKey = apiKey;
        this.apiEndpoint = 'https://api.openai.com/v1/chat/completions';
        this.chatEndpoint = options.chatEndpoint || window.CHATBOT_API_ENDPOINT || './ai-chatbot-proxy.php';
        this.useProxy = options.useProxy !== undefined ? options.useProxy : true;
        this.conversationHistory = [];
        this.isOpen = false;
        this.isTyping = false;

        this.systemContext = `Eres un asistente virtual amigable para Luke's House Casa Tranquila, una cabana turistica ubicada en La Fortuna, Costa Rica.

INFORMACION CLAVE:
- Ubicacion: La Fortuna, cerca del Volcan Arenal
- Tipo de alojamiento: Tiny House (casa pequena) con patio de 1 acre
- Fauna: Visitada por perezosos, tucanes, ranas, colibries, monos aulladores
- Caracteristicas: Refugio en la naturaleza, ambiente tranquilo
- Atracciones cercanas: Volcan Arenal, Rio Celeste, Cerro Chato, Puentes Mistico Park, Proyecto Asis, Eco Termales Fortuna

SERVICIOS:
- Sistema de reservas en linea
- Metodos de pago: Tarjeta de credito/debito
- Informacion turistica de la zona
- Galeria de fotos

Tu trabajo es:
1. Responder preguntas sobre la propiedad y servicios
2. Ayudar con el proceso de reserva
3. Proporcionar recomendaciones turisticas de La Fortuna
4. Ser amable, profesional y util
5. Responder en espanol principalmente, pero puedes usar ingles si el usuario lo prefiere

Manten respuestas concisas y utiles. Si no sabes algo especifico, sugiere contactar directamente al propietario.`;

        this.init();
    }

    init() {
        this.createChatbotUI();
        this.attachEventListeners();
        this.addWelcomeMessage();
    }

    createChatbotUI() {
        const chatbotHTML = `
            <div id="ai-chatbot-container" class="chatbot-closed">
                <button id="chatbot-toggle" class="chatbot-toggle" aria-label="Abrir chat">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                    <span class="chatbot-badge" id="unread-badge" style="display: none;">1</span>
                </button>

                <div id="chatbot-window" class="chatbot-window" style="display: none;">
                    <div class="chatbot-header">
                        <div class="chatbot-header-info">
                            <h3>Asistente Virtual</h3>
                            <span class="chatbot-status">En linea</span>
                        </div>
                        <button id="chatbot-close" class="chatbot-close-btn" aria-label="Cerrar chat">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    <div id="chatbot-messages" class="chatbot-messages"></div>

                    <div class="chatbot-input-container">
                        <input
                            type="text"
                            id="chatbot-input"
                            class="chatbot-input"
                            placeholder="Escribe tu mensaje..."
                            autocomplete="off"
                        />
                        <button id="chatbot-send" class="chatbot-send-btn" aria-label="Enviar mensaje">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="22" y1="2" x2="11" y2="13"></line>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                            </svg>
                        </button>
                    </div>

                    <div class="chatbot-quick-actions">
                        <button class="quick-action-btn" data-message="Como puedo hacer una reserva?">Reservar</button>
                        <button class="quick-action-btn" data-message="Que atracciones hay cerca?">Atracciones</button>
                        <button class="quick-action-btn" data-message="Cuales son los precios?">Precios</button>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', chatbotHTML);
    }

    attachEventListeners() {
        const toggleBtn = document.getElementById('chatbot-toggle');
        const closeBtn = document.getElementById('chatbot-close');
        const sendBtn = document.getElementById('chatbot-send');
        const input = document.getElementById('chatbot-input');
        const quickActionBtns = document.querySelectorAll('.quick-action-btn');

        toggleBtn.addEventListener('click', () => this.toggleChat());
        closeBtn.addEventListener('click', () => this.closeChat());
        sendBtn.addEventListener('click', () => this.sendMessage());
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.sendMessage();
        });

        quickActionBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const message = btn.getAttribute('data-message');
                this.sendMessage(message);
            });
        });
    }

    toggleChat() {
        this.isOpen = !this.isOpen;
        const container = document.getElementById('ai-chatbot-container');
        const windowElement = document.getElementById('chatbot-window');
        const badge = document.getElementById('unread-badge');

        if (this.isOpen) {
            container.classList.remove('chatbot-closed');
            container.classList.add('chatbot-open');
            windowElement.style.display = 'flex';
            badge.style.display = 'none';
            document.getElementById('chatbot-input').focus();
        } else {
            this.closeChat();
        }
    }

    closeChat() {
        this.isOpen = false;
        const container = document.getElementById('ai-chatbot-container');
        const windowElement = document.getElementById('chatbot-window');

        container.classList.remove('chatbot-open');
        container.classList.add('chatbot-closed');
        windowElement.style.display = 'none';
    }

    addWelcomeMessage() {
        const welcomeMessage = 'Hola. Soy el asistente virtual de Luke\\'s House Casa Tranquila. En que puedo ayudarte hoy?';
        this.addMessage(welcomeMessage, 'bot');

        if (!this.isOpen) {
            document.getElementById('unread-badge').style.display = 'flex';
        }
    }

    async sendMessage(messageText = null) {
        const input = document.getElementById('chatbot-input');
        const message = messageText || input.value.trim();

        if (!message) return;

        input.value = '';
        this.addMessage(message, 'user');

        this.conversationHistory.push({
            role: 'user',
            content: message
        });

        this.showTypingIndicator();

        try {
            const response = await this.callOpenAI(message);
            this.hideTypingIndicator();
            this.addMessage(response, 'bot');

            this.conversationHistory.push({
                role: 'assistant',
                content: response
            });
        } catch (error) {
            this.hideTypingIndicator();
            console.error('Error al comunicarse con la IA:', error);
            this.addMessage('Lo siento, hubo un error al procesar tu mensaje. Intenta de nuevo o contacta al propietario.', 'bot', true);
        }
    }

    async callOpenAI(userMessage) {
        const messages = [
            { role: 'system', content: this.systemContext },
            ...this.conversationHistory.slice(-10)
        ];

        const model = (window.AI_CONFIG && window.AI_CONFIG.MODEL) ? window.AI_CONFIG.MODEL : 'gpt-3.5-turbo';

        if (this.useProxy) {
            const proxyResponse = await fetch(this.chatEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    model: model,
                    messages: messages,
                    max_tokens: 500,
                    temperature: 0.7
                })
            });

            if (!proxyResponse.ok) {
                throw new Error(`Proxy API Error: ${proxyResponse.status}`);
            }

            const proxyData = await proxyResponse.json();
            if (!proxyData.success || !proxyData.reply) {
                throw new Error(proxyData.error || 'Respuesta invalida del proxy');
            }

            return proxyData.reply;
        }

        if (!this.apiKey || this.apiKey === 'TU_API_KEY_AQUI') {
            return 'No hay API key configurada para conexion directa. Configura el proxy o define una API key valida.';
        }

        const directResponse = await fetch(this.apiEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${this.apiKey}`
            },
            body: JSON.stringify({
                model: model,
                messages: messages,
                max_tokens: 500,
                temperature: 0.7
            })
        });

        if (!directResponse.ok) {
            throw new Error(`OpenAI API Error: ${directResponse.status}`);
        }

        const data = await directResponse.json();
        return data.choices[0].message.content;
    }

    addMessage(text, sender, isError = false) {
        const messagesContainer = document.getElementById('chatbot-messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `chatbot-message ${sender}-message ${isError ? 'error-message' : ''}`;

        const timestamp = new Date().toLocaleTimeString('es-CR', {
            hour: '2-digit',
            minute: '2-digit'
        });

        messageDiv.innerHTML = `
            <div class="message-content">${this.formatMessage(text)}</div>
            <div class="message-time">${timestamp}</div>
        `;

        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    formatMessage(text) {
        text = text.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank">$1</a>');
        text = text.replace(/\n/g, '<br>');
        return text;
    }

    showTypingIndicator() {
        const messagesContainer = document.getElementById('chatbot-messages');
        const typingDiv = document.createElement('div');
        typingDiv.className = 'chatbot-message bot-message typing-indicator';
        typingDiv.id = 'typing-indicator';
        typingDiv.innerHTML = `
            <div class="message-content">
                <span class="typing-dot"></span>
                <span class="typing-dot"></span>
                <span class="typing-dot"></span>
            </div>
        `;

        messagesContainer.appendChild(typingDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        this.isTyping = true;
    }

    hideTypingIndicator() {
        const typingIndicator = document.getElementById('typing-indicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
        this.isTyping = false;
    }

    clearHistory() {
        this.conversationHistory = [];
        const messagesContainer = document.getElementById('chatbot-messages');
        messagesContainer.innerHTML = '';
        this.addWelcomeMessage();
    }
}

window.AIChatbot = AIChatbot;
