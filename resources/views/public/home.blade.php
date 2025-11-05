@extends('layouts.app')

@section('content')
<style>
  /* Modern Color Palette */
  :root {
    --primary: #6366f1;
    --primary-dark: #4f46e5;
    --secondary: #8b5cf6;
    --accent: #06b6d4;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --dark: #1f2937;
    --light: #f8fafc;
    --gray: #6b7280;
    --gray-light: #e5e7eb;
    --white: #ffffff;
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  }

  /* Global Styles */
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  html {
    scroll-behavior: smooth;
  }

  body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    line-height: 1.6;
    color: var(--dark);
  }

  /* Modern Animations */
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes fadeInLeft {
    from {
      opacity: 0;
      transform: translateX(-30px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  @keyframes fadeInRight {
    from {
      opacity: 0;
      transform: translateX(30px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  @keyframes float {
    0%, 100% {
      transform: translateY(0px);
    }
    50% {
      transform: translateY(-20px);
    }
  }

  @keyframes pulse {
    0%, 100% {
      opacity: 1;
    }
    50% {
      opacity: 0.5;
    }
  }

  @keyframes gradient {
    0% {
      background-position: 0% 50%;
    }
    50% {
      background-position: 100% 50%;
    }
    100% {
      background-position: 0% 50%;
    }
  }

  @keyframes slideIn {
    from {
      transform: translateX(100%);
    }
    to {
      transform: translateX(0);
    }
  }

  .animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out;
  }

  .animate-fade-in-left {
    animation: fadeInLeft 0.8s ease-out;
  }

  .animate-fade-in-right {
    animation: fadeInRight 0.8s ease-out;
  }

  .animate-float {
    animation: float 6s ease-in-out infinite;
  }

  .animate-pulse {
    animation: pulse 2s ease-in-out infinite;
  }

  @keyframes shimmer {
    0% {
      background-position: -200% 0;
    }
    100% {
      background-position: 200% 0;
    }
  }

  @keyframes particleFloat {
    0%, 100% {
      transform: translateY(0px) rotate(0deg);
      opacity: 0.7;
    }
    50% {
      transform: translateY(-20px) rotate(180deg);
      opacity: 1;
    }
  }

  @keyframes badgePulse {
    0%, 100% {
      transform: scale(1);
      opacity: 1;
    }
    50% {
      transform: scale(1.05);
      opacity: 0.8;
    }
  }

  .animate-gradient {
    background-size: 200% 200%;
    animation: gradient 3s ease infinite;
  }

  .animate-shimmer {
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    background-size: 200% 100%;
    animation: shimmer 2s infinite;
  }

  /* Enhanced Hero Section */
  .hero-section {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
  }

  .hero-bg {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
      radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
      radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%),
      radial-gradient(circle at 40% 40%, rgba(120, 219, 255, 0.2) 0%, transparent 50%);
  }

  /* Animated Particles */
  .hero-particles {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
  }

  .particle {
    position: absolute;
    width: 4px;
    height: 4px;
    background: rgba(255, 255, 255, 0.6);
    border-radius: 50%;
    animation: particleFloat 6s ease-in-out infinite;
  }

  .particle:nth-child(2n) {
    width: 6px;
    height: 6px;
    background: rgba(255, 255, 255, 0.4);
  }

  .particle:nth-child(3n) {
    width: 3px;
    height: 3px;
    background: rgba(255, 255, 255, 0.8);
  }

  .hero-content {
    position: relative;
    z-index: 10;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
  }

  .hero-text {
    color: white;
  }

  .hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 2rem;
    animation: fadeInUp 0.8s ease-out;
    position: relative;
    overflow: hidden;
  }

  .badge-icon {
    animation: badgePulse 2s ease-in-out infinite;
  }

  .badge-pulse {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    animation: shimmer 3s infinite;
  }

  .title-line {
    display: block;
    animation: fadeInUp 0.8s ease-out both;
  }

  .title-line:nth-child(2) {
    animation-delay: 0.2s;
  }

  .title-line:nth-child(3) {
    animation-delay: 0.4s;
  }

  .btn-pulse {
    position: relative;
    overflow: hidden;
  }

  .btn-pulse::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    animation: shimmer 2s infinite;
  }

  /* Trust Indicators */
  .trust-indicators {
    display: flex;
    gap: 2rem;
    margin-top: 2rem;
    flex-wrap: wrap;
    justify-content: center;
  }

  .trust-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    transition: all 0.3s ease;
  }

  .trust-item:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-5px);
  }

  .trust-icon {
    font-size: 1.5rem;
  }

  .trust-text {
    font-size: 0.875rem;
    font-weight: 600;
    text-align: center;
  }

  .hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 1.5rem;
    animation: fadeInUp 0.8s ease-out 0.2s both;
  }

  .hero-title .gradient-text {
    background: linear-gradient(135deg, #fff 0%, #e0e7ff 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .hero-subtitle {
    font-size: 1.25rem;
    opacity: 0.9;
    margin-bottom: 2rem;
    animation: fadeInUp 0.8s ease-out 0.4s both;
  }

  .hero-buttons {
    display: flex;
    gap: 1rem;
    animation: fadeInUp 0.8s ease-out 0.6s both;
  }

  /* Modern Buttons */
  .btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1rem;
    position: relative;
    overflow: hidden;
  }

  .btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: var(--shadow-lg);
  }

  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-xl);
  }

  .btn-secondary {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
  }

  .btn-secondary:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
  }

  .btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    box-shadow: var(--shadow-lg);
  }

  .btn-success:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-xl);
  }

  /* Hero Visual */
  .hero-visual {
    position: relative;
    animation: fadeInRight 0.8s ease-out 0.4s both;
  }

  .hero-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    padding: 2rem;
    box-shadow: var(--shadow-xl);
    position: relative;
    overflow: hidden;
  }

  .hero-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2, #667eea);
    animation: gradient 3s ease infinite;
  }

  /* Enhanced Visual Content */
  .visual-header {
    text-align: center;
    margin-bottom: 2rem;
  }

  .visual-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    animation: badgePulse 2s ease-in-out infinite;
  }

  .visual-header h3 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
  }

  .visual-header p {
    opacity: 0.9;
    font-size: 0.875rem;
  }

  .visual-features {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .visual-feature {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    transition: all 0.3s ease;
  }

  .visual-feature:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
  }

  .visual-feature .feature-icon {
    font-size: 1.5rem;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
  }

  .visual-feature .feature-content {
    flex: 1;
  }

  .visual-feature .feature-title {
    font-weight: 600;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
  }

  .visual-feature .feature-subtitle {
    font-size: 0.75rem;
    opacity: 0.8;
  }

  /* Progress Indicator */
  .progress-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
  }

  .progress-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    opacity: 0.5;
    transition: all 0.3s ease;
  }

  .progress-step.active {
    opacity: 1;
  }

  .step-number {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.875rem;
  }

  .progress-step.active .step-number {
    background: rgba(255, 255, 255, 0.3);
    animation: badgePulse 2s ease-in-out infinite;
  }

  .step-text {
    font-size: 0.75rem;
    font-weight: 600;
    text-align: center;
  }

  .progress-line {
    width: 30px;
    height: 2px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 1px;
  }

  .floating-elements {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
  }

  .floating-element {
    position: absolute;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
  }

  .floating-element:nth-child(1) {
    width: 60px;
    height: 60px;
    top: 20%;
    left: 10%;
    animation-delay: 0s;
  }

  .floating-element:nth-child(2) {
    width: 40px;
    height: 40px;
    top: 60%;
    right: 20%;
    animation-delay: 2s;
  }

  .floating-element:nth-child(3) {
    width: 80px;
    height: 80px;
    bottom: 20%;
    left: 20%;
    animation-delay: 4s;
  }

  /* Features Section */
  .features-section {
    padding: 6rem 0;
    background: var(--light);
  }

  .section-header {
    text-align: center;
    margin-bottom: 4rem;
  }

  .section-title {
    font-size: 3rem;
    font-weight: 800;
    color: var(--dark);
    margin-bottom: 1rem;
  }

  .section-subtitle {
    font-size: 1.25rem;
    color: var(--gray);
    max-width: 600px;
    margin: 0 auto;
  }

  .features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
  }

  .feature-card {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }

  .feature-card:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-xl);
  }

  .feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
  }

  .feature-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
    color: white;
  }

  .feature-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 1rem;
  }

  .feature-description {
    color: var(--gray);
    line-height: 1.6;
  }

  /* Stats Section */
  .stats-section {
    padding: 4rem 0;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    color: white;
  }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
  }

  .stat-item {
    text-align: center;
  }

  .stat-number {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
  }

  .stat-label {
    font-size: 1rem;
    opacity: 0.9;
  }

  /* Pricing Section */
  .pricing-section {
    padding: 6rem 0;
    background: white;
  }

  .pricing-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
  }

  .pricing-card {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
    position: relative;
    border: 2px solid transparent;
  }

  .pricing-card:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-xl);
    border-color: var(--primary);
  }

  .pricing-card.featured {
    border-color: var(--primary);
    transform: scale(1.05);
  }

  .pricing-card.featured::before {
    content: 'Most Popular';
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    padding: 0.5rem 1.5rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
  }

  .pricing-header {
    text-align: center;
    margin-bottom: 2rem;
  }

  .pricing-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 0.5rem;
  }

  .pricing-price {
    font-size: 3rem;
    font-weight: 800;
    color: var(--primary);
    margin-bottom: 0.5rem;
  }

  .pricing-period {
    color: var(--gray);
  }

  .pricing-features {
    list-style: none;
    margin-bottom: 2rem;
  }

  .pricing-features li {
    padding: 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .pricing-features li::before {
    content: '✓';
    color: var(--success);
    font-weight: bold;
  }

  /* Testimonials Section */
  .testimonials-section {
    padding: 6rem 0;
    background: var(--light);
  }

  .testimonials-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
  }

  .testimonial-card {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
  }

  .testimonial-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
  }

  .testimonial-content {
    margin-bottom: 1.5rem;
    font-style: italic;
    color: var(--gray);
    line-height: 1.6;
  }

  .testimonial-author {
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .testimonial-avatar {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
  }

  .testimonial-info h4 {
    font-weight: 600;
    color: var(--dark);
  }

  .testimonial-info p {
    color: var(--gray);
    font-size: 0.875rem;
  }

  /* Enhanced Testimonials */
  .testimonial-rating {
    margin-bottom: 1rem;
  }

  .stars {
    display: flex;
    gap: 0.25rem;
  }

  .stars span {
    font-size: 1rem;
    animation: badgePulse 2s ease-in-out infinite;
    animation-delay: calc(var(--i) * 0.1s);
  }

  .stars span:nth-child(1) { --i: 0; }
  .stars span:nth-child(2) { --i: 1; }
  .stars span:nth-child(3) { --i: 2; }
  .stars span:nth-child(4) { --i: 3; }
  .stars span:nth-child(5) { --i: 4; }

  /* FAQ Section */
  .faq-section {
    padding: 6rem 0;
    background: white;
  }

  .faq-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 2rem;
  }

  .faq-item {
    background: white;
    border-radius: 12px;
    margin-bottom: 1rem;
    box-shadow: var(--shadow);
    overflow: hidden;
    transition: all 0.3s ease;
  }

  .faq-item:hover {
    box-shadow: var(--shadow-lg);
  }

  .faq-question {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .faq-question:hover {
    background: var(--light);
  }

  .faq-question h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--dark);
    margin: 0;
  }

  .faq-toggle {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.25rem;
    transition: all 0.3s ease;
  }

  .faq-item.active .faq-toggle {
    transform: rotate(45deg);
    background: var(--success);
  }

  .faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: all 0.3s ease;
  }

  .faq-item.active .faq-answer {
    max-height: 200px;
  }

  .faq-answer p {
    padding: 0 1.5rem 1.5rem;
    color: var(--gray);
    line-height: 1.6;
    margin: 0;
  }

  /* CTA Section */
  .cta-section {
    padding: 6rem 0;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    color: white;
    text-align: center;
  }

  .cta-content {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 2rem;
  }

  .cta-title {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 1rem;
  }

  .cta-subtitle {
    font-size: 1.25rem;
    opacity: 0.9;
    margin-bottom: 2rem;
  }

  .cta-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
  }

  /* Responsive Design */
  @media (max-width: 768px) {
    .hero-content {
      grid-template-columns: 1fr;
      text-align: center;
      gap: 2rem;
    }

    .hero-title {
      font-size: 2.5rem;
    }

    .hero-buttons {
      flex-direction: column;
      align-items: center;
    }

    .features-grid {
      grid-template-columns: 1fr;
    }

    .pricing-grid {
      grid-template-columns: 1fr;
    }

    .testimonials-grid {
      grid-template-columns: 1fr;
    }

    .cta-buttons {
      flex-direction: column;
      align-items: center;
    }
  }

  /* Smooth scrolling and animations */
  .scroll-reveal {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.6s ease;
  }

  .scroll-reveal.revealed {
    opacity: 1;
    transform: translateY(0);
  }
