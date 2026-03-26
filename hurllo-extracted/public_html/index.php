<?php
/**
 * Hurllo - South Africa Car Hire Intelligence Platform
 * Main Router - SEO-friendly GET parameter routing
 */

define('CARVIO_ROOT', __DIR__);

// Security: prevent direct file access to includes/pages
$page = preg_replace('/[^a-z0-9\-_]/', '', strtolower($_GET['page'] ?? 'home'));

// Valid pages
$validPages = [
    'home', 'directory', 'company', 'vehicle', 'vehicles',
    'compare', 'education', 'rankings', 'airport', 'destination', 'tourist'
];

if (!in_array($page, $validPages)) {
    $page = '404';
}

// Load header
require_once CARVIO_ROOT . '/includes/header.php';

// Route to page
$pagePath = CARVIO_ROOT . '/pages/' . $page . '.php';

if (file_exists($pagePath)) {
    require_once $pagePath;
} else {
    // 404 fallback
    http_response_code(404);
    echo '<div class="container" style="padding: 4rem 0; text-align: center;">';
    echo '<h1 style="color: var(--color-primary); font-size: 4rem;">404</h1>';
    echo '<p style="color: var(--color-text-muted); font-size: 1.2rem;">Page not found</p>';
    echo '<a href="/" class="btn btn--primary" style="margin-top: 1.5rem;">Return Home</a>';
    echo '</div>';
}

// Load footer
require_once CARVIO_ROOT . '/includes/footer.php';
