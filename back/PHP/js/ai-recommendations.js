class AIRecommendations {
    constructor(apiKey) {
        this.apiKey = apiKey;
        this.apiEndpoint = 'https://api.openai.com/v1/chat/completions';
        
        this.attractions = {
            aventura: [
                {
                    name: 'Volcán Arenal',
                    description: 'Caminata al volcán más activo de Costa Rica',
                    difficulty: 'media',
                    duration: '4-5 horas',
                    price: '$30-50',
                    image: './img/Fortuna/parque-nacional-volcan-arenal-alajuela.webp'
                },
                {
                    name: 'Cerro Chato',
                    description: 'Caminata desafiante con laguna en el cráter',
                    difficulty: 'alta',
                    duration: '6-7 horas',
                    price: '$40-60',
                    image: './img/Fortuna/Cerro Chato.jpeg'
                },
                {
                    name: 'Río Balsa Rafting',
                    description: 'Rafting en aguas rápidas clase II-III',
                    difficulty: 'media',
                    duration: '3-4 horas',
                    price: '$65-85',
                    image: './img/Fortuna/Río Balsa.jpeg'
                }
            ],
            naturaleza: [
                {
                    name: 'Río Celeste',
                    description: 'Río de color turquesa único en el mundo',
                    difficulty: 'baja',
                    duration: '3-4 horas',
                    price: '$25-40',
                    image: './img/Fortuna/Rio Celeste.jpeg'
                },
                {
                    name: 'Proyecto Asis',
                    description: 'Santuario de animales rescatados',
                    difficulty: 'baja',
                    duration: '2-3 horas',
                    price: '$35-50',
                    image: './img/Proyecto-Asis-Tour-1024x678.jpg'
                },
                {
                    name: 'Puentes Místico Park',
                    description: 'Puentes colgantes en el bosque nuboso',
                    difficulty: 'baja',
                    duration: '2-3 horas',
                    price: '$30-45',
                    image: './img/Fortuna/Puentes Mistico Park.jpeg'
                }
            ],
            relajacion: [
                {
                    name: 'Eco Termales Fortuna',
                    description: 'Aguas termales naturales en ambiente privado',
                    difficulty: 'ninguna',
                    duration: '3-4 horas',
                    price: '$45-65',
                    image: './img/ecotermales-fortuna.jpg'
                },
                {
                    name: 'Catarata La Fortuna',
                    description: 'Impresionante cascada de 70 metros',
                    difficulty: 'baja',
                    duration: '2-3 horas',
                    price: '$18-25',
                    image: './img/Fortuna/the-main-falls.jpg'
                }
            ],
            familia: [
                {
                    name: 'Proyecto Asis',
                    description: 'Interacción con animales rescatados',
                    difficulty: 'baja',
                    duration: '2-3 horas',
                    price: '$35-50',
                    image: './img/Proyecto-Asis-Tour-1024x678.jpg'
                },
                {
                    name: 'Paseo a Caballo',
                    description: 'Tour ecuestre por la zona rural',
                    difficulty: 'baja',
                    duration: '2-3 horas',
                    price: '$40-60',
                    image: './img/Fortuna/pacos-horses.jpg'
                }
            ]
        };
    }
    
    async getPersonalizedRecommendations(preferences) {
        const { interests, duration, difficulty, budget } = preferences;
        
        let recommendations = this.filterAttractions(interests, difficulty, budget);
        
        if (this.apiKey && this.apiKey !== 'TU_API_KEY_AQUI') {
            try {
                const aiEnhanced = await this.enhanceWithAI(recommendations, preferences);
                return aiEnhanced;
            } catch (error) {
                console.error('Error al obtener recomendaciones de IA:', error);
                return recommendations;
            }
        }
        
        return recommendations;
    }
    
    filterAttractions(interests, difficulty, budget) {
        let filtered = [];
        
        interests.forEach(interest => {
            if (this.attractions[interest]) {
                filtered = [...filtered, ...this.attractions[interest]];
            }
        });
        
        filtered = Array.from(new Set(filtered.map(a => a.name)))
            .map(name => filtered.find(a => a.name === name));
        
        if (difficulty && difficulty !== 'todas') {
            const difficultyLevels = {
                'baja': ['ninguna', 'baja'],
                'media': ['baja', 'media'],
                'alta': ['media', 'alta']
            };
            
            filtered = filtered.filter(attr => 
                difficultyLevels[difficulty].includes(attr.difficulty)
            );
        }
        
        filtered = this.shuffleArray(filtered);
        
        return filtered.slice(0, 6);
    }
    
    async enhanceWithAI(recommendations, preferences) {
        const prompt = `Como experto en turismo de La Fortuna, Costa Rica, analiza estas actividades y proporciona una breve explicación personalizada (máximo 50 palabras) de por qué cada una es ideal para alguien con estos intereses: ${preferences.interests.join(', ')}.

Actividades:
${recommendations.map(r => `- ${r.name}: ${r.description}`).join('\n')}

Responde en formato JSON con este estructura:
{
  "recommendations": [
    {
      "name": "nombre de la actividad",
      "reason": "razón personalizada breve"
    }
  ]
}`;

        try {
            const response = await fetch(this.apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${this.apiKey}`
                },
                body: JSON.stringify({
                    model: 'gpt-3.5-turbo',
                    messages: [
                        {
                            role: 'system',
                            content: 'Eres un experto en turismo de La Fortuna, Costa Rica. Proporciona recomendaciones personalizadas y concisas.'
                        },
                        {
                            role: 'user',
                            content: prompt
                        }
                    ],
                    max_tokens: 500,
                    temperature: 0.7
                })
            });
            
            if (!response.ok) {
                throw new Error(`API Error: ${response.status}`);
            }
            
            const data = await response.json();
            const aiResponse = JSON.parse(data.choices[0].message.content);
            
            return recommendations.map(rec => {
                const aiRec = aiResponse.recommendations.find(r => r.name === rec.name);
                return {
                    ...rec,
                    aiReason: aiRec ? aiRec.reason : null
                };
            });
            
        } catch (error) {
            console.error('Error al enriquecer con IA:', error);
            return recommendations;
        }
    }
    
    async generateItinerary(days, preferences) {
        const allRecommendations = await this.getPersonalizedRecommendations(preferences);
    
        const itinerary = [];
        const activitiesPerDay = Math.ceil(allRecommendations.length / days);
        
        for (let day = 1; day <= days; day++) {
            const startIndex = (day - 1) * activitiesPerDay;
            const dayActivities = allRecommendations.slice(startIndex, startIndex + activitiesPerDay);
            
            itinerary.push({
                day: day,
                activities: dayActivities,
                theme: this.getDayTheme(dayActivities)
            });
        }
        
        return itinerary;
    }
    
    getDayTheme(activities) {
        const themes = {
            aventura: 0,
            naturaleza: 0,
            relajacion: 0
        };
        
        activities.forEach(activity => {
            if (activity.difficulty === 'alta' || activity.difficulty === 'media') {
                themes.aventura++;
            }
            if (activity.name.includes('Río') || activity.name.includes('Proyecto')) {
                themes.naturaleza++;
            }
            if (activity.name.includes('Termales') || activity.name.includes('Catarata')) {
                themes.relajacion++;
            }
        });
        
        const maxTheme = Object.keys(themes).reduce((a, b) => 
            themes[a] > themes[b] ? a : b
        );
        
        return maxTheme;
    }
    
    getWeatherBasedRecommendations(weather) {
        if (weather === 'soleado') {
            return this.filterAttractions(['aventura', 'naturaleza'], 'todas', null);
        } else if (weather === 'lluvioso') {
            return this.filterAttractions(['relajacion'], 'baja', null);
        } else {
            return this.filterAttractions(['naturaleza', 'familia'], 'media', null);
        }
    }
    
    renderRecommendations(recommendations, containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        container.innerHTML = '';
        
        recommendations.forEach(rec => {
            const card = this.createRecommendationCard(rec);
            container.appendChild(card);
        });
    }
    
    createRecommendationCard(recommendation) {
        const card = document.createElement('div');
        card.className = 'recommendation-card';
        
        card.innerHTML = `
            <div class="recommendation-image" style="background-image: url('${recommendation.image}')">
                <span class="difficulty-badge ${recommendation.difficulty}">${this.translateDifficulty(recommendation.difficulty)}</span>
            </div>
            <div class="recommendation-content">
                <h3>${recommendation.name}</h3>
                <p class="description">${recommendation.description}</p>
                ${recommendation.aiReason ? `<p class="ai-reason"><strong>💡 Por qué te gustará:</strong> ${recommendation.aiReason}</p>` : ''}
                <div class="recommendation-details">
                    <span class="detail"><i class="fas fa-clock"></i> ${recommendation.duration}</span>
                    <span class="detail"><i class="fas fa-dollar-sign"></i> ${recommendation.price}</span>
                </div>
                <button class="btn-more-info" onclick="window.location.href='informacion.html'">
                    Más información
                </button>
            </div>
        `;
        
        return card;
    }
    
    translateDifficulty(difficulty) {
        const translations = {
            'ninguna': 'Fácil',
            'baja': 'Fácil',
            'media': 'Moderado',
            'alta': 'Difícil'
        };
        return translations[difficulty] || difficulty;
    }
    
    shuffleArray(array) {
        const shuffled = [...array];
        for (let i = shuffled.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
        }
        return shuffled;
    }
    
    createRecommendationWidget(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        const widget = document.createElement('div');
        widget.className = 'recommendation-widget';
        widget.innerHTML = `
            <div class="widget-header">
                <h2>🎯 Encuentra tu Aventura Perfecta</h2>
                <p>Déjanos ayudarte a planificar tu visita</p>
            </div>
            <div class="widget-form">
                <div class="form-group">
                    <label>¿Qué te interesa?</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="interest" value="aventura"> 🏔️ Aventura</label>
                        <label><input type="checkbox" name="interest" value="naturaleza"> 🌿 Naturaleza</label>
                        <label><input type="checkbox" name="interest" value="relajacion"> 🧘 Relajación</label>
                        <label><input type="checkbox" name="interest" value="familia"> 👨‍👩‍👧 Familia</label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Nivel de dificultad</label>
                    <select id="difficulty-select">
                        <option value="todas">Todas</option>
                        <option value="baja">Fácil</option>
                        <option value="media">Moderado</option>
                        <option value="alta">Difícil</option>
                    </select>
                </div>
                <button id="get-recommendations-btn" class="btn-primary">
                    Obtener Recomendaciones
                </button>
            </div>
            <div id="recommendations-results" class="recommendations-grid"></div>
        `;
        
        container.appendChild(widget);
        
        document.getElementById('get-recommendations-btn').addEventListener('click', () => {
            this.handleRecommendationRequest();
        });
    }
    
    async handleRecommendationRequest() {
        const interests = Array.from(document.querySelectorAll('input[name="interest"]:checked'))
            .map(cb => cb.value);
        
        if (interests.length === 0) {
            alert('Por favor selecciona al menos un interés');
            return;
        }
        
        const difficulty = document.getElementById('difficulty-select').value;
        
        const preferences = {
            interests: interests,
            difficulty: difficulty,
            budget: null
        };
        
        const resultsContainer = document.getElementById('recommendations-results');
        resultsContainer.innerHTML = '<div class="loading">Buscando las mejores opciones para ti...</div>';
        
        const recommendations = await this.getPersonalizedRecommendations(preferences);
        
        this.renderRecommendations(recommendations, 'recommendations-results');
    }
}

window.AIRecommendations = AIRecommendations;
