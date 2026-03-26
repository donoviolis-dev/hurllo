<?php
$allVehicles = getVehicles();
$market = getMarketData();

// Filters
$filterSegment = $_GET['segment'] ?? '';
$filterAutomatic = !empty($_GET['automatic']);
$filter7Seater = !empty($_GET['seven_seater']);
$filterDiesel = !empty($_GET['diesel']);
$filterLargeBoot = !empty($_GET['large_boot']);
$filterElectric = !empty($_GET['electric']);

// Apply filters
$vehicles = array_filter($allVehicles, function($v) use ($filterSegment, $filterAutomatic, $filter7Seater, $filterDiesel, $filterLargeBoot, $filterElectric) {
    if ($filterSegment && strtolower($v['segment']) !== strtolower($filterSegment)) return false;
    if ($filterAutomatic && stripos($v['transmission'], 'automatic') === false) return false;
    if ($filter7Seater && $v['seating_capacity'] < 7) return false;
    if ($filterDiesel && stripos($v['fuel_type'], 'diesel') === false) return false;
    if ($filterLargeBoot && $v['boot_space_litres'] < 400) return false;
    if ($filterElectric && stripos($v['fuel_type'], 'electric') === false && stripos($v['fuel_type'], 'hybrid') === false) return false;
    return true;
});

$vehicles = array_values($vehicles);
$segments = array_unique(array_column($allVehicles, 'segment'));
$popularSegments = $market['popular_vehicle_segments_percent'] ?? [];

// Breadcrumbs
echo breadcrumbs([
    ['label' => 'Home', 'url' => '/'],
    ['label' => 'Vehicle Intelligence Hub']
]);
?>

<section class="page-hero page-hero--vehicles">
    <div class="container">
        <h1 class="page-title">Vehicle Intelligence Hub</h1>
        <p class="page-subtitle">Explore all <?= count($allVehicles) ?> rental vehicles available in South Africa. Compare specs, demand, and availability across all major companies.</p>
    </div>
</section>

<section class="section vehicles-section">
    <div class="container">
        <!-- Filter Bar -->
        <div class="vehicle-filter-bar">
            <form method="GET" action="/" class="vehicle-filters">
                <input type="hidden" name="page" value="vehicles">

                <div class="filter-chips">
                    <span class="filter-chips-label">Segment:</span>
                    <a href="<?= url('vehicles') ?>" class="filter-chip <?= !$filterSegment ? 'filter-chip--active' : '' ?>">All</a>
                    <?php foreach ($segments as $seg): ?>
                    <a href="<?= url('vehicles', '', ['segment' => urlencode($seg)]) ?>" class="filter-chip <?= strtolower($filterSegment) === strtolower($seg) ? 'filter-chip--active' : '' ?>">
                        <?= segmentIcon($seg) ?> <?= htmlspecialchars($seg) ?>
                    </a>
                    <?php endforeach; ?>
                </div>

                <div class="filter-toggles">
                    <label class="filter-toggle">
                        <input type="checkbox" name="automatic" value="1" <?= $filterAutomatic ? 'checked' : '' ?> onchange="this.form.submit()">
                        <span>Automatic</span>
                    </label>
                    <label class="filter-toggle">
                        <input type="checkbox" name="seven_seater" value="1" <?= $filter7Seater ? 'checked' : '' ?> onchange="this.form.submit()">
                        <span>7-Seater</span>
                    </label>
                    <label class="filter-toggle">
                        <input type="checkbox" name="diesel" value="1" <?= $filterDiesel ? 'checked' : '' ?> onchange="this.form.submit()">
                        <span>Diesel</span>
                    </label>
                    <label class="filter-toggle">
                        <input type="checkbox" name="large_boot" value="1" <?= $filterLargeBoot ? 'checked' : '' ?> onchange="this.form.submit()">
                        <span>Boot 400L+</span>
                    </label>
                    <label class="filter-toggle">
                        <input type="checkbox" name="electric" value="1" <?= $filterElectric ? 'checked' : '' ?> onchange="this.form.submit()">
                        <span>⚡ Electric/Hybrid</span>
                    </label>
                </div>
            </form>
        </div>

        <div class="vehicles-count">
            Showing <strong><?= count($vehicles) ?></strong> of <?= count($allVehicles) ?> vehicles
            <?php if ($filterSegment || $filterAutomatic || $filter7Seater || $filterDiesel || $filterLargeBoot || $filterElectric): ?>
            — <a href="<?= url('vehicles') ?>">Clear filters</a>
            <?php endif; ?>
        </div>

        <!-- Vehicle Grid -->
        <div class="vehicles-grid">
            <?php foreach ($vehicles as $vehicle): ?>
            <div class="vehicle-card">
                <div class="vehicle-card__image">
                    <?= vehicleImage($vehicle) ?>
                    <div class="vehicle-card__segment-badge">
                        <?= segmentIcon($vehicle['segment']) ?> <?= htmlspecialchars($vehicle['segment']) ?>
                    </div>
                </div>
                <div class="vehicle-card__body">
                    <h3 class="vehicle-card__name">
                        <a href="<?= url('vehicle', $vehicle['slug']) ?>"><?= htmlspecialchars($vehicle['name']) ?></a>
                    </h3>

                    <div class="vehicle-card__specs">
                        <span class="spec-chip">👥 <?= $vehicle['seating_capacity'] ?> seats</span>
                        <span class="spec-chip">🧳 <?= htmlspecialchars($vehicle['boot_space_display'] ?? $vehicle['boot_space_litres'] . 'L') ?></span>
                        <span class="spec-chip">⚙️ <?= htmlspecialchars($vehicle['transmission']) ?></span>
                        <span class="spec-chip">⛽ <?= htmlspecialchars($vehicle['fuel_type']) ?></span>
                    </div>

                    <div class="vehicle-card__demand">
                        <span class="demand-label">Demand</span>
                        <div class="demand-bar-mini">
                            <div class="demand-bar-mini__fill" style="width: <?= $vehicle['demand_percent'] ?>%"></div>
                        </div>
                        <span class="demand-value"><?= $vehicle['demand_percent'] ?>%</span>
                    </div>

                    <div class="vehicle-card__companies">
                        <span class="companies-label">Available at:</span>
                        <span class="companies-count"><?= count($vehicle['companies'] ?? []) ?> companies</span>
                    </div>

                    <div class="vehicle-card__price">
                        <span class="price-label">Avg Rate</span>
                        <span class="price-value"><?= formatZAR($vehicle['avg_daily_rate_ZAR']) ?>/day</span>
                    </div>

                    <a href="<?= url('vehicle', $vehicle['slug']) ?>" class="btn btn--primary btn--sm btn--full">View Intelligence</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($vehicles)): ?>
        <div class="empty-state">
            <p>No vehicles match your filters. <a href="<?= url('vehicles') ?>">Clear all filters</a></p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Segment Overview -->
<section class="section segment-overview">
    <div class="container">
        <h2 class="section-title">Market by Segment</h2>
        <div class="segment-grid">
            <?php foreach ($segments as $segName):
                $segVehicles = array_filter($allVehicles, fn($v) => $v['segment'] === $segName);
                $segCount = count($segVehicles);
                $segPopKey = '';
                if (stripos($segName, 'economy') !== false) $segPopKey = 'economy_cars';
                elseif (stripos($segName, 'suv') !== false) $segPopKey = 'suvs';
                elseif (stripos($segName, 'luxury') !== false) $segPopKey = 'luxury';
                elseif (stripos($segName, 'electric') !== false || stripos($segName, 'hybrid') !== false) $segPopKey = 'electric_hybrids';
                $segPopPercent = $segPopKey ? ($popularSegments[$segPopKey] ?? 0) : 0;
            ?>
            <div class="segment-card">
                <div class="segment-card__icon"><?= segmentIcon($segName) ?></div>
                <h3 class="segment-card__name"><?= htmlspecialchars($segName) ?></h3>
                <div class="segment-card__price"><?= $segCount ?> models</div>
                <?php if ($segPopPercent): ?>
                <div class="segment-card__demand">
                    <div class="demand-bar-mini">
                        <div class="demand-bar-mini__fill" style="width: <?= $segPopPercent ?>%"></div>
                    </div>
                    <span><?= $segPopPercent ?>% of market</span>
                </div>
                <?php endif; ?>
                <a href="<?= url('vehicles', '', ['segment' => urlencode($segName)]) ?>" class="btn btn--outline btn--sm" style="margin-top:0.75rem;">Browse <?= htmlspecialchars($segName) ?></a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
