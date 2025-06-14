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
        .chat-section {
            padding: 60px 0;
        }

        .chatbot-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
            overflow: hidden;
        }

        .chat-header {
            color: white;
            padding: 20px 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .chat-avatar {
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .chat-info h5 {
            margin: 0;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .chat-info p {
            margin: 0;
            opacity: 0.8;
            font-size: 0.9rem;
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-left: auto;
            animation: pulse 2s infinite;
        }
        
        /* Enhanced status indicator styles */
        .status-indicator.online {
            background: #28a745;
            animation: none;
        }
        
        .status-indicator.offline {
            background: #dc3545;
            animation: pulse 2s infinite;
        }
        
        .status-indicator.checking {
            background: #ffc107;
            animation: pulse 1s infinite;
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
            padding: 30px;
            background: #f8f9fa;
        }

        .message {
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .message.user {
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: 600;
            flex-shrink: 0;
        }

        .message.bot .message-avatar {
            background: linear-gradient(135deg, #cab491 0%, #a68b57 100%);
            color: white;
        }

        .message.user .message-avatar {
            background: linear-gradient(135deg, #666666 0%, #333333 100%);
            color: white;
        }

        .message-content {
            max-width: 70%;
            padding: 15px 20px;
            border-radius: 20px;
            position: relative;
        }

        .message.bot .message-content {
            background: white;
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .message.user .message-content {
            background: linear-gradient(135deg, #cab491 0%, #a68b57 100%);
            color: white;
        }

        .message-time {
            font-size: 0.75rem;
            color: #999;
            margin-top: 5px;
        }

        /* Typing Indicator */
        .typing-indicator {
            display: none;
            padding: 15px 20px;
            background: white;
            border-radius: 20px;
            border: 1px solid #e9ecef;
            margin-bottom: 20px;
        }

        .typing-dots {
            display: flex;
            gap: 4px;
        }

        .typing-dots span {
            width: 8px;
            height: 8px;
            background: #cab491;
            border-radius: 50%;
            animation: typing 1.4s infinite ease-in-out;
        }

        .typing-dots span:nth-child(1) { animation-delay: -0.32s; }
        .typing-dots span:nth-child(2) { animation-delay: -0.16s; }

        @keyframes typing {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }

        /* Chat Input */
        .chat-input-container {
            padding: 25px 30px;
            background: white;
            border-top: 1px solid #e9ecef;
        }

        .chat-input-group {
            display: flex;
            gap: 15px;
            align-items: flex-end;
        }

        .chat-input {
            flex: 1;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            padding: 12px 20px;
            font-size: 1rem;
            resize: none;
            min-height: 50px;
            max-height: 120px;
            transition: all 0.3s ease;
        }

        .chat-input:focus {
            outline: none;
            border-color: #cab491;
            box-shadow: 0 0 0 0.2rem rgba(202, 180, 145, 0.25);
        }

        .send-button {
            width: 50px;
            height: 50px;
            border: none;
            border-radius: 50%;
            background: linear-gradient(135deg, #cab491 0%, #a68b57 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .send-button:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(202, 180, 145, 0.3);
        }

        .send-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* Quick Questions */
        .quick-questions {
            padding: 20px 30px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }

        .quick-questions h6 {
            color: #666;
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        .question-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .question-chip {
            background: white;
            border: 1px solid #cab491;
            color: #a68b57;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .question-chip:hover {
            background: #cab491;
            color: white;
            transform: translateY(-2px);
        }

        /* Welcome Message */
        .welcome-message {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }

        .welcome-message i {
            font-size: 3rem;
            color: #cab491;
            margin-bottom: 20px;
        }

        .welcome-message h4 {
            color: #333;
            margin-bottom: 10px;
        }

        /* Back to Top */
        .scrollToTop {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #cab491 0%, #a68b57 100%);
            color: white;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .scrollToTop:hover {
            background: linear-gradient(135deg, #a68b57 0%, #8b6f42 100%);
            color: white;
            transform: translateY(-3px);
        }

        /* Footer */
        #footer {
            background: #343a40;
            color: white;
            padding: 60px 0;
        }

        #footer .footer-top {
            border-bottom: 1px solid #495057;
            padding-bottom: 30px;
        }

        #footer h3 {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        #footer ul {
            list-style: none;
            padding: 0;
        }

        #footer ul li {
            margin-bottom: 10px;
        }

        #footer ul li a {
            color: #adb5bd;
            text-decoration: none;
        }

        #footer ul li a:hover {
            color: #fff;
        }

        #footer .social-links {
            margin-top: 10px;
        }

        #footer .social-links a {
            color: #adb5bd;
            margin-right: 15px;
            font-size: 1.2rem;
        }

        #footer .social-links a:hover {
            color: #fff;
        }

        .copyright {
            text-align: center;
            padding: 20px 0;
            font-size: 0.9rem;
            background: #23272b;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .chatbot-hero h1 {
                font-size: 2rem;
            }

            .chatbot-container {
                margin: 0 15px;
            }

            .chat-messages {
                height: 300px;
                padding: 20px;
            }

            .message-content {
                max-width: 85%;
            }

            .chat-input-container,
            .quick-questions {
                padding: 20px;
            }

            #footer {
                padding: 40px 0;
            }

            #footer h3 {
                font-size: 1.25rem;
            }

            #footer .social-links a {
                font-size: 1rem;
            }
        }
    </style>

    {{-- Design layer (also loaded standalone here because embed mode skips the navbar that normally links it) --}}
    <link href="{{ asset('css/ceramic.css') }}" rel="stylesheet">
    <style>
        /* Embed mode — chat fills the floating popup, no page chrome */
        body.chat-embed { margin: 0 !important; background: #fff !important; }
        body.chat-embed .chat-section { padding: 0 !important; background: #fff !important; }
        body.chat-embed .chat-section .container { max-width: 100% !important; padding: 0 !important; }
        body.chat-embed .chatbot-container {
            border: 0 !important; border-radius: 0 !important; box-shadow: none !important;
            max-width: 100% !important; height: 100vh; display: flex; flex-direction: column;
        }
        body.chat-embed .chat-messages { flex: 1 1 auto; }
    </style>
</head>

<body class="{{ request()->boolean('embed') ? 'chat-embed' : '' }}">
    @unless(request()->boolean('embed'))
    @include('layouts.navbar')

    <!-- Chatbot Hero Section -->
    <section class="chatbot-hero">
        <div class="container">
            <h1>Pemandu Virtual CAD</h1>
            <p>Tanyakan apapun tentang Kampung Wisata Keramik Dinoyo. Pemandu virtual kami siap membantu Anda mengenal lebih dalam tentang kerajinan keramik tradisional!</p>
        </div>
    </section>
    @endunless

    <!-- Chat Section -->
    <section class="chat-section">
        <div class="container">
            <div class="chatbot-container">
                <!-- Chat Header -->
                <div class="chat-header">
                    <div class="chat-avatar">
                        <i class="fas fa-robot"></i>
                    </div>                    <div class="chat-info">
                        <h5>Pemandu CAD</h5>
                        <p>Pemandu Virtual Kampung Wisata Keramik Dinoyo</p>
                    </div>
                    <div class="status-indicator"></div>
                </div>

                <!-- Chat Messages -->
                <div class="chat-messages" id="chatMessages">                    <div class="welcome-message">
                        <i class="fas fa-comments"></i>
                        <h4>Halo! Selamat datang di website CAD (Cerami Art Dinoyo)!</h4>
                        <p>Saya adalah pemandu virtual Kampung Wisata Keramik Dinoyo. Silakan tanyakan apapun tentang keramik, workshop, fasilitas, atau informasi lainnya tentang kampung wisata kami!</p>
                    </div>
                </div>

                <!-- Quick Questions -->
                <div class="quick-questions">
                    <h6>Pertanyaan Cepat:</h6>                    <div class="question-chips">
                        <div class="question-chip" onclick="sendQuickQuestion('Apa itu Kampung Wisata Keramik Dinoyo?')">Tentang Kampung Keramik</div>
                        <div class="question-chip" onclick="sendQuickQuestion('Apakah ada workshop pembuatan keramik?')">Workshop Keramik</div>
                        <div class="question-chip" onclick="sendQuickQuestion('Produk keramik apa saja yang dijual?')">Produk Tersedia</div>
                        <div class="question-chip" onclick="sendQuickQuestion('Berapa biaya workshop keramik?')">Biaya Workshop</div>
                        <div class="question-chip" onclick="sendQuickQuestion('Di mana lokasi Kampung Keramik Dinoyo?')">Lokasi</div>
                        <div class="question-chip" onclick="sendQuickQuestion('Apa jam operasional kampung keramik?')">Jam Operasional</div>
                    </div>
                </div>

                <!-- Chat Input -->
                <div class="chat-input-container">
                    <div class="chat-input-group">
                        <textarea 
                            class="chat-input" 
                            id="chatInput" 
                            placeholder="Ketik pesan Anda di sini..."
                            rows="1"
                        ></textarea>
                        <button class="send-button" id="sendButton" onclick="sendMessage()">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @unless(request()->boolean('embed'))
    <!--========================
      Footer
    ============================-->
    @include('layouts.footer')

    <a href="#" class="scrollToTop"><i class="ion-chevron-up"></i></a>
    @endunless

    <!-- JavaScript Libraries -->
    <script src="{{ asset('lib/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('lib/jquery/jquery-migrate.min.js') }}"></script>
    <!-- Bootstrap JS from CDN for consistency -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('lib/superfish/hoverIntent.js') }}"></script>
    <script src="{{ asset('lib/superfish/superfish.min.js') }}"></script>
    <script src="{{ asset('lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('lib/counterup/counterup.min.js') }}"></script>
    <script src="{{ asset('lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('lib/isotope/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('lib/lightbox/js/lightbox.min.js') }}"></script>
    <script src="{{ asset('lib/touchSwipe/jquery.touchSwipe.min.js') }}"></script>
    <!-- Main Javascript File -->
    <script src="{{ asset('js/main.js') }}"></script>
    
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

        // Chat elements
        const chatMessages = document.getElementById('chatMessages');
        const chatInput = document.getElementById('chatInput');
        const sendButton = document.getElementById('sendButton');
        
        // Status indicator
        const statusIndicator = document.querySelector('.status-indicator');
        
        // Initialize chatbot
        document.addEventListener('DOMContentLoaded', function() {
            checkChatbotHealth();
            updateStatusIndicator();
        });
        
        // Fallback responses untuk ketika API tidak tersedia
        const fallbackResponses = {
            'tentang kampung keramik': 'Kampung Wisata Keramik Dinoyo adalah sentra keramik di Kota Malang yang berlokasi di Jl. Mt Haryono 9 No.336, Dinoyo, Kec. Lowokwaru. Kampung ini mayoritas penduduknya bekerja sebagai pengerajin keramik dari bahan baku kaolin.',
            'workshop keramik': 'Ya, kami menyediakan workshop pembuatan keramik! Workshop tersedia setiap hari dari jam 08:00-15:00. Anda bisa belajar langsung cara membuat keramik dari para pengrajin ahli.',
            'produk tersedia': 'Kami menjual berbagai produk keramik seperti vas bunga, mug, gelas mini, piring, mangkuk, dan berbagai souvenir keramik dengan warna dan corak yang beraneka rupa.',
            'biaya workshop': 'Biaya workshop bervariasi tergantung paket yang dipilih. Silakan hubungi kami untuk informasi detail mengenai harga workshop.',
            'lokasi': 'Kampung Wisata Keramik Dinoyo berlokasi di Jl. Mt Haryono 9 No.336, Dinoyo, Kec. Lowokwaru, Kota Malang, Jawa Timur.',
            'jam operasional': 'Kami buka setiap hari dari jam 08:00 - 15:00. Jika ada acara khusus atau hari libur, akan diinformasikan lebih lanjut.',
            'default': 'Halo! Saya adalah pemandu virtual Kampung Wisata Keramik Dinoyo. Saat ini sistem sedang mengalami gangguan, tapi saya masih bisa membantu dengan informasi dasar. Silakan tanyakan tentang lokasi, jam operasional, workshop, atau produk keramik kami.'
        };
        
        // Function to get fallback response
        function getFallbackResponse(message) {
            const lowerMessage = message.toLowerCase();
            
            for (const [key, response] of Object.entries(fallbackResponses)) {
                if (lowerMessage.includes(key) || lowerMessage.includes(key.replace(' ', ''))) {
                    return response;
                }
            }
            
            return fallbackResponses.default;
        }
        
        // Check chatbot health dengan Cohere API validation
        async function checkChatbotHealth() {
            console.log('🔍 Checking Cohere API health...');
            chatbotStatus = 'checking';
            updateStatusIndicator();
            
            // First validate API key format
            if (!COHERE_API_KEY || COHERE_API_KEY === '' || COHERE_API_KEY.length < 20) {
                console.error('❌ Invalid Cohere API key format');
                chatbotStatus = 'offline';
                updateStatusIndicator();
                return;
            }
            
            try {
                console.log('🔑 Cohere API Key validation passed');
                console.log('🧪 Testing Cohere API connectivity...');
                
                const testMessage = 'Hello';
                const response = await callCohereAPI(testMessage, true); // true = health check
                
                if (response && response.length > 0) {
                    chatbotStatus = 'online';
                    console.log('✅ Cohere API is online and healthy');
                    console.log('📊 Test response:', response.substring(0, 50) + '...');
                } else {
                    throw new Error('Empty response from Cohere API');
                }
            } catch (error) {
                chatbotStatus = 'offline';
                console.warn('⚠️ Cohere API unavailable:', error.message);
                
                // Check if it's an API key issue
                if (error.message.includes('invalid_api_token') || error.message.includes('401')) {
                    console.error('🔑 Cohere API Key appears to be invalid or expired');
                } else if (error.message.includes('quota') || error.message.includes('rate_limit')) {
                    console.error('📊 Cohere API quota exceeded or rate limited');
                } else if (error.message.includes('timeout')) {
                    console.error('⏰ Cohere API request timeout');
                } else {
                    console.error('🌐 Network or Cohere API service issue');
                }
            }
            
            updateStatusIndicator();
        }
        
        // Update status indicator
        function updateStatusIndicator() {
            if (!statusIndicator) return;
            
            statusIndicator.className = 'status-indicator';
            
            switch (chatbotStatus) {
                case 'online':
                    statusIndicator.classList.add('online');
                    statusIndicator.title = 'Chatbot Online - AI Assistant Aktif';
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
        }        // System prompt for CAD Assistant
        const systemPrompt = `Kamu adalah Seorang Pemandu di Kampung Wisata Keramik Dinoyo di Website yang bernama CAD atau Ceramic Art Dinoyo. Tujuanmu adalah Menjawab pertanyaan dari Pengunjung Kampung Wisata Keramik Dinoyo. Awali percakapan dengan sambutan kepada pengunjung. Gunakan bahasa yang mudah dipahami. Berikut saya berikan beberapa data terkait Kampung Wisata Keramik Dinoyo yang bisa kamu jadikan acuan untuk menjawab pertanyaan dari user.

KNOWLEDGE BASE LENGKAP KAMPUNG WISATA KERAMIK DINOYO:

1. "Apa itu Kampung Wisata Keramik Dinoyo?"
- Kampoeng Wisata Keramik Dinoyo ada sebuah kampung yang mayoritas penduduknya bekerja sebagai pengerajin dan penjual keramik dari bahan baku kaolin, bukan dari tanah liat. Kalian bisa melihat, mengamati, dan membuat keramik secara langsung ditempat ini. Adapula spot foto yang lagi hits berada sekitar eks pabrik kermik Dinoyo.
- Kampung Wisata Keramik Dinoyo adalah sentra keramik di Kota Malang. Mulai dari keramik souvenir berbagai bentuk, dari gelas mini, mug, vas bunga dengan warna dan corak beraneka rupa. Namun kampung wisata keramik bukan hanya jadi sentra penjualan saja. Tetapi di sini kamu juga bisa belajar dan mengenal kerajinan keramik.

2. LOKASI: Jl. Mt Haryono 9 No.336, Dinoyo, Kec. Lowokwaru, Kota Malang, Jawa Timur

3. JAM OPERASIONAL: Buka Setiap Hari dari Jam 08:00 - 15:00. Jika terdapat acara atau hari libur maka akan di informasikan lebih lanjut.

4. BIAYA MASUK: Tidak ada biaya masuk ke kampung wisata ini, dengan kata lain kamu bisa masuk secara gratis. Namun usahakan untuk membeli barang jualan penduduk lokal sebagai oleh-oleh untuk keluarga di rumah. Dengan membelinya, kamu dapat membantu perekonomian masyarakat setempat.

5. BAHASA: Penduduk setempat menggunakan Bahasa lokal untuk berkomunikasi. Tak ada salahnya untuk belajar bahasa daerah yang kamu kunjungi ini agar kamu mudah melakukan komunikasi dengan penduduk lokal.

6. FOTOGRAFI: Bisa, Pastikan meminta izin kepada warga setempat terlebih dahulu sebelum melakukan apapun. Termasuk ketika kamu hendak mengambil gambar.

7. ATURAN KHUSUS: Setiap daerah adat memiliki kepercayaan dan hukum adat yang berbeda. Sebagai pengunjung hargailah tradisi yang ada dan patuhi aturan hukum daerah setempat, baik yang tertulis maupun yang tidak tertulis.

8. FASILITAS:
- Taman keramik
- Mushola / Toilet
- Parkir
- Tanda arah kampung
- Gapura
- 20 Galeri dan toko keramik
- 24 pengrajin
- 1 Ruang workshop
- Warung makan
- 2 caffe
- Pabrik Keramik

9. SEJARAH: Kerajinan Keramik Dinoyo merupakan salah satu komoditas khas yang diproduksi oleh suatu kawasan di Kota Malang. Kerajinan ini telah diproduksi turun menurun dan turut andil untuk mendongkrak kegiatan ekonomi Kota Malang. Perkembangan kawasan ini bermula dari suatu kawasan yang dihuni dengan mayoritas warganya yang berprofesi sebagai pengrajin keramik. Kampung Keramik Dinoyo mengalami perkembangan pesat ditandai dengan pernah berdirinya sebuah pabrik keramik. Kerajinan keramik di Kampung Wisata Keramik Dinoyo sudah berkembang sejak Kerajaan Kanjuruhan.

10. AKTIVITAS YANG BISA DILAKUKAN:
a. Berkeliling ke sentra pembuatan keramik
b. Belajar proses pembuatan keramik di Museum Keramik Dinoyo
c. Mengenal bahan baku pembuatan keramik, seperti lempung putih, kaulin, pasir kwarsa, ballclay, dan feldspar
d. Bermain dengan membuat berbagai mainan dari lempung putih
e. Mengetahui sejarah Kampung Wisata Keramik Dinoyo yang dimulai sejak tahun 1957
f. Mengetahui bagaimana masyarakat Dinoyo mengadopsi bahan-bahan keramik dari Cina dan memadukannya dengan bahan keramik lokal

11. WORKSHOP PEMBUATAN KERAMIK:
- Ya, di Kampung Keramik Dinoyo ada program edukasi dan workshop pembuatan keramik
- Paket wisata edukasi meliputi: Pembentukan keramik, Mewarnai keramik, Desain keramik, Pelatihan membuat keramik
- Biaya paket wisata edukasi adalah Rp 50.000 per peserta
- Terbuka untuk pelajar, mahasiswa, dan keluarga
- Pendaftaran bisa dilakukan secara offline langsung ke tempat atau online melalui booking di website

12. PRODUK KERAMIK YANG DIJUAL:
Mug, Vas, Cangkir mini, Teko, Hiasan, Souvenir, Pot bunga, Guci, Piring keramik

13. KEUNIKAN KERAMIK DINOYO:
Keramik Dinoyo memiliki ciri khas corak, desain, dan warna natural yang sejuk. Motif yang sering digunakan adalah bunga dan daun. Keramik Dinoyo umumnya terbuat dari tanah liat dan porselen.

14. PESANAN CUSTOM:
Ya, Kampung Keramik Dinoyo menerima pesanan keramik custom. Silahkan pergi ke Katalog dan cari pengrajin yang membuka untuk Keramik Custom.

15. TOKO OLEH-OLEH:
Tentu saja, tidak hanya oleh-oleh tetapi kami punya souvenir yang bisa digunakan sebagai bingkisan untuk tamu di acara tertentu seperti pernikahan, dll.

16. TEMPAT PARKIR:
Tempat parkir disediakan di tempat workshop atau pabrik

17. FASILITAS DISABILITAS:
Tidak ada untuk saat ini, maaf

18. EVENT/FESTIVAL:
Silahkan cek tab informasi untuk mengetahui event terdekat di Kampung ini

19. PROMO/DISKON:
Untuk Sementara ini tidak ada

20. KONTAK:
Telp: 0812-3553-1979
Instagram: keramikdinoyo

21. KEHILANGAN BARANG:
Segala kehilangan barang merupakan bentuk tanggung jawab masing-masing pengunjung

22. CARA MENGGUNAKAN WEBSITE CAD:
Saat tiba di Website kami, kami sarankan anda untuk mendaftar sebagai pengunjung dengan menekan register dibagian kanan atas halaman. Fitur-fitur utama:
A. Informasi: berisi Informasi terkini mengenai Kampung Wisata Keramik Dinoyo
B. Katalog: marketplace dari pengrajin yang ada di Kampung Wisata Keramik Dinoyo
C. Booking: untuk workshop keramik dengan 2 paket untuk pengunjung
D. Testimoni: Pengunjung dapat meninggalkan Testimoni
E. VTour: melihat area dan pabrik dari Kampung Wisata Keramik Dinoyo Secara Online
F. Contact: informasi kontak yang bisa dihubungi

23. PEMESANAN ONLINE:
Kami menyediakan 2 paket untuk pengunjung. Setelah menekan salah satu dari paket maka Pengunjung akan diarahkan ke menu detail booking untuk mengisi informasi yang dibutuhkan. Perlu diketahui 2 pengunjung atau lebih tidak bisa memilih tanggal yang sama.

24. HARI LIBUR NASIONAL:
Hari Libur dan sebagainya di informasikan di bagian Informasi

25. HEWAN PELIHARAAN:
Tidak bisa, dikhawatirkan akan mengganggu kegiatan pengrajin dan workshop setempat.

26. AREA BERMAIN ANAK:
Tidak ada, anak-anak difokuskan untuk workshop agar mereka dapat mengenal bagaimana pembuatan dari keramik.

Jawab pertanyaan dengan informatif dan ramah sesuai data di atas. Jika ditanya hal di luar topik, arahkan kembali ke informasi Kampung Keramik Dinoyo.`;

        // Initialize chat
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-resize textarea
            chatInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });

            // Send message on Enter key
            chatInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });

            // Back to top functionality
            const backToTop = document.querySelector('.scrollToTop');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 100) {
                    backToTop.style.display = 'flex';
                } else {
                    backToTop.style.display = 'none';
                }
            });

            backToTop.addEventListener('click', (e) => {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });

        // Send message function dengan Cohere API
        async function sendMessage() {
            const message = chatInput.value.trim();
            if (!message) return;

            console.log('💬 Sending message to Cohere:', message);
            
            // Add user message to chat
            addMessage(message, 'user');
            chatInput.value = '';
            chatInput.style.height = 'auto';

            // Show typing indicator
            showTypingIndicator();

            try {
                let response;
                
                if (chatbotStatus === 'online') {
                    // Try Cohere API first
                    try {
                        response = await callCohereAPI(message);
                        console.log('✅ Got response from Cohere API');
                        retryCount = 0; // Reset retry count on success
                    } catch (apiError) {
                        console.warn('⚠️ Cohere API failed, trying fallback:', apiError.message);
                        
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

        // Call Cohere API dengan enhanced error handling dan debugging
        async function callCohereAPI(userMessage, isHealthCheck = false) {
            console.log('🌐 Calling Cohere API...', isHealthCheck ? '(Health Check)' : '');
            console.log('🔑 Using Cohere API Key:', COHERE_API_KEY.substring(0, 8) + '...' + COHERE_API_KEY.substring(COHERE_API_KEY.length - 4));
            
            const prompt = isHealthCheck 
                ? 'Respond with "OK" if you can receive this message.'
                : `${systemPrompt}\n\nPertanyaan pengguna: ${userMessage}\n\nJawaban sebagai pemandu CAD:`;
            
            const requestBody = {
                model: 'command-light',
                prompt: prompt,
                max_tokens: isHealthCheck ? 10 : 300,
                temperature: 0.7,
                k: 0,
                stop_sequences: [],
                return_likelihoods: 'NONE'
            };

            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 15000); // 15 second timeout

            try {
                console.log('📤 Sending request to Cohere API');
                console.log('📦 Request payload size:', JSON.stringify(requestBody).length, 'chars');
                
                const response = await fetch(COHERE_API_URL, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${COHERE_API_KEY}`,
                        'Content-Type': 'application/json',
                        'Cohere-Version': '2022-12-06'
                    },
                    body: JSON.stringify(requestBody),
                    signal: controller.signal
                });

                clearTimeout(timeoutId);
                
                console.log('📥 Cohere response status:', response.status);
                console.log('📥 Response headers:', Object.fromEntries(response.headers.entries()));
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('❌ Cohere API Error Response:', errorText);
                    
                    // Parse error details
                    let errorDetails = 'Unknown error';
                    try {
                        const errorJson = JSON.parse(errorText);
                        errorDetails = errorJson.message || errorText;
                    } catch (e) {
                        errorDetails = errorText;
                    }
                    
                    throw new Error(`Cohere API request failed: ${response.status} - ${errorDetails}`);
                }

                const data = await response.json();
                console.log('✅ Cohere API Response received');
                console.log('📊 Response structure:', Object.keys(data));
                
                if (!data.generations || !data.generations[0] || !data.generations[0].text) {
                    console.error('❌ Invalid Cohere response structure:', data);
                    throw new Error('Invalid response structure from Cohere API');
                }
                
                const responseText = data.generations[0].text.trim();
                console.log('📝 Response text length:', responseText.length);
                console.log('📝 Response preview:', responseText.substring(0, 100) + '...');
                
                return responseText;
                
            } catch (error) {
                clearTimeout(timeoutId);
                
                if (error.name === 'AbortError') {
                    console.error('⏰ Request timeout');
                    throw new Error('Request timeout - silakan coba lagi');
                }
                
                console.error('❌ Cohere API Call Error Details:', {
                    message: error.message,
                    stack: error.stack,
                    name: error.name
                });
                throw error;
            }
        }

        // Add message to chat dengan status indicator
        function addMessage(text, sender) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${sender}`;

            const avatar = document.createElement('div');
            avatar.className = 'message-avatar';
            avatar.innerHTML = sender === 'user' ? '<i class="fas fa-user"></i>' : '<i class="fas fa-robot"></i>';

            const content = document.createElement('div');
            content.className = 'message-content';
            
            // Add status indicator for bot messages
            if (sender === 'bot' && chatbotStatus === 'offline') {
                text = '<small class="text-muted"><i class="fas fa-exclamation-triangle"></i> Mode Offline</small><br>' + text;
            }
            
            content.innerHTML = text.replace(/\n/g, '<br>');

            const time = document.createElement('div');
            time.className = 'message-time';
            time.textContent = new Date().toLocaleTimeString('id-ID', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });

            content.appendChild(time);
            messageDiv.appendChild(avatar);
            messageDiv.appendChild(content);

            // Remove welcome message if exists
            const welcomeMessage = chatMessages.querySelector('.welcome-message');
            if (welcomeMessage) {
                welcomeMessage.remove();
            }

            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Show typing indicator
        function showTypingIndicator() {
            const typingDiv = document.createElement('div');
            typingDiv.className = 'message bot';
            typingDiv.id = 'typingIndicator';

            const avatar = document.createElement('div');
            avatar.className = 'message-avatar';
            avatar.innerHTML = '<i class="fas fa-robot"></i>';

            const content = document.createElement('div');
            content.className = 'typing-indicator';
            content.style.display = 'block';
            content.innerHTML = `
                <div class="typing-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            `;

            typingDiv.appendChild(avatar);
            typingDiv.appendChild(content);
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

        // Disable send button when input is empty
        chatInput.addEventListener('input', function() {
            sendButton.disabled = !this.value.trim();
        });
    </script>
</body>
</html>
