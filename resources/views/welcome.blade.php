<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hôpital Sendwe - Lubumbashi | Excellence en Soins de Santé - RDC</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f2b3d 0%, #1a4d8c 50%, #2c6e9e 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        :root {
            --primary: #1a4d8c;
            --primary-dark: #0e3570;
            --primary-light: #2c6e9e;
            --secondary: #d4af37;
            --secondary-dark: #b8960c;
            --white: #ffffff;
            --gray-50: #f8f9fa;
            --gray-100: #f1f3f5;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --gray-600: #6c757d;
            --gray-800: #343a40;
            --success: #2ecc71;
            --danger: #e74c3c;
        }
        
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            padding: 1rem 0;
            transition: all 0.3s ease;
        }
        
        .navbar.scrolled {
            padding: 0.5rem 0;
            background: white;
        }
        
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }
        
        .logo-text h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            margin: 0;
        }
        
        .logo-text p {
            font-size: 0.7rem;
            color: var(--secondary);
            font-weight: 600;
            letter-spacing: 1px;
        }
        
        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--gray-800);
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: var(--primary);
        }
        
        .btn {
            padding: 0.7rem 1.8rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            border: none;
            font-size: 0.9rem;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(26, 77, 140, 0.3);
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }
        
        .btn-outline:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: var(--secondary);
            color: var(--primary-dark);
        }
        
        .btn-secondary:hover {
            background: var(--secondary-dark);
            transform: translateY(-2px);
        }
        
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding-top: 80px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }
        
        .hero-text h1 {
            font-size: 3.5rem;
            font-weight: 800;
            color: white;
            line-height: 1.2;
            margin-bottom: 1.5rem;
        }
        
        .hero-text h1 span {
            color: var(--secondary);
        }
        
        .hero-text p {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        
        .hero-stats {
            display: flex;
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .stat {
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--secondary);
        }
        
        .stat-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.8rem;
        }
        
        .hero-image {
            position: relative;
        }
        
        .hero-image img {
            width: 100%;
            border-radius: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            object-fit: cover;
            height: 450px;
        }
        
        .floating-card {
            position: absolute;
            background: white;
            padding: 1rem;
            border-radius: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            animation: float 3s ease-in-out infinite;
        }
        
        .floating-card-1 {
            top: -20px;
            right: -30px;
            animation-delay: 0s;
        }
        
        .floating-card-2 {
            bottom: 50px;
            left: -40px;
            animation-delay: 1s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .features {
            padding: 5rem 0;
            background: white;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .section-title h2 {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        .section-title p {
            color: var(--gray-600);
            font-size: 1.1rem;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            text-align: center;
            border: 1px solid var(--gray-200);
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 2rem;
        }
        
        .feature-card h3 {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: var(--gray-800);
        }
        
        .feature-card p {
            color: var(--gray-600);
            line-height: 1.6;
        }
        
        .gallery-section {
            padding: 5rem 0;
            background: var(--gray-50);
        }
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .gallery-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .gallery-card:hover {
            transform: translateY(-8px);
        }
        
        .gallery-card img {
            width: 100%;
            height: 240px;
            object-fit: cover;
            transition: all 0.4s;
        }
        
        .gallery-card:hover img {
            transform: scale(1.03);
        }
        
        .gallery-card .caption {
            padding: 1.2rem;
            text-align: center;
            font-weight: 600;
            color: var(--primary);
            background: white;
        }
        
        .rdc-tag {
            display: inline-block;
            background: var(--secondary);
            color: var(--primary-dark);
            font-size: 0.7rem;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            margin-left: 8px;
            font-weight: bold;
        }
        
        .services {
            padding: 5rem 0;
            background: var(--gray-50);
        }
        
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
        
        .service-item {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 20px;
            transition: all 0.3s;
        }
        
        .service-item i {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        .service-item h4 {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        
        .cta {
            padding: 5rem 0;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            text-align: center;
        }
        
        .cta h2 {
            font-size: 2.5rem;
            color: white;
            margin-bottom: 1rem;
        }
        
        .cta p {
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }
        
        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .footer {
            background: #0a1a2a;
            color: white;
            padding: 3rem 0;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
        
        .footer-section h4 {
            margin-bottom: 1rem;
            color: var(--secondary);
        }
        
        .footer-section p, .footer-section a {
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.8;
            text-decoration: none;
        }
        
        .footer-section a:hover {
            color: var(--secondary);
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .social-links a {
            width: 35px;
            height: 35px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .social-links a:hover {
            background: var(--secondary);
            transform: translateY(-3px);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.5);
        }
        
        @media (max-width: 768px) {
            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
            }
            .hero-text h1 {
                font-size: 2.5rem;
            }
            .hero-stats {
                justify-content: center;
            }
            .nav-links {
                display: none;
            }
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar" id="navbar">
    <div class="container">
        <div class="nav-content">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-hospital-user"></i>
                </div>
                <div class="logo-text">
                    <h1>HÔPITAL SENDWE</h1>
                    <p>Lubumbashi • RDC <span class="rdc-tag">🇨🇩 RD Congo</span></p>
                </div>
            </div>
            <div class="nav-links">
                <a href="#accueil">Accueil</a>
                <a href="#services">Services</a>
                <a href="#galerie">Galerie</a>
                <a href="#contact">Contact</a>
                
                <!-- 🔑 UNIQUEMENT CONNEXION -->
                <a href="{{ route('login') }}" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Connexion
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero" id="accueil">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text" data-aos="fade-right">
                <h1>Application web de gestion des dossiers médicaux <br> <span>Votre Santé, Notre Mission</span></h1>
                <p>Hôpital Sendwe - Lubumbashi (RDC). Des soins de qualité avec une technologie de pointe et une équipe médicale dévouée à votre bien-être. Ici, chaque patient compte, chaque vie est précieuse.</p>
                <div class="hero-stats">
                    <div class="stat">
                        <div class="stat-number">10+</div>
                        <div class="stat-label">Médecins Experts</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">1000+</div>
                        <div class="stat-label">Patients Satisfaits</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Service d'Urgence</div>
                    </div>
                </div>
                <div style="margin-top: 2rem;">
                    <!-- 🔑 UNIQUEMENT CONNEXION -->
                    <a href="{{ route('login') }}" class="btn btn-secondary">
                        <i class="fas fa-sign-in-alt"></i> Accéder à mon espace
                    </a>
                </div>
            </div>
            <div class="hero-image" data-aos="fade-left">
                <img src="https://pbs.twimg.com/media/GY5fsKlWYAAhTRI.jpg" alt="Hôpital Sendwe Lubumbashi">
                <div class="floating-card floating-card-1">
                    <i class="fas fa-stethoscope" style="color: var(--primary); font-size: 1.5rem;"></i>
                    <div>
                        <strong>Consultation</strong><br>
                        <small>Disponible 24/7</small>
                    </div>
                </div>
                <div class="floating-card floating-card-2">
                    <i class="fas fa-microscope" style="color: var(--primary); font-size: 1.5rem;"></i>
                    <div>
                        <strong>Laboratoire</strong><br>
                        <small>Résultats rapides</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Galerie -->
<section class="gallery-section" id="galerie">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Hôpital Sendwe en images <i class="fas fa-camera-retro"></i></h2>
            <p>Découvrez nos installations modernes et le visage de nos patients & personnels</p>
        </div>
        <div class="gallery-grid">
            <div class="gallery-card" data-aos="fade-up" data-aos-delay="100">
                <img src="https://pbs.twimg.com/media/GY5fsKlWYAAhTRI.jpg" alt="Hôpital Sendwe">
                <div class="caption"><i class="fas fa-building"></i> Hôpital Sendwe - Entrée principale</div>
            </div>
            
            <div class="gallery-card" data-aos="fade-up" data-aos-delay="250">
                <img src="data:image/jpeg;base64,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" alt="Salle d'attente">
                <div class="caption"><i class="fas fa-users"></i> Équipements modernes</div>
            </div>
            <div class="gallery-card" data-aos="fade-up" data-aos-delay="300">
                <img src="https://images.unsplash.com/photo-1593642634443-44adaa06623a?w=500&h=300&fit=crop" alt="Équipe soignante">
                <div class="caption"><i class="fas fa-heartbeat"></i> Notre équipe soignante</div>
            </div>
            <div class="gallery-card" data-aos="fade-up" data-aos-delay="350">
                <img src="https://images.unsplash.com/photo-1532938911079-1b06ac7ceec7?w=500&h=300&fit=crop" alt="Patients heureux">
                <div class="caption"><i class="fas fa-smile"></i> Patients satisfaits</div>
            </div>
        </div>
        <p style="text-align: center; margin-top: 1.5rem; color: var(--gray-600); font-style: italic;"><i class="fas fa-hand-holding-heart"></i> RDC - Hôpital Sendwe : l'excellence médicale au service de tous nos compatriotes.</p>
    </div>
</section>

<!-- Features Section -->
<section class="features">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Pourquoi Choisir Hôpital Sendwe ?</h2>
            <p>Une excellence médicale au service de la communauté de Lubumbashi, RDC</p>
        </div>
        <div class="features-grid">
            <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-icon"><i class="fas fa-user-md"></i></div>
                <h3>Médecins Experts</h3>
                <p>Une équipe de médecins qualifiés et expérimentés.</p>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-icon"><i class="fas fa-laptop-medical"></i></div>
                <h3>Dossiers Médicaux Numériques</h3>
                <p>Accès rapide et sécurisé à l'historique médical des patients.</p>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-icon"><i class="fas fa-clock"></i></div>
                <h3>Service Rapide</h3>
                <p>Réduction du temps d'attente grâce à notre système de gestion moderne.</p>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services" id="services">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Nos Services Médicaux</h2>
            <p>Des soins complets pour toute la famille</p>
        </div>
        <div class="services-grid">
            <div class="service-item" data-aos="fade-up" data-aos-delay="100"><i class="fas fa-heartbeat"></i><h4>Cardiologie</h4><p>Soins cardiaques complets</p></div>
            <div class="service-item" data-aos="fade-up" data-aos-delay="150"><i class="fas fa-brain"></i><h4>Neurologie</h4><p>Diagnostic avancé</p></div>
            <div class="service-item" data-aos="fade-up" data-aos-delay="200"><i class="fas fa-baby-carriage"></i><h4>Pédiatrie</h4><p>Spécialistes enfants</p></div>
            <div class="service-item" data-aos="fade-up" data-aos-delay="250"><i class="fas fa-flask"></i><h4>Laboratoire</h4><p>Analyses fiables</p></div>
            <div class="service-item" data-aos="fade-up" data-aos-delay="300"><i class="fas fa-x-ray"></i><h4>Radiologie</h4><p>Imagerie moderne</p></div>
            <div class="service-item" data-aos="fade-up" data-aos-delay="350"><i class="fas fa-ambulance"></i><h4>Urgences 24/7</h4><p>Service permanent</p></div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta">
    <div class="container" data-aos="fade-up">
        <h2>Prêt à prendre soin de votre santé ?</h2>
        <p>Connectez-vous pour accéder à vos dossiers médicaux et à nos services personnalisés.</p>
        <div class="cta-buttons">
            <!-- 🔑 UNIQUEMENT CONNEXION -->
            <a href="{{ route('login') }}" class="btn btn-secondary">
                <i class="fas fa-sign-in-alt"></i> Se connecter
            </a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer" id="contact">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h4>Hôpital Sendwe</h4>
                <p><i class="fas fa-map-marker-alt"></i> Lubumbashi, Haut-Katanga, RDC</p>
                <p><i class="fas fa-phone"></i> +243 999 999 999</p>
                <p><i class="fas fa-envelope"></i> contact@gmail.com</p>
            </div>
            <div class="footer-section">
                <h4>Horaires</h4>
                <p>Consultations: Lun-Ven: 8h - 17h</p>
                <p>Urgences: 24h/24, 7j/7</p>
                <p>Laboratoire: Lun-Sam: 7h - 18h</p>
            </div>
            <div class="footer-section">
                <h4>Liens Rapides</h4>
                <p><a href="#accueil">Accueil</a></p>
                <p><a href="#services">Services</a></p>
                <p><a href="#galerie">Galerie photos</a></p>
                <!-- 🔑 UNIQUEMENT CONNEXION -->
                <p><a href="{{ route('login') }}">Connexion</a></p>
            </div>
            <div class="footer-section">
                <h4>Suivez-nous</h4>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 Hôpital Sendwe - Lubumbashi, République Démocratique du Congo. Tous droits réservés.</p>
        </div>
    </div>
</footer>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 1000, once: true });
    
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 50) navbar.classList.add('scrolled');
        else navbar.classList.remove('scrolled');
    });
    
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) target.scrollIntoView({ behavior: 'smooth' });
        });
    });
</script>
</body>
</html>