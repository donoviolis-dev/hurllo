<?php
$slug = getSlug();
$vehicle = getVehicleBySlug($slug);

if (!$vehicle) {
    http_response_code(404);
    echo '<div class="container" style="padding: 4rem 0; text-align: center;">';
    echo '<h1 style="color: var(--color-primary);">Vehicle Not Found</h1>';
    echo '<p>The vehicle you\'re looking for doesn\'t exist.</p>';
    echo '<a href="' . url('vehicles') . '" class="btn btn--primary">Browse Vehicles</a>';
    echo '</div>';
    return;
}

$companiesOffering = getCompaniesForVehicleByList($vehicle);
$market = getMarketData();

// Segment rate from market data
$segmentRates = $market['average_daily_rates_ZAR'] ?? [];
$segmentKey = strtolower(str_replace(['/', ' '], ['', '_'], $vehicle['segment']));
$segmentRate = $segmentRates[$segmentKey] ?? $segmentRates['economy'] ?? '380-550';

// Breadcrumbs
echo breadcrumbs([
    ['label' => 'Home', 'url' => '/'],
    ['label' => 'Vehicle Hub', 'url' => url('vehicles')],
    ['label' => $vehicle['name']]
]);
?>

<!-- Vehicle Schema -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "<?= htmlspecialchars($vehicle['name']) ?> Car Hire South Africa",
    "description": "<?= htmlspecialchars($vehicle['description'] ?? '') ?>",
    "category": "<?= htmlspecialchars($vehicle['segment']) ?> Car Hire"
}
</script>

<!-- Vehicle Hero -->
<section class="vehicle-hero">
    <div class="container">
        <div class="vehicle-hero__inner">
            <div class="vehicle-hero__image">
                <?= vehicleImage($vehicle, 'hero') ?>
            </div>
            <div class="vehicle-hero__info">
                <div class="vehicle-hero__segment">
                    <?= segmentIcon($vehicle['segment']) ?> <?= htmlspecialchars($vehicle['segment']) ?> Class
                </div>
                <h1 class="vehicle-hero__name"><?= htmlspecialchars($vehicle['name']) ?> Car Hire</h1>
                <p class="vehicle-hero__desc"><?= htmlspecialchars($vehicle['description'] ?? '') ?></p>

                <div class="vehicle-hero__stats">
                    <div class="vehicle-stat">
                        <span class="vehicle-stat__value"><?= $vehicle['doors'] ?></span>
                        <span class="vehicle-stat__label">Doors</span>
                    </div>
                    <div class="vehicle-stat">
                        <span class="vehicle-stat__value"><?= $vehicle['seating_capacity'] ?></span>
                        <span class="vehicle-stat__label">Seats</span>
                    </div>
                    <div class="vehicle-stat">
                        <span class="vehicle-stat__value"><?= $vehicle['boot_space_display'] ?? $vehicle['boot_space_litres'] . 'L' ?></span>
                        <span class="vehicle-stat__label">Boot</span>
                    </div>
                    <div class="vehicle-stat">
                        <span class="vehicle-stat__value"><?= $vehicle['demand_percent'] ?>%</span>
                        <span class="vehicle-stat__label">Demand</span>
                    </div>
                </div>

                <div class="vehicle-hero__badges">
                    <span class="badge badge--info"><?= htmlspecialchars($vehicle['transmission']) ?></span>
                    <span class="badge badge--info"><?= htmlspecialchars($vehicle['fuel_type']) ?></span>
                    <?php if ($vehicle['seating_capacity'] >= 7): ?>
                    <span class="badge badge--success">👨‍👩‍👧‍👦 7-Seater</span>
                    <?php endif; ?>
                    <?php if (stripos($vehicle['fuel_type'], 'electric') !== false || stripos($vehicle['fuel_type'], 'hybrid') !== false): ?>
                    <span class="badge badge--success">⚡ Eco-Friendly</span>
                    <?php endif; ?>
                </div>

                <div class="vehicle-hero__price">
                    <span class="price-from-label">Average Daily Rate</span>
                    <span class="price-from-value"><?= formatZAR($vehicle['avg_daily_rate_ZAR']) ?>/day</span>
                </div>

                <div class="vehicle-hero__companies">
                    <span class="companies-label">Available at <?= count($vehicle['companies'] ?? []) ?> companies</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Full Specs -->
<section class="section specs-section">
    <div class="container">
        <div class="specs-grid">
            <div class="specs-card">
                <h2 class="specs-card__title">Full Specifications</h2>
                <div class="specs-table">
                    <div class="spec-row">
                        <span class="spec-label">Segment</span>
                        <span class="spec-value"><?= htmlspecialchars($vehicle['segment']) ?></span>
                    </div>
                    <div class="spec-row">
                        <span class="spec-label">Doors</span>
                        <span class="spec-value"><?= $vehicle['doors'] ?></span>
                    </div>
                    <div class="spec-row">
                        <span class="spec-label">Seating Capacity</span>
                        <span class="spec-value"><?= $vehicle['seating_capacity'] ?> passengers</span>
                    </div>
                    <div class="spec-row">
                        <span class="spec-label">Boot Space</span>
                        <span class="spec-value"><?= htmlspecialchars($vehicle['boot_space_display'] ?? $vehicle['boot_space_litres'] . 'L') ?></span>
                    </div>
                    <div class="spec-row">
                        <span class="spec-label">Fuel Type</span>
                        <span class="spec-value"><?= htmlspecialchars($vehicle['fuel_type']) ?></span>
                    </div>
                    <div class="spec-row">
                        <span class="spec-label">Transmission</span>
                        <span class="spec-value"><?= htmlspecialchars($vehicle['transmission']) ?></span>
                    </div>
                    <div class="spec-row">
                        <span class="spec-label">Companies Offering</span>
                        <span class="spec-value"><?= count($vehicle['companies'] ?? []) ?> companies</span>
                    </div>
                </div>

                <!-- Companies offering this vehicle -->
                <?php if (!empty($vehicle['companies'])): ?>
                <div class="airports-list" style="margin-top:1.5rem;">
                    <h4>Available at These Companies</h4>
                    <div class="airport-tags">
                        <?php foreach ($vehicle['companies'] as $companyName): ?>
                        <?php
                        $co = null;
                        foreach (getCompanies() as $c) {
                            if ($c['name'] === $companyName) { $co = $c; break; }
                        }
                        ?>
                        <?php if ($co): ?>
                        <a href="<?= url('company', $co['slug']) ?>" class="airport-tag" style="text-decoration:none;">
                            🏢 <?= htmlspecialchars($companyName) ?>
                        </a>
                        <?php else: ?>
                        <span class="airport-tag">🏢 <?= htmlspecialchars($companyName) ?></span>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Market Insights -->
            <div class="specs-card">
                <h2 class="specs-card__title">Market Insights</h2>

                <div class="market-insight">
                    <div class="insight-label">Demand Index</div>
                    <div class="demand-meter">
                        <div class="demand-meter__fill" style="width: <?= $vehicle['demand_percent'] ?>%"></div>
                    </div>
                    <div class="insight-value"><?= $vehicle['demand_percent'] ?>% demand</div>
                </div>

                <div class="market-insight">
                    <div class="insight-label">Popularity Index</div>
                    <div class="popularity-stars">
                        <?php for ($i = 1; $i <= 10; $i++): ?>
                        <span class="star <?= $i <= $vehicle['popularity_index'] ? 'star--active' : '' ?>">★</span>
                        <?php endfor; ?>
                    </div>
                    <div class="insight-value"><?= $vehicle['popularity_index'] ?>/10</div>
                </div>

                <div class="market-insight">
                    <div class="insight-label">Average Daily Rate</div>
                    <div class="insight-value insight-value--large"><?= formatZAR($vehicle['avg_daily_rate_ZAR']) ?>/day</div>
                </div>

                <div class="market-insight">
                    <div class="insight-label">Segment Market Rate</div>
                    <div class="insight-value">R <?= htmlspecialchars($segmentRate) ?>/day</div>
                </div>

                <div class="market-insight">
                    <div class="insight-label">Availability</div>
                    <div class="insight-value"><?= count($vehicle['companies'] ?? []) ?> rental companies</div>
                </div>

                <!-- Segment popularity from market data -->
                <?php
                $popularSegments = $market['popular_vehicle_segments_percent'] ?? [];
                $segmentPopKey = '';
                if (stripos($vehicle['segment'], 'economy') !== false) $segmentPopKey = 'economy_cars';
                elseif (stripos($vehicle['segment'], 'suv') !== false) $segmentPopKey = 'suvs';
                elseif (stripos($vehicle['segment'], 'luxury') !== false) $segmentPopKey = 'luxury';
                elseif (stripos($vehicle['segment'], 'electric') !== false || stripos($vehicle['segment'], 'hybrid') !== false) $segmentPopKey = 'electric_hybrids';
                
                if ($segmentPopKey && isset($popularSegments[$segmentPopKey])):
                ?>
                <div class="market-insight">
                    <div class="insight-label">Segment Market Share</div>
                    <div class="demand-meter">
                        <div class="demand-meter__fill" style="width: <?= $popularSegments[$segmentPopKey] ?>%"></div>
                    </div>
                    <div class="insight-value"><?= $popularSegments[$segmentPopKey] ?>% of all rentals</div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Companies Offering This Vehicle -->
<?php if (!empty($companiesOffering)): ?>
<section class="section companies-offering">
    <div class="container">
        <h2 class="section-title">Companies Offering <?= htmlspecialchars($vehicle['name']) ?></h2>
        <div class="offering-grid">
            <?php foreach ($companiesOffering as $co):
                $coScores = getAllScores($co);
            ?>
            <div class="offering-card">
                <div class="offering-card__logo">
                    <?= companyLogo($co, 'small') ?>
                </div>
                <div class="offering-card__info">
                    <a href="<?= url('company', $co['slug']) ?>" class="offering-card__name"><?= htmlspecialchars($co['name']) ?></a>
                    <div class="offering-card__price">From <?= formatZAR($co['economy_rate_min']) ?>/day</div>
                    <div class="offering-card__score">Score: <?= number_format($coScores['overall'], 1) ?>/10</div>
                </div>
                <a href="<?= url('company', $co['slug']) ?>" class="btn btn--outline btn--sm">View Report</a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
