class AssistantIA {
    constructor() {
        this.isOpen = false;
        this.chatContainer = null;
        this.init();
    }

    init() {
        // Créer le conteneur du chat
        this.createChatContainer();
        
        // Ajouter les écouteurs d'événements
        this.addEventListeners();
    }

    createChatContainer() {
        this.chatContainer = document.createElement('div');
        this.chatContainer.className = 'assistant-chat';
        this.chatContainer.innerHTML = `
            <div class="assistant-header">
                <h3>Assistant Ohmyfood</h3>
                <button class="close-assistant" aria-label="Fermer l'assistant">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="assistant-messages">
                <div class="message assistant">
                    Bonjour ! Je suis votre assistant virtuel. Comment puis-je vous aider aujourd'hui ?
                </div>
            </div>
            <div class="assistant-input">
                <input type="text" placeholder="Tapez votre message..." aria-label="Message">
                <button class="send-message" aria-label="Envoyer le message">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        `;
        document.body.appendChild(this.chatContainer);
    }

    addEventListeners() {
        // Ouvrir/fermer l'assistant
        const assistantButton = document.querySelector('.assistant-ia');
        const closeButton = this.chatContainer.querySelector('.close-assistant');
        
        assistantButton.addEventListener('click', () => this.toggleChat());
        closeButton.addEventListener('click', () => this.toggleChat());

        // Envoyer un message
        const input = this.chatContainer.querySelector('input');
        const sendButton = this.chatContainer.querySelector('.send-message');

        const sendMessage = () => {
            const message = input.value.trim();
            if (message) {
                this.sendUserMessage(message);
                input.value = '';
            }
        };

        sendButton.addEventListener('click', sendMessage);
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    }

    toggleChat() {
        this.isOpen = !this.isOpen;
        this.chatContainer.classList.toggle('active');
        
        // Focus sur l'input quand on ouvre le chat
        if (this.isOpen) {
            this.chatContainer.querySelector('input').focus();
        }
    }

    sendUserMessage(message) {
        const messagesContainer = this.chatContainer.querySelector('.assistant-messages');
        
        // Ajouter le message de l'utilisateur
        const userMessage = document.createElement('div');
        userMessage.className = 'message user';
        userMessage.textContent = message;
        messagesContainer.appendChild(userMessage);

        // Simuler une réponse de l'assistant
        setTimeout(() => {
            this.sendAssistantResponse(message);
        }, 1000);

        // Faire défiler vers le bas
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    sendAssistantResponse(userMessage) {
        const messagesContainer = this.chatContainer.querySelector('.assistant-messages');
        
        // Logique simple de réponse basée sur des mots-clés
        let response = "Je ne suis pas sûr de comprendre. Pouvez-vous reformuler votre question ?";
        
        if (userMessage.toLowerCase().includes('réservation')) {
            response = "Pour faire une réservation, vous pouvez cliquer sur le bouton 'Découvrir les restaurants' et choisir l'établissement qui vous convient.";
        } else if (userMessage.toLowerCase().includes('horaires')) {
            response = "Les horaires d'ouverture varient selon les restaurants. Vous pouvez les consulter sur la page de chaque établissement.";
        } else if (userMessage.toLowerCase().includes('contact')) {
            response = "Vous pouvez nous contacter via le formulaire de contact ou par téléphone au 01 23 45 67 89.";
        }

        const assistantMessage = document.createElement('div');
        assistantMessage.className = 'message assistant';
        assistantMessage.textContent = response;
        messagesContainer.appendChild(assistantMessage);

        // Faire défiler vers le bas
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
}

// Initialiser l'assistant
document.addEventListener('DOMContentLoaded', () => {
    new AssistantIA();
}); 