<?php
$companies = getCompanies();
$vehicles = getVehicles();
$market = getMarketData();
$rankedCompanies = getRankedCompanies();
$topCompanies = array_slice($rankedCompanies, 0, 3);

// Market overview from new JSON structure
$avgDailyRates = $market['average_daily_rates_ZAR'] ?? [];
$economyRange = $avgDailyRates['economy'] ?? '380-550';
$economyParts = explode('-', $economyRange);
$economyAvg = (int)(((int)$economyParts[0] + (int)($economyParts[1] ?? $economyParts[0])) / 2);

$bookingChannels = $market['booking_channels'] ?? [];
$popularSegments = $market['popular_vehicle_segments_percent'] ?? [];
$seasonalDemand = $market['seasonal_demand_peaks'] ?? [];
$customerSatisfaction = $market['customer_satisfaction_scores'] ?? [];
$creativeInsights = $market['creative_insights'] ?? [];
$crossBorderData = $market['cross_border_rental'] ?? [];

// Calculate market averages from companies
$allDeposits = array_column($companies, 'deposit_avg');
$allExcess = array_column($companies, 'excess_avg');
$avgDeposit = count($allDeposits) ? (int)round(array_sum($allDeposits) / count($allDeposits)) : 3000;
$avgExcess = count($allExcess) ? (int)round(array_sum($allExcess) / count($allExcess)) : 15000;

$currentMonth = date('F');
$seasonalNote = '';
foreach ($seasonalDemand as $period => $note) {
    if (stripos($period, strtolower(substr($currentMonth, 0, 3))) !== false) {
        $seasonalNote = $note;
        break;
    }
}
if (!$seasonalNote) {
    $seasonalNote = 'Normal season — good availability';
}
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">
                <span class="badge badge--info">🇿🇦 South Africa's #1 Car Hire Intelligence Platform</span>
            </div>
            <h1 class="hero-title">
                Stop Guessing.<br>
                <span class="text-gradient">Start Knowing.</span>
            </h1>
            <p class="hero-subtitle">
                We analyse deposits, excess, refund speeds, financial exposure, and fleet quality across <?= count($companies) ?> major South African car hire companies — so you can make an informed decision.
            </p>
            <div class="hero-actions">
                <a href="<?= url('directory') ?>" class="btn btn--primary btn--lg">
                    <span>Explore Directory</span>
                    <span class="btn-arrow">→</span>
                </a>
                <a href="<?= url('compare') ?>" class="btn btn--outline btn--lg">Compare Companies</a>
            </div>
            <div class="hero-stats">
                <div class="hero-stat">
                    <span class="hero-stat__value"><?= count($companies) ?></span>
                    <span class="hero-stat__label">Companies Analysed</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat__value"><?= count($vehicles) ?></span>
                    <span class="hero-stat__label">Vehicles Tracked</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat__value">9</span>
                    <span class="hero-stat__label">Intelligence Metrics</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat__value">100%</span>
                    <span class="hero-stat__label">Independent</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Seasonal Demand Banner -->
<section class="seasonal-banner">
    <div class="container">
        <div class="seasonal-inner">
            <div class="seasonal-icon">📅</div>
            <div class="seasonal-content">
                <strong><?= htmlspecialchars($currentMonth) ?> Market:</strong>
                <span class="seasonal-note"><?= htmlspecialchars($seasonalNote) ?></span>
            </div>
            <div class="seasonal-demand">
                <div class="demand-bar">
                    <div class="demand-bar__fill" style="width: 70%"></div>
                </div>
                <span class="demand-value">Active Season</span>
            </div>
        </div>
    </div>
</section>

<!-- Live Market Snapshot -->
<section class="section market-snapshot">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Live Market Snapshot</h2>
            <p class="section-subtitle">Real-time intelligence on South Africa's car hire market</p>
        </div>
        <div class="market-grid">
            <div class="market-card">
                <div class="market-card__icon">💰</div>
                <div class="market-card__value"><?= formatZAR($avgDeposit) ?></div>
                <div class="market-card__label">Average Deposit</div>
                <div class="market-card__note">Across <?= count($companies) ?> companies</div>
            </div>
            <div class="market-card">
                <div class="market-card__icon">⚠️</div>
                <div class="market-card__value"><?= formatZAR($avgExcess) ?></div>
                <div class="market-card__label">Average Excess</div>
                <div class="market-card__note">Your financial exposure</div>
            </div>
            <div class="market-card">
                <div class="market-card__icon">🚗</div>
                <div class="market-card__value">R <?= htmlspecialchars($economyRange) ?></div>
                <div class="market-card__label">Economy Daily Rate</div>
                <div class="market-card__note">Market range</div>
            </div>
            <div class="market-card">
                <div class="market-card__icon">📱</div>
                <div class="market-card__value"><?= $bookingChannels['online_percent'] ?? 62 ?>%</div>
                <div class="market-card__label">Book Online</div>
                <div class="market-card__note">Of all bookings</div>
            </div>
            <div class="market-card">
                <div class="market-card__icon">🌍</div>
                <div class="market-card__value"><?= count($crossBorderData['allowed_countries'] ?? []) ?> countries</div>
                <div class="market-card__label">Cross-Border Allowed</div>
                <div class="market-card__note"><?= implode(', ', array_slice($crossBorderData['allowed_countries'] ?? [], 0, 2)) ?></div>
            </div>
            <div class="market-card">
                <div class="market-card__icon">⭐</div>
                <div class="market-card__value"><?= $customerSatisfaction['average_google_reviews'] ?? 4.2 ?>/5</div>
                <div class="market-card__label">Avg Google Rating</div>
                <div class="market-card__note">Industry average</div>
            </div>
        </div>
    </div>
</section>

<!-- Rental Risk Simulator -->
<section class="section risk-simulator">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Rental Risk Simulator</h2>
            <p class="section-subtitle">Calculate your total financial exposure before you rent</p>
        </div>
        <div class="simulator-card">
            <div class="simulator-inputs">
                <div class="simulator-group">
                    <label class="simulator-label">Rental Duration (days)</label>
                    <input type="range" id="sim-days" min="1" max="30" value="7" class="simulator-slider">
                    <div class="simulator-value"><span id="sim-days-val">7</span> days</div>
                </div>
                <div class="simulator-group">
                    <label class="simulator-label">Daily Rate (ZAR)</label>
                    <input type="range" id="sim-rate" min="200" max="2000" value="500" step="50" class="simulator-slider">
                    <div class="simulator-value">R <span id="sim-rate-val">500</span>/day</div>
                </div>
                <div class="simulator-group">
                    <label class="simulator-label">Deposit Amount (ZAR)</label>
                    <input type="range" id="sim-deposit" min="500" max="6000" value="3000" step="500" class="simulator-slider">
                    <div class="simulator-value">R <span id="sim-deposit-val">3 000</span></div>
                </div>
                <div class="simulator-group">
                    <label class="simulator-label">Excess Amount (ZAR)</label>
                    <input type="range" id="sim-excess" min="5000" max="30000" value="15000" step="1000" class="simulator-slider">
                    <div class="simulator-value">R <span id="sim-excess-val">15 000</span></div>
                </div>
            </div>
            <div class="simulator-results">
                <div class="sim-result-card">
                    <div class="sim-result-label">Rental Cost</div>
                    <div class="sim-result-value" id="sim-rental-cost">R 3 500</div>
                </div>
                <div class="sim-result-card sim-result-card--warning">
                    <div class="sim-result-label">Deposit Held</div>
                    <div class="sim-result-value" id="sim-deposit-held">R 3 000</div>
                </div>
                <div class="sim-result-card sim-result-card--danger">
                    <div class="sim-result-label">Max Exposure</div>
                    <div class="sim-result-value" id="sim-max-exposure">R 18 000</div>
                    <div class="sim-result-note">Deposit + Excess</div>
                </div>
                <div class="sim-result-card sim-result-card--total">
                    <div class="sim-result-label">Total Upfront</div>
                    <div class="sim-result-value" id="sim-total">R 6 500</div>
                    <div class="sim-result-note">Rental + Deposit</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Top Ranked Companies -->
<section class="section top-companies">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Top Ranked Companies</h2>
            <p class="section-subtitle">Based on our 9-metric intelligence scoring system</p>
            <a href="<?= url('rankings') ?>" class="section-link">View Full Rankings →</a>
        </div>
        <div class="company-cards-grid">
            <?php foreach ($topCompanies as $rank => $company):
                $scores = getAllScores($company);
            ?>
            <div class="company-card company-card--featured" data-rank="<?= $rank + 1 ?>">
                <div class="company-card__rank">#<?= $rank + 1 ?></div>
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

                <div class="company-card__vehicle-preview">
                    <?= companyAffordableImage($company) ?>
                    <div class="vehicle-preview-label">
                        From <strong><?= htmlspecialchars($company['most_affordable_model']) ?></strong>
                        <span class="price-from"><?= formatZAR($company['economy_rate_min']) ?>/day</span>
                    </div>
                </div>

                <div class="company-card__scores">
                    <?= scoreBar($scores['financial_exposure'], 'Financial Exposure') ?>
                    <?= scoreBar($scores['price_competitiveness'], 'Price') ?>
                    <?= scoreBar($scores['deposit_flexibility'], 'Deposit Flexibility') ?>
                </div>

                <div class="company-card__meta">
                    <div class="meta-item">
                        <span class="meta-label">Deposit</span>
                        <span class="meta-value"><?= formatZAR($company['deposit_avg']) ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Refund</span>
                        <span class="meta-value"><?= htmlspecialchars($company['refund_speed']) ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Cross-Border</span>
                        <span class="meta-value"><?= $company['cross_border_allowed'] ? '✅ Yes' : '❌ No' ?></span>
                    </div>
                </div>

                <div class="company-card__actions">
                    <a href="<?= url('company', $company['slug']) ?>" class="btn btn--primary btn--sm">View Report</a>
                    <?php if (!empty($company['website_url'])): ?>
                    <a href="<?= htmlspecialchars($company['website_url']) ?>" class="btn btn--outline btn--sm" target="_blank" rel="noopener">Visit Site</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="section-cta">
            <a href="<?= url('directory') ?>" class="btn btn--primary btn--lg">View All <?= count($companies) ?> Companies</a>
        </div>
    </div>
</section>

<!-- Market Intelligence Block -->
<section class="section market-intelligence">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Market Intelligence</h2>
            <p class="section-subtitle">Key insights from South Africa's car hire market</p>
        </div>
        <div class="intelligence-grid">
            <div class="intelligence-card">
                <h3>Most Popular Segments</h3>
                <?php foreach ($popularSegments as $segment => $percent): ?>
                <div class="intel-row">
                    <span class="intel-label"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $segment))) ?></span>
                    <div class="intel-bar">
                        <div class="intel-bar__fill" style="width: <?= $percent ?>%"></div>
                    </div>
                    <span class="intel-value"><?= $percent ?>%</span>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="intelligence-card">
                <h3>Customer Priorities</h3>
                <ol class="priority-list">
                    <?php foreach (array_slice($market['customer_priorities_ranked'] ?? [], 0, 5) as $priority): ?>
                    <li><?= htmlspecialchars($priority) ?></li>
                    <?php endforeach; ?>
                </ol>
            </div>
            <div class="intelligence-card">
                <h3>Emerging Trends</h3>
                <ul class="trend-list">
                    <?php foreach (array_slice($market['emerging_trends'] ?? [], 0, 4) as $trend): ?>
                    <li>📈 <?= htmlspecialchars($trend) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Transparency Block -->
<section class="section transparency-block">
    <div class="container">
        <div class="transparency-inner">
            <h2 class="section-title">Why Hurllo is Different</h2>
            <p class="section-subtitle">We're not a booking platform. We don't earn commissions. We don't have affiliate deals.</p>
            <div class="transparency-points">
                <div class="transparency-point">
                    <span class="point-icon">🔍</span>
                    <div>
                        <strong>100% Independent</strong>
                        <p>No affiliate relationships. No sponsored rankings. Pure data.</p>
                    </div>
                </div>
                <div class="transparency-point">
                    <span class="point-icon">📊</span>
                    <div>
                        <strong>Data-Driven Scores</strong>
                        <p>All scores calculated dynamically from real policy data.</p>
                    </div>
                </div>
                <div class="transparency-point">
                    <span class="point-icon">💡</span>
                    <div>
                        <strong>Financial Transparency</strong>
                        <p>We expose the true cost of renting — deposits, excess, and hidden risks.</p>
                    </div>
                </div>
                <div class="transparency-point">
                    <span class="point-icon">🇿🇦</span>
                    <div>
                        <strong>South Africa Focused</strong>
                        <p>Built specifically for the South African car hire market with <?= count($companies) ?> companies tracked.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Tourism Planning Section -->
<section class="section tourism-planning">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Planning a Trip to South Africa?</h2>
            <p class="section-subtitle">Everything you need to know about renting a car for your South Africa adventure</p>
        </div>
        <div class="tourism-cards-grid">
            <a href="<?= url('tourist') ?>" class="tourism-card tourism-card--primary">
                <div class="tourism-card__icon">🌍</div>
                <h3 class="tourism-card__title">Tourist Car Hire Guide</h3>
                <p class="tourism-card__desc">Everything international visitors need to know about renting in South Africa</p>
            </a>
            <div class="tourism-card">
                <div class="tourism-card__icon">✈️</div>
                <h3 class="tourism-card__title">Compare by Airport</h3>
                <div class="airport-pills">
                    <a href="<?= url('airport', 'or-tambo') ?>" class="airport-pill">OR Tambo</a>
                    <a href="<?= url('airport', 'cape-town') ?>" class="airport-pill">Cape Town</a>
                    <a href="<?= url('airport', 'king-shaka') ?>" class="airport-pill">King Shaka</a>
                </div>
            </div>
            <a href="<?= url('education', '', ['topic' => 'road-trip-guide']) ?>" class="tourism-card">
                <div class="tourism-card__icon">🗺️</div>
                <h3 class="tourism-card__title">Road Trip Planner</h3>
                <p class="tourism-card__desc">Guide to South Africa's best self-drive routes</p>
            </a>
        </div>
    </div>
</section>

<!-- SEO Grid Links -->
<section class="section seo-grid">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Explore by Category</h2>
        </div>
        <div class="seo-grid-inner">
            <div class="seo-category">
                <h3>By Company</h3>
                <div class="seo-links">
                    <?php foreach (array_slice($companies, 0, 10) as $c): ?>
                    <a href="<?= url('company', $c['slug']) ?>"><?= htmlspecialchars($c['name']) ?></a>
                    <?php endforeach; ?>
                    <a href="<?= url('directory') ?>">View All <?= count($companies) ?> Companies →</a>
                </div>
            </div>
            <div class="seo-category">
                <h3>By Vehicle Segment</h3>
                <div class="seo-links">
                    <?php
                    $segments = array_unique(array_column($vehicles, 'segment'));
                    foreach ($segments as $seg):
                    ?>
                    <a href="<?= url('vehicles', '', ['segment' => urlencode($seg)]) ?>"><?= segmentIcon($seg) ?> <?= htmlspecialchars($seg) ?> Cars</a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="seo-category">
                <h3>Intelligence Reports</h3>
                <div class="seo-links">
                    <a href="<?= url('rankings') ?>">Company Rankings</a>
                    <a href="<?= url('directory', '', ['sort' => 'lowest_exposure']) ?>">Lowest Exposure</a>
                    <a href="<?= url('directory', '', ['sort' => 'fastest_refund']) ?>">Fastest Refund</a>
                    <a href="<?= url('directory', '', ['sort' => 'lowest_price']) ?>">Lowest Price</a>
                    <a href="<?= url('directory', '', ['filter_cross_border' => '1']) ?>">Cross-Border Allowed</a>
                </div>
            </div>
        </div>
    </div>
</section>
