class AIChatbot {
    constructor(apiKey, options = {}) {
        this.apiKey = apiKey;
        this.apiEndpoint = 'https://api.openai.com/v1/chat/completions';
        this.chatEndpoint = options.chatEndpoint || window.CHATBOT_API_ENDPOINT || './ai-chatbot-proxy.php';
        this.useProxy = options.useProxy !== undefined ? options.useProxy : true;
        this.persistHistory = options.persistHistory === true;
        this.storageKey = options.storageKey || null;
        this.maxStoredMessages = Number.isFinite(options.maxStoredMessages) ? options.maxStoredMessages : 40;
        this.conversationHistory = [];
        this.isOpen = false;
        this.isTyping = false;
        this.isRestoringHistory = false;
        this.recommendationFlow = {
            interests: [],
            difficulty: 'todas'
        };

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

        const restored = this.restoreHistory();
        if (!restored) {
            this.addWelcomeMessage();
        }
    }

    getHistoryStorageKey() {
        if (this.storageKey) return this.storageKey;
        return null;
    }

    restoreHistory() {
        if (!this.persistHistory) return false;

        const key = this.getHistoryStorageKey();
        if (!key) return false;

        try {
            const raw = localStorage.getItem(key);
            if (!raw) return false;

            const parsed = JSON.parse(raw);
            if (!parsed || !Array.isArray(parsed.conversationHistory)) return false;

            this.conversationHistory = parsed.conversationHistory
                .filter(m => m && (m.role === 'user' || m.role === 'assistant') && typeof m.content === 'string')
                .slice(-Math.max(0, this.maxStoredMessages));

            if (this.conversationHistory.length === 0) return false;

            this.isRestoringHistory = true;
            this.conversationHistory.forEach(m => {
                this.addMessage(m.content, m.role === 'user' ? 'user' : 'bot');
            });
            this.isRestoringHistory = false;

            return true;
        } catch (error) {
            console.error('No se pudo restaurar el historial del chat:', error);
            this.isRestoringHistory = false;
            return false;
        }
    }

    saveHistory() {
        if (!this.persistHistory) return;

        const key = this.getHistoryStorageKey();
        if (!key) return;

        try {
            const trimmed = this.conversationHistory.slice(-Math.max(0, this.maxStoredMessages));
            localStorage.setItem(key, JSON.stringify({ conversationHistory: trimmed }));
        } catch (error) {
            console.error('No se pudo guardar el historial del chat:', error);
        }
    }

    appendToHistory(role, content) {
        if (role !== 'user' && role !== 'assistant') return;
        if (typeof content !== 'string' || content.trim() === '') return;

        this.conversationHistory.push({ role, content });
        this.conversationHistory = this.conversationHistory.slice(-Math.max(0, this.maxStoredMessages));
        this.saveHistory();
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
                        <button class="quick-action-btn" data-action="recommendations">Recomendaciones</button>
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
                const action = btn.getAttribute('data-action');
                if (action === 'recommendations') {
                    this.startRecommendationsFlow();
                    return;
                }

                const message = btn.getAttribute('data-message');
                this.sendMessage(message);
            });
        });
    }

    openChat() {
        if (!this.isOpen) {
            this.toggleChat();
        }
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

        this.appendToHistory('user', message);

        this.showTypingIndicator();

        try {
            const response = await this.callOpenAI(message);
            this.hideTypingIndicator();
            this.addMessage(response, 'bot');

            this.appendToHistory('assistant', response);
        } catch (error) {
            this.hideTypingIndicator();
            console.error('Error al comunicarse con la IA:', error);
            this.addMessage('Lo siento, hubo un error al procesar tu mensaje. Intenta de nuevo o contacta al propietario.', 'bot', true);
            this.appendToHistory('assistant', 'Lo siento, hubo un error al procesar tu mensaje. Intenta de nuevo o contacta al propietario.');
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

    addChoiceMessage(text, choices, onChoose) {
        const messagesContainer = document.getElementById('chatbot-messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = 'chatbot-message bot-message';

        const timestamp = new Date().toLocaleTimeString('es-CR', {
            hour: '2-digit',
            minute: '2-digit'
        });

        const choicesHtml = Array.isArray(choices) && choices.length > 0
            ? `<div class="chatbot-choices">
                ${choices.map(c => `<button type="button" class="chatbot-choice-btn" data-choice="${String(c.value)}">${c.label}</button>`).join('')}
               </div>`
            : '';

        messageDiv.innerHTML = `
            <div class="message-content">
                ${this.formatMessage(text)}
                ${choicesHtml}
            </div>
            <div class="message-time">${timestamp}</div>
        `;

        if (choicesHtml) {
            const buttons = messageDiv.querySelectorAll('.chatbot-choice-btn');
            buttons.forEach(btn => {
                btn.addEventListener('click', () => {
                    buttons.forEach(b => b.disabled = true);
                    const value = btn.getAttribute('data-choice');
                    const label = btn.textContent || value;
                    onChoose(value, label);
                });
            });
        }

        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    startRecommendationsFlow() {
        this.openChat();
        this.recommendationFlow = { interests: [], difficulty: 'todas' };

        this.addChoiceMessage(
            'Perfecto. Elige lo que te interesa y te muestro opciones:',
            [
                { label: '🏔️ Aventura', value: 'aventura' },
                { label: '🌿 Naturaleza', value: 'naturaleza' },
                { label: '🧘 Relajación', value: 'relajacion' },
                { label: '👨‍👩‍👧 Familia', value: 'familia' }
            ],
            (value, label) => {
                this.recommendationFlow.interests = [value];
                this.addMessage(label, 'user');
                this.appendToHistory('user', label);
                this.askRecommendationsDifficulty();
            }
        );
    }

    askRecommendationsDifficulty() {
        this.addChoiceMessage(
            '¿Qué nivel de dificultad prefieres?',
            [
                { label: 'Todas', value: 'todas' },
                { label: 'Fácil', value: 'baja' },
                { label: 'Moderado', value: 'media' },
                { label: 'Difícil', value: 'alta' }
            ],
            (value, label) => {
                this.recommendationFlow.difficulty = value;
                this.addMessage(label, 'user');
                this.appendToHistory('user', label);
                this.showRecommendations();
            }
        );
    }

    async showRecommendations() {
        const preferences = {
            interests: this.recommendationFlow.interests,
            difficulty: this.recommendationFlow.difficulty,
            budget: null
        };

        const recommender = window.aiRecommendations
            || (window.AIRecommendations ? new window.AIRecommendations(this.apiKey) : null);

        if (!recommender || typeof recommender.getPersonalizedRecommendations !== 'function') {
            this.addMessage('Lo siento, el sistema de recomendaciones no esta disponible en este momento.', 'bot', true);
            this.appendToHistory('assistant', 'Lo siento, el sistema de recomendaciones no esta disponible en este momento.');
            return;
        }

        this.showTypingIndicator();
        try {
            const recommendations = await recommender.getPersonalizedRecommendations(preferences);
            this.hideTypingIndicator();

            if (!Array.isArray(recommendations) || recommendations.length === 0) {
                this.addMessage('No encontre recomendaciones con esas preferencias. Prueba otra categoria.', 'bot');
                this.appendToHistory('assistant', 'No encontre recomendaciones con esas preferencias. Prueba otra categoria.');
                this.startRecommendationsFlow();
                return;
            }

            const text = recommendations.map((r, i) => {
                const duration = r.duration ? `⏱️ ${r.duration}` : '';
                const price = r.price ? `💲 ${r.price}` : '';
                const meta = [duration, price].filter(Boolean).join(' | ');
                return `${i + 1}) ${r.name}\n${r.description}${meta ? `\n${meta}` : ''}`;
            }).join('\n\n');

            this.addMessage(`Estas son algunas opciones para ti:\n\n${text}`, 'bot');
            this.appendToHistory('assistant', `Estas son algunas opciones para ti:\n\n${text}`);

            this.addChoiceMessage(
                '¿Que quieres hacer ahora?',
                [
                    { label: 'Más opciones', value: 'more' },
                    { label: 'Cambiar categoría', value: 'change' },
                    { label: 'Ver información', value: 'info' }
                ],
                (value) => {
                    if (value === 'more') {
                        this.showRecommendations();
                        return;
                    }
                    if (value === 'change') {
                        this.startRecommendationsFlow();
                        return;
                    }
                    if (value === 'info') {
                        window.location.href = 'informacion.php';
                    }
                }
            );
        } catch (error) {
            this.hideTypingIndicator();
            console.error('Error al obtener recomendaciones:', error);
            this.addMessage('Lo siento, no pude generar recomendaciones ahora mismo. Intenta de nuevo en un momento.', 'bot', true);
            this.appendToHistory('assistant', 'Lo siento, no pude generar recomendaciones ahora mismo. Intenta de nuevo en un momento.');
        }
    }

    clearHistory() {
        this.conversationHistory = [];
        const messagesContainer = document.getElementById('chatbot-messages');
        messagesContainer.innerHTML = '';
        this.addWelcomeMessage();

        if (this.persistHistory) {
            const key = this.getHistoryStorageKey();
            if (key) {
                try {
                    localStorage.removeItem(key);
                } catch (error) {
                    console.error('No se pudo limpiar el historial guardado:', error);
                }
            }
        }
    }
}

window.AIChatbot = AIChatbot;

window.openRecommendationsChat = function () {
    if (!window.chatbot || typeof window.chatbot.startRecommendationsFlow !== 'function') {
        return false;
    }

    window.chatbot.startRecommendationsFlow();
    return true;
};
