<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Tamamen farklı ana sayfa tasarımı: /home - yeni layout, renk ve 3D -->
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet" />
<style>
/* ===== Yeni tasarım değişkenleri ===== */
:root {
  --hp-bg: #0a0e17;
  --hp-surface: #111827;
  --hp-surface-2: #1a2234;
  --hp-accent: #06b6d4;
  --hp-accent-soft: rgba(6, 182, 212, 0.25);
  --hp-text: #f1f5f9;
  --hp-text-muted: #94a3b8;
  --hp-border: rgba(148, 163, 184, 0.12);
  --hp-radius: 16px;
  --hp-radius-sm: 10px;
  --hp-ease: cubic-bezier(0.22, 1, 0.36, 1);
  --hp-perspective: 1600px;
  --hp-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
}

/* ===== Sayfa & wrapper ===== */
body.page-bg {
  background: var(--hp-bg);
  min-height: 100vh;
  font-family: 'Outfit', sans-serif;
  -webkit-font-smoothing: antialiased;
  position: relative;
  overflow-x: hidden;
}
body.page-bg::before {
  content: "";
  position: fixed;
  inset: 0;
  background: 
    radial-gradient(ellipse 80% 50% at 50% -20%, var(--hp-accent-soft) 0%, transparent 50%),
    radial-gradient(ellipse 60% 40% at 100% 50%, rgba(6, 182, 212, 0.08) 0%, transparent 50%),
    radial-gradient(ellipse 40% 60% at 0% 80%, rgba(6, 182, 212, 0.06) 0%, transparent 50%);
  pointer-events: none;
  z-index: 0;
}
body.page-bg .home-page-wrap { position: relative; z-index: 1; }

/* ===== Yeni layout: üst bar (logo sol, profil sağ) ===== */
.home-page-wrap {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  width: 100%;
}

.hp-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 max(1.25rem, env(safe-area-inset-left)) 0 max(1.25rem, env(safe-area-inset-right));
  min-height: 64px;
  background: rgba(17, 24, 39, 0.85);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid var(--hp-border);
  position: sticky;
  top: 0;
  z-index: 100;
}
.hp-bar-left {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}
.hp-bar .home-logo-wrap {
  display: inline-flex;
  align-items: center;
  transition: transform 0.3s var(--hp-ease), filter 0.3s var(--hp-ease);
  filter: drop-shadow(0 0 20px rgba(6, 182, 212, 0.15));
}
@media (hover: hover) {
  .hp-bar .home-logo-wrap:hover {
    transform: scale(1.05) rotateY(-5deg);
    filter: drop-shadow(0 0 28px rgba(6, 182, 212, 0.3));
  }
}
.hp-bar .logo-img {
  height: 40px;
  width: auto;
  object-fit: contain;
}
@media (min-width: 768px) {
  .hp-bar .logo-img { height: 48px; }
}
.home-sidebar-toggle {
  min-width: 44px;
  min-height: 44px;
  border-radius: var(--hp-radius-sm);
  background: var(--hp-surface-2);
  border: 1px solid var(--hp-border);
  color: var(--hp-text);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s, border-color 0.2s, color 0.2s;
  -webkit-tap-highlight-color: transparent;
}
.home-sidebar-toggle:hover {
  background: rgba(6, 182, 212, 0.15);
  border-color: var(--hp-accent);
  color: var(--hp-accent);
}
@media (min-width: 992px) {
  .home-sidebar-toggle { display: none; }
}
.hp-bar .home-sidebar-toggle-desktop {
  min-width: 44px;
  min-height: 44px;
  padding: 0 1rem;
  border-radius: var(--hp-radius-sm);
  background: var(--hp-surface-2);
  border: 1px solid var(--hp-border);
  color: var(--hp-text);
  font-weight: 500;
  font-size: 0.9rem;
  transition: background 0.2s, border-color 0.2s, color 0.2s, transform 0.2s;
  -webkit-tap-highlight-color: transparent;
}
.hp-bar .home-sidebar-toggle-desktop:hover {
  background: rgba(6, 182, 212, 0.15);
  border-color: var(--hp-accent);
  color: var(--hp-accent);
  transform: translateY(-1px);
}

/* ===== Ana içerik alanı ===== */
.hp-main {
  flex: 1;
  width: 100%;
  max-width: 1280px;
  margin: 0 auto;
  padding: 2rem max(1.25rem, env(safe-area-inset-right)) 3rem max(1.25rem, env(safe-area-inset-left));
}
@media (min-width: 768px) {
  .hp-main { padding: 3rem 2rem 4rem; }
}

/* ===== Hero: Hoşgeldin (büyük tipografi, 3D his) ===== */
.hp-hero {
  margin-bottom: 2.5rem;
  position: relative;
}
.hp-hero-inner {
  position: relative;
  z-index: 1;
  padding: 2rem 0;
}
.hp-welcome {
  font-size: 1.5rem;
  font-weight: 500;
  color: var(--hp-text-muted);
  margin: 0 0 0.25em 0;
  line-height: 1.3;
}
.hp-welcome-name {
  display: block;
  font-size: clamp(2rem, 6vw, 3.25rem);
  font-weight: 700;
  color: var(--hp-text);
  letter-spacing: -0.02em;
  background: linear-gradient(135deg, #fff 0%, var(--hp-accent) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
.hp-hero-deco {
  position: absolute;
  right: -10%;
  top: 50%;
  transform: translateY(-50%) rotate(-12deg);
  width: 280px;
  height: 180px;
  background: linear-gradient(135deg, var(--hp-accent-soft) 0%, transparent 60%);
  border-radius: 24px;
  filter: blur(40px);
  opacity: 0.7;
  pointer-events: none;
}
@media (max-width: 767px) {
  .hp-hero-deco { width: 160px; height: 100px; right: -20%; opacity: 0.5; }
}

/* ===== Bölüm başlıkları ===== */
.hp-section-label {
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: var(--hp-accent);
  margin-bottom: 1rem;
}

/* ===== Uygulamalar: bento grid, 3D kartlar ===== */
.hp-apps {
  margin-bottom: 2.5rem;
}
.hp-apps-grid {
  display: grid;
  gap: 1rem;
  grid-template-columns: 1fr;
  perspective: var(--hp-perspective);
}
@media (min-width: 576px) {
  .hp-apps-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
  }
}
@media (min-width: 992px) {
  .hp-apps-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
  }
}
.hp-app-card {
  display: block;
  text-decoration: none;
  border-radius: var(--hp-radius);
  overflow: hidden;
  min-height: 140px;
  background: var(--hp-surface-2);
  border: 1px solid var(--hp-border);
  box-shadow: var(--hp-shadow);
  transition: transform 0.4s var(--hp-ease), box-shadow 0.4s var(--hp-ease), border-color 0.3s;
  position: relative;
  transform-style: preserve-3d;
  animation: hpCardIn 0.6s var(--hp-ease) backwards;
  animation-delay: calc(0.06s * (var(--stagger, 0) + 1));
}
.hp-app-card::before {
  content: "";
  position: absolute;
  inset: 0;
  border-radius: inherit;
  background: linear-gradient(160deg, rgba(255,255,255,0.06) 0%, transparent 45%);
  pointer-events: none;
}
@media (min-width: 576px) {
  .hp-app-card { min-height: 160px; }
}
@media (min-width: 992px) {
  .hp-app-card { min-height: 180px; }
}
@media (hover: hover) {
  .hp-app-card:hover {
    transform: translateY(-10px) rotateX(6deg) rotateY(-6deg);
    box-shadow: 0 32px 64px -16px rgba(0, 0, 0, 0.5), 0 0 0 1px var(--hp-accent);
    border-color: var(--hp-accent);
  }
}
@media (hover: none) {
  .hp-app-card:hover { transform: translateY(-6px); }
}
@keyframes hpCardIn {
  from {
    opacity: 0;
    transform: translateY(30px) rotateX(10deg);
  }
  to {
    opacity: 1;
    transform: translateY(0) rotateX(0);
  }
}
.hp-app-card .card-body {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: center;
  text-align: left;
  padding: 1.5rem;
  position: relative;
  z-index: 1;
  transform: translateZ(24px);
  backface-visibility: hidden;
}
@media (min-width: 768px) {
  .hp-app-card .card-body { padding: 1.75rem; }
}
.hp-app-card .app-icon-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 48px;
  height: 48px;
  margin-bottom: 0.75rem;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  background: var(--hp-accent-soft);
  color: var(--hp-accent);
}
@media (min-width: 768px) {
  .hp-app-card .app-icon-badge { width: 56px; height: 56px; font-size: 0.9rem; }
}
.hp-app-card .app-title {
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--hp-text);
  line-height: 1.35;
  margin: 0;
  letter-spacing: -0.01em;
}
@media (min-width: 768px) {
  .hp-app-card .app-title { font-size: 1.05rem; }
}
/* Uygulama kartı renk varyantları */
.hp-app-card.app-card-hedas {
  background: linear-gradient(160deg, #831843 0%, #4c0519 100%);
  border-color: rgba(251, 113, 133, 0.3);
}
.hp-app-card.app-card-hedas .app-icon-badge { background: rgba(251, 113, 133, 0.3); color: #fda4af; }
.hp-app-card.app-card-edts {
  background: linear-gradient(160deg, #78350f 0%, #451a03 100%);
  border-color: rgba(251, 191, 36, 0.3);
}
.hp-app-card.app-card-edts .app-icon-badge { background: rgba(251, 191, 36, 0.25); color: #fcd34d; }
.hp-app-card.app-card-generic {
  background: linear-gradient(160deg, var(--app-color, #1e293b) 0%, #0f172a 100%);
}
.hp-app-card.app-card-generic .app-icon-badge { background: var(--hp-accent-soft); color: var(--hp-accent); }

.hp-no-apps {
  padding: 2rem;
  border-radius: var(--hp-radius);
  background: var(--hp-surface-2);
  border: 1px dashed var(--hp-border);
  color: var(--hp-text-muted);
  text-align: center;
}

/* ===== Güncellemeler: yatay şerit / kompakt kartlar ===== */
.hp-updates {
  margin-bottom: 2rem;
}
.hp-updates-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.75rem;
  margin-bottom: 1rem;
}
.hp-btn-show-all {
  min-height: 44px;
  padding: 0 1rem;
  border-radius: var(--hp-radius-sm);
  background: transparent;
  border: 1px solid var(--hp-accent);
  color: var(--hp-accent);
  font-weight: 500;
  font-size: 0.875rem;
  transition: background 0.2s, color 0.2s;
  -webkit-tap-highlight-color: transparent;
}
.hp-btn-show-all:hover {
  background: var(--hp-accent);
  color: var(--hp-bg);
}
.hp-updates-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
.hp-update-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  padding: 1rem 1.25rem;
  border-radius: var(--hp-radius-sm);
  background: var(--hp-surface-2);
  border: 1px solid var(--hp-border);
  text-decoration: none;
  color: inherit;
  transition: transform 0.2s, border-color 0.2s, background 0.2s;
}
.hp-update-item:hover {
  border-color: var(--hp-accent);
  background: rgba(6, 182, 212, 0.08);
  transform: translateX(6px);
}
.hp-update-title {
  font-weight: 500;
  color: var(--hp-text);
  font-size: 0.9rem;
  flex: 1;
  min-width: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.hp-update-date {
  font-size: 0.8rem;
  color: var(--hp-text-muted);
  flex-shrink: 0;
}

/* ===== Footer ===== */
.hp-footer-wrap {
  margin-top: auto;
  padding: 1.5rem max(1.25rem, env(safe-area-inset-left));
  padding-bottom: max(1.5rem, env(safe-area-inset-bottom));
  border-top: 1px solid var(--hp-border);
}
.hp-footer-wrap .home-footer {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: center;
  gap: 0.5rem 1rem;
}
.hp-footer-wrap .menu-link {
  font-size: 0.875rem;
  color: var(--hp-text-muted);
  text-decoration: none;
  transition: color 0.2s;
}
.hp-footer-wrap .menu-link:hover { color: var(--hp-accent); }
.hp-footer-wrap .min-touch {
  min-height: 44px;
  min-width: 44px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  -webkit-tap-highlight-color: transparent;
}
.hp-footer-wrap .fs-7,
.hp-footer-wrap span:last-child {
  font-size: 0.8rem;
  color: var(--hp-text-muted);
  opacity: 0.8;
}

/* ===== Scroll animasyonu (mevcut script ile uyumlu) ===== */
.animate-on-scroll {
  opacity: 0;
  transform: translateY(24px);
  transition: opacity 0.55s var(--hp-ease), transform 0.55s var(--hp-ease);
}
.animate-on-scroll.animated-in {
  opacity: 1;
  transform: translateY(0);
}

/* ===== Sidebar (drawer) – yeni tema ile uyumlu ===== */
.sidebar[data-kt-drawer-name="sidebar"] {
  background: var(--hp-surface);
  backdrop-filter: blur(16px);
  border-left: 1px solid var(--hp-border);
  box-shadow: -20px 0 60px rgba(0, 0, 0, 0.4);
}
@media (max-width: 991.98px) {
  .sidebar[data-kt-drawer-name="sidebar"] {
    width: 100% !important;
    max-width: 380px;
    border-left: none;
  }
}
.home-sidebar-title {
  font-size: 1rem;
  font-weight: 600;
  color: var(--hp-text);
  letter-spacing: -0.01em;
}
@media (min-width: 768px) {
  .home-sidebar-title { font-size: 1.1rem; }
}
#kt_sidebar .timeline-content .border.rounded {
  border-color: var(--hp-border) !important;
  background: var(--hp-surface-2);
}
#kt_sidebar .timeline-icon .symbol-label {
  background: var(--hp-accent-soft);
  border: 1px solid var(--hp-border);
}
#kt_sidebar .detail_duyuru_toggle {
  font-size: 0.9rem;
  line-height: 1.55;
  color: var(--hp-text-muted);
}
#kt_sidebar .menu-link {
  transition: color 0.2s, background-color 0.2s;
}

.hidden { display: none !important; }
</style>
