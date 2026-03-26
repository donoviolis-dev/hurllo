<?php
$slug = getSlug();
$airport = getAirportBySlug($slug);

if (!$airport) {
    header('Location: /');
    exit;
}

$companies = getCompanies();
$airportCompanies = array_filter($companies, function($c) use ($airport) {
    return in_array($c['slug'], $airport['companies_present']);
});
$airportCompanies = array_values($airportCompanies);

// Sort by overall score
usort($airportCompanies, function($a, $b) {
    $scoreA = calculateOverallScore($a);
    $scoreB = calculateOverallScore($b);
    return $scoreB <=> $scoreA;
});

// Calculate stats
$deposits = array_column($airportCompanies, 'deposit_avg');
$excesses = array_column($airportCompanies, 'excess_avg');
$avgDeposit = count($deposits) ? (int)round(array_sum($deposits) / count($deposits)) : 0;
$avgExcess = count($excesses) ? (int)round(array_sum($excesses) / count($excesses)) : 0;

// Calculate fastest refund
$refundDays = [];
foreach ($airportCompanies as $c) {
    $speed = $c['refund_speed'] ?? '5-10 days';
    $parts = explode('-', $speed);
    $refundDays[] = (int)$parts[0];
}
$fastestRefund = count($refundDays) ? min($refundDays) : 0;

// Breadcrumbs
echo breadcrumbs([
    ['label' => 'Home', 'url' => '/'],
    ['label' => 'Car Hire by Airport', 'url' => url('tourist')],
    ['label' => $airport['name']]
]);
?>

<!-- Airport Schema -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "name": "<?= htmlspecialchars($airport['name']) ?> Car Hire",
    "description": "Car hire services at <?= htmlspecialchars($airport['name']) ?>, <?= htmlspecialchars($airport['city']) ?>",
    "address": {
        "@type": "PostalAddress",
        "addressLocality": "<?= htmlspecialchars($airport['city']) ?>",
        "addressRegion": "<?= htmlspecialchars($airport['province']) ?>",
        "addressCountry": "ZA"
    },
    "areaServed": "South Africa"
}
</script>

<!-- Airport Hero -->
<section class="page-hero page-hero--airport">
    <div class="container">
        <div class="airport-hero">
            <div class="airport-hero__content">
                <h1 class="page-title">Car Hire at <?= htmlspecialchars($airport['name']) ?></h1>
                <p class="page-subtitle"><?= htmlspecialchars($airport['description']) ?></p>
                <p class="airport-tourist-notes"><?= htmlspecialchars($airport['tourist_notes']) ?></p>
                <div class="airport-badges">
                    <span class="airport-badge">✈️ <?= htmlspecialchars($airport['iata']) ?></span>
                    <span class="airport-badge">📍 <?= htmlspecialchars($airport['city']) ?></span>
                    <span class="airport-badge">🏛️ <?= htmlspecialchars($airport['province']) ?></span>
                    <?php if (!empty($airport['peak_months'])): ?>
                    <span class="airport-badge">📅 Peak: <?= htmlspecialchars($airport['peak_months'][0]) ?>, <?= htmlspecialchars($airport['peak_months'][1]) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stat Bar -->
<section class="section airport-stats">
    <div class="container">
        <div class="stats-grid stats-grid--4">
            <div class="stat-card">
                <div class="stat-card__value"><?= count($airportCompanies) ?></div>
                <div class="stat-card__label">Companies Available</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__value"><?= formatZAR($avgDeposit) ?></div>
                <div class="stat-card__label">Average Deposit</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__value"><?= formatZAR($avgExcess) ?></div>
                <div class="stat-card__label">Average Excess</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__value"><?= $fastestRefund ?> days</div>
                <div class="stat-card__label">Fastest Refund</div>
            </div>
        </div>
    </div>
</section>

<!-- Companies at Airport -->
<section class="section airport-companies">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Car Hire Companies at <?= htmlspecialchars($airport['name']) ?></h2>
            <p class="section-subtitle">Ranked by overall intelligence score</p>
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
                        <span class="meta-label">Excess</span>
                        <span class="meta-value"><?= formatZAR($company['excess_avg']) ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Refund</span>
                        <span class="meta-value"><?= htmlspecialchars($company['refund_speed']) ?></span>
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

<!-- Nearby Destinations -->
<section class="section airport-destinations">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Nearby Destinations</h2>
            <p class="section-subtitle">Popular places to visit from <?= htmlspecialchars($airport['city']) ?></p>
        </div>
        <div class="destination-pills">
            <?php foreach ($airport['nearby_destinations'] as $dest): ?>
            <a href="<?= url('destination', makeSlug($dest)) ?>" class="destination-pill">
                📍 <?= htmlspecialchars($dest) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Tourist Tips -->
<?php require_once CARVIO_ROOT . '/includes/tourist_tips.php'; ?>

<!-- SEO Links -->
<section class="section seo-links">
    <div class="container">
        <div class="seo-links-grid">
            <a href="<?= url('education') ?>" class="seo-link-card">
                <span class="seo-link-icon">📚</span>
                <span class="seo-link-text">Read the Car Hire Guide</span>
            </a>
            <a href="<?= url('rankings') ?>" class="seo-link-card">
                <span class="seo-link-icon">🏆</span>
                <span class="seo-link-text">View Full Rankings</span>
            </a>
            <a href="<?= url('directory') ?>" class="seo-link-card">
                <span class="seo-link-icon">🏢</span>
                <span class="seo-link-text">Browse All Companies</span>
            </a>
        </div>
    </div>
</section>