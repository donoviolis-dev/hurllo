<?php
/**
 * Hurllo - Car Hire Intelligence Platform
 * Core Functions Library - Adapted for user JSON structure
 */

if (!defined('CARVIO_ROOT')) {
    define('CARVIO_ROOT', dirname(__DIR__));
}

/**
 * Load and cache JSON data files
 */
function loadJSON(string $filename): array {
    static $cache = [];
    if (isset($cache[$filename])) {
        return $cache[$filename];
    }
    $path = CARVIO_ROOT . '/data/' . $filename;
    if (!file_exists($path)) {
        return [];
    }
    $data = json_decode(file_get_contents($path), true);
    $cache[$filename] = $data ?? [];
    return $cache[$filename];
}

/**
 * Get all companies (returns flat array with normalised fields)
 */
function getCompanies(): array {
    $raw = loadJSON('companies.json');
    $companies = $raw['companies'] ?? [];
    // Normalise each company to add computed fields
    return array_map('normaliseCompany', $companies);
}

/**
 * Normalise a company record to add slug and convenience fields
 */
function normaliseCompany(array $c): array {
    // Generate slug from company_name
    $c['slug'] = makeSlug($c['company_name'] ?? '');
    $c['name'] = $c['company_name'] ?? '';

    // Flatten deposit
    $c['deposit_avg'] = $c['costs']['typical_deposit']['average'] ?? 3000;
    $c['deposit_min'] = $c['costs']['typical_deposit']['min'] ?? 2000;
    $c['deposit_max'] = $c['costs']['typical_deposit']['max'] ?? 5000;
    $c['deposit_type'] = $c['costs']['typical_deposit']['hold_type'] ?? 'credit_card_only';

    // Flatten excess
    $c['excess_avg'] = $c['costs']['typical_excess']['average'] ?? 15000;
    $c['excess_min'] = $c['costs']['typical_excess']['min'] ?? 10000;
    $c['excess_max'] = $c['costs']['typical_excess']['max'] ?? 20000;

    // Flatten pricing
    $c['economy_rate_min'] = $c['pricing']['economy_daily_rate']['min'] ?? 380;
    $c['economy_rate_max'] = $c['pricing']['economy_daily_rate']['max'] ?? 550;
    $c['economy_rate_avg'] = $c['pricing']['economy_daily_rate']['average'] ?? 465;

    // Flatten services
    $c['cross_border_allowed'] = $c['services']['cross_border_allowed'] ?? false;
    $c['supports_debit_card'] = ($c['costs']['typical_deposit']['hold_type'] ?? '') !== 'credit_card_only';
    $c['mileage_policy'] = $c['policies']['mileage_policy'] ?? 'unknown';
    $c['long_term_available'] = $c['pricing']['long_term_rental_available'] ?? false;

    // Flatten locations
    $c['locations_count'] = $c['locations']['total_locations'] ?? 0;
    $c['major_airports'] = $c['locations']['airports'] ?? [];

    // Flatten reviews
    $c['trustpilot_score'] = $c['reviews']['trustpilot_score'] ?? 3.5;
    $c['google_score'] = $c['reviews']['google_review_score'] ?? 4.0;
    $c['combined_score'] = $c['reviews']['combined_satisfaction_score'] ?? 3.75;

    // Most affordable model
    $c['most_affordable_model'] = ($c['most_affordable_model']['vehicle_brand'] ?? '') . ' ' . ($c['most_affordable_model']['vehicle_model'] ?? '');
    $c['most_affordable_model'] = trim($c['most_affordable_model']);
    $c['most_affordable_price_range'] = $c['most_affordable_model_data']['estimated_daily_price_range_ZAR'] ?? $c['economy_rate_min'] . '-' . $c['economy_rate_max'];

    // Logo filename (slug-based)
    $c['logo'] = $c['slug'] . '.png';

    // Most affordable image (slug of model)
    $modelSlug = makeSlug($c['most_affordable_model']);
    $c['most_affordable_image'] = $modelSlug . '.png';

    // Refund speed (derived from brand type)
    $globalBrands = ['avis', 'hertz', 'europcar', 'budget', 'sixt', 'alamo', 'national', 'thrifty', 'dollar', 'keddy'];
    $c['refund_speed'] = in_array($c['slug'], $globalBrands) ? '5-10 business days' : '3-7 business days';

    // Foreign licence acceptance (major brands accept, hyper-local may not)
    $majorBrands = ['avis', 'budget', 'hertz', 'europcar', 'sixt', 'first-car-rental', 'thrifty', 'dollar', 'tempest', 'green-motion', 'europcar', 'bidvest-car-rental'];
    $c['accepts_foreign_licence'] = in_array($c['slug'], $majorBrands);

    return $c;
}

/**
 * Get all vehicles (returns flat array with normalised fields)
 */
function getVehicles(): array {
    $raw = loadJSON('vehicles.json');
    $vehicles = $raw['vehicles_offered'] ?? [];
    return array_map('normaliseVehicle', $vehicles);
}

/**
 * Normalise a vehicle record
 */
function normaliseVehicle(array $v): array {
    $v['name'] = $v['vehicle_model'] ?? '';
    $v['slug'] = makeSlug($v['name']);
    $v['image'] = $v['slug'] . '.png';

    // Boot space - handle string values like "open load bin (~1000L equivalent)"
    $boot = $v['boot_space_litres'] ?? 0;
    if (is_string($boot)) {
        preg_match('/(\d+)/', $boot, $m);
        $v['boot_space_litres'] = (int)($m[1] ?? 0);
        $v['boot_space_display'] = $boot;
    } else {
        $v['boot_space_display'] = $boot . 'L';
    }

    // Demand and popularity based on segment
    $segmentDemand = [
        'Economy' => 85,
        'Compact' => 65,
        'SUV' => 80,
        'Pickup/Utility' => 70,
        'Luxury' => 45,
        'Electric/Hybrid' => 55,
    ];
    $v['demand_percent'] = $segmentDemand[$v['segment'] ?? 'Economy'] ?? 60;
    $v['popularity_index'] = (int)round($v['demand_percent'] / 10);

    // Average daily rate based on segment
    $segmentRates = [
        'Economy' => 465,
        'Compact' => 550,
        'SUV' => 900,
        'Pickup/Utility' => 850,
        'Luxury' => 2000,
        'Electric/Hybrid' => 750,
    ];
    $v['avg_daily_rate_ZAR'] = $segmentRates[$v['segment'] ?? 'Economy'] ?? 500;

    // Description
    $v['description'] = 'The ' . $v['name'] . ' is a popular ' . ($v['segment'] ?? '') . ' rental vehicle available across South Africa.';

    return $v;
}

/**
 * Get market data
 */
function getMarketData(): array {
    $raw = loadJSON('market.json');
    return $raw['market_data_customer_view'] ?? $raw;
}

/**
 * Get all airports
 */
function getAirports(): array {
    $raw = loadJSON('airports.json');
    return $raw ?? [];
}

/**
 * Get a single airport by slug
 */
function getAirportBySlug(string $slug): ?array {
    $airports = getAirports();
    foreach ($airports as $airport) {
        if ($airport['slug'] === $slug) {
            return $airport;
        }
    }
    return null;
}

/**
 * Get all destinations
 */
function getDestinations(): array {
    $raw = loadJSON('destinations.json');
    return $raw ?? [];
}

/**
 * Get a single destination by slug
 */
function getDestinationBySlug(string $slug): ?array {
    $destinations = getDestinations();
    foreach ($destinations as $destination) {
        if ($destination['slug'] === $slug) {
            return $destination;
        }
    }
    return null;
}

/**
 * Get currencies data
 */
function getCurrencies(): array {
    $raw = loadJSON('currencies.json');
    return $raw ?? ['base' => 'ZAR', 'rates' => [], 'symbols' => []];
}

/**
 * Convert amount from ZAR to specified currency
 */
function convertCurrency(int|float $amountZar, string $currency, ?array $currenciesData = null): string {
    if ($currency === 'ZAR') {
        return formatZAR($amountZar);
    }
    
    if ($currenciesData === null) {
        $currenciesData = getCurrencies();
    }
    
    $rate = $currenciesData['rates'][$currency] ?? 1;
    $symbol = $currenciesData['symbols'][$currency] ?? $currency;
    
    $converted = round($amountZar * $rate);
    
    return $symbol . number_format($converted, 0, '.', ' ');
}

/**
 * Generate URL slug from string
 */
function makeSlug(string $str): string {
    $str = strtolower(trim($str));
    $str = preg_replace('/[^a-z0-9\s\-]/', '', $str);
    $str = preg_replace('/[\s\-]+/', '-', $str);
    return trim($str, '-');
}

/**
 * Get a single company by slug
 */
function getCompanyBySlug(string $slug): ?array {
    $companies = getCompanies();
    foreach ($companies as $company) {
        if ($company['slug'] === $slug) {
            return $company;
        }
    }
    return null;
}

/**
 * Get a single vehicle by slug
 */
function getVehicleBySlug(string $slug): ?array {
    $vehicles = getVehicles();
    foreach ($vehicles as $vehicle) {
        if ($vehicle['slug'] === $slug) {
            return $vehicle;
        }
    }
    return null;
}

/**
 * Get companies offering a specific vehicle
 */
function getCompaniesForVehicle(string $vehicleName): array {
    $companies = getCompanies();
    return array_values(array_filter($companies, function($c) use ($vehicleName) {
        $brands = $c['vehicle_brands_available'] ?? [];
        // Check if any brand matches the vehicle name
        foreach ($brands as $brand) {
            if (stripos($vehicleName, $brand) !== false) return true;
        }
        // Also check most_affordable_model
        if (stripos($c['most_affordable_model'] ?? '', $vehicleName) !== false) return true;
        return false;
    }));
}

/**
 * Get companies offering a vehicle by checking the vehicle's companies array
 */
function getCompaniesForVehicleByList(array $vehicle): array {
    $vehicleCompanies = $vehicle['companies'] ?? [];
    if (empty($vehicleCompanies)) return [];
    
    $allCompanies = getCompanies();
    return array_values(array_filter($allCompanies, function($c) use ($vehicleCompanies) {
        return in_array($c['name'], $vehicleCompanies);
    }));
}

/**
 * Format price in ZAR
 */
function formatZAR(int|float $amount): string {
    return 'R ' . number_format($amount, 0, '.', ' ');
}

/**
 * Get current page from GET parameter
 */
function getCurrentPage(): string {
    return $_GET['page'] ?? 'home';
}

/**
 * Get slug from GET parameter
 */
function getSlug(): string {
    return preg_replace('/[^a-z0-9\-]/', '', strtolower($_GET['slug'] ?? ''));
}

/**
 * Build internal URL
 */
function url(string $page, string $slug = '', array $extra = []): string {
    $params = ['page' => $page];
    if ($slug) $params['slug'] = $slug;
    $params = array_merge($params, $extra);
    return '/?' . http_build_query($params);
}

/**
 * Generate breadcrumbs
 */
function breadcrumbs(array $crumbs): string {
    $html = '<nav class="breadcrumbs" aria-label="Breadcrumb"><ol itemscope itemtype="https://schema.org/BreadcrumbList">';
    foreach ($crumbs as $i => $crumb) {
        $pos = $i + 1;
        $isLast = ($i === count($crumbs) - 1);
        $html .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        if (!$isLast && isset($crumb['url'])) {
            $html .= '<a itemprop="item" href="' . htmlspecialchars($crumb['url']) . '"><span itemprop="name">' . htmlspecialchars($crumb['label']) . '</span></a>';
        } else {
            $html .= '<span itemprop="name">' . htmlspecialchars($crumb['label']) . '</span>';
        }
        $html .= '<meta itemprop="position" content="' . $pos . '">';
        $html .= '</li>';
        if (!$isLast) $html .= '<li class="separator">›</li>';
    }
    $html .= '</ol></nav>';
    return $html;
}

/**
 * Get company logo image tag
 */
function companyLogo(array $company, string $size = 'medium'): string {
    $logo = $company['logo'] ?? 'default-logo.png';
    $name = htmlspecialchars($company['name'] ?? $company['company_name'] ?? '');
    $src = '/assets/images/logos/' . htmlspecialchars($logo);
    $fallback = '/assets/images/logos/default-logo.svg';
    $classes = 'company-logo company-logo--' . $size;
    return '<img src="' . $src . '" alt="' . $name . ' Car Hire South Africa Logo" class="' . $classes . '" loading="lazy" onerror="this.onerror=null;this.src=\'' . $fallback . '\'">';
}

/**
 * Get vehicle image tag
 */
function vehicleImage(array $vehicle, string $size = 'medium'): string {
    $image = $vehicle['image'] ?? 'default-vehicle.png';
    $name = htmlspecialchars($vehicle['name'] ?? $vehicle['vehicle_model'] ?? '');
    $src = '/assets/images/vehicles/' . htmlspecialchars($image);
    $fallback = '/assets/images/vehicles/default-vehicle.svg';
    $classes = 'vehicle-image vehicle-image--' . $size;
    return '<img src="' . $src . '" alt="' . $name . ' car hire South Africa" class="' . $classes . '" loading="lazy" onerror="this.onerror=null;this.src=\'' . $fallback . '\'">';
}

/**
 * Get most affordable model image for company card
 */
function companyAffordableImage(array $company): string {
    $image = $company['most_affordable_image'] ?? 'default-vehicle.png';
    $model = htmlspecialchars($company['most_affordable_model'] ?? 'Vehicle');
    $src = '/assets/images/vehicles/' . htmlspecialchars($image);
    $fallback = '/assets/images/vehicles/default-vehicle.svg';
    return '<img src="' . $src . '" alt="' . $model . ' rental in South Africa" class="company-affordable-image" loading="lazy" onerror="this.onerror=null;this.src=\'' . $fallback . '\'">';
}

/**
 * Get risk badge HTML
 */
function riskBadge(string $level, string $label): string {
    $classes = [
        'low' => 'badge badge--success',
        'medium' => 'badge badge--warning',
        'high' => 'badge badge--danger',
        'info' => 'badge badge--info'
    ];
    $class = $classes[$level] ?? 'badge badge--info';
    return '<span class="' . $class . '">' . htmlspecialchars($label) . '</span>';
}

/**
 * Get deposit risk level
 */
function getDepositRisk(int $deposit): string {
    if ($deposit <= 2000) return 'low';
    if ($deposit <= 3500) return 'medium';
    return 'high';
}

/**
 * Get excess risk level
 */
function getExcessRisk(int $excess): string {
    if ($excess <= 10000) return 'low';
    if ($excess <= 16000) return 'medium';
    return 'high';
}

/**
 * Get refund speed rating
 */
function getRefundRating(string $refundSpeed): string {
    if (strpos($refundSpeed, '1-3') !== false || strpos($refundSpeed, '2-4') !== false || strpos($refundSpeed, '3-7') !== false) return 'good';
    if (strpos($refundSpeed, '5-10') !== false) return 'average';
    return 'slow';
}

/**
 * Sort companies by criteria
 */
function sortCompanies(array $companies, string $sortBy): array {
    require_once __DIR__ . '/scoring.php';

    usort($companies, function($a, $b) use ($sortBy) {
        switch ($sortBy) {
            case 'lowest_exposure':
                return $a['deposit_avg'] <=> $b['deposit_avg'];
            case 'lowest_price':
                return $a['economy_rate_min'] <=> $b['economy_rate_min'];
            case 'fastest_refund':
                $aRefund = (int)explode('-', $a['refund_speed'])[0];
                $bRefund = (int)explode('-', $b['refund_speed'])[0];
                return $aRefund <=> $bRefund;
            case 'most_airports':
                return $b['locations_count'] <=> $a['locations_count'];
            default:
                $aScore = calculateOverallScore($a);
                $bScore = calculateOverallScore($b);
                return $bScore <=> $aScore;
        }
    });

    return $companies;
}

/**
 * Filter companies by criteria
 */
function filterCompanies(array $companies, array $filters): array {
    return array_values(array_filter($companies, function($company) use ($filters) {
        if (!empty($filters['max_deposit'])) {
            if ($company['deposit_avg'] > (int)$filters['max_deposit']) return false;
        }
        if (!empty($filters['debit_card'])) {
            if (!$company['supports_debit_card']) return false;
        }
        if (!empty($filters['cross_border'])) {
            if (!$company['cross_border_allowed']) return false;
        }
        if (!empty($filters['unlimited_mileage'])) {
            if ($company['mileage_policy'] !== 'unlimited') return false;
        }
        return true;
    }));
}

/**
 * Get segment icon
 */
function segmentIcon(string $segment): string {
    $icons = [
        'Economy' => '🚗',
        'Compact' => '🚙',
        'SUV' => '🚐',
        'Pickup/Utility' => '🛻',
        'Luxury' => '🏎️',
        'Electric/Hybrid' => '⚡',
        'Van' => '🚌'
    ];
    return $icons[$segment] ?? '🚗';
}

/**
 * Truncate text
 */
function truncate(string $text, int $length = 120): string {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '...';
}

/**
 * Get page title
 */
