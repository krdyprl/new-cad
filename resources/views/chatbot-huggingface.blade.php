<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>Chatbot - Ceramic Art Dinoyo</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicons -->
    <link href="{{ asset('img/Cadputih.png') }}" rel="icon">
    <link href="{{ asset('img/Cadputih.png') }}" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,700,700i|Montserrat:300,400,500,700" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Ionicons -->
    <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet">

    <!-- Master CSS - Controls all colors consistently -->
    <link href="{{ asset('css/master.css') }}" rel="stylesheet">

    <style>
        body {
            padding: 0;
            background: #f8f9fa;
        }

        /* Chat Header Section */
        .chatbot-hero {
            padding: 140px 0 80px 0;
            color: white;
            text-align: center;
        }

        .chatbot-hero h1 {
            font-size: 3rem;
            font-weight: 300;
            margin-bottom: 20px;
        }

        .chatbot-hero p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Chat Container */
        .chat-container {
            max-width: 800px;
            margin: -60px auto 50px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            overflow: hidden;
            position: relative;
            z-index: 10;
        }

        /* Chat Header */
        .chat-header {
            background: linear-gradient(135deg, var(--ceramic-primary), var(--ceramic-secondary));
            color: white;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .chat-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .chat-info h5 {
            margin: 0;
            font-weight: 600;
        }

        .chat-info p {
            margin: 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #4CAF50;
            margin-left: auto;
            position: relative;
        }

        .status-indicator.offline {
            background: #f44336;
        }

        .status-indicator.checking {
            background: #ff9800;
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        /* Chat Messages */
        .chat-messages {
            height: 400px;
            overflow-y: auto;
            padding: 20px;
            background: #f8f9fa;
        }

        .message {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .message.user {
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            flex-shrink: 0;
        }

        .message.bot .message-avatar {
            background: linear-gradient(135deg, var(--ceramic-primary), var(--ceramic-secondary));
        }

        .message.user .message-avatar {
            background: #6c757d;
        }

        .message-content {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 18px;
            word-wrap: break-word;
            line-height: 1.5;
        }

        .message.bot .message-content {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 18px 18px 18px 4px;
        }

        .message.user .message-content {
            background: linear-gradient(135deg, var(--ceramic-primary), var(--ceramic-secondary));
            color: white;
            border-radius: 18px 18px 4px 18px;
        }

        /* Quick Questions */
        .quick-questions {
            padding: 15px 20px;
            background: white;
            border-top: 1px solid #e0e0e0;
        }

        .quick-questions h6 {
            margin-bottom: 10px;
            color: #666;
            font-size: 0.9rem;
        }

        .question-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .question-chip {
            background: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 20px;
            padding: 8px 15px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .question-chip:hover {
            background: var(--ceramic-primary);
            color: white;
            border-color: var(--ceramic-primary);
        }

        /* Chat Input */
        .chat-input {
            padding: 20px;
            background: white;
            border-top: 1px solid #e0e0e0;
        }

        .input-group {
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }

        .message-input {
            flex: 1;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            padding: 12px 20px;
            resize: none;
            min-height: 50px;
            max-height: 120px;
            outline: none;
            transition: all 0.3s ease;
        }

        .message-input:focus {
            border-color: var(--ceramic-primary);
        }

        .send-button {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--ceramic-primary), var(--ceramic-secondary));
            border: none;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .send-button:hover {
            transform: scale(1.05);
        }

        .send-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Typing Indicator */
        .typing-indicator {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .typing-dots {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 18px 18px 18px 4px;
            padding: 12px 16px;
            display: flex;
            gap: 4px;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #bbb;
            animation: typing 1.4s ease-in-out infinite both;
        }

        .typing-dot:nth-child(1) { animation-delay: -0.32s; }
        .typing-dot:nth-child(2) { animation-delay: -0.16s; }
        .typing-dot:nth-child(3) { animation-delay: 0s; }

        @keyframes typing {
            0%, 80%, 100% {
                transform: scale(0.8);
                opacity: 0.5;
            }
            40% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .chat-container {
                margin: -40px 20px 20px;
                border-radius: 15px;
            }
            
            .chatbot-hero h1 {
                font-size: 2rem;
            }
            
            .message-content {
                max-width: 85%;
            }
        }
    </style>
</head>
<body>
    @include('includes.navbar')

    <!-- Chatbot Hero Section -->
    <section class="chatbot-hero ceramic-gradient">
        <div class="container">
            <h1><i class="fas fa-robot me-3"></i>Pemandu Virtual CAD</h1>
            <p>Tanyakan apapun tentang Kampung Wisata Keramik Dinoyo. Pemandu virtual kami siap membantu Anda mengenal lebih dalam tentang kerajinan keramik tradisional!</p>
        </div>
    </section>

    <!-- Chat Container -->
    <div class="container">
        <div class="chat-container">
            <!-- Chat Header -->
            <div class="chat-header">
                <div class="chat-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="chat-info">
                    <h5>CAD Assistant</h5>
                    <p>Pemandu Virtual Kampung Wisata Keramik Dinoyo</p>
                </div>
                <div class="status-indicator" id="statusIndicator" title="Status Chatbot"></div>
            </div>

            <!-- Chat Messages -->
            <div class="chat-messages" id="chatMessages">
                <div class="message bot">
                    <div class="message-avatar">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="message-content">
                        <p>Halo! Selamat datang di Kampung Wisata Keramik Dinoyo! 👋</p>
                        <p>Saya adalah pemandu virtual Kampung Wisata Keramik Dinoyo. Silakan tanyakan apapun tentang keramik, workshop, fasilitas, atau informasi lainnya tentang kampung wisata kami!</p>
                    </div>
                </div>
            </div>

            <!-- Quick Questions -->
            <div class="quick-questions">
                <h6>Pertanyaan Cepat:</h6>
                <div class="question-chips">
                    <div class="question-chip" onclick="sendQuickQuestion('Apa itu Kampung Wisata Keramik Dinoyo?')">Tentang Kampung Keramik</div>
                    <div class="question-chip" onclick="sendQuickQuestion('Berapa harga workshop keramik?')">Harga Workshop</div>
                    <div class="question-chip" onclick="sendQuickQuestion('Jam operasional kapan?')">Jam Buka</div>
                    <div class="question-chip" onclick="sendQuickQuestion('Bagaimana cara booking?')">Cara Booking</div>
                    <div class="question-chip" onclick="sendQuickQuestion('Fasilitas apa saja yang tersedia?')">Fasilitas</div>
                </div>
            </div>

            <!-- Chat Input -->
            <div class="chat-input">
                <div class="input-group">
                    <textarea 
                        class="message-input" 
                        id="messageInput" 
                        placeholder="Ketik pertanyaan Anda tentang keramik Dinoyo..."
                        rows="1"></textarea>
                    <button class="send-button" id="sendButton" onclick="sendMessage()">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('includes.footer')

    <!-- Back to Top -->
    <a href="#" class="scrollToTop">
        <i class="fas fa-chevron-up"></i>
    </a>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery.easing@1.4.1/jquery.easing.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/superfish@1.7.10/dist/js/superfish.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/wow.js@1.2.1/dist/wow.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/waypoints@4.0.1/lib/waypoints.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/counterup2@1.0.4/dist/index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/owl.carousel@2.3.4/dist/owl.carousel.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/isotope-layout@3.0.6/dist/isotope.pkgd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightbox2@2.11.3/dist/lightbox.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-touchswipe@1.6.19/jquery.touchSwipe.min.js"></script>
    
    <script>
        // API Configuration - Hugging Face
        const HF_API_KEY = 'hf_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'; // User will add their key
        const HF_API_URL = 'https://api-inference.huggingface.co/models/microsoft/DialoGPT-medium';
        const HF_BACKUP_MODEL = 'https://api-inference.huggingface.co/models/facebook/blenderbot-400M-distill';
        
        // Validate API key format
        if (!HF_API_KEY || HF_API_KEY.length < 20 || HF_API_KEY === 'hf_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx') {
            console.error('❌ Please set your Hugging Face API key');
            chatbotStatus = 'offline';
        }
        
        // Chatbot status management
        let chatbotStatus = 'checking'; // checking, online, offline
        let retryCount = 0;
        const maxRetries = 3;

        // DOM elements
        const chatMessages = document.getElementById('chatMessages');
        const chatInput = document.getElementById('messageInput');
        const sendButton = document.getElementById('sendButton');
        const statusIndicator = document.getElementById('statusIndicator');

        // Quick responses for offline mode
        const quickResponses = {
            'tentang kampung keramik': 'Kampung Wisata Keramik Dinoyo adalah sentra keramik di Kota Malang yang berlokasi di Jl. Mt Haryono 9 No.336, Dinoyo, Kec. Lowokwaru. Kampung ini mayoritas penduduknya bekerja sebagai pengerajin keramik dari bahan baku kaolin.',
            'harga workshop': 'Workshop keramik memiliki berbagai paket: Workshop Dasar (Rp 50.000), Workshop Menengah (Rp 100.000), Workshop Lanjutan (Rp 150.000). Harga sudah termasuk bahan dan pembakaran.',
            'jam operasional': 'Kampung Wisata Keramik Dinoyo buka setiap hari dari jam 08:00 - 15:00 WIB. Untuk informasi khusus hari libur, silakan hubungi langsung.',
            'lokasi': 'Kampung Wisata Keramik Dinoyo berlokasi di Jl. Mt Haryono 9 No.336, Dinoyo, Kec. Lowokwaru, Kota Malang, Jawa Timur.',
            'fasilitas': 'Fasilitas yang tersedia: Taman keramik, Studio workshop, Galeri produk, Mushola/Toilet, Area parkir, Spot foto Instagramable, dan Toko souvenir.',
            'default': 'Halo! Saya adalah pemandu virtual Kampung Wisata Keramik Dinoyo. Saat ini sistem sedang mengalami gangguan, tapi saya masih bisa membantu dengan informasi dasar. Silakan tanyakan tentang lokasi, jam operasional, workshop, atau produk keramik kami.'
        };

        // Initialize chatbot
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-resize textarea
            chatInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 120) + 'px';
            });

            // Send on Enter (but allow Shift+Enter for new line)
            chatInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });

            // Initial health check
            setTimeout(() => {
                checkChatbotHealth();
            }, 1000);

            // Auto-scroll to bottom on load
            window.addEventListener('load', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });

        // Update status indicator
        function updateStatusIndicator() {
            statusIndicator.className = 'status-indicator';
            
            switch(chatbotStatus) {
                case 'online':
                    statusIndicator.classList.add('online');
                    statusIndicator.title = 'Chatbot Online - Siap Membantu';
                    break;
                case 'offline':
                    statusIndicator.classList.add('offline');
                    statusIndicator.title = 'Chatbot Offline - Mode Fallback Aktif';
                    break;
                case 'checking':
                    statusIndicator.classList.add('checking');
                    statusIndicator.title = 'Memeriksa Status Chatbot...';
                    break;
            }
        }

        // Conversation context for Hugging Face
        const conversationContext = `Anda adalah pemandu virtual Kampung Wisata Keramik Dinoyo di Malang, Jawa Timur. Informasi lengkap:

🏺 LOKASI & KEGIATAN:
- Alamat: Jl. Mt Haryono 9 No.336, Dinoyo, Kec. Lowokwaru, Kota Malang
- Workshop keramik, pottery class, wisata edukasi
- Studio, galeri, toko souvenir, spot foto Instagramable

💰 HARGA:
- Workshop Dasar: Rp 50.000/orang
- Workshop Menengah: Rp 100.000/orang  
- Workshop Lanjutan: Rp 150.000/orang
- Produk keramik: Rp 15.000-500.000

📅 OPERASIONAL:
- Setiap hari: 08.00-15.00 WIB
- Gratis masuk kampung

📞 KONTAK:
- Tel: (0341) 123456
- WA: 081234567890
- Email: info@keramikdinoyo.com

🏺 FASILITAS:
- Taman keramik, Studio workshop, Galeri, Mushola/Toilet, Parkir, Spot foto

Jawab ramah dalam bahasa Indonesia, fokus pada informasi keramik Dinoyo.`;

        // Send message function dengan Hugging Face API
        async function sendMessage() {
            const message = chatInput.value.trim();
            if (!message) return;

            console.log('💬 Sending message to Hugging Face:', message);
            
            // Add user message to chat
            addMessage(message, 'user');
            chatInput.value = '';
            chatInput.style.height = 'auto';

            // Show typing indicator
            showTypingIndicator();

            try {
                let response;
                
                if (chatbotStatus === 'online') {
                    // Try Hugging Face API first
                    try {
                        response = await callHuggingFaceAPI(message);
                        console.log('✅ Got response from Hugging Face API');
                        retryCount = 0; // Reset retry count on success
                    } catch (apiError) {
                        console.warn('⚠️ Hugging Face API failed, trying fallback:', apiError.message);
                        
                        // If API fails, mark as offline and use fallback
                        chatbotStatus = 'offline';
                        updateStatusIndicator();
                        response = getFallbackResponse(message);
                    }
                } else {
                    // Use fallback response
                    console.log('📋 Using fallback response');
                    response = getFallbackResponse(message);
                    
                    // Try to reconnect every 5 messages in offline mode
                    if (retryCount % 5 === 0) {
                        setTimeout(() => checkChatbotHealth(), 1000);
                    }
                    retryCount++;
                }
                
                hideTypingIndicator();
                addMessage(response, 'bot');
                
            } catch (error) {
                console.error('❌ Error in sendMessage:', error);
                hideTypingIndicator();
                
                const errorMessage = chatbotStatus === 'offline' 
                    ? getFallbackResponse(message)
                    : 'Maaf, terjadi kesalahan teknis. Silakan coba lagi dalam beberapa saat.';
                    
                addMessage(errorMessage, 'bot');
            }
        }

        // Call Hugging Face API
        async function callHuggingFaceAPI(userMessage, isHealthCheck = false) {
            console.log('🌐 Calling Hugging Face API...', isHealthCheck ? '(Health Check)' : '');
            
            const conversationText = isHealthCheck 
                ? 'Test connection'
                : `${conversationContext}\\n\\nUser: ${userMessage}\\nAssistant:`;
                
            const requestBody = {
                inputs: conversationText,
                parameters: {
                    max_new_tokens: isHealthCheck ? 10 : 200,
                    temperature: 0.7,
                    do_sample: true,
                    top_p: 0.9,
                    repetition_penalty: 1.1,
                    return_full_text: false
                },
                options: {
                    wait_for_model: true,
                    use_cache: false
                }
            };

            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 second timeout

            try {
                console.log('📤 Sending request to Hugging Face API');
                
                const response = await fetch(HF_API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${HF_API_KEY}`,
                        'User-Agent': 'CeramicChatbot/1.0'
                    },
                    body: JSON.stringify(requestBody),
                    signal: controller.signal
                });

                clearTimeout(timeoutId);
                
                console.log('📥 Hugging Face response status:', response.status);
                
                if (!response.ok) {
                    if (response.status === 429) {
                        throw new Error('Rate limit exceeded. Model sedang sibuk, silakan coba lagi sebentar.');
                    } else if (response.status === 401 || response.status === 403) {
                        throw new Error('Invalid API key or access denied.');
                    } else if (response.status === 503) {
                        throw new Error('Model sedang loading, mohon tunggu sebentar dan coba lagi...');
                    } else if (response.status >= 500) {
                        throw new Error('Hugging Face server error');
                    } else {
                        const errorText = await response.text();
                        throw new Error(`API Error ${response.status}: ${errorText}`);
                    }
                }

                const data = await response.json();
                console.log('✅ Hugging Face API Response received');
                
                let botResponse = '';
                
                if (Array.isArray(data) && data[0] && data[0].generated_text) {
                    // Extract only the new response part
                    const fullText = data[0].generated_text;
                    const assistantIndex = fullText.lastIndexOf('Assistant:');
                    if (assistantIndex !== -1) {
                        botResponse = fullText.substring(assistantIndex + 10).trim();
                    } else {
                        botResponse = fullText.replace(conversationText, '').trim();
                    }
                } else if (data.generated_text) {
                    botResponse = data.generated_text.trim();
                } else {
                    throw new Error('Invalid response format from Hugging Face API');
                }

                // Clean up response
                botResponse = botResponse.replace(/^(Assistant:|Bot:|AI:)/i, '').trim();
                
                if (!botResponse || botResponse.length < 3) {
                    throw new Error('Empty or too short response from API');
                }

                return botResponse;

            } catch (error) {
                clearTimeout(timeoutId);
                console.error('❌ Hugging Face API Error:', error);
                
                if (error.name === 'AbortError') {
                    throw new Error('Request timeout - model took too long to respond');
                }
                
                throw error;
            }
        }

        // Get fallback response
        function getFallbackResponse(message) {
            const lowerMessage = message.toLowerCase();
            
            // Check for specific keywords
            for (const [key, response] of Object.entries(quickResponses)) {
                if (key !== 'default' && lowerMessage.includes(key.replace(' ', ''))) {
                    return response;
                }
            }
            
            // Return default fallback
            return quickResponses.default;
        }

        // Check chatbot health
        async function checkChatbotHealth() {
            console.log('🏥 Checking chatbot health...');
            updateStatusIndicator();
            
            if (!HF_API_KEY || HF_API_KEY === 'hf_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' || HF_API_KEY.length < 20) {
                console.log('❌ API key not configured');
                chatbotStatus = 'offline';
                updateStatusIndicator();
                return;
            }
            
            try {
                await callHuggingFaceAPI('test', true);
                console.log('✅ Chatbot health check passed');
                chatbotStatus = 'online';
            } catch (error) {
                console.log('❌ Chatbot health check failed:', error.message);
                chatbotStatus = 'offline';
            }
            
            updateStatusIndicator();
        }

        // Add message to chat
        function addMessage(message, sender) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${sender}`;
            
            const avatar = document.createElement('div');
            avatar.className = 'message-avatar';
            avatar.innerHTML = sender === 'bot' ? '<i class="fas fa-robot"></i>' : '<i class="fas fa-user"></i>';
            
            const content = document.createElement('div');
            content.className = 'message-content';
            content.innerHTML = `<p>${message}</p>`;
            
            messageDiv.appendChild(avatar);
            messageDiv.appendChild(content);
            
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Show typing indicator
        function showTypingIndicator() {
            const typingDiv = document.createElement('div');
            typingDiv.className = 'typing-indicator';
            typingDiv.id = 'typingIndicator';
            
            const avatar = document.createElement('div');
            avatar.className = 'message-avatar';
            avatar.innerHTML = '<i class="fas fa-robot"></i>';
            avatar.style.background = 'linear-gradient(135deg, var(--ceramic-primary), var(--ceramic-secondary))';
            
            const dots = document.createElement('div');
            dots.className = 'typing-dots';
            for (let i = 0; i < 3; i++) {
                const dot = document.createElement('div');
                dot.className = 'typing-dot';
                dots.appendChild(dot);
            }
            
            typingDiv.appendChild(avatar);
            typingDiv.appendChild(dots);
            
            chatMessages.appendChild(typingDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Hide typing indicator
        function hideTypingIndicator() {
            const typingIndicator = document.getElementById('typingIndicator');
            if (typingIndicator) {
                typingIndicator.remove();
            }
        }

        // Send quick question
        function sendQuickQuestion(question) {
            chatInput.value = question;
            sendMessage();
        }

        // Initialize status
        updateStatusIndicator();
    </script>
</body>
</html>