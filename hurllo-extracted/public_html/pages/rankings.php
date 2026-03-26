<?php
$rankedCompanies = getRankedCompanies();

// Breadcrumbs
echo breadcrumbs([
    ['label' => 'Home', 'url' => '/'],
    ['label' => 'Company Rankings']
]);
?>

<section class="page-hero page-hero--rankings">
    <div class="container">
        <h1 class="page-title">Best Car Hire Companies South Africa</h1>
        <p class="page-subtitle">All <?= count($rankedCompanies) ?> companies ranked by our 9-metric intelligence scoring system. Updated regularly. No sponsored placements.</p>
    </div>
</section>

<section class="section rankings-section">
    <div class="container">

        <!-- Scoring Methodology -->
        <div class="methodology-card">
            <h2>How We Rank</h2>
            <p>Our rankings are calculated dynamically using 9 weighted intelligence metrics. No sponsored placements. No affiliate bias. Pure data from <?= count($rankedCompanies) ?> companies.</p>
            <div class="methodology-grid">
                <div class="methodology-item">
                    <span class="methodology-weight">20%</span>
                    <span class="methodology-label">Financial Exposure</span>
                </div>
                <div class="methodology-item">
                    <span class="methodology-weight">20%</span>
                    <span class="methodology-label">Price Competitiveness</span>
                </div>
                <div class="methodology-item">
                    <span class="methodology-weight">15%</span>
                    <span class="methodology-label">Deposit Flexibility</span>
                </div>
                <div class="methodology-item">
                    <span class="methodology-weight">15%</span>
                    <span class="methodology-label">Airport Coverage</span>
                </div>
                <div class="methodology-item">
                    <span class="methodology-weight">10%</span>
                    <span class="methodology-label">Fleet Quality</span>
                </div>
                <div class="methodology-item">
                    <span class="methodology-weight">10%</span>
                    <span class="methodology-label">Transparency</span>
                </div>
                <div class="methodology-item">
                    <span class="methodology-weight">10%</span>
                    <span class="methodology-label">Refund Speed</span>
                </div>
            </div>
        </div>

        <!-- Rankings Table -->
        <div class="rankings-table-wrapper">
            <table class="rankings-table">
                <thead>
                    <tr>
                        <th class="rank-col">Rank</th>
                        <th class="company-col">Company</th>
                        <th class="score-col">Overall</th>
                        <th class="score-col">Financial</th>
                        <th class="score-col">Price</th>
                        <th class="score-col">Deposit</th>
                        <th class="score-col">Airports</th>
                        <th class="score-col">Refund</th>
                        <th class="action-col">Report</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rankedCompanies as $rank => $company):
                        $scores = getAllScores($company);
                        $rankNum = $rank + 1;
                    ?>
                    <tr class="rankings-row <?= $rankNum <= 3 ? 'rankings-row--top' : '' ?>">
                        <td class="rank-cell">
                            <span class="rank-number">
                                <?php if ($rankNum === 1): ?>🥇
                                <?php elseif ($rankNum === 2): ?>🥈
                                <?php elseif ($rankNum === 3): ?>🥉
                                <?php else: ?>#<?= $rankNum ?>
                                <?php endif; ?>
                            </span>
                        </td>
                        <td class="company-cell">
                            <div class="rankings-company">
                                <?= companyLogo($company, 'small') ?>
                                <div class="rankings-company-info">
                                    <a href="<?= url('company', $company['slug']) ?>" class="rankings-company-name">
                                        <?= htmlspecialchars($company['name']) ?>
                                    </a>
                                    <div class="rankings-company-meta">
                                        <?php if ($company['cross_border_allowed']): ?>
                                        <span class="mini-badge mini-badge--green">Cross-Border ✓</span>
                                        <?php endif; ?>
                                        <span class="mini-badge"><?= $company['locations_count'] ?> locations</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="score-cell">
                            <span class="score-pill <?= scoreColor($scores['overall']) ?>"><?= number_format($scores['overall'], 1) ?></span>
                        </td>
                        <td class="score-cell">
                            <span class="score-pill <?= scoreColor($scores['financial_exposure']) ?>"><?= number_format($scores['financial_exposure'], 1) ?></span>
                        </td>
                        <td class="score-cell">
                            <span class="score-pill <?= scoreColor($scores['price_competitiveness']) ?>"><?= number_format($scores['price_competitiveness'], 1) ?></span>
                        </td>
                        <td class="score-cell">
                            <span class="score-pill <?= scoreColor($scores['deposit_flexibility']) ?>"><?= number_format($scores['deposit_flexibility'], 1) ?></span>
                        </td>
                        <td class="score-cell">
                            <span class="score-pill <?= scoreColor($scores['airport_coverage']) ?>"><?= number_format($scores['airport_coverage'], 1) ?></span>
                        </td>
                        <td class="score-cell">
                            <span class="score-pill <?= scoreColor($scores['refund_speed']) ?>"><?= number_format($scores['refund_speed'], 1) ?></span>
                        </td>
                        <td class="action-cell">
                            <a href="<?= url('company', $company['slug']) ?>" class="btn btn--primary btn--xs">Report</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Category Rankings -->
        <div class="category-rankings">
            <h2 class="section-title">Category Leaders</h2>
            <div class="category-grid">
                <?php
                $categories = [
                    'lowest_exposure' => ['label' => '💰 Lowest Financial Exposure', 'sort' => 'financial_exposure', 'desc' => true],
                    'lowest_price' => ['label' => '💵 Most Affordable', 'sort' => 'price_competitiveness', 'desc' => true],
                    'fastest_refund' => ['label' => '⚡ Fastest Refund', 'sort' => 'refund_speed', 'desc' => true],
                    'best_airports' => ['label' => '✈️ Best Airport Coverage', 'sort' => 'airport_coverage', 'desc' => true],
                    'most_transparent' => ['label' => '🔍 Most Transparent', 'sort' => 'transparency', 'desc' => true],
                    'best_fleet' => ['label' => '🚗 Best Fleet', 'sort' => 'fleet_quality', 'desc' => true],
                ];

                foreach ($categories as $catKey => $cat):
                    $sortedForCat = $rankedCompanies;
                    usort($sortedForCat, function($a, $b) use ($cat) {
                        $scoresA = getAllScores($a);
                        $scoresB = getAllScores($b);
                        if ($cat['desc']) {
                            return $scoresB[$cat['sort']] <=> $scoresA[$cat['sort']];
                        } else {
                            return $scoresA[$cat['sort']] <=> $scoresB[$cat['sort']];
                        }
                    });
                    $winner = $sortedForCat[0];
                    $winnerScores = getAllScores($winner);
                ?>
                <div class="category-card">
                    <div class="category-card__label"><?= $cat['label'] ?></div>
                    <div class="category-card__winner">
                        <?= companyLogo($winner, 'small') ?>
                        <a href="<?= url('company', $winner['slug']) ?>" class="category-card__name">
                            <?= htmlspecialchars($winner['name']) ?>
                        </a>
                    </div>
                    <div class="category-card__score <?= scoreColor($winnerScores[$cat['sort']]) ?>">
                        <?= number_format($winnerScores[$cat['sort']], 1) ?>/10
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Location Rankings -->
        <div class="location-rankings">
            <h2 class="section-title">Rankings by Location</h2>
            <div class="location-links-grid">
                <?php
                $locations = [
                    'johannesburg' => 'Johannesburg',
                    'cape-town' => 'Cape Town',
                    'durban' => 'Durban',
                    'or-tambo' => 'OR Tambo Airport',
                    'cape-town-airport' => 'Cape Town Airport',
                    'king-shaka' => 'King Shaka Airport',
                    'pretoria' => 'Pretoria',
                    'port-elizabeth' => 'Port Elizabeth',
                    'lanseria' => 'Lanseria Airport',
                    'bloemfontein' => 'Bloemfontein',
                ];
                foreach ($locations as $locSlug => $locName):
                ?>
                <a href="<?= url('directory', '', ['location' => $locSlug]) ?>" class="location-link">
                    📍 Best Car Hire in <?= htmlspecialchars($locName) ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