function getPageTitle(string $page, string $slug = ''): string {
    $titles = [
        'home' => 'Hurllo - South Africa Car Hire Intelligence Platform',
        'directory' => 'Car Hire Company Directory South Africa | Hurllo',
        'compare' => 'Compare Car Hire Companies South Africa | Hurllo',
        'education' => 'Car Hire Guide South Africa | Hurllo',
        'rankings' => 'Best Car Hire Companies South Africa Rankings | Hurllo',
        'vehicles' => 'Vehicle Intelligence Hub South Africa | Hurllo',
        'tourist' => 'Car Hire for Tourists South Africa | Hurllo',
    ];

    if ($page === 'company' && $slug) {
        $company = getCompanyBySlug($slug);
        if ($company) {
            return $company['name'] . ' Car Hire Review & Intelligence Report | Hurllo';
        }
    }

    if ($page === 'vehicle' && $slug) {
        $vehicle = getVehicleBySlug($slug);
        if ($vehicle) {
            return $vehicle['name'] . ' Car Hire South Africa | Hurllo';
        }
    }

    if ($page === 'airport' && $slug) {
        $airport = getAirportBySlug($slug);
        if ($airport) {
            return 'Car Hire at ' . $airport['name'] . ' | Hurllo';
        }
    }

    if ($page === 'destination' && $slug) {
        $destination = getDestinationBySlug($slug);
        if ($destination) {
            return 'Car Hire for ' . $destination['name'] . ' | Hurllo South Africa';
        }
    }

    return $titles[$page] ?? 'Hurllo - Car Hire Intelligence';
}

/**
 * Get meta description
 */
function getMetaDescription(string $page, string $slug = ''): string {
    $descriptions = [
        'home' => 'South Africa\'s #1 Car Hire Intelligence Platform. Compare deposits, excess, refund speeds, and financial exposure across all 26 major rental companies.',
        'directory' => 'Browse and compare all 26 major car hire companies in South Africa. Filter by deposit, excess, debit card acceptance, and more.',
        'compare' => 'Side-by-side comparison of South African car hire companies. Compare prices, deposits, excess, and intelligence scores.',
        'education' => 'Complete guide to car hire in South Africa. Learn about deposits, excess, cross-border rules, and how to minimise financial risk.',
        'rankings' => 'Rankings of the best car hire companies in South Africa based on price, transparency, financial exposure, and customer experience.',
        'vehicles' => 'Explore all rental vehicles available in South Africa. Compare specs, prices, and availability across rental companies.',
        'tourist' => 'Planning a trip to South Africa? Compare car hire options for tourists — deposits, foreign licence policies, and refund speeds.',
        'airport' => '',
        'destination' => '',
    ];

    if ($page === 'company' && $slug) {
        $company = getCompanyBySlug($slug);
        if ($company) {
            return 'Full intelligence report for ' . $company['name'] . ' car hire in South Africa. Deposit avg: ' . formatZAR($company['deposit_avg']) . '. Excess avg: ' . formatZAR($company['excess_avg']) . '. Refund: ' . $company['refund_speed'] . '.';
        }
    }

    if ($page === 'vehicle' && $slug) {
        $vehicle = getVehicleBySlug($slug);
        if ($vehicle) {
            return $vehicle['name'] . ' is a ' . $vehicle['segment'] . ' vehicle available for hire in South Africa. Average daily rate: ' . formatZAR($vehicle['avg_daily_rate_ZAR']) . '.';
        }
    }

    if ($page === 'airport' && $slug) {
        $airport = getAirportBySlug($slug);
        if ($airport) {
            return 'Compare car hire companies at ' . $airport['name'] . ', ' . $airport['city'] . '. Deposits, excess amounts, refund speeds and more — fully independent analysis.';
        }
    }

    if ($page === 'destination' && $slug) {
        $destination = getDestinationBySlug($slug);
        if ($destination) {
            return 'Planning a trip to ' . $destination['name'] . '? Compare the best car hire options, ideal vehicle types, and financial exposure. Independent analysis for tourists.';
        }
    }

    return $descriptions[$page] ?? 'Hurllo - South Africa Car Hire Intelligence Platform';
}

/**
 * Get deposit type label
 */
function getDepositTypeLabel(array $company): string {
    $holdType = $company['deposit_type'] ?? 'credit_card_only';
    $labels = [
        'credit_card_only' => 'Credit Card Only',
        'credit_or_debit' => 'Credit or Debit Card',
        'debit_accepted' => 'Debit Card Accepted',
    ];
    return $labels[$holdType] ?? ucwords(str_replace('_', ' ', $holdType));
}

/**
 * Get mileage policy label
 */
function getMileageLabel(string $policy): string {
    $labels = [
        'unlimited' => 'Unlimited KM',
        'capped' => 'Capped KM',
        'varies' => 'Varies by Vehicle',
        'unknown' => 'Check with Company',
    ];
    return $labels[$policy] ?? ucfirst($policy);
}
