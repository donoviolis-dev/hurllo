<?php
$slug = getSlug();
$company = getCompanyBySlug($slug);

if (!$company) {
    http_response_code(404);
    echo '<div class="container" style="padding: 4rem 0; text-align: center;">';
    echo '<h1 style="color: var(--color-primary);">Company Not Found</h1>';
    echo '<p>The company you\'re looking for doesn\'t exist.</p>';
    echo '<a href="' . url('directory') . '" class="btn btn--primary">Browse Directory</a>';
    echo '</div>';
    return;
}

$scores = getAllScores($company);
$allCompanies = getRankedCompanies();
$rank = 0;
foreach ($allCompanies as $i => $c) {
    if ($c['slug'] === $slug) { $rank = $i + 1; break; }
}

$vehicles = getVehicles();
$companyVehicles = array_filter($vehicles, function($v) use ($company) {
    return in_array($company['name'], $v['companies'] ?? []);
});

// Breadcrumbs
echo breadcrumbs([
    ['label' => 'Home', 'url' => '/'],
    ['label' => 'Directory', 'url' => url('directory')],
    ['label' => $company['name'] . ' Car Hire']
]);
?>

<!-- Company Schema -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "name": "<?= htmlspecialchars($company['name']) ?> Car Hire South Africa",
    "url": "<?= htmlspecialchars($company['website_url'] ?? '') ?>",
    "areaServed": "South Africa",
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "<?= $company['google_score'] ?>",
        "bestRating": "5",
        "ratingCount": "100"
    }
}
</script>

<!-- Company Hero -->
<section class="company-hero">
    <div class="container">
        <div class="company-hero__inner">
            <div class="company-hero__logo">
                <?= companyLogo($company, 'large') ?>
            </div>
            <div class="company-hero__info">
                <div class="company-hero__rank">
                    <span class="rank-badge">Ranked #<?= $rank ?> of <?= count($allCompanies) ?> in South Africa</span>
                </div>
                <h1 class="company-hero__name"><?= htmlspecialchars($company['name']) ?> Car Hire</h1>
                <p class="company-hero__desc">
                    <?= htmlspecialchars($company['name']) ?> is a <?= htmlspecialchars($company['headquarters_country'] ?? 'South African') ?>-based car hire company
                    <?php if ($company['year_established'] ?? false): ?>
                    established in <?= $company['year_established'] ?>
                    <?php endif; ?>
                    with <?= $company['locations_count'] ?> locations across South Africa.
                    <?php if (!empty($company['reviews']['common_strengths'])): ?>
                    Known for: <?= htmlspecialchars($company['reviews']['common_strengths']) ?>.
                    <?php endif; ?>
                </p>
                <div class="company-hero__badges">
                    <?php if ($company['cross_border_allowed']): ?>
                    <?= riskBadge('low', '🌍 Cross-Border Allowed') ?>
                    <?php else: ?>
                    <?= riskBadge('medium', '🌍 SA Only') ?>
                    <?php endif; ?>
                    <?= riskBadge('info', '🛣️ ' . htmlspecialchars(getMileageLabel($company['mileage_policy']))) ?>
                    <?php if ($company['long_term_available']): ?>
                    <?= riskBadge('low', '📅 Long-Term Available') ?>
                    <?php endif; ?>
                    <?php if ($company['services']['loyalty_program_available'] ?? false): ?>
                    <?= riskBadge('info', '⭐ Loyalty Program') ?>
                    <?php endif; ?>
                    <?php if ($company['services']['roadside_assistance_included'] ?? false): ?>
                    <?= riskBadge('low', '🔧 Roadside Assist') ?>
                    <?php endif; ?>
                </div>
                <div class="company-hero__actions">
                    <?php if (!empty($company['website_url'])): ?>
                    <a href="<?= htmlspecialchars($company['website_url']) ?>" class="btn btn--primary" target="_blank" rel="noopener">Visit <?= htmlspecialchars($company['name']) ?> ↗</a>
                    <?php endif; ?>
                    <a href="<?= url('compare', '', ['a' => $company['slug']]) ?>" class="btn btn--outline">Compare This Company</a>
                </div>
            </div>
            <div class="company-hero__score">
                <div class="overall-score-display">
                    <div class="overall-score-value <?= scoreColor($scores['overall']) ?>" data-score="<?= $scores['overall'] ?>">
                        <?= number_format($scores['overall'], 1) ?>
                    </div>
                    <div class="overall-score-label">Intelligence Score</div>
                    <div class="overall-score-rating"><?= scoreLabel($scores['overall']) ?></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Intelligence Dashboard -->
<section class="section intelligence-dashboard">
    <div class="container">
        <h2 class="section-title">Intelligence Dashboard</h2>
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="dashboard-card__score <?= scoreColor($scores['financial_exposure']) ?>"><?= number_format($scores['financial_exposure'], 1) ?></div>
                <div class="dashboard-card__label">Financial Exposure</div>
                <div class="dashboard-card__desc">Deposit + Excess risk level</div>
            </div>
            <div class="dashboard-card">
                <div class="dashboard-card__score <?= scoreColor($scores['deposit_flexibility']) ?>"><?= number_format($scores['deposit_flexibility'], 1) ?></div>
                <div class="dashboard-card__label">Deposit Flexibility</div>
                <div class="dashboard-card__desc">Payment options & deposit size</div>
            </div>
            <div class="dashboard-card">
                <div class="dashboard-card__score <?= scoreColor($scores['price_competitiveness']) ?>"><?= number_format($scores['price_competitiveness'], 1) ?></div>
                <div class="dashboard-card__label">Price Competitiveness</div>
                <div class="dashboard-card__desc">Value vs market average</div>
            </div>
            <div class="dashboard-card">
                <div class="dashboard-card__score <?= scoreColor($scores['airport_coverage']) ?>"><?= number_format($scores['airport_coverage'], 1) ?></div>
                <div class="dashboard-card__label">Airport Coverage</div>
                <div class="dashboard-card__desc">Locations & airport presence</div>
            </div>
            <div class="dashboard-card">
                <div class="dashboard-card__score <?= scoreColor($scores['fleet_quality']) ?>"><?= number_format($scores['fleet_quality'], 1) ?></div>
                <div class="dashboard-card__label">Fleet Quality</div>
                <div class="dashboard-card__desc">Vehicle variety & services</div>
            </div>
            <div class="dashboard-card">
                <div class="dashboard-card__score <?= scoreColor($scores['transparency']) ?>"><?= number_format($scores['transparency'], 1) ?></div>
                <div class="dashboard-card__label">Transparency</div>
                <div class="dashboard-card__desc">Policy clarity & openness</div>
            </div>
            <div class="dashboard-card">
                <div class="dashboard-card__score <?= scoreColor($scores['refund_speed']) ?>"><?= number_format($scores['refund_speed'], 1) ?></div>
                <div class="dashboard-card__label">Refund Speed</div>
                <div class="dashboard-card__desc">Deposit return time</div>
            </div>
            <div class="dashboard-card">
                <div class="dashboard-card__score <?= scoreColor($scores['customer_satisfaction']) ?>"><?= number_format($scores['customer_satisfaction'], 1) ?></div>
                <div class="dashboard-card__label">Customer Satisfaction</div>
                <div class="dashboard-card__desc">Google + Trustpilot scores</div>
            </div>
        </div>
    </div>
</section>

<!-- Financial Exposure Detail -->
<section class="section financial-detail">
    <div class="container">
        <div class="detail-grid">
            <!-- Financial Data -->
            <div class="detail-card">
                <h2 class="detail-card__title">💰 Financial Exposure</h2>
                <div class="detail-rows">
                    <div class="detail-row">
                        <span class="detail-row__label">Deposit Range</span>
                        <span class="detail-row__value detail-row__value--<?= getDepositRisk($company['deposit_avg']) ?>">
                            <?= formatZAR($company['deposit_min']) ?> – <?= formatZAR($company['deposit_max']) ?>
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-row__label">Average Deposit</span>
                        <span class="detail-row__value detail-row__value--<?= getDepositRisk($company['deposit_avg']) ?>">
                            <?= formatZAR($company['deposit_avg']) ?>
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-row__label">Excess Range</span>
                        <span class="detail-row__value detail-row__value--<?= getExcessRisk($company['excess_avg']) ?>">
                            <?= formatZAR($company['excess_min']) ?> – <?= formatZAR($company['excess_max']) ?>
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-row__label">Average Excess</span>
                        <span class="detail-row__value detail-row__value--<?= getExcessRisk($company['excess_avg']) ?>">
                            <?= formatZAR($company['excess_avg']) ?>
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-row__label">Deposit Type</span>
                        <span class="detail-row__value"><?= htmlspecialchars(getDepositTypeLabel($company)) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-row__label">Refund Speed</span>
                        <span class="detail-row__value"><?= htmlspecialchars($company['refund_speed']) ?></span>
                    </div>
                    <?php if ($company['costs']['excess_reduction']['available'] ?? false): ?>
                    <div class="detail-row">
                        <span class="detail-row__label">Excess Reduction</span>
                        <span class="detail-row__value detail-row__value--low">
                            ✅ Available (<?= formatZAR($company['costs']['excess_reduction']['cost_per_day_average'] ?? 200) ?>/day)
                        </span>
                    </div>
                    <?php endif; ?>
                    <div class="detail-row detail-row--total">
                        <span class="detail-row__label">Max Total Exposure</span>
                        <span class="detail-row__value detail-row__value--high">
                            <?= formatZAR($company['deposit_max'] + $company['excess_max']) ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Coverage & Policy -->
            <div class="detail-card">
                <h2 class="detail-card__title">🗺️ Coverage & Policy</h2>
                <div class="detail-rows">
                    <div class="detail-row">
                        <span class="detail-row__label">Total Locations</span>
                        <span class="detail-row__value"><?= $company['locations_count'] ?> branches</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-row__label">Airport Locations</span>
                        <span class="detail-row__value"><?= count($company['major_airports']) ?> airports</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-row__label">Mileage Policy</span>
                        <span class="detail-row__value"><?= htmlspecialchars(getMileageLabel($company['mileage_policy'])) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-row__label">Fuel Policy</span>
                        <span class="detail-row__value"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $company['policies']['fuel_policy'] ?? 'full_to_full'))) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-row__label">Long-Term Rental</span>
                        <span class="detail-row__value"><?= $company['long_term_available'] ? '✅ Available' : '❌ Not Available' ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-row__label">Cross-Border</span>
                        <span class="detail-row__value"><?= $company['cross_border_allowed'] ? '✅ Allowed' : '❌ Not Allowed' ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-row__label">24/7 Support</span>
                        <span class="detail-row__value"><?= ($company['services']['support_24_hour'] ?? false) ? '✅ Yes' : '❌ No' ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-row__label">Corporate Accounts</span>
                        <span class="detail-row__value"><?= ($company['services']['corporate_accounts_available'] ?? false) ? '✅ Yes' : '❌ No' ?></span>
                    </div>
                </div>

                <!-- Major Airports -->
                <?php if (!empty($company['major_airports'])): ?>
                <div class="airports-list">
                    <h4>Airport Locations</h4>
                    <div class="airport-tags">
                        <?php foreach ($company['major_airports'] as $airport): ?>
                        <span class="airport-tag">✈️ <?= htmlspecialchars($airport) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Cities -->
                <?php if (!empty($company['locations']['cities'])): ?>
                <div class="airports-list" style="margin-top:1rem;">
                    <h4>City Locations</h4>
                    <div class="airport-tags">
                        <?php foreach ($company['locations']['cities'] as $city): ?>
                        <span class="airport-tag">📍 <?= htmlspecialchars($city) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section class="section price-section">
    <div class="container">
        <div class="price-card">
            <h2 class="price-card__title">Pricing Overview</h2>
            <div class="price-range-display">
                <div class="price-range-item">
                    <span class="price-range-label">Economy Daily Rate</span>
                    <span class="price-range-value price-range-value--highlight">
                        <?= formatZAR($company['economy_rate_min']) ?> – <?= formatZAR($company['economy_rate_max']) ?>
                    </span>
                </div>
                <?php if (!empty($company['pricing']['suv_daily_rate'])): ?>
                <div class="price-range-item">
                    <span class="price-range-label">SUV Daily Rate</span>
                    <span class="price-range-value">
                        <?= formatZAR($company['pricing']['suv_daily_rate']['min']) ?> – <?= formatZAR($company['pricing']['suv_daily_rate']['max']) ?>
                    </span>
                </div>
                <?php endif; ?>
                <?php if (!empty($company['pricing']['luxury_daily_rate'])): ?>
                <div class="price-range-item">
                    <span class="price-range-label">Luxury Daily Rate</span>
                    <span class="price-range-value">
                        <?= formatZAR($company['pricing']['luxury_daily_rate']['min']) ?> – <?= formatZAR($company['pricing']['luxury_daily_rate']['max']) ?>
                    </span>
                </div>
                <?php endif; ?>
            </div>
            <div class="detail-rows" style="margin-top:1rem;">
                <div class="detail-row">
                    <span class="detail-row__label">Most Affordable Model</span>
                    <span class="detail-row__value"><?= htmlspecialchars($company['most_affordable_model']) ?></span>
                </div>
            </div>
            <p class="price-disclaimer">* Prices are estimated ranges and may vary by season, vehicle availability, and booking platform. Always confirm directly with the company.</p>
        </div>
    </div>
</section>

<!-- Reviews Section -->
<section class="section reviews-section">
    <div class="container">
        <div class="detail-card">
            <h2 class="detail-card__title">⭐ Customer Reviews & Reputation</h2>
            <div class="detail-rows">
                <div class="detail-row">
                    <span class="detail-row__label">Google Review Score</span>
                    <span class="detail-row__value detail-row__value--<?= $company['google_score'] >= 4.0 ? 'low' : 'medium' ?>">
                        <?= $company['google_score'] ?>/5 ⭐
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-row__label">Trustpilot Score</span>
                    <span class="detail-row__value detail-row__value--<?= $company['trustpilot_score'] >= 4.0 ? 'low' : 'medium' ?>">
                        <?= $company['trustpilot_score'] ?>/5
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-row__label">Combined Score</span>
                    <span class="detail-row__value"><?= $company['combined_score'] ?>/5</span>
                </div>
                <?php if (!empty($company['reviews']['common_strengths'])): ?>
                <div class="detail-row">
                    <span class="detail-row__label">Common Strengths</span>
                    <span class="detail-row__value detail-row__value--low">✅ <?= htmlspecialchars($company['reviews']['common_strengths']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($company['reviews']['common_complaints'])): ?>
                <div class="detail-row">
                    <span class="detail-row__label">Common Complaints</span>
                    <span class="detail-row__value detail-row__value--medium">⚠️ <?= htmlspecialchars($company['reviews']['common_complaints']) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Fleet Section -->
<?php if (!empty($company['vehicle_brands_available'])): ?>
<section class="section fleet-section">
    <div class="container">
        <h2 class="section-title">Vehicle Brands Available</h2>
        <div class="fleet-brands">
            <?php foreach ($company['vehicle_brands_available'] as $brand): ?>
            <div class="brand-tag">🚗 <?= htmlspecialchars($brand) ?></div>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($companyVehicles)): ?>
        <h3 style="margin-top:2rem; margin-bottom:1rem;">Specific Models Available</h3>
        <div class="fleet-grid">
            <?php foreach ($companyVehicles as $vehicle): ?>
            <div class="fleet-card">
                <div class="fleet-card__image">
                    <?= vehicleImage($vehicle, 'small') ?>
                </div>
                <div class="fleet-card__info">
                    <a href="<?= url('vehicle', $vehicle['slug']) ?>" class="fleet-card__name"><?= htmlspecialchars($vehicle['name']) ?></a>
                    <span class="fleet-card__segment"><?= htmlspecialchars($vehicle['segment']) ?></span>
                    <span class="fleet-card__price">~<?= formatZAR($vehicle['avg_daily_rate_ZAR']) ?>/day</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>
