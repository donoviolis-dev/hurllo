<?php
$airports = getAirports();
$destinations = getDestinations();
$allCompanies = getCompanies();

// Filter for tourist-friendly companies (accept foreign licence)
$touristCompanies = array_filter($allCompanies, function($c) {
    return ($c['accepts_foreign_licence'] ?? true) === true;
});
$touristCompanies = array_values($touristCompanies);

// Sort by overall score
usort($touristCompanies, function($a, $b) {
    $scoreA = calculateOverallScore($a);
    $scoreB = calculateOverallScore($b);
    return $scoreB <=> $scoreA;
});

// Lowest deposit for tourists
$lowestDepositCompanies = $touristCompanies;
usort($lowestDepositCompanies, function($a, $b) {
    return $a['deposit_avg'] <=> $b['deposit_avg'];
});
$lowestDepositCompanies = array_slice($lowestDepositCompanies, 0, 5);

// Fastest refund for tourists
$fastestRefundCompanies = $touristCompanies;
usort($fastestRefundCompanies, function($a, $b) {
    $partsA = explode('-', $a['refund_speed'] ?? '5-10');
    $partsB = explode('-', $b['refund_speed'] ?? '5-10');
    return (int)$partsA[0] <=> (int)$partsB[0];
});
$fastestRefundCompanies = array_slice($fastestRefundCompanies, 0, 5);

$topTouristCompanies = array_slice($touristCompanies, 0, 5);

// Breadcrumbs
echo breadcrumbs([
    ['label' => 'Home', 'url' => '/'],
    ['label' => 'For Tourists']
]);
?>

<!-- Tourist Hub Schema -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        {
            "@type": "Question",
            "name": "Can I use my foreign driving licence in South Africa?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Yes, a valid foreign licence is accepted for up to 12 months. An International Driving Permit (IDP) is recommended and required by some companies."
            }
        },
        {
            "@type": "Question",
            "name": "Do I need a credit card to rent a car in South Africa?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Most companies require a credit card for the deposit hold. Some accept debit cards but charge a higher deposit."
            }
        },
        {
            "@type": "Question",
            "name": "Is it safe to self-drive in South Africa?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Yes, major tourist routes are well-maintained and clearly signposted. Stay on main roads, avoid driving at night in unfamiliar areas, and keep doors locked."
            }
        },
        {
            "@type": "Question",
            "name": "What side of the road do South Africans drive on?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Left-hand side, like the UK and Australia."
            }
        },
        {
            "@type": "Question",
            "name": "How much deposit will I pay for a car hire in South Africa?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Deposits typically range from R3,000 to R15,000 depending on the company and vehicle class. Budget for at least R8,000 on your card."
            }
        }
    ]
}
</script>

<!-- Hero -->
<section class="page-hero page-hero--tourist">
    <div class="container">
        <div class="tourist-hero">
            <h1 class="page-title">Car Hire in South Africa for Tourists & International Visitors</h1>
            <p class="page-subtitle">Independent analysis of deposits, excess amounts, foreign licence policies, and refund speeds — so you know exactly what to expect before you arrive.</p>
            <div class="hero-actions">
                <a href="<?= url('directory', '', ['tourist' => '1']) ?>" class="btn btn--primary btn--lg">
                    <span>Browse All Companies</span>
                </a>
                <a href="<?= url('education', '', ['topic' => 'tourist-guide']) ?>" class="btn btn--outline btn--lg">Read the Tourist Guide</a>
            </div>
        </div>
    </div>
</section>

<!-- By Airport -->
<section class="section tourist-airports">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Car Hire by Airport</h2>
            <p class="section-subtitle">Find car hire at South Africa's main international airports</p>
        </div>
        <div class="airport-cards-grid">
            <?php foreach ($airports as $airport): ?>
            <a href="<?= url('airport', $airport['slug']) ?>" class="airport-card">
                <div class="airport-card__header">
                    <span class="airport-card__iata"><?= htmlspecialchars($airport['iata']) ?></span>
                    <span class="airport-card__city"><?= htmlspecialchars($airport['city']) ?></span>
                </div>
                <div class="airport-card__name"><?= htmlspecialchars($airport['name']) ?></div>
                <div class="airport-card__companies"><?= count($airport['companies_present']) ?> companies</div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- By Destination -->
<section class="section tourist-destinations">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Car Hire by Destination</h2>
            <p class="section-subtitle">Find the best car hire for your South Africa road trip</p>
        </div>
        <div class="destination-cards-grid">
            <?php foreach ($destinations as $dest): ?>
            <a href="<?= url('destination', $dest['slug']) ?>" class="destination-card">
                <div class="destination-card__name"><?= htmlspecialchars($dest['name']) ?></div>
                <div class="destination-card__region"><?= htmlspecialchars($dest['region']) ?></div>
                <div class="destination-card__vehicles">
                    <?php foreach (array_slice($dest['best_vehicle_segments'], 0, 2) as $seg): ?>
                    <span class="segment-tag"><?= segmentIcon($seg) ?></span>
                    <?php endforeach; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Tourist Rankings -->
<section class="section tourist-rankings">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Tourist-Friendly Rankings</h2>
            <p class="section-subtitle">Companies that accept foreign licences, sorted by score</p>
        </div>
        <div class="rankings-tables-grid">
            <!-- Most Tourist-Friendly -->
            <div class="ranking-table-card">
                <h3 class="ranking-table-title">🏆 Most Tourist-Friendly</h3>
                <table class="ranking-table">
                    <tbody>
                        <?php foreach ($topTouristCompanies as $company): 
                            $scores = getAllScores($company);
                        ?>
                        <tr>
                            <td class="ranking-company-cell">
                                <a href="<?= url('company', $company['slug']) ?>"><?= htmlspecialchars($company['name']) ?></a>
                            </td>
                            <td class="ranking-score-cell">
                                <span class="score-pill <?= scoreColor($scores['overall']) ?>"><?= number_format($scores['overall'], 1) ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Lowest Deposit -->
            <div class="ranking-table-card">
                <h3 class="ranking-table-title">💰 Lowest Deposit for Tourists</h3>
                <table class="ranking-table">
                    <tbody>
                        <?php foreach ($lowestDepositCompanies as $company): ?>
                        <tr>
                            <td class="ranking-company-cell">
                                <a href="<?= url('company', $company['slug']) ?>"><?= htmlspecialchars($company['name']) ?></a>
                            </td>
                            <td class="ranking-score-cell">
                                <span class="deposit-value"><?= formatZAR($company['deposit_avg']) ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Fastest Refund -->
            <div class="ranking-table-card">
                <h3 class="ranking-table-title">⚡ Fastest Deposit Refund</h3>
                <table class="ranking-table">
                    <tbody>
                        <?php foreach ($fastestRefundCompanies as $company): ?>
                        <tr>
                            <td class="ranking-company-cell">
                                <a href="<?= url('company', $company['slug']) ?>"><?= htmlspecialchars($company['name']) ?></a>
                            </td>
                            <td class="ranking-score-cell">
                                <span class="refund-value"><?= htmlspecialchars($company['refund_speed']) ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Tourist Tips -->
<?php require_once CARVIO_ROOT . '/includes/tourist_tips.php'; ?>

<!-- Education Articles -->
<section class="section tourist-education">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Essential Reading for International Visitors</h2>
        </div>
        <div class="education-cards-grid">
            <a href="<?= url('education', '', ['topic' => 'tourist-guide']) ?>" class="education-card">
                <span class="education-card__icon">🛂</span>
                <h3 class="education-card__title">Complete Guide to Renting a Car in South Africa as a Tourist</h3>
                <p class="education-card__desc">Everything international visitors need to know before picking up a car in South Africa.</p>
            </a>
            <a href="<?= url('education', '', ['topic' => 'road-trip-guide']) ?>" class="education-card">
                <span class="education-card__icon">🗺️</span>
                <h3 class="education-card__title">South Africa Road Trip Guide for Tourists</h3>
                <p class="education-card__desc">Plan the perfect self-drive holiday with our route guide for South Africa's most iconic roads.</p>
            </a>
            <a href="<?= url('education', '', ['topic' => 'deposit-guide-tourists']) ?>" class="education-card">
                <span class="education-card__icon">💳</span>
                <h3 class="education-card__title">Car Hire Deposits in South Africa — A Tourist's Guide</h3>
                <p class="education-card__desc">Deposits are the biggest financial surprise for international visitors. Here's exactly what to expect.</p>
            </a>
        </div>
    </div>
</section>