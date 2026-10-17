<!DOCTYPE html>
<html lang="es" data-theme="dark">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tienda | CeroDistancia</title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    /* ===================================================
       THEME VARIABLES (DARK DEFAULT / LIGHT SWITCHABLE)
       =================================================== */
    :root, [data-theme="light"] {
      --bg-body: #f3f4f6;
      --card-bg: #ffffff;
      --card-border: #e5e7eb;
      --text-main: #111827;
      --text-muted: #6b7280;
      --accent: #10b981;
      --accent-hover: #059669;
      --nav-bg: rgba(255, 255, 255, 0.85);
      --nav-border: rgba(0, 0, 0, 0.08);
      --badge-bg: #e5e7eb;
      --counter-bg: #f3f4f6;
      --modal-bg: #ffffff;
      --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.06);
    }

    [data-theme="dark"] {
      --bg-body: #0b0f17;
      --card-bg: rgba(17, 24, 39, 0.8);
      --card-border: rgba(255, 255, 255, 0.08);
      --text-main: #f9fafb;
      --text-muted: #9ca3af;
      --accent: #10b981;
      --accent-hover: #059669;
      --nav-bg: rgba(11, 15, 23, 0.85);
      --nav-border: rgba(255, 255, 255, 0.08);
      --badge-bg: rgba(255, 255, 255, 0.08);
      --counter-bg: rgba(255, 255, 255, 0.05);
      --modal-bg: #111827;
      --shadow-soft: 0 8px 30px rgba(0, 0, 0, 0.4);
    }

    body {
      background-color: var(--bg-body);
      color: var(--text-main);
      font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
      transition: background-color 0.3s ease, color 0.3s ease;
      min-height: 100vh;
    }

    /* TOP BAR CON TOGGLE DÍA / NOCHE */
    .top-bar-store {
      background: var(--nav-bg);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border-bottom: 1px solid var(--nav-border);
      position: sticky;
      top: 0;
      z-index: 100;
      padding: 12px 0;
      transition: background-color 0.3s ease;
    }

    .theme-toggle-btn {
      background: var(--counter-bg);
      border: 1px solid var(--card-border);
      color: var(--text-main);
      border-radius: 9999px;
      padding: 6px 14px;
      font-size: 0.85rem;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .theme-toggle-btn:hover {
      border-color: var(--accent);
      color: var(--accent);
    }

    /* HERO / BANNER TIENDA */
    .store-hero-banner {
      height: 220px;
      background-size: cover;
      background-position: center;
      border-radius: 20px;
      position: relative;
      overflow: hidden;
      box-shadow: var(--shadow-soft);
      border: 1px solid var(--card-border);
    }

    .store-hero-banner::after {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.6) 100%);
    }

    .store-info-card {
      background: var(--card-bg);
      border: 1px solid var(--card-border);
      border-radius: 20px;
      padding: 24px;
      box-shadow: var(--shadow-soft);
      backdrop-filter: blur(12px);
      transition: background-color 0.3s ease;
    }

    /* PRODUCTOS EN CARDS VERTICALES ELEGANTES */
    .prod-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 20px;
    }

    @media (max-width: 576px) {
      .prod-container {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
      }
    }

    .producto-card {
      background: var(--card-bg);
      border: 1px solid var(--card-border);
      border-radius: 16px;
      padding: 12px;
      box-shadow: var(--shadow-soft);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      height: 100%;
      transition: transform 0.2s ease, border-color 0.2s ease, background-color 0.3s ease;
    }

    .producto-card:hover {
      transform: translateY(-4px);
      border-color: rgba(16, 185, 129, 0.4);
    }

    .producto-img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 12px;
      border: 1px solid var(--card-border);
      background-color: rgba(255, 255, 255, 0.03);
    }

    @media (max-width: 576px) {
      .producto-img {
        height: 130px;
      }
      .producto-card {
        padding: 10px;
      }
    }

    .producto-info {
      display: flex;
      flex-direction: column;
      flex-grow: 1;
      padding-top: 10px;
    }

    .producto-nombre {
      font-weight: 700;
      font-size: 0.95rem;
      margin-bottom: 4px;
      color: var(--text-main);
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
      line-height: 1.3;
    }

    .producto-desc {
      color: #cbd5e1 !important;
      font-size: 0.8rem;
      line-height: 1.35;
      margin-bottom: 10px;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    [data-theme="light"] .producto-desc {
      color: #64748b !important;
    }

    .producto-precio {
      font-size: 1.25rem;
      font-weight: 800;
      color: var(--accent);
      margin-bottom: 12px;
    }

    .btn-qty-action {
      background: var(--counter-bg);
      border: 1px solid var(--card-border);
      color: var(--text-main);
      width: 30px;
      height: 30px;
      border-radius: 8px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      transition: all 0.15s ease;
    }

    .btn-qty-action:hover {
      background: var(--accent);
      color: #fff;
      border-color: var(--accent);
    }

    /* BOTÓN DESTINO SUPERIOR DEL CARRITO (TOP BAR) */
    .cart-target-btn {
      position: relative;
      background: var(--counter-bg);
      border: 1px solid var(--card-border);
      border-radius: 50px;
      padding: 6px 14px;
      display: flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
      font-weight: 700;
      font-size: 0.85rem;
      color: var(--text-main);
      transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .cart-target-btn:hover {
      border-color: var(--accent);
      color: var(--accent);
    }

    .cart-target-icon {
      font-size: 1rem;
      color: var(--accent);
      display: flex;
      align-items: center;
    }

    .cart-counter-badge {
      background: #ef4444;
      color: white;
      font-size: 11px;
      font-weight: 800;
      min-width: 20px;
      height: 20px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0 5px;
      box-shadow: 0 2px 6px rgba(239, 68, 68, 0.4);
      transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .cart-target-btn.bump {
      animation: cartTargetImpact 0.65s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .cart-target-btn.bump .cart-counter-badge {
      animation: badgePop 0.55s ease-out;
    }

    @keyframes cartTargetImpact {
      0% { transform: scale(1) translateX(0); }
      25% { transform: scale(1.25) translateX(6px) rotate(5deg); border-color: var(--accent); box-shadow: 0 0 20px rgba(16, 185, 129, 0.6); }
      50% { transform: scale(0.92) translateX(-4px) rotate(-3deg); }
      75% { transform: scale(1.06) translateX(2px) rotate(1deg); }
      100% { transform: scale(1) translateX(0) rotate(0deg); }
    }

    @keyframes badgePop {
      0% { transform: scale(1); }
      50% { transform: scale(1.5); background: #10b981; }
      100% { transform: scale(1); }
    }

    /* BOTÓN FLOTANTE CARRITO */
    #btnCarrito {
      position: fixed;
      bottom: 24px;
      right: 24px;
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      color: white;
      padding: 14px 24px;
      border-radius: 9999px;
      z-index: 999;
      box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
      font-weight: 700;
      font-size: 0.95rem;
      border: none;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    #btnCarrito:hover {
      transform: scale(1.05);
      box-shadow: 0 15px 30px rgba(16, 185, 129, 0.55);
    }

    /* MODALES CON TEMA */
    .modal-content {
      background-color: var(--modal-bg) !important;
      color: var(--text-main) !important;
      border: 1px solid var(--card-border) !important;
      border-radius: 20px;
    }

    .modal-header, .modal-footer {
      border-color: var(--card-border) !important;
    }

    .form-control-theme {
      background: var(--counter-bg) !important;
      border: 1px solid var(--card-border) !important;
      color: var(--text-main) !important;
      border-radius: 10px;
      padding: 10px 14px;
    }

    .form-control-theme:focus {
      border-color: var(--accent) !important;
      box-shadow: 0 0 10px rgba(16, 185, 129, 0.2) !important;
    }

    /* BOTÓN OPCIÓN EXTRA */
    .btn-option {
      border: 1px solid var(--card-border);
      background: var(--counter-bg);
      color: var(--text-main);
      font-weight: 600;
      width: 100%;
      padding: 12px;
      margin-bottom: 8px;
      border-radius: 10px;
      text-align: left;
      transition: all 0.2s ease;
      cursor: pointer;
    }

    .btn-option.selected {
      background: rgba(16, 185, 129, 0.15);
      border-color: var(--accent);
      color: var(--accent);
    }

    /* ===================================================
       SUB-BARRA DE ANIMACIÓN DEL CHANGUITO (EFECTO CARRITO)
       =================================================== */
    .anim-bar-container {
      position: sticky;
      top: 52px;
      left: 0;
      right: 0;
      width: 100%;
      height: 0;
      opacity: 0;
      overflow: hidden;
      background: var(--card-bg);
      border-bottom: 2px solid var(--accent);
      z-index: 998;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
      transition: height 0.32s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.25s ease;
    }

    .anim-bar-container.active {
      height: 110px;
      opacity: 1;
    }

    .anim-bar-track {
      position: relative;
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    .anim-track-line {
      position: absolute;
      bottom: 20px;
      left: 0;
      right: 0;
      height: 2px;
      background: linear-gradient(90deg, transparent, #64748b 10%, #10b981 50%, #64748b 90%, transparent);
      opacity: 0.5;
    }

    .anim-status-label {
      position: absolute;
      top: 8px;
      font-size: 13px;
      font-weight: 700;
      color: var(--accent);
      display: flex;
      align-items: center;
      gap: 6px;
      opacity: 0;
      transform: translateY(-6px);
      transition: all 0.3s ease;
    }

    .anim-bar-container.active .anim-status-label {
      opacity: 1;
      transform: translateY(0);
    }

    .anim-product {
      position: absolute;
      top: 24px;
      left: 50%;
      transform: translateX(-50%) scale(0);
      background: var(--card-bg);
      border: 1.5px solid var(--accent);
      padding: 3px 12px;
      border-radius: 20px;
      box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
      font-size: 12px;
      font-weight: 700;
      color: var(--text-main);
      z-index: 25;
      opacity: 0;
      white-space: nowrap;
      pointer-events: none;
    }

    .anim-box {
      position: absolute;
      top: 44px;
      left: 50%;
      transform: translateX(-50%) scale(0);
      width: 40px;
      height: 36px;
      background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
      border-radius: 8px;
      box-shadow: 0 6px 14px rgba(180, 83, 9, 0.4);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 18px;
      z-index: 20;
      opacity: 0;
      pointer-events: none;
    }

    .anim-cart {
      position: absolute;
      bottom: 16px;
      left: -120px;
      width: 60px;
      height: 50px;
      z-index: 15;
      pointer-events: none;
      filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.3));
    }

    .anim-cart svg {
      width: 100%;
      height: 100%;
    }

    .anim-sparkle {
      position: absolute;
      top: 42px;
      left: 50%;
      transform: translate(-50%, -50%) scale(0);
      color: #f59e0b;
      font-size: 24px;
      z-index: 30;
      opacity: 0;
    }

    .animating-active .anim-product {
      animation: dropProductInBox 0.85s cubic-bezier(0.25, 1, 0.5, 1) forwards;
    }

    .animating-active .anim-box {
      animation: boxAppears 0.85s ease forwards;
    }

    .animating-cart .anim-cart {
      animation: cartRollsToRight 2.2s cubic-bezier(0.25, 1, 0.5, 1) forwards;
    }

    .animating-cart .anim-box {
      animation: boxEntersAndExitsToRight 2.2s cubic-bezier(0.25, 1, 0.5, 1) forwards;
    }

    .animating-sparkle .anim-sparkle {
      animation: sparkleFlash 0.55s ease-out forwards;
    }

    @keyframes dropProductInBox {
      0% { transform: translateX(-50%) translateY(-25px) scale(0.4); opacity: 0; }
      40% { transform: translateX(-50%) translateY(0) scale(1.05); opacity: 1; }
      80% { transform: translateX(-50%) translateY(16px) scale(0.8); opacity: 1; }
      100% { transform: translateX(-50%) translateY(24px) scale(0); opacity: 0; }
    }

    @keyframes boxAppears {
      0% { transform: translateX(-50%) scale(0.3); opacity: 0; }
      35% { transform: translateX(-50%) scale(1.1); opacity: 1; }
      60% { transform: translateX(-50%) scale(0.95); opacity: 1; }
      80% { transform: translateX(-50%) scale(1.12); opacity: 1; }
      100% { transform: translateX(-50%) scale(1); opacity: 1; }
    }

    @keyframes cartRollsToRight {
      0% { left: -120px; }
      36% { left: 50%; transform: translateX(-50%); }
      52% { left: 50%; transform: translateX(-50%); }
      85% { left: 88%; transform: translateY(-4px) scale(0.95); }
      100% { left: 106%; transform: translateY(-16px) scale(0.8); opacity: 0; }
    }

    @keyframes boxEntersAndExitsToRight {
      0% { transform: translateX(-50%) scale(1); opacity: 1; }
      32% { transform: translateX(-50%) scale(1); opacity: 1; }
      48% { transform: translateX(-50%) translateY(18px) scale(0.68); opacity: 1; }
      85% { transform: translateX(calc(-50% + 185px)) translateY(14px) scale(0.62); opacity: 1; }
      100% { transform: translateX(calc(-50% + 250px)) translateY(-2px) scale(0.45); opacity: 0; }
    }

    @keyframes sparkleFlash {
      0% { transform: translate(-50%, -50%) scale(0) rotate(0deg); opacity: 0; }
      50% { transform: translate(-50%, -50%) scale(1.3) rotate(90deg); opacity: 1; }
      100% { transform: translate(-50%, -50%) scale(0.2) rotate(180deg); opacity: 0; }
    }
  </style>
</head>

<body>

  <!-- BARRA SUPERIOR FIJA -->
  <div class="top-bar-store">
    <div class="container d-flex justify-content-between align-items-center" style="max-width: 960px;">
      <a href="panel.html" id="btnVolverPanel" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
        <i class="bi bi-arrow-left"></i> Panel
      </a>

      <div class="d-flex align-items-center gap-2">
        <!-- BOTÓN TOGGLE DÍA / NOCHE -->
        <button id="themeToggleBtn" class="theme-toggle-btn" onclick="toggleTheme()" title="Cambiar modo de vista">
          <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
          <span id="themeLabel">Noche</span>
        </button>

        <!-- BOTÓN DESTINO CARRITO EN NAVBAR -->
        <button class="cart-target-btn" id="headerCartBtn" onclick="abrirCarrito()">
          <span class="cart-target-icon">
            <i class="bi bi-bag-check-fill"></i>
          </span>
          <span>Carrito</span>
          <span class="cart-counter-badge" id="cartCountBadge">0</span>
        </button>
      </div>
    </div>
  </div>

  <!-- SUB-BARRA DE ANIMACIÓN DEL CHANGUITO (EFECTO CARRITO) -->
  <div class="anim-bar-container" id="animBar">
    <div class="anim-bar-track" id="animTrack">
      <div class="anim-status-label">
        <i class="bi bi-box-seam-fill"></i> <span id="statusLabelText">Empacando artículo en tu carrito...</span>
      </div>
      <div class="anim-track-line"></div>

      <!-- 1. Producto entrando a la caja -->
      <div class="anim-product" id="animProduct">
        <span>📦</span>
        <span id="animProductName">Artículo</span>
      </div>

      <!-- 2. Caja compacta -->
      <div class="anim-box" id="animBox">
        <i class="bi bi-box2-fill"></i>
      </div>

      <!-- 3. Destello de confirmación -->
      <div class="anim-sparkle" id="animSparkle">
        <i class="bi bi-stars"></i>
      </div>

      <!-- 4. Changuito que rueda hacia la DERECHA -->
      <div class="anim-cart" id="animCart">
        <svg viewBox="0 0 100 80" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M10 12H24L36 52H76L86 22H30" stroke="#10b981" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M35 30H82M38 40H79M46 22V50M60 22V50M74 22V50" stroke="#059669" stroke-width="2.2" stroke-linecap="round"/>
          <path d="M18 12H8" stroke="#0f172a" stroke-width="5" stroke-linecap="round"/>
          <path d="M36 52L30 65H80" stroke="#047857" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
          <circle cx="42" cy="68" r="7" fill="#0f172a" stroke="#ffffff" stroke-width="2.5"/>
          <circle cx="74" cy="68" r="7" fill="#0f172a" stroke="#ffffff" stroke-width="2.5"/>
        </svg>
      </div>
    </div>
  </div>

  <div class="container py-4" style="max-width: 960px;">

    <!-- AVISO MODO DEMO/PREVIEW -->
    <div id="previewAviso" class="alert alert-warning border-0 shadow-sm rounded-4 p-3 mb-4 d-none">
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-info-circle-fill fs-5"></i>
        <div>
          <strong>Vista previa de tienda</strong> — Los pedidos por WhatsApp se habilitan una vez suscripta tu tienda.
        </div>
      </div>
    </div>

    <!-- BANNER Y CABECERA DE LA TIENDA -->
    <div id="banner" class="store-hero-banner mb-4"></div>

    <div class="store-info-card mb-4">
      <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
          <h2 id="nombre" class="fw-bold mb-1"></h2>
          <p id="descripcion" class="text-muted mb-0"></p>
        </div>
        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill fw-semibold">
          <i class="bi bi-check-circle-fill me-1"></i> Catálogo Activo
        </span>
      </div>
    </div>

    <!-- SECCIÓN PRODUCTOS -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="fw-bold mb-0">Catálogo de Productos</h4>
      <span class="text-muted small" id="cantProductosTxt">Cargando...</span>
    </div>

    <div id="listaProductos" class="prod-container"></div>

    <!-- BLOQUE SUSCRIPCIÓN MERCADO PAGO SI ES PREVIEW -->
    <div id="bloqueCompra" class="my-5" style="display: none;">
      <div class="p-4 rounded-4 text-center border border-success border-opacity-25 shadow-sm" style="background: rgba(16, 185, 129, 0.08);">
        <h4 class="fw-bold mb-2">Activá tu tienda online hoy</h4>
        <p class="text-muted mb-3">Habilitá pedidos automáticos directos a tu WhatsApp con suscripción mensual de Mercado Pago.</p>
        <button class="btn btn-success px-4 py-3 fw-bold rounded-3 shadow" onclick="aceptarMiTienda()">
          <i class="bi bi-lightning-charge-fill me-1"></i> Comprar y Activar Mi Tienda
        </button>
      </div>
    </div>

  </div>

  <!-- BOTÓN FLOTANTE CARRITO -->
  <button id="btnCarrito" onclick="abrirCarrito()">
    <i class="bi bi-bag-check-fill fs-5"></i>
    <span id="btnCarritoTxt">Ver carrito (0)</span>
  </button>

  <!-- MODAL CARRITO -->
  <div class="modal fade" id="modalCarrito" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold">
            <i class="bi bi-bag-fill text-success me-2"></i>Tu Pedido
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <div id="carritoItems"></div>
          <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
            <span class="fs-5 fw-semibold">Total a pagar:</span>
            <span class="fs-4 fw-bold text-success">$<span id="totalCarrito">0</span></span>
          </div>
        </div>
        <div class="modal-footer border-0">
          <button class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Seguir mirando</button>
          <button class="btn btn-success fw-bold px-4 rounded-3" onclick="abrirModalWhatsApp()">
            Continuar compra <i class="bi bi-arrow-right ms-1"></i>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL DATOS WHATSAPP -->
  <div class="modal fade" id="modalWhatsApp" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title fw-bold">
            <i class="bi bi-whatsapp me-2"></i>Datos para el pedido
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label text-muted small">Tu Nombre *</label>
            <input id="wNombre" class="form-control form-control-theme" placeholder="Ej: Juan">
          </div>
          <div class="mb-3">
            <label class="form-label text-muted small">Tu Apellido *</label>
            <input id="wApellido" class="form-control form-control-theme" placeholder="Ej: Perez">
          </div>
          <div class="mb-3">
            <label class="form-label text-muted small">Tu WhatsApp (sin 0 ni 15) *</label>
            <input id="wTelefono" class="form-control form-control-theme" placeholder="Ej: 1122334455" type="tel">
          </div>

          <div id="opcionesContainer" class="mt-4 pt-3 border-top"></div>
        </div>
        <div class="modal-footer border-0">
          <button class="btn btn-success w-100 py-3 fw-bold rounded-3 shadow" onclick="enviarWhatsApp()">
            <i class="bi bi-whatsapp me-2"></i> Enviar pedido por WhatsApp
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    /* ========================================================
       MODO DÍA / NOCHE (LOCALSTORAGE PERSISTENTE)
       ======================================================== */
    function initTheme() {
      const savedTheme = localStorage.getItem("cd_theme") || "dark";
      applyTheme(savedTheme);
    }

    function applyTheme(theme) {
      document.documentElement.setAttribute("data-theme", theme);
      localStorage.setItem("cd_theme", theme);

      const icon = document.getElementById("themeIcon");
      const label = document.getElementById("themeLabel");

      if (theme === "dark") {
        if (icon) icon.className = "bi bi-moon-stars-fill text-warning";
        if (label) label.innerText = "Noche";
      } else {
        if (icon) icon.className = "bi bi-sun-fill text-warning";
        if (label) label.innerText = "Día";
      }
    }

    function toggleTheme() {
      const current = document.documentElement.getAttribute("data-theme") || "dark";
      const next = current === "dark" ? "light" : "dark";
      applyTheme(next);
    }

    initTheme();

    /* ========================================================
       LÓGICA DE TIENDA Y CARRITO
       ======================================================== */
    let tienda_slug = "";
    let tienda = null;
    let tienda_id = "";
    let telefonoTienda = "";
    let carrito = JSON.parse(localStorage.getItem("carrito")) || [];
    let esPreview = true;

    function guardarCarrito() {
      localStorage.setItem("carrito", JSON.stringify(carrito));
      actualizarBotonCarrito();
    }

    function actualizarBotonCarrito() {
      const totalItems = carrito.reduce((sum, item) => sum + item.cantidad, 0);
      const btn = document.getElementById("btnCarritoTxt");
      if (btn) btn.innerText = `Ver carrito (${totalItems})`;

      const badge = document.getElementById("cartCountBadge");
      if (badge) badge.innerText = totalItems;
    }

    document.addEventListener("DOMContentLoaded", () => {
      actualizarBotonCarrito();

      let slug = new URLSearchParams(location.search).get("slug");
      if (!slug) {
        const path = window.location.pathname;
        const parts = path.split("/").filter(p => p.length > 0 && !p.includes(".html") && !p.includes(".php"));
        if (parts.length > 0) slug = parts[parts.length - 1];
      }

      if (!slug) {
        document.body.innerHTML = "<div class='container py-5 text-center'><h3>Error: No se especificó ninguna tienda.</h3><a href='index.html' class='btn btn-success mt-3'>Volver al inicio</a></div>";
        return;
      }

      fetch("backend/get_tienda.php?slug=" + encodeURIComponent(slug))
        .then(r => r.json())
        .then(tiendaData => {
          tienda = tiendaData;

          if (!tienda || tienda.error) {
            alert("No se encontró la tienda");
            return;
          }

          tienda_id = tienda.id;
          telefonoTienda = tienda.telefono || "";
          esPreview = tienda.estado !== "activa";

          // BANNER / LOGO
          const banner = document.getElementById("banner");
          if (tienda.logo) {
            banner.style.backgroundImage = `url('${tienda.logo}')`;
          } else {
            banner.style.backgroundImage = `url('img/ilustracion2.png')`;
          }

          document.getElementById("nombre").innerText = tienda.nombre;
          document.getElementById("descripcion").innerText = tienda.descripcion || "Bienvenidos a nuestro catálogo online.";
          document.title = `${tienda.nombre} | CeroDistancia`;

          // OPCIONES PERSONALIZADAS
          window.tiendaOpciones = [];
          window.tiendaTituloOpciones = tienda.titulo_opciones || "";
          if (tienda.opcion1) window.tiendaOpciones.push(tienda.opcion1);
          if (tienda.opcion2) window.tiendaOpciones.push(tienda.opcion2);
          if (tienda.opcion3) window.tiendaOpciones.push(tienda.opcion3);

          // ESTADO PREVIEW / ACTIVA
          const isActivo = (tienda.estado === 'activa' || tienda.estado === 'activo');
          const isPreviewParam = new URLSearchParams(window.location.search).get('preview') === 'true';

          const aviso = document.getElementById("previewAviso");
          const bloqueCompra = document.getElementById("bloqueCompra");

          if (!isActivo) {
            if (isPreviewParam) {
              if (aviso) aviso.classList.remove("d-none");
              if (bloqueCompra) bloqueCompra.style.display = "block";
            } else {
              window.location.href = "login.html";
              return;
            }
          } else {
            if (aviso) aviso.classList.add("d-none");
            if (bloqueCompra) bloqueCompra.style.display = "none";
          }

          cargarProductos();

          // OCULTAR BOTÓN VOLVER SI NO ES PREVIEW NI PARÁMETRO EXPLÍCITO
          if (!esPreview && !isPreviewParam) {
            const btnVolver = document.getElementById("btnVolverPanel");
            if (btnVolver) btnVolver.style.display = 'none';
          }
        });
    });

    function cargarProductos() {
      fetch("backend/get_productos.php?tienda_id=" + tienda_id)
        .then(r => r.json())
        .then(productos => {
          const cont = document.getElementById("listaProductos");
          const cantTxt = document.getElementById("cantProductosTxt");
          cont.innerHTML = "";

          if (!Array.isArray(productos) || productos.length === 0) {
            cont.innerHTML = "<div class='p-4 text-center text-muted'>Aún no hay productos cargados en esta tienda.</div>";
            if (cantTxt) cantTxt.innerText = "0 artículos";
            return;
          }

          if (cantTxt) cantTxt.innerText = `${productos.length} artículos disponibles`;

          productos.forEach(p => {
            const item = carrito.find(i => i.id === p.id);
            const cantidad = item ? item.cantidad : 0;
            const stockActivo = (p.activo === undefined || p.activo === null) ? true : (parseInt(p.activo) === 1);

            cont.innerHTML += `
              <div class="producto-card">
                ${p.imagen 
                  ? `<img src="${p.imagen}" class="producto-img" alt="${p.nombre}">` 
                  : `<div class="producto-img d-flex align-items-center justify-content-center text-muted"><i class="bi bi-box-seam fs-1"></i></div>`
                }

                <div class="producto-info">
                  <div class="producto-nombre">${p.nombre}</div>
                  <div class="producto-precio">$${p.precio}</div>
                  ${p.descripcion ? `<div class="producto-desc">${p.descripcion}</div>` : ""}

                  <div class="mt-auto pt-2">
                    ${!stockActivo
                      ? `<button class="btn btn-secondary btn-sm w-100 rounded-pill" disabled>Sin Stock</button>`
                      : (cantidad === 0
                        ? `<button class="btn btn-success btn-sm w-100 fw-bold rounded-pill py-2" onclick="agregar(${p.id}, '${p.nombre.replace(/'/g, "\\'")}', ${p.precio})">
                             <i class="bi bi-plus-lg me-1"></i> Agregar
                           </button>`
                        : `<div class="d-flex align-items-center justify-content-between rounded-pill p-1" style="background:var(--counter-bg); border:1px solid var(--card-border);">
                             <button class="btn-qty-action" onclick="restar(${p.id})">−</button>
                             <span class="fw-bold px-2">${cantidad}</span>
                             <button class="btn-qty-action" onclick="sumar(${p.id})">+</button>
                           </div>`
                      )
                    }
                  </div>
                </div>
              </div>
            `;
          });
        });
    }

    /* ==============================================================
       ANIMACIÓN DEL CHANGUITO RODANTE (EFECTO CARRITO)
       ============================================================== */
    let isRunningAnim = false;

    function ejecutarAnimacionHaciaDerecha(nombreProducto, callback) {
      const animBar = document.getElementById("animBar");
      const track = document.getElementById("animTrack");
      const prodNameSpan = document.getElementById("animProductName");
      const statusText = document.getElementById("statusLabelText");
      const headerCartBtn = document.getElementById("headerCartBtn");

      if (!animBar || !track) {
        if (typeof callback === 'function') callback();
        return;
      }

      if (isRunningAnim) {
        if (typeof callback === 'function') callback();
        return;
      }
      isRunningAnim = true;

      const displayNombre = nombreProducto.length > 25 ? nombreProducto.substring(0, 25) + "..." : nombreProducto;
      if (prodNameSpan) prodNameSpan.innerText = displayNombre;
      if (statusText) statusText.innerText = `Empacando "${displayNombre}"...`;

      // 1. Abrir sub-barra reactiva
      animBar.classList.add("active");

      // 2. Cae el producto dentro de la caja en el centro
      setTimeout(() => {
        track.className = "anim-bar-track animating-active";
      }, 100);

      // 3. Entra el changuito desde la izquierda hacia el centro
      setTimeout(() => {
        track.classList.add("animating-cart");
      }, 550);

      // 4. La caja cae dentro del changuito y salta el destello
      setTimeout(() => {
        track.classList.add("animating-sparkle");
        if (statusText) statusText.innerText = "¡Listo en el changuito!";
      }, 900);

      // 5. El changuito choca a la derecha contra el botón de Carrito
      setTimeout(() => {
        if (headerCartBtn) {
          headerCartBtn.classList.add("bump");
          setTimeout(() => headerCartBtn.classList.remove("bump"), 600);
        }

        if (typeof callback === 'function') callback();
      }, 2000);

      // 6. Se pliega suavemente la barra
      setTimeout(() => {
        animBar.classList.remove("active");
        setTimeout(() => {
          track.className = "anim-bar-track";
          isRunningAnim = false;
        }, 350);
      }, 2400);
    }

    function agregar(id, nombre, precio) {
      carrito.push({ id, nombre, precio, cantidad: 1 });
      guardarCarrito();
      ejecutarAnimacionHaciaDerecha(nombre, () => {
        cargarProductos();
      });
    }

    function sumar(id) {
      const item = carrito.find(i => i.id === id);
      if (item) {
        item.cantidad++;
        guardarCarrito();
        cargarProductos();
        renderizarCarrito();
      }
    }

    function restar(id) {
      const item = carrito.find(i => i.id === id);
      if (item) {
        item.cantidad--;
        if (item.cantidad <= 0) {
          carrito = carrito.filter(i => i.id !== id);
        }
        guardarCarrito();
        cargarProductos();
        renderizarCarrito();
      }
    }

    function abrirCarrito() {
      renderizarCarrito();
      const modal = new bootstrap.Modal(document.getElementById("modalCarrito"));
      modal.show();
    }

    function renderizarCarrito() {
      const cont = document.getElementById("carritoItems");
      cont.innerHTML = "";

      if (carrito.length === 0) {
        cont.innerHTML = "<div class='text-center py-4 text-muted'>Tu carrito está vacío.</div>";
        document.getElementById("totalCarrito").innerText = "0";
        return;
      }

      let total = 0;
      carrito.forEach(p => {
        const subtotal = p.precio * p.cantidad;
        total += subtotal;

        cont.innerHTML += `
          <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-secondary border-opacity-10">
            <div>
              <div class="fw-bold">${p.nombre}</div>
              <small class="text-muted">$${p.precio} x ${p.cantidad}</small>
            </div>
            <div class="d-flex align-items-center gap-3">
              <span class="fw-bold text-success">$${subtotal}</span>
              <div class="d-flex align-items-center gap-1">
                <button class="btn-qty-action" onclick="restar(${p.id})">−</button>
                <span class="fw-bold px-2">${p.cantidad}</span>
                <button class="btn-qty-action" onclick="sumar(${p.id})">+</button>
              </div>
            </div>
          </div>
        `;
      });

      document.getElementById("totalCarrito").innerText = total;
    }

    function abrirModalWhatsApp() {
      if (carrito.length === 0) return alert("Tu carrito está vacío.");

      const modalCarritoEl = document.getElementById("modalCarrito");
      const modalCarritoInst = bootstrap.Modal.getInstance(modalCarritoEl);
      if (modalCarritoInst) modalCarritoInst.hide();

      // RENDERIZAR OPCIONES EXTRA
      const cont = document.getElementById("opcionesContainer");
      cont.innerHTML = "";

      if (window.tiendaOpciones && window.tiendaOpciones.length > 0) {
        const titulo = window.tiendaTituloOpciones || "Opciones del Pedido";
        cont.innerHTML += `<h6 class="fw-bold mb-3">${titulo}</h6>`;

        window.tiendaOpciones.forEach((opc, idx) => {
          cont.innerHTML += `
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" value="${opc}" id="opc_${idx}">
              <label class="form-check-label fw-semibold" for="opc_${idx}">${opc}</label>
            </div>
          `;
        });
      }

      const modalWA = new bootstrap.Modal(document.getElementById("modalWhatsApp"));
      modalWA.show();
    }

    function enviarWhatsApp() {
      const nombre = document.getElementById("wNombre").value.trim();
      const apellido = document.getElementById("wApellido").value.trim();
      const telefonoCliente = document.getElementById("wTelefono").value.trim();

      if (!nombre || !apellido || !telefonoCliente) {
        return alert("Por favor completá todos los campos para poder comunicarte.");
      }
      if (!telefonoTienda) {
        return alert("Esta tienda aún no tiene cargado un número de WhatsApp de contacto.");
      }

      let mensaje = `👋 Hola, mi nombre es *${nombre} ${apellido}*.\n`;
      mensaje += `Quisiera realizar el siguiente pedido en *${document.getElementById("nombre").innerText}*:\n\n`;

      let total = 0;
      carrito.forEach(p => {
        mensaje += `▪️ ${p.cantidad}x ${p.nombre} ($${p.precio}) = $${p.precio * p.cantidad}\n`;
        total += p.precio * p.cantidad;
      });

      mensaje += `\n💰 *Total: $${total}*\n`;
      mensaje += `📱 Mi contacto: ${telefonoCliente}\n`;

      const cont = document.getElementById("opcionesContainer");
      const checked = cont.querySelectorAll("input[type='checkbox']:checked");

      if (checked.length > 0) {
        const titulo = window.tiendaTituloOpciones || "Opciones";
        mensaje += `\n📝 *${titulo}:*`;
        checked.forEach(chk => {
          mensaje += `\n☑ ${chk.value}`;
        });
      }

      const url = `https://wa.me/${telefonoTienda}?text=${encodeURIComponent(mensaje)}`;
      window.open(url, "_blank");
    }

    function aceptarMiTienda() {
      if (!esPreview) return alert("La tienda ya está activa");
      fetch("backend/crear_suscripcion_mp.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ tienda_id })
      })
        .then(r => r.json())
        .then(d => {
          if (d.init_point) location.href = d.init_point;
          else alert("Error al conectar con Mercado Pago");
        });
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>