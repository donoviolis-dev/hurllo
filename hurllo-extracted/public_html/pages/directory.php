<?php
$allCompanies = getCompanies();
$market = getMarketData();

// Get filters from GET
$sortBy = preg_replace('/[^a-z_]/', '', $_GET['sort'] ?? 'overall');
$filterDebit = !empty($_GET['filter_debit']);
$filterCrossBorder = !empty($_GET['filter_cross_border']);
$filterUnlimited = !empty($_GET['filter_unlimited']);
$filterMaxDeposit = isset($_GET['max_deposit']) ? (int)$_GET['max_deposit'] : 0;
$touristMode = !empty($_GET['tourist']);

// Currency selector
$currency = $_GET['currency'] ?? 'ZAR';
$currencies = getCurrencies();

// Apply filters
$filters = [];
if ($filterDebit) $filters['debit_card'] = true;
if ($filterCrossBorder) $filters['cross_border'] = true;
if ($filterUnlimited) $filters['unlimited_mileage'] = true;
if ($filterMaxDeposit > 0) $filters['max_deposit'] = $filterMaxDeposit;

$companies = filterCompanies($allCompanies, $filters);

// Tourist mode filter
if ($touristMode) {
    $companies = array_values(array_filter($companies, function($c) {
        return ($c['accepts_foreign_licence'] ?? true) === true;
    }));
}

$companies = sortCompanies(array_values($companies), $sortBy);

// Pagination
$perPage = 8;
$currentPageNum = max(1, (int)($_GET['p'] ?? 1));
$totalCompanies = count($companies);
$totalPages = ceil($totalCompanies / $perPage);
$offset = ($currentPageNum - 1) * $perPage;
$pagedCompanies = array_slice($companies, $offset, $perPage);

// Market averages
$allDeposits = array_column($allCompanies, 'deposit_avg');
$allExcess = array_column($allCompanies, 'excess_avg');
$avgDeposit = count($allDeposits) ? (int)round(array_sum($allDeposits) / count($allDeposits)) : 3000;
$avgExcess = count($allExcess) ? (int)round(array_sum($allExcess) / count($allExcess)) : 15000;

// Breadcrumbs
$crumbs = [
    ['label' => 'Home', 'url' => '/'],
    ['label' => 'Company Directory']
];
if ($touristMode) {
    $crumbs[] = ['label' => 'For Tourists'];
}
echo breadcrumbs($crumbs);
?>

<section class="page-hero page-hero--directory">
    <div class="container">
        <h1 class="page-title">Car Hire Company Directory</h1>
        <p class="page-subtitle">Compare all <?= count($allCompanies) ?> major South African car hire companies by intelligence score, financial exposure, and policy transparency.</p>
    </div>
</section>

<?php if ($touristMode): ?>
<div class="tourist-mode-banner">
    <div class="container">
        <span class="tourist-banner-icon">🌍</span>
        <span class="tourist-banner-text">Showing <?= $totalCompanies ?> companies that accept foreign licences</span>
        <a href="<?= url('directory') ?>" class="tourist-banner-close">×</a>
    </div>
</div>
<?php endif; ?>

<section class="section directory-section">
    <div class="container">
        <div class="directory-layout">

            <!-- Sidebar Filters -->
            <aside class="directory-sidebar">
                <!-- Tourist Mode Toggle -->
                <div class="filter-panel filter-panel--tourist">
                    <div class="tourist-mode-toggle">
                        <label class="tourist-toggle-label">
                            <span class="tourist-toggle-title">Tourist Mode</span>
                            <span class="tourist-toggle-desc">Shows companies that accept foreign licences</span>
                        </label>
                        <a href="<?= url('directory', '', array_merge($_GET, ['tourist' => $touristMode ? null : '1'])) ?>" class="tourist-toggle-switch <?= $touristMode ? 'tourist-toggle-switch--active' : '' ?>">
                            <span class="tourist-toggle-slider"></span>
                        </a>
                    </div>
                </div>

                <!-- Currency Selector -->
                <div class="filter-panel">
                    <h3 class="filter-panel__title">Show Prices In</h3>
                    <form method="GET" action="/">
                        <input type="hidden" name="page" value="directory">
                        <?php if ($touristMode): ?><input type="hidden" name="tourist" value="1"><?php endif; ?>
                        <input type="hidden" name="sort" value="<?= htmlspecialchars($sortBy) ?>">
                        <?php if ($filterMaxDeposit): ?><input type="hidden" name="max_deposit" value="<?= $filterMaxDeposit ?>"><?php endif; ?>
                        <?php if ($filterCrossBorder): ?><input type="hidden" name="filter_cross_border" value="1"><?php endif; ?>
                        <?php if ($filterUnlimited): ?><input type="hidden" name="filter_unlimited" value="1"><?php endif; ?>
                        <select name="currency" class="filter-select" onchange="this.form.submit()">
                            <option value="ZAR" <?= $currency === 'ZAR' ? 'selected' : '' ?>>ZAR (R)</option>
                            <option value="USD" <?= $currency === 'USD' ? 'selected' : '' ?>>USD ($)</option>
                            <option value="EUR" <?= $currency === 'EUR' ? 'selected' : '' ?>>EUR (€)</option>
                            <option value="GBP" <?= $currency === 'GBP' ? 'selected' : '' ?>>GBP (£)</option>
                            <option value="AUD" <?= $currency === 'AUD' ? 'selected' : '' ?>>AUD (A$)</option>
                        </select>
                        <small class="currency-note">Rates are indicative.</small>
                    </form>
                </div>

                <div class="filter-panel">
                    <h3 class="filter-panel__title">Filter Companies</h3>
                    <form method="GET" action="/" id="filter-form">
                        <input type="hidden" name="page" value="directory">
                        <?php if ($touristMode): ?><input type="hidden" name="tourist" value="1"><?php endif; ?>
                        <input type="hidden" name="currency" value="<?= htmlspecialchars($currency) ?>">
                        <input type="hidden" name="sort" value="<?= htmlspecialchars($sortBy) ?>">

                        <div class="filter-group">
                            <label class="filter-label">Max Deposit (ZAR)</label>
                            <select name="max_deposit" class="filter-select" onchange="this.form.submit()">
                                <option value="">Any Deposit</option>
                                <option value="2000" <?= $filterMaxDeposit == 2000 ? 'selected' : '' ?>>Up to R2,000</option>
                                <option value="2500" <?= $filterMaxDeposit == 2500 ? 'selected' : '' ?>>Up to R2,500</option>
                                <option value="3000" <?= $filterMaxDeposit == 3000 ? 'selected' : '' ?>>Up to R3,000</option>
                                <option value="4000" <?= $filterMaxDeposit == 4000 ? 'selected' : '' ?>>Up to R4,000</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label class="filter-checkbox">
                                <input type="checkbox" name="filter_cross_border" value="1" <?= $filterCrossBorder ? 'checked' : '' ?> onchange="this.form.submit()">
                                <span>Cross-Border Allowed</span>
                            </label>
                        </div>

                        <div class="filter-group">
                            <label class="filter-checkbox">
                                <input type="checkbox" name="filter_unlimited" value="1" <?= $filterUnlimited ? 'checked' : '' ?> onchange="this.form.submit()">
                                <span>Unlimited Mileage</span>
                            </label>
                        </div>

                        <?php if ($filterDebit || $filterCrossBorder || $filterUnlimited || $filterMaxDeposit || $touristMode): ?>
                        <a href="<?= url('directory') ?>" class="btn btn--outline btn--sm btn--full" style="margin-top:0.5rem;">Clear Filters</a>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Market Quick Stats -->
                <div class="sidebar-widget">
                    <h3 class="sidebar-widget__title">Market Averages</h3>
                    <div class="sidebar-stats">
                        <div class="sidebar-stat">
                            <span>Avg Deposit</span>
                            <strong><?= formatZAR($avgDeposit) ?></strong>
                        </div>
                        <div class="sidebar-stat">
                            <span>Avg Excess</span>
                            <strong><?= formatZAR($avgExcess) ?></strong>
                        </div>
                        <div class="sidebar-stat">
                            <span>Companies</span>
                            <strong><?= count($allCompanies) ?></strong>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="directory-main">
                <!-- Sort Bar -->
                <div class="sort-bar">
                    <div class="sort-bar__count">
                        Showing <strong><?= $totalCompanies ?></strong> of <?= count($allCompanies) ?> companies
                    </div>
                    <div class="sort-bar__options">
                        <span class="sort-label">Sort:</span>
                        <?php
                        $sortOptions = [
                            'overall' => 'Best Score',
                            'lowest_exposure' => 'Lowest Exposure',
                            'lowest_price' => 'Lowest Price',
                            'fastest_refund' => 'Fastest Refund',
                            'most_airports' => 'Most Airports'
                        ];
                        foreach ($sortOptions as $key => $label):
                            $params = array_merge($_GET, ['sort' => $key, 'p' => 1]);
                            $isActive = $sortBy === $key;
                        ?>
                        <a href="/?<?= http_build_query($params) ?>" class="sort-option <?= $isActive ? 'sort-option--active' : '' ?>">
                            <?= htmlspecialchars($label) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Company Cards -->
                <div class="company-cards-list" id="company-list">
                    <?php foreach ($pagedCompanies as $company):
                        $scores = getAllScores($company);
                        $depositRisk = getDepositRisk($company['deposit_avg']);
                        $excessRisk = getExcessRisk($company['excess_avg']);
                        $refundRating = getRefundRating($company['refund_speed']);
                    ?>
                    <div class="company-card company-card--list">
                        <!-- Card Header -->
                        <div class="company-card__header">
                            <div class="company-card__logo-wrap">
                                <?= companyLogo($company) ?>
                            </div>
                            <div class="company-card__title-area">
                                <h2 class="company-card__name">
                                    <a href="<?= url('company', $company['slug']) ?>"><?= htmlspecialchars($company['name']) ?> Car Hire</a>
                                </h2>
                                <div class="company-card__badges">
                                    <?php if ($company['cross_border_allowed']): ?>
                                    <?= riskBadge('info', '🌍 Cross-Border') ?>
                                    <?php endif; ?>
                                    <?php if ($company['mileage_policy'] === 'unlimited'): ?>
                                    <?= riskBadge('info', '🛣️ Unlimited KM') ?>
                                    <?php endif; ?>
                                    <?php if ($company['services']['loyalty_program_available'] ?? false): ?>
                                    <?= riskBadge('low', '⭐ Loyalty Program') ?>
                                    <?php endif; ?>
                                    <?php if ($company['services']['roadside_assistance_included'] ?? false): ?>
                                    <?= riskBadge('low', '🔧 Roadside Assist') ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="company-card__overall-score">
                                <?= scoreCircle($scores['overall'], 'Score') ?>
                            </div>
                        </div>

                        <!-- Vehicle Preview -->
                        <div class="company-card__vehicle-row">
                            <div class="vehicle-thumb">
                                <?= companyAffordableImage($company) ?>
                            </div>
                            <div class="vehicle-thumb-info">
                                <span class="vehicle-thumb-model"><?= htmlspecialchars($company['most_affordable_model']) ?></span>
                                <span class="vehicle-thumb-price">From <?= formatZAR($company['economy_rate_min']) ?>/day</span>
                            </div>
                        </div>

                        <!-- Score Bars -->
                        <div class="company-card__score-grid">
                            <div class="score-col">
                                <?= scoreBar($scores['financial_exposure'], 'Financial Exposure') ?>
                                <?= scoreBar($scores['price_competitiveness'], 'Price') ?>
                                <?= scoreBar($scores['deposit_flexibility'], 'Deposit Flexibility') ?>
                            </div>
                            <div class="score-col">
                                <?= scoreBar($scores['transparency'], 'Transparency') ?>
                                <?= scoreBar($scores['fleet_quality'], 'Fleet Quality') ?>
                                <?= scoreBar($scores['airport_coverage'], 'Airport Coverage') ?>
                            </div>
                        </div>

                        <!-- Key Data -->
                        <div class="company-card__data-grid">
                            <div class="data-item">
                                <span class="data-label">Avg Deposit</span>
                                <span class="data-value data-value--<?= $depositRisk ?>"><?= formatZAR($company['deposit_avg']) ?></span>
                            </div>
                            <div class="data-item">
                                <span class="data-label">Avg Excess</span>
                                <span class="data-value data-value--<?= $excessRisk ?>"><?= formatZAR($company['excess_avg']) ?></span>
                            </div>
                            <div class="data-item">
                                <span class="data-label">Refund Speed</span>
                                <span class="data-value data-value--<?= $refundRating ?>"><?= htmlspecialchars($company['refund_speed']) ?></span>
                            </div>
                            <div class="data-item">
                                <span class="data-label">Deposit Type</span>
                                <span class="data-value"><?= htmlspecialchars(getDepositTypeLabel($company)) ?></span>
                            </div>
                            <div class="data-item">
                                <span class="data-label">Mileage</span>
                                <span class="data-value"><?= htmlspecialchars(getMileageLabel($company['mileage_policy'])) ?></span>
                            </div>
                            <div class="data-item">
                                <span class="data-label">Locations</span>
                                <span class="data-value"><?= $company['locations_count'] ?> branches</span>
                            </div>
                        </div>

                        <!-- Airports -->
                        <?php if (!empty($company['major_airports'])): ?>
                        <div class="company-card__cross-border">
                            <span class="cross-border-label">Airports:</span>
                            <?php foreach (array_slice($company['major_airports'], 0, 4) as $airport): ?>
                            <span class="cross-border-tag">✈️ <?= htmlspecialchars($airport) ?></span>
                            <?php endforeach; ?>
                            <?php if (count($company['major_airports']) > 4): ?>
                            <span class="cross-border-tag">+<?= count($company['major_airports']) - 4 ?> more</span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Actions -->
                        <div class="company-card__actions">
                            <a href="<?= url('company', $company['slug']) ?>" class="btn btn--primary">View Intelligence Report</a>
                            <?php if (!empty($company['website_url'])): ?>
                            <a href="<?= htmlspecialchars($company['website_url']) ?>" class="btn btn--outline" target="_blank" rel="noopener noreferrer">Visit Website ↗</a>
                            <?php endif; ?>
                            <a href="<?= url('compare', '', ['a' => $company['slug']]) ?>" class="btn btn--ghost">+ Compare</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($currentPageNum > 1): ?>
                    <a href="/?<?= http_build_query(array_merge($_GET, ['p' => $currentPageNum - 1])) ?>" class="pagination__btn">← Prev</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="/?<?= http_build_query(array_merge($_GET, ['p' => $i])) ?>" class="pagination__btn <?= $i === $currentPageNum ? 'pagination__btn--active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>

                    <?php if ($currentPageNum < $totalPages): ?>
                    <a href="/?<?= http_build_query(array_merge($_GET, ['p' => $currentPageNum + 1])) ?>" class="pagination__btn">Next →</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
