<?php
$slug = getSlug();
$destination = getDestinationBySlug($slug);

if (!$destination) {
    header('Location: /');
    exit;
}

// Get nearest airport
$airports = getAirports();
$nearestAirport = null;
foreach ($airports as $a) {
    if ($a['slug'] === $destination['nearest_airport_slug']) {
        $nearestAirport = $a;
        break;
    }
}

// Get companies at nearest airport
$companies = getCompanies();
$airportCompanies = [];
if ($nearestAirport) {
    $airportCompanies = array_filter($companies, function($c) use ($nearestAirport) {
        return in_array($c['slug'], $nearestAirport['companies_present']);
    });
    $airportCompanies = array_values($airportCompanies);
    // Sort by overall score
    usort($airportCompanies, function($a, $b) {
        $scoreA = calculateOverallScore($a);
        $scoreB = calculateOverallScore($b);
        return $scoreB <=> $scoreB;
    });
    $airportCompanies = array_slice($airportCompanies, 0, 5);
}

// Get vehicles for best segments
$allVehicles = getVehicles();
$recommendedVehicles = array_filter($allVehicles, function($v) use ($destination) {
    $segment = $v['segment'] ?? '';
    return in_array($segment, $destination['best_vehicle_segments']);
});
$recommendedVehicles = array_values(array_slice($recommendedVehicles, 0, 6));

// Driving difficulty class
$difficultyClass = getDrivingDifficultyClass($destination['driving_difficulty']);

// Breadcrumbs
echo breadcrumbs([
    ['label' => 'Home', 'url' => '/'],
    ['label' => 'Car Hire by Destination', 'url' => url('tourist')],
    ['label' => $destination['name']]
]);
?>

<!-- Destination Schema -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "TouristDestination",
    "name": "<?= htmlspecialchars($destination['name']) ?>",
    "description": "<?= htmlspecialchars($destination['description']) ?>",
    "address": {
        "@type": "PostalAddress",
        "addressRegion": "<?= htmlspecialchars($destination['region']) ?>",
        "addressCountry": "ZA"
    }
}
</script>

<!-- Destination Hero -->
<section class="page-hero page-hero--destination">
    <div class="container">
        <div class="destination-hero">
            <div class="destination-hero__content">
                <h1 class="page-title">Car Hire for <?= htmlspecialchars($destination['name']) ?></h1>
                <p class="page-subtitle"><?= htmlspecialchars($destination['tagline']) ?></p>
                <p class="destination-description"><?= htmlspecialchars($destination['description']) ?></p>
                <div class="destination-badges">
                    <span class="destination-badge">🏛️ <?= htmlspecialchars($destination['region']) ?></span>
                    <span class="destination-badge">📅 <?= htmlspecialchars($destination['peak_season']) ?></span>
                    <span class="destination-badge">⏱️ Avg Trip: <?= htmlspecialchars($destination['avg_trip_days']) ?> days</span>
                    <span class="destination-badge">🌍 <?= $destination['cross_border_needed'] ? 'Cross-Border Required' : 'SA Only' ?></span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Facts -->
<section class="section destination-facts">
    <div class="container">
        <div class="facts-grid facts-grid--4">
            <div class="fact-card">
                <div class="fact-card__label">Best Vehicle Types</div>
                <div class="fact-card__value">
                    <?php foreach ($destination['best_vehicle_segments'] as $seg): ?>
                    <span class="segment-pill"><?= segmentIcon($seg) ?> <?= htmlspecialchars($seg) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="fact-card">
                <div class="fact-card__label">Road Type</div>
                <div class="fact-card__value"><?= htmlspecialchars(strlen($destination['road_type']) > 40 ? substr($destination['road_type'], 0, 40) . '...' : $destination['road_type']) ?></div>
            </div>
            <div class="fact-card">
                <div class="fact-card__label">Driving Difficulty</div>
                <div class="fact-card__value">
                    <span class="difficulty-badge <?= $difficultyClass ?>"><?= htmlspecialchars($destination['driving_difficulty']) ?></span>
                </div>
            </div>
            <div class="fact-card">
                <div class="fact-card__label">Nearest Airport</div>
                <div class="fact-card__value">
                    <?php if ($nearestAirport): ?>
                    <a href="<?= url('airport', $nearestAirport['slug']) ?>" class="airport-link">
                        ✈️ <?= htmlspecialchars($nearestAirport['name']) ?>
                    </a>
                    <?php else: ?>
                    N/A
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Recommended Vehicles -->
<?php if (!empty($recommendedVehicles)): ?>
<section class="section destination-vehicles">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Best Vehicle Types for <?= htmlspecialchars($destination['name']) ?></h2>
            <p class="section-subtitle">Recommended vehicles for this destination</p>
        </div>
        <div class="vehicles-grid">
            <?php foreach ($recommendedVehicles as $vehicle): ?>
            <div class="vehicle-card">
                <div class="vehicle-card__image">
                    <?= vehicleImage($vehicle) ?>
                </div>
                <div class="vehicle-card__body">
                    <h3 class="vehicle-card__name">
                        <a href="<?= url('vehicle', $vehicle['slug']) ?>"><?= htmlspecialchars($vehicle['name']) ?></a>
                    </h3>
                    <div class="vehicle-card__specs">
                        <span class="spec-chip">👥 <?= $vehicle['seating_capacity'] ?> seats</span>
                        <span class="spec-chip">🧳 <?= htmlspecialchars($vehicle['boot_space_display'] ?? $vehicle['boot_space_litres'] . 'L') ?></span>
                        <span class="spec-chip">⚙️ <?= htmlspecialchars($vehicle['transmission']) ?></span>
                    </div>
                    <div class="vehicle-card__price">
                        <span class="price-label">Avg Rate</span>
                        <span class="price-value"><?= formatZAR($vehicle['avg_daily_rate_ZAR']) ?>/day</span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Highlights -->
<section class="section destination-highlights">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">What to See & Do</h2>
        </div>
        <div class="highlights-pills">
            <?php foreach ($destination['highlights'] as $highlight): ?>
            <span class="highlight-pill">⭐ <?= htmlspecialchars($highlight) ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Top Companies -->
<?php if (!empty($airportCompanies)): ?>
<section class="section destination-companies">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Top Car Hire Companies Near <?= htmlspecialchars($destination['name']) ?></h2>
            <p class="section-subtitle">Based at <?= htmlspecialchars($nearestAirport['name'] ?? 'nearby airport') ?></p>
        </div>
        <div class="company-cards-grid">
            <?php foreach ($airportCompanies as $company):
                $scores = getAllScores($company);
            ?>
            <div class="company-card">
                <div class="company-card__header">
                    <div class="company-card__logo">
                        <?= companyLogo($company) ?>
                    </div>
                    <div class="company-card__info">
                        <h3 class="company-card__name">
                            <a href="<?= url('company', $company['slug']) ?>"><?= htmlspecialchars($company['name']) ?></a>
                        </h3>
                        <div class="company-card__score-main">
                            <?= scoreCircle($scores['overall'], 'Overall') ?>
                        </div>
                    </div>
                </div>
                <div class="company-card__meta">
                    <div class="meta-item">
                        <span class="meta-label">Deposit</span>
                        <span class="meta-value"><?= formatZAR($company['deposit_avg']) ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">From</span>
                        <span class="meta-value"><?= formatZAR($company['economy_rate_min']) ?>/day</span>
                    </div>
                </div>
                <div class="company-card__actions">
                    <a href="<?= url('company', $company['slug']) ?>" class="btn btn--primary btn--sm">View Report</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Tourist Tips -->
<?php require_once CARVIO_ROOT . '/includes/tourist_tips.php'; ?>

<!-- SEO Links -->
<section class="section seo-links">
    <div class="container">
        <div class="seo-links-grid">
            <a href="<?= url('tourist') ?>" class="seo-link-card">
                <span class="seo-link-icon">🌍</span>
                <span class="seo-link-text">Tourist Hub</span>
            </a>
            <a href="<?= url('rankings') ?>" class="seo-link-card">
                <span class="seo-link-icon">🏆</span>
                <span class="seo-link-text">View Rankings</span>
            </a>
            <a href="<?= url('compare') ?>" class="seo-link-card">
                <span class="seo-link-icon">⚖️</span>
                <span class="seo-link-text">Compare Companies</span>
            </a>
        </div>
    </div>
</section>