<?php
if (!defined('CARVIO_ROOT')) {
    define('CARVIO_ROOT', dirname(__DIR__));
}
require_once CARVIO_ROOT . '/includes/functions.php';
require_once CARVIO_ROOT . '/includes/scoring.php';

$currentPage = getCurrentPage();
$currentSlug = getSlug();
$pageTitle = getPageTitle($currentPage, $currentSlug);
$metaDesc = getMetaDescription($currentPage, $currentSlug);
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($metaDesc) ?>">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($metaDesc) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= htmlspecialchars($baseUrl . $_SERVER['REQUEST_URI']) ?>">
    <link rel="canonical" href="<?= htmlspecialchars($baseUrl . $_SERVER['REQUEST_URI']) ?>">
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <!-- Organization Schema -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "Hurllo",
        "description": "South Africa's Car Hire Intelligence Platform",
        "url": "<?= htmlspecialchars($baseUrl) ?>",
        "logo": "<?= htmlspecialchars($baseUrl) ?>/assets/images/hurllo.png",
        "areaServed": "South Africa",
        "knowsAbout": ["Car Hire", "Vehicle Rental", "South Africa Travel"]
    }
    </script>
    
    <?php if ($currentPage === 'home'): ?>
    <!-- Breadcrumb Schema for Home -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "Hurllo",
        "url": "<?= htmlspecialchars($baseUrl) ?>",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "<?= htmlspecialchars($baseUrl) ?>/?page=directory&search={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>
    <?php endif; ?>
</head>
<body class="dark-mode" data-page="<?= htmlspecialchars($currentPage) ?>">

<!-- Skip to content -->
<a href="#main-content" class="skip-link">Skip to main content</a>

<!-- Header -->
<header class="site-header" id="site-header">
    <div class="container">
        <div class="header-inner">
            <!-- Logo -->
            <a href="/" class="site-logo" aria-label="Hurllo Home">
                <img src="/assets/images/hurllo.png" alt="Hurllo" class="logo-image" />
                <span class="logo-tagline">Intelligence</span>
            </a>
            
            <!-- Main Navigation -->
            <nav class="main-nav" id="main-nav" aria-label="Main navigation">
                <ul class="nav-list">
                    <li class="nav-item <?= $currentPage === 'home' ? 'nav-item--active' : '' ?>">
                        <a href="/" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item <?= $currentPage === 'directory' ? 'nav-item--active' : '' ?>">
                        <a href="<?= url('directory') ?>" class="nav-link">Directory</a>
                    </li>
                    <li class="nav-item <?= $currentPage === 'vehicles' ? 'nav-item--active' : '' ?>">
                        <a href="<?= url('vehicles') ?>" class="nav-link">Vehicles</a>
                    </li>
                    <li class="nav-item <?= $currentPage === 'compare' ? 'nav-item--active' : '' ?>">
                        <a href="<?= url('compare') ?>" class="nav-link">Compare</a>
                    </li>
                    <li class="nav-item <?= $currentPage === 'rankings' ? 'nav-item--active' : '' ?>">
                        <a href="<?= url('rankings') ?>" class="nav-link">Rankings</a>
                    </li>
                    <li class="nav-item <?= $currentPage === 'education' ? 'nav-item--active' : '' ?>">
                        <a href="<?= url('education') ?>" class="nav-link">Guide</a>
                    </li>
                    <li class="nav-item <?= $currentPage === 'tourist' ? 'nav-item--active' : '' ?>">
                        <a href="<?= url('tourist') ?>" class="nav-link">For Tourists</a>
                    </li>
                    <li class="nav-item <?= $currentPage === 'tourist' ? 'nav-item--active' : '' ?>">
                        <a href="<?= url('tourist') ?>" class="nav-link">For Tourists</a>
                    </li>
                </ul>
            </nav>
            
            <!-- Header CTA -->
            <div class="header-actions">
                <a href="<?= url('compare') ?>" class="btn btn--primary btn--sm">Compare Now</a>
                <button class="nav-toggle" id="nav-toggle" aria-label="Toggle navigation" aria-expanded="false">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Nav Overlay -->
<div class="mobile-nav-overlay" id="mobile-nav-overlay"></div>

<!-- Main Content -->
<main id="main-content">