</style>

<!-- Enhanced Hero Section -->
<section class="hero-section">
  <div class="hero-bg"></div>
  
  <!-- Animated Background Elements -->
  <div class="hero-particles">
    <div class="particle" style="top: 20%; left: 10%; animation-delay: 0s;"></div>
    <div class="particle" style="top: 60%; left: 80%; animation-delay: 2s;"></div>
    <div class="particle" style="top: 80%; left: 20%; animation-delay: 4s;"></div>
    <div class="particle" style="top: 30%; left: 70%; animation-delay: 1s;"></div>
    <div class="particle" style="top: 70%; left: 50%; animation-delay: 3s;"></div>
  </div>
  
  <div class="hero-content">
    <div class="hero-text">
      <div class="hero-badge">
        <span class="badge-icon">🚀</span>
        <span>Hollyn Hosting - TZ Ready</span>
        <div class="badge-pulse"></div>
      </div>
      
      <h1 class="hero-title">
        <span class="title-line">Host <span class="gradient-text">faster.</span></span>
        <span class="title-line">Scale <span class="gradient-text">smarter.</span></span>
        <span class="title-line">Go live in <span class="gradient-text">minutes.</span></span>
      </h1>
      
      <p class="hero-subtitle">
        Professional web hosting with <strong>auto-provision</strong>, <strong>cPanel access</strong>, and <strong>24/7 support</strong>. 
        Perfect for businesses, developers, and entrepreneurs in Tanzania.
      </p>
      
      <!-- Enhanced CTA Buttons -->
      <div class="hero-buttons">
        <a href="{{ route('plans') }}" class="btn btn-primary btn-pulse">
          <span>View Plans</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </a>
        @guest
        <a href="{{ route('register') }}" class="btn btn-secondary">
          <span>Get Started Free</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="8.5" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
            <path d="M20 8v6M23 11l-3 3-3-3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </a>
        @endguest
      </div>
      
      <!-- Trust Indicators -->
      <div class="trust-indicators">
        <div class="trust-item">
          <div class="trust-icon">⚡</div>
          <div class="trust-text">30s Setup</div>
        </div>
        <div class="trust-item">
          <div class="trust-icon">🔒</div>
          <div class="trust-text">99.9% Uptime</div>
        </div>
        <div class="trust-item">
          <div class="trust-icon">💳</div>
          <div class="trust-text">ZenoPay Ready</div>
        </div>
        <div class="trust-item">
          <div class="trust-icon">🇹🇿</div>
          <div class="trust-text">TZ Support</div>
        </div>
      </div>
    </div>
    
    <div class="hero-visual">
      <div class="hero-card">
        <div class="floating-elements">
          <div class="floating-element"></div>
          <div class="floating-element"></div>
          <div class="floating-element"></div>
        </div>
        
        <!-- Enhanced Visual Content -->
        <div class="visual-header">
          <div class="visual-icon">⚡</div>
          <h3>Auto-Provision</h3>
          <p>Your hosting account is ready in seconds</p>
        </div>
        
        <div class="visual-features">
          <div class="visual-feature">
            <div class="feature-icon">🎛️</div>
            <div class="feature-content">
              <div class="feature-title">cPanel Access</div>
              <div class="feature-subtitle">Instant</div>
            </div>
          </div>
          <div class="visual-feature">
            <div class="feature-icon">🔐</div>
            <div class="feature-content">
              <div class="feature-title">SSL Certificate</div>
              <div class="feature-subtitle">Free</div>
            </div>
          </div>
          <div class="visual-feature">
            <div class="feature-icon">📧</div>
            <div class="feature-content">
              <div class="feature-title">Email Accounts</div>
              <div class="feature-subtitle">Unlimited</div>
            </div>
          </div>
          <div class="visual-feature">
            <div class="feature-icon">💾</div>
            <div class="feature-content">
              <div class="feature-title">SSD Storage</div>
              <div class="feature-subtitle">Fast</div>
            </div>
          </div>
        </div>
        
        <!-- Progress Indicator -->
        <div class="progress-indicator">
          <div class="progress-step active">
            <div class="step-number">1</div>
            <div class="step-text">Choose Plan</div>
          </div>
          <div class="progress-line"></div>
          <div class="progress-step active">
            <div class="step-number">2</div>
            <div class="step-text">Auto-Provision</div>
          </div>
          <div class="progress-line"></div>
          <div class="progress-step">
            <div class="step-number">3</div>
            <div class="step-text">Go Live</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Features Section -->
