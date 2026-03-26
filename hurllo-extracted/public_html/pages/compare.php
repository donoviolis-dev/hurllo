<?php
$allCompanies = getCompanies();
$allVehicles = getVehicles();

$companyA = getCompanyBySlug($_GET['a'] ?? '');
$companyB = getCompanyBySlug($_GET['b'] ?? '');
$vehicleA = getVehicleBySlug($_GET['va'] ?? '');
$vehicleB = getVehicleBySlug($_GET['vb'] ?? '');

$mode = (isset($_GET['va']) || isset($_GET['vb'])) ? 'vehicle' : 'company';

// Breadcrumbs
echo breadcrumbs([
    ['label' => 'Home', 'url' => '/'],
    ['label' => 'Compare']
]);
?>

<section class="page-hero page-hero--compare">
    <div class="container">
        <h1 class="page-title">Compare Car Hire</h1>
        <p class="page-subtitle">Side-by-side intelligence comparison of South African car hire companies and vehicles.</p>
    </div>
</section>

<section class="section compare-section">
    <div class="container">

        <!-- Mode Toggle -->
        <div class="compare-mode-toggle">
            <a href="<?= url('compare') ?>" class="mode-btn <?= $mode === 'company' ? 'mode-btn--active' : '' ?>">🏢 Compare Companies</a>
            <a href="<?= url('compare', '', ['va' => '', 'vb' => '']) ?>" class="mode-btn <?= $mode === 'vehicle' ? 'mode-btn--active' : '' ?>">🚗 Compare Vehicles</a>
        </div>

        <?php if ($mode === 'company'): ?>
        <!-- Company Comparison -->
        <div class="compare-selector">
            <form method="GET" action="/">
                <input type="hidden" name="page" value="compare">
                <div class="compare-selects">
                    <div class="compare-select-group">
                        <label>Company A</label>
                        <select name="a" class="compare-select">
                            <option value="">Select Company...</option>
                            <?php foreach ($allCompanies as $c): ?>
                            <option value="<?= htmlspecialchars($c['slug']) ?>" <?= ($companyA && $companyA['slug'] === $c['slug']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="compare-vs">VS</div>
                    <div class="compare-select-group">
                        <label>Company B</label>
                        <select name="b" class="compare-select">
                            <option value="">Select Company...</option>
                            <?php foreach ($allCompanies as $c): ?>
                            <option value="<?= htmlspecialchars($c['slug']) ?>" <?= ($companyB && $companyB['slug'] === $c['slug']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn--primary">Compare</button>
                </div>
            </form>
        </div>

        <?php if ($companyA && $companyB):
            $scoresA = getAllScores($companyA);
            $scoresB = getAllScores($companyB);
            $winnerSlug = $scoresA['overall'] >= $scoresB['overall'] ? $companyA['slug'] : $companyB['slug'];
            $winnerName = $scoresA['overall'] >= $scoresB['overall'] ? $companyA['name'] : $companyB['name'];
        ?>

        <!-- Winner Banner -->
        <div class="winner-banner">
            <div class="winner-banner__icon">🏆</div>
            <div class="winner-banner__text">
                <strong><?= htmlspecialchars($winnerName) ?></strong> wins this comparison
                <span class="winner-score"><?= number_format(max($scoresA['overall'], $scoresB['overall']), 1) ?>/10 overall score</span>
            </div>
        </div>

        <!-- Comparison Table -->
        <div class="compare-table-wrapper">
            <table class="compare-table">
                <thead>
                    <tr>
                        <th class="compare-table__metric">Metric</th>
                        <th class="compare-table__company <?= $winnerSlug === $companyA['slug'] ? 'compare-table__company--winner' : '' ?>">
                            <?= companyLogo($companyA, 'small') ?>
                            <span><?= htmlspecialchars($companyA['name']) ?></span>
                            <?php if ($winnerSlug === $companyA['slug']): ?><span class="winner-crown">👑</span><?php endif; ?>
                        </th>
                        <th class="compare-table__company <?= $winnerSlug === $companyB['slug'] ? 'compare-table__company--winner' : '' ?>">
                            <?= companyLogo($companyB, 'small') ?>
                            <span><?= htmlspecialchars($companyB['name']) ?></span>
                            <?php if ($winnerSlug === $companyB['slug']): ?><span class="winner-crown">👑</span><?php endif; ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="compare-row compare-row--highlight">
                        <td>Overall Intelligence Score</td>
                        <td class="<?= $scoresA['overall'] >= $scoresB['overall'] ? 'compare-cell--winner' : '' ?>">
                            <span class="compare-score <?= scoreColor($scoresA['overall']) ?>"><?= number_format($scoresA['overall'], 1) ?></span>
                        </td>
                        <td class="<?= $scoresB['overall'] >= $scoresA['overall'] ? 'compare-cell--winner' : '' ?>">
                            <span class="compare-score <?= scoreColor($scoresB['overall']) ?>"><?= number_format($scoresB['overall'], 1) ?></span>
                        </td>
                    </tr>

                    <?php
                    $scoreMetrics = [
                        'financial_exposure' => 'Financial Exposure',
                        'deposit_flexibility' => 'Deposit Flexibility',
                        'price_competitiveness' => 'Price Competitiveness',
                        'airport_coverage' => 'Airport Coverage',
                        'fleet_quality' => 'Fleet Quality',
                        'transparency' => 'Transparency',
                        'refund_speed' => 'Refund Speed',
                        'cross_border' => 'Cross-Border',
                        'customer_satisfaction' => 'Customer Satisfaction',
                    ];
                    foreach ($scoreMetrics as $key => $label):
                        $aWins = $scoresA[$key] >= $scoresB[$key];
                        $bWins = $scoresB[$key] >= $scoresA[$key];
                    ?>
                    <tr class="compare-row">
                        <td><?= htmlspecialchars($label) ?></td>
                        <td class="<?= $aWins ? 'compare-cell--winner' : '' ?>">
                            <span class="compare-score <?= scoreColor($scoresA[$key]) ?>"><?= number_format($scoresA[$key], 1) ?></span>
                        </td>
                        <td class="<?= $bWins ? 'compare-cell--winner' : '' ?>">
                            <span class="compare-score <?= scoreColor($scoresB[$key]) ?>"><?= number_format($scoresB[$key], 1) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <tr class="compare-row compare-row--section">
                        <td colspan="3"><strong>Financial Data</strong></td>
                    </tr>
                    <tr class="compare-row">
                        <td>Avg Deposit</td>
                        <td class="<?= $companyA['deposit_avg'] <= $companyB['deposit_avg'] ? 'compare-cell--winner' : '' ?>"><?= formatZAR($companyA['deposit_avg']) ?></td>
                        <td class="<?= $companyB['deposit_avg'] <= $companyA['deposit_avg'] ? 'compare-cell--winner' : '' ?>"><?= formatZAR($companyB['deposit_avg']) ?></td>
                    </tr>
                    <tr class="compare-row">
                        <td>Deposit Range</td>
                        <td><?= formatZAR($companyA['deposit_min']) ?> – <?= formatZAR($companyA['deposit_max']) ?></td>
                        <td><?= formatZAR($companyB['deposit_min']) ?> – <?= formatZAR($companyB['deposit_max']) ?></td>
                    </tr>
                    <tr class="compare-row">
                        <td>Avg Excess</td>
                        <td class="<?= $companyA['excess_avg'] <= $companyB['excess_avg'] ? 'compare-cell--winner' : '' ?>"><?= formatZAR($companyA['excess_avg']) ?></td>
                        <td class="<?= $companyB['excess_avg'] <= $companyA['excess_avg'] ? 'compare-cell--winner' : '' ?>"><?= formatZAR($companyB['excess_avg']) ?></td>
                    </tr>
                    <tr class="compare-row">
                        <td>Refund Speed</td>
                        <td><?= htmlspecialchars($companyA['refund_speed']) ?></td>
                        <td><?= htmlspecialchars($companyB['refund_speed']) ?></td>
                    </tr>
                    <tr class="compare-row">
                        <td>Deposit Type</td>
                        <td><?= htmlspecialchars(getDepositTypeLabel($companyA)) ?></td>
                        <td><?= htmlspecialchars(getDepositTypeLabel($companyB)) ?></td>
                    </tr>
                    <tr class="compare-row">
                        <td>Excess Reduction</td>
                        <td><?= ($companyA['costs']['excess_reduction']['available'] ?? false) ? '✅ Available' : '❌ No' ?></td>
                        <td><?= ($companyB['costs']['excess_reduction']['available'] ?? false) ? '✅ Available' : '❌ No' ?></td>
                    </tr>

                    <tr class="compare-row compare-row--section">
                        <td colspan="3"><strong>Coverage</strong></td>
                    </tr>
                    <tr class="compare-row">
                        <td>Total Locations</td>
                        <td class="<?= $companyA['locations_count'] >= $companyB['locations_count'] ? 'compare-cell--winner' : '' ?>"><?= $companyA['locations_count'] ?> branches</td>
                        <td class="<?= $companyB['locations_count'] >= $companyA['locations_count'] ? 'compare-cell--winner' : '' ?>"><?= $companyB['locations_count'] ?> branches</td>
                    </tr>
                    <tr class="compare-row">
                        <td>Major Airports</td>
                        <td class="<?= count($companyA['major_airports']) >= count($companyB['major_airports']) ? 'compare-cell--winner' : '' ?>"><?= count($companyA['major_airports']) ?> airports</td>
                        <td class="<?= count($companyB['major_airports']) >= count($companyA['major_airports']) ? 'compare-cell--winner' : '' ?>"><?= count($companyB['major_airports']) ?> airports</td>
                    </tr>
                    <tr class="compare-row">
                        <td>Mileage Policy</td>
                        <td><?= htmlspecialchars(getMileageLabel($companyA['mileage_policy'])) ?></td>
                        <td><?= htmlspecialchars(getMileageLabel($companyB['mileage_policy'])) ?></td>
                    </tr>
                    <tr class="compare-row">
                        <td>Cross-Border</td>
                        <td><?= $companyA['cross_border_allowed'] ? '✅ Allowed' : '❌ No' ?></td>
                        <td><?= $companyB['cross_border_allowed'] ? '✅ Allowed' : '❌ No' ?></td>
                    </tr>
                    <tr class="compare-row">
                        <td>Loyalty Program</td>
                        <td><?= ($companyA['services']['loyalty_program_available'] ?? false) ? '✅ Yes' : '❌ No' ?></td>
                        <td><?= ($companyB['services']['loyalty_program_available'] ?? false) ? '✅ Yes' : '❌ No' ?></td>
                    </tr>
                    <tr class="compare-row">
                        <td>24/7 Support</td>
                        <td><?= ($companyA['services']['support_24_hour'] ?? false) ? '✅ Yes' : '❌ No' ?></td>
                        <td><?= ($companyB['services']['support_24_hour'] ?? false) ? '✅ Yes' : '❌ No' ?></td>
                    </tr>

                    <tr class="compare-row compare-row--section">
                        <td colspan="3"><strong>Pricing</strong></td>
                    </tr>
                    <tr class="compare-row">
                        <td>Economy Rate</td>
                        <td><?= formatZAR($companyA['economy_rate_min']) ?> – <?= formatZAR($companyA['economy_rate_max']) ?></td>
                        <td><?= formatZAR($companyB['economy_rate_min']) ?> – <?= formatZAR($companyB['economy_rate_max']) ?></td>
                    </tr>
                    <tr class="compare-row">
                        <td>Starting From</td>
                        <td class="<?= $companyA['economy_rate_min'] <= $companyB['economy_rate_min'] ? 'compare-cell--winner' : '' ?>"><?= formatZAR($companyA['economy_rate_min']) ?>/day</td>
                        <td class="<?= $companyB['economy_rate_min'] <= $companyA['economy_rate_min'] ? 'compare-cell--winner' : '' ?>"><?= formatZAR($companyB['economy_rate_min']) ?>/day</td>
                    </tr>

                    <tr class="compare-row compare-row--section">
                        <td colspan="3"><strong>Reviews</strong></td>
                    </tr>
                    <tr class="compare-row">
                        <td>Google Score</td>
                        <td class="<?= $companyA['google_score'] >= $companyB['google_score'] ? 'compare-cell--winner' : '' ?>"><?= $companyA['google_score'] ?>/5</td>
                        <td class="<?= $companyB['google_score'] >= $companyA['google_score'] ? 'compare-cell--winner' : '' ?>"><?= $companyB['google_score'] ?>/5</td>
                    </tr>
                    <tr class="compare-row">
                        <td>Trustpilot Score</td>
                        <td class="<?= $companyA['trustpilot_score'] >= $companyB['trustpilot_score'] ? 'compare-cell--winner' : '' ?>"><?= $companyA['trustpilot_score'] ?>/5</td>
                        <td class="<?= $companyB['trustpilot_score'] >= $companyA['trustpilot_score'] ? 'compare-cell--winner' : '' ?>"><?= $companyB['trustpilot_score'] ?>/5</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="compare-cta">
            <a href="<?= url('company', $companyA['slug']) ?>" class="btn btn--outline">Full <?= htmlspecialchars($companyA['name']) ?> Report</a>
            <a href="<?= url('company', $companyB['slug']) ?>" class="btn btn--outline">Full <?= htmlspecialchars($companyB['name']) ?> Report</a>
        </div>

        <?php else: ?>
        <div class="compare-prompt">
            <p>Select two companies above to compare their intelligence scores, financial data, and policies side-by-side.</p>
            <div class="compare-quick-links">
                <h3>Quick Comparisons</h3>
                <div class="quick-compare-grid">
                    <?php
                    $quickPairs = [
                        ['avis', 'budget'],
                        ['hertz', 'europcar'],
                        ['tempest', 'first-car-rental'],
                        ['avis', 'hertz'],
                        ['sixt', 'europcar'],
                        ['woodford', 'bidvest-car-rental'],
                    ];
                    foreach ($quickPairs as $pair):
                        $ca = getCompanyBySlug($pair[0]);
                        $cb = getCompanyBySlug($pair[1]);
                        if ($ca && $cb):
                    ?>
                    <a href="<?= url('compare', '', ['a' => $pair[0], 'b' => $pair[1]]) ?>" class="quick-compare-card">
                        <?= htmlspecialchars($ca['name']) ?> vs <?= htmlspecialchars($cb['name']) ?>
                    </a>
                    <?php endif; endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <!-- Vehicle Comparison -->
        <div class="compare-selector">
            <form method="GET" action="/">
                <input type="hidden" name="page" value="compare">
                <div class="compare-selects">
                    <div class="compare-select-group">
                        <label>Vehicle A</label>
                        <select name="va" class="compare-select">
                            <option value="">Select Vehicle...</option>
                            <?php foreach ($allVehicles as $v): ?>
                            <option value="<?= htmlspecialchars($v['slug']) ?>" <?= ($vehicleA && $vehicleA['slug'] === $v['slug']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($v['name']) ?> (<?= htmlspecialchars($v['segment']) ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="compare-vs">VS</div>
                    <div class="compare-select-group">
                        <label>Vehicle B</label>
                        <select name="vb" class="compare-select">
                            <option value="">Select Vehicle...</option>
                            <?php foreach ($allVehicles as $v): ?>
                            <option value="<?= htmlspecialchars($v['slug']) ?>" <?= ($vehicleB && $vehicleB['slug'] === $v['slug']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($v['name']) ?> (<?= htmlspecialchars($v['segment']) ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn--primary">Compare</button>
                </div>
            </form>
        </div>

        <?php if ($vehicleA && $vehicleB): ?>
        <div class="compare-table-wrapper">
            <table class="compare-table">
                <thead>
                    <tr>
                        <th>Specification</th>
                        <th><?= htmlspecialchars($vehicleA['name']) ?></th>
                        <th><?= htmlspecialchars($vehicleB['name']) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>Segment</td><td><?= htmlspecialchars($vehicleA['segment']) ?></td><td><?= htmlspecialchars($vehicleB['segment']) ?></td></tr>
                    <tr><td>Doors</td><td><?= $vehicleA['doors'] ?></td><td><?= $vehicleB['doors'] ?></td></tr>
                    <tr><td>Seating</td>
                        <td class="<?= $vehicleA['seating_capacity'] >= $vehicleB['seating_capacity'] ? 'compare-cell--winner' : '' ?>"><?= $vehicleA['seating_capacity'] ?></td>
                        <td class="<?= $vehicleB['seating_capacity'] >= $vehicleA['seating_capacity'] ? 'compare-cell--winner' : '' ?>"><?= $vehicleB['seating_capacity'] ?></td>
                    </tr>
                    <tr><td>Boot Space</td>
                        <td class="<?= $vehicleA['boot_space_litres'] >= $vehicleB['boot_space_litres'] ? 'compare-cell--winner' : '' ?>"><?= htmlspecialchars($vehicleA['boot_space_display'] ?? $vehicleA['boot_space_litres'] . 'L') ?></td>
                        <td class="<?= $vehicleB['boot_space_litres'] >= $vehicleA['boot_space_litres'] ? 'compare-cell--winner' : '' ?>"><?= htmlspecialchars($vehicleB['boot_space_display'] ?? $vehicleB['boot_space_litres'] . 'L') ?></td>
                    </tr>
                    <tr><td>Fuel Type</td><td><?= htmlspecialchars($vehicleA['fuel_type']) ?></td><td><?= htmlspecialchars($vehicleB['fuel_type']) ?></td></tr>
                    <tr><td>Transmission</td><td><?= htmlspecialchars($vehicleA['transmission']) ?></td><td><?= htmlspecialchars($vehicleB['transmission']) ?></td></tr>
                    <tr><td>Demand</td>
                        <td class="<?= $vehicleA['demand_percent'] >= $vehicleB['demand_percent'] ? 'compare-cell--winner' : '' ?>"><?= $vehicleA['demand_percent'] ?>%</td>
                        <td class="<?= $vehicleB['demand_percent'] >= $vehicleA['demand_percent'] ? 'compare-cell--winner' : '' ?>"><?= $vehicleB['demand_percent'] ?>%</td>
                    </tr>
                    <tr><td>Avg Daily Rate</td>
                        <td class="<?= $vehicleA['avg_daily_rate_ZAR'] <= $vehicleB['avg_daily_rate_ZAR'] ? 'compare-cell--winner' : '' ?>"><?= formatZAR($vehicleA['avg_daily_rate_ZAR']) ?></td>
                        <td class="<?= $vehicleB['avg_daily_rate_ZAR'] <= $vehicleA['avg_daily_rate_ZAR'] ? 'compare-cell--winner' : '' ?>"><?= formatZAR($vehicleB['avg_daily_rate_ZAR']) ?></td>
                    </tr>
                    <tr><td>Companies Offering</td>
                        <td class="<?= count($vehicleA['companies'] ?? []) >= count($vehicleB['companies'] ?? []) ? 'compare-cell--winner' : '' ?>"><?= count($vehicleA['companies'] ?? []) ?></td>
                        <td class="<?= count($vehicleB['companies'] ?? []) >= count($vehicleA['companies'] ?? []) ? 'compare-cell--winner' : '' ?>"><?= count($vehicleB['companies'] ?? []) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="compare-prompt">
            <p>Select two vehicles above to compare their specifications side-by-side.</p>
        </div>
        <?php endif; // vehicleA && vehicleB ?>
        <?php endif; // mode === vehicle ?>
    </div>
</section>