<section class="features-section">
  <div class="section-header">
    <h2 class="section-title">Why Choose Hollyn?</h2>
    <p class="section-subtitle">
      We provide everything you need to host your website with confidence
    </p>
  </div>
  
  <div class="features-grid">
    <div class="feature-card scroll-reveal">
      <div class="feature-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <h3 class="feature-title">Lightning Fast Setup</h3>
      <p class="feature-description">
        Auto-provision your hosting account in seconds. Get cPanel access immediately after payment with zero manual setup required.
      </p>
    </div>
    
    <div class="feature-card scroll-reveal">
      <div class="feature-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path d="M12 20s8-4 8-10a8 8 0 10-16 0c0 6 8 10 8 10z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <h3 class="feature-title">99.9% Uptime</h3>
      <p class="feature-description">
        Reliable hosting infrastructure with guaranteed uptime. Your website stays online when your customers need it most.
      </p>
    </div>
    
    <div class="feature-card scroll-reveal">
      <div class="feature-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M21 12c-1 0-3-1-3-3s2-3 3-3 3 1 3 3-2 3-3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M3 12c1 0 3-1 3-3s-2-3-3-3-3 1-3 3 2 3 3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <h3 class="feature-title">Free SSL Certificate</h3>
      <p class="feature-description">
        Secure your website with free SSL certificates. Protect your visitors' data and boost your SEO rankings automatically.
      </p>
    </div>
    
    <div class="feature-card scroll-reveal">
      <div class="feature-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path d="M3 7h18M3 12h14M3 17h10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <h3 class="feature-title">1-Click Apps</h3>
      <p class="feature-description">
        Install WordPress, Laravel, and other popular applications with just one click. No technical knowledge required.
      </p>
    </div>
    
    <div class="feature-card scroll-reveal">
      <div class="feature-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path d="M4 4h16v8H4zM10 12v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <h3 class="feature-title">Scalable Resources</h3>
      <p class="feature-description">
        Start small and scale up as your website grows. Upgrade your plan anytime without downtime or data loss.
      </p>
    </div>
    
    <div class="feature-card scroll-reveal">
      <div class="feature-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path d="M22 12h-4l-3 9L9 3l-3 9H2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <h3 class="feature-title">24/7 Support</h3>
      <p class="feature-description">
        Get help when you need it with our dedicated support team. We're here to ensure your website runs smoothly.
      </p>
    </div>
  </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
  <div class="stats-grid">
    <div class="stat-item scroll-reveal">
      <div class="stat-number">99.9%</div>
      <div class="stat-label">Uptime Guarantee</div>
    </div>
    <div class="stat-item scroll-reveal">
      <div class="stat-number">500+</div>
      <div class="stat-label">Happy Customers</div>
    </div>
    <div class="stat-item scroll-reveal">
      <div class="stat-number">24/7</div>
      <div class="stat-label">Support Available</div>
    </div>
    <div class="stat-item scroll-reveal">
      <div class="stat-number">30s</div>
      <div class="stat-label">Setup Time</div>
    </div>
  </div>
</section>

<!-- Pricing Section -->
<section class="pricing-section">
  <div class="section-header">
    <h2 class="section-title">Choose Your Plan</h2>
    <p class="section-subtitle">
      Flexible pricing options to fit your needs and budget
    </p>
  </div>
  
  <div class="pricing-grid">
    <div class="pricing-card scroll-reveal">
      <div class="pricing-header">
        <h3 class="pricing-title">Starter</h3>
        <div class="pricing-price">TZS 25,000</div>
        <div class="pricing-period">per month</div>
      </div>
      
      <ul class="pricing-features">
        <li>1 Website</li>
        <li>10GB SSD Storage</li>
        <li>Unlimited Bandwidth</li>
        <li>Free SSL Certificate</li>
        <li>cPanel Access</li>
        <li>Email Support</li>
      </ul>
      
      <a href="{{ route('plans') }}" class="btn btn-primary" style="width: 100%; justify-content: center;">
        Choose Plan
      </a>
    </div>
    
    <div class="pricing-card featured scroll-reveal">
      <div class="pricing-header">
        <h3 class="pricing-title">Professional</h3>
        <div class="pricing-price">TZS 50,000</div>
        <div class="pricing-period">per month</div>
      </div>
      
      <ul class="pricing-features">
        <li>5 Websites</li>
        <li>50GB SSD Storage</li>
        <li>Unlimited Bandwidth</li>
        <li>Free SSL Certificate</li>
        <li>cPanel Access</li>
        <li>Priority Support</li>
        <li>Daily Backups</li>
      </ul>
      
      <a href="{{ route('plans') }}" class="btn btn-success" style="width: 100%; justify-content: center;">
        Most Popular
      </a>
    </div>
    
    <div class="pricing-card scroll-reveal">
      <div class="pricing-header">
        <h3 class="pricing-title">Business</h3>
        <div class="pricing-price">TZS 100,000</div>
        <div class="pricing-period">per month</div>
      </div>
      
      <ul class="pricing-features">
        <li>Unlimited Websites</li>
        <li>100GB SSD Storage</li>
        <li>Unlimited Bandwidth</li>
        <li>Free SSL Certificate</li>
        <li>cPanel Access</li>
        <li>24/7 Phone Support</li>
        <li>Daily Backups</li>
        <li>Advanced Security</li>
      </ul>
      
      <a href="{{ route('plans') }}" class="btn btn-primary" style="width: 100%; justify-content: center;">
        Choose Plan
      </a>
    </div>
  </div>
</section>

<!-- Enhanced Testimonials Section -->
<section class="testimonials-section">
  <div class="section-header">
    <h2 class="section-title">What Our Customers Say</h2>
    <p class="section-subtitle">
      Real feedback from satisfied customers across Tanzania
    </p>
  </div>
  
  <div class="testimonials-grid">
    <div class="testimonial-card scroll-reveal">
      <div class="testimonial-rating">
        <div class="stars">
          <span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span>
        </div>
      </div>
      <div class="testimonial-content">
        "Hollyn hosting made it so easy to get my business online. The auto-provision feature saved me hours of setup time!"
      </div>
      <div class="testimonial-author">
        <div class="testimonial-avatar">JM</div>
        <div class="testimonial-info">
          <h4>John Mwalimu</h4>
          <p>Business Owner, Dar es Salaam</p>
        </div>
      </div>
    </div>
    
    <div class="testimonial-card scroll-reveal">
      <div class="testimonial-rating">
        <div class="stars">
          <span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span>
        </div>
      </div>
      <div class="testimonial-content">
        "Excellent customer support and reliable hosting. My website has been running smoothly for months without any issues."
      </div>
      <div class="testimonial-author">
        <div class="testimonial-avatar">SA</div>
        <div class="testimonial-info">
          <h4>Sarah Ahmed</h4>
          <p>Web Developer, Arusha</p>
        </div>
      </div>
    </div>
    
    <div class="testimonial-card scroll-reveal">
      <div class="testimonial-rating">
        <div class="stars">
          <span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span>
        </div>
      </div>
      <div class="testimonial-content">
        "The pricing is fair and the features are exactly what I needed. Highly recommend for anyone starting a website in Tanzania."
      </div>
      <div class="testimonial-author">
        <div class="testimonial-avatar">DK</div>
        <div class="testimonial-info">
          <h4>David Kimaro</h4>
          <p>Entrepreneur, Mwanza</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FAQ Section -->
<section class="faq-section">
  <div class="section-header">
    <h2 class="section-title">Frequently Asked Questions</h2>
    <p class="section-subtitle">
      Get answers to common questions about our hosting services
    </p>
  </div>
  
  <div class="faq-container">
    <div class="faq-item scroll-reveal">
      <div class="faq-question">
        <h3>How quickly is my hosting account set up?</h3>
        <div class="faq-toggle">+</div>
      </div>
      <div class="faq-answer">
        <p>Your hosting account is automatically provisioned within 30 seconds of payment confirmation. You'll receive your cPanel login credentials immediately via email.</p>
      </div>
    </div>
    
    <div class="faq-item scroll-reveal">
      <div class="faq-question">
        <h3>What payment methods do you accept?</h3>
        <div class="faq-toggle">+</div>
      </div>
      <div class="faq-answer">
        <p>We accept all major payment methods through ZenoPay, including mobile money (M-Pesa, Tigo Pesa, Airtel Money), bank transfers, and credit cards.</p>
      </div>
    </div>
    
    <div class="faq-item scroll-reveal">
      <div class="faq-question">
        <h3>Do you provide SSL certificates?</h3>
        <div class="faq-toggle">+</div>
      </div>
      <div class="faq-answer">
        <p>Yes! All our hosting plans include free SSL certificates. Your website will be automatically secured with HTTPS encryption.</p>
      </div>
    </div>
    
    <div class="faq-item scroll-reveal">
      <div class="faq-question">
        <h3>What kind of support do you offer?</h3>
        <div class="faq-toggle">+</div>
      </div>
      <div class="faq-answer">
        <p>We provide 24/7 customer support via email and live chat. Our support team is based in Tanzania and understands local business needs.</p>
      </div>
    </div>
    
    <div class="faq-item scroll-reveal">
      <div class="faq-question">
        <h3>Can I upgrade my plan later?</h3>
        <div class="faq-toggle">+</div>
      </div>
      <div class="faq-answer">
        <p>Absolutely! You can upgrade your hosting plan at any time without downtime. Your data will be seamlessly transferred to the new plan.</p>
      </div>
    </div>
    
    <div class="faq-item scroll-reveal">
      <div class="faq-question">
        <h3>Do you offer backups?</h3>
        <div class="faq-toggle">+</div>
      </div>
      <div class="faq-answer">
        <p>Yes, we provide daily automated backups for all hosting accounts. You can restore your website from any backup point within the last 30 days.</p>
      </div>
    </div>
  </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
  <div class="cta-content">
    <h2 class="cta-title">Ready to Get Started?</h2>
    <p class="cta-subtitle">
      Join hundreds of satisfied customers and launch your website today
    </p>
    
    <div class="cta-buttons">
      <a href="{{ route('plans') }}" class="btn btn-secondary">
        <span>View All Plans</span>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
          <path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </a>
      @guest
      <a href="{{ route('register') }}" class="btn btn-primary">
        <span>Create Account</span>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
          <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <circle cx="8.5" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
          <path d="M20 8v6M23 11l-3 3-3-3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </a>
      @endguest
    </div>
  </div>
</section>

<script>
// Scroll reveal animation
function scrollReveal() {
  const elements = document.querySelectorAll('.scroll-reveal');
  const windowHeight = window.innerHeight;
  
  elements.forEach(element => {
    const elementTop = element.getBoundingClientRect().top;
    const elementVisible = 150;
    
    if (elementTop < windowHeight - elementVisible) {
      element.classList.add('revealed');
    }
  });
}

// Initialize scroll reveal
window.addEventListener('scroll', scrollReveal);
scrollReveal(); // Run on load

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute('href'));
    if (target) {
      target.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
    }
  });
});

// FAQ Toggle Functionality
document.querySelectorAll('.faq-question').forEach(question => {
  question.addEventListener('click', function() {
    const faqItem = this.parentElement;
    const isActive = faqItem.classList.contains('active');
    
    // Close all other FAQ items
    document.querySelectorAll('.faq-item').forEach(item => {
      item.classList.remove('active');
    });
    
    // Toggle current item
    if (!isActive) {
      faqItem.classList.add('active');
    }
  });
});

// Add loading animation
window.addEventListener('load', function() {
  document.body.classList.add('loaded');
});
</script>

@endsection