<?php
/**
 * Hurllo - Car Hire Intelligence Platform
 * Dynamic Scoring Engine - Adapted for user JSON structure
 * All scores calculated dynamically from JSON values - no hardcoded scores
 */

if (!defined('CARVIO_ROOT')) {
    define('CARVIO_ROOT', dirname(__DIR__));
}

/**
 * Calculate Financial Exposure Score (out of 10)
 * Lower deposit + lower excess = higher score
 */
function calculateFinancialExposureScore(array $company): float {
    $deposit = $company['deposit_avg'] ?? 3000;
    $excessAvg = $company['excess_avg'] ?? 15000;

    // Deposit scoring (max 5 points)
    if ($deposit <= 2000) $depositScore = 5.0;
    elseif ($deposit <= 2500) $depositScore = 4.5;
    elseif ($deposit <= 3000) $depositScore = 4.0;
    elseif ($deposit <= 3500) $depositScore = 3.0;
    elseif ($deposit <= 4000) $depositScore = 2.0;
    elseif ($deposit <= 4500) $depositScore = 1.5;
    else $depositScore = 1.0;

    // Excess scoring (max 5 points)
    if ($excessAvg <= 8000) $excessScore = 5.0;
    elseif ($excessAvg <= 10000) $excessScore = 4.5;
    elseif ($excessAvg <= 12000) $excessScore = 4.0;
    elseif ($excessAvg <= 14000) $excessScore = 3.5;
    elseif ($excessAvg <= 16000) $excessScore = 3.0;
    elseif ($excessAvg <= 18000) $excessScore = 2.0;
    else $excessScore = 1.0;

    return round($depositScore + $excessScore, 1);
}

/**
 * Calculate Deposit Flexibility Score (out of 10)
 */
function calculateDepositFlexibilityScore(array $company): float {
    $score = 5.0;

    // Debit card acceptance (+3 points)
    if ($company['supports_debit_card'] ?? false) {
        $score += 3.0;
    }

    // Deposit amount modifier
    $deposit = $company['deposit_avg'] ?? 3000;
    if ($deposit <= 2000) $score += 2.0;
    elseif ($deposit <= 2500) $score += 1.5;
    elseif ($deposit <= 3000) $score += 1.0;
    elseif ($deposit <= 3500) $score += 0.5;
    elseif ($deposit > 4000) $score -= 1.0;

    return round(min(10.0, max(1.0, $score)), 1);
}

/**
 * Calculate Price Competitiveness Score (out of 10)
 */
function calculatePriceCompetitivenessScore(array $company): float {
    $avgPrice = $company['economy_rate_avg'] ?? 500;
    $marketAvg = 510; // Market average from user's JSON data

    $ratio = $avgPrice / $marketAvg;

    if ($ratio <= 0.75) return 10.0;
    if ($ratio <= 0.85) return 9.0;
    if ($ratio <= 0.92) return 8.0;
    if ($ratio <= 1.0) return 7.0;
    if ($ratio <= 1.08) return 6.0;
    if ($ratio <= 1.15) return 5.0;
    if ($ratio <= 1.25) return 4.0;
    if ($ratio <= 1.4) return 3.0;
    return 2.0;
}

/**
 * Calculate Airport Coverage Score (out of 10)
 */
function calculateAirportCoverageScore(array $company): float {
    $airports = count($company['major_airports'] ?? []);
    $locations = $company['locations_count'] ?? 0;

    // Airport score (max 6 points)
    $airportScore = min(6.0, $airports * 1.2);

    // Location score (max 4 points)
    if ($locations >= 15) $locationScore = 4.0;
    elseif ($locations >= 10) $locationScore = 3.5;
    elseif ($locations >= 8) $locationScore = 3.0;
    elseif ($locations >= 6) $locationScore = 2.5;
    elseif ($locations >= 4) $locationScore = 2.0;
    elseif ($locations >= 2) $locationScore = 1.5;
    else $locationScore = 1.0;

    return round(min(10.0, $airportScore + $locationScore), 1);
}

/**
 * Calculate Fleet Quality Score (out of 10)
 */
function calculateFleetQualityScore(array $company): float {
    $brands = count($company['vehicle_brands_available'] ?? []);
    $longTerm = $company['long_term_available'] ?? false;
    $loyalty = $company['services']['loyalty_program_available'] ?? false;
    $roadside = $company['services']['roadside_assistance_included'] ?? false;

    // Brand variety base score
    if ($brands >= 7) $score = 7.0;
    elseif ($brands >= 5) $score = 6.0;
    elseif ($brands >= 4) $score = 5.0;
    elseif ($brands >= 3) $score = 4.0;
    else $score = 3.0;

    if ($longTerm) $score += 1.0;
    if ($loyalty) $score += 1.0;
    if ($roadside) $score += 1.0;

    return round(min(10.0, $score), 1);
}

/**
 * Calculate Transparency Score (out of 10)
 */
function calculateTransparencyScore(array $company): float {
    $score = 5.0;

    // Mileage policy clarity
    if (($company['mileage_policy'] ?? '') === 'unlimited') $score += 1.5;
    elseif (($company['mileage_policy'] ?? '') === 'capped') $score += 0.5;

    // Cross-border clarity
    if ($company['cross_border_allowed'] ?? false) $score += 1.0;

    // Review scores indicate transparency
    $combined = $company['combined_score'] ?? 3.75;
    if ($combined >= 4.0) $score += 1.5;
    elseif ($combined >= 3.8) $score += 1.0;
    elseif ($combined >= 3.5) $score += 0.5;

    // Excess reduction available
    if ($company['costs']['excess_reduction']['available'] ?? false) $score += 1.0;

    return round(min(10.0, max(1.0, $score)), 1);
}

/**
 * Calculate Refund Speed Score (out of 10)
 */
function calculateRefundSpeedScore(array $company): float {
    $refundSpeed = $company['refund_speed'] ?? '5-10 business days';
    $parts = explode('-', $refundSpeed);
    $minDays = (int)$parts[0];

    if ($minDays <= 1) return 10.0;
    if ($minDays <= 2) return 9.0;
    if ($minDays <= 3) return 8.0;
    if ($minDays <= 4) return 7.0;
    if ($minDays <= 5) return 6.0;
    if ($minDays <= 7) return 5.0;
    if ($minDays <= 10) return 3.5;
    return 2.0;
}

/**
 * Calculate Cross-Border Score (out of 10)
 */
function calculateCrossBorderScore(array $company): float {
    $allowed = $company['cross_border_allowed'] ?? false;
    if (!$allowed) return 2.0;

    // If allowed, score based on locations (more locations = more cross-border capability)
    $locations = $company['locations_count'] ?? 0;
    if ($locations >= 10) return 9.0;
    if ($locations >= 8) return 8.0;
    if ($locations >= 6) return 7.0;
    if ($locations >= 4) return 6.0;
    return 5.0;
}

/**
 * Calculate Customer Satisfaction Score (out of 10)
 */
function calculateCustomerSatisfactionScore(array $company): float {
    $combined = $company['combined_score'] ?? 3.75;
    // Scale from 1-5 to 1-10
    return round(min(10.0, $combined * 2), 1);
}

/**
 * Calculate Overall Intelligence Score (out of 10)
 * Weighted combination of all scores
 */
function calculateOverallScore(array $company): float {
    $weights = [
        'financial_exposure' => 0.20,
        'deposit_flexibility' => 0.15,
        'price_competitiveness' => 0.20,
        'airport_coverage' => 0.15,
        'fleet_quality' => 0.10,
        'transparency' => 0.10,
        'refund_speed' => 0.10,
    ];

    $scores = [
        'financial_exposure' => calculateFinancialExposureScore($company),
        'deposit_flexibility' => calculateDepositFlexibilityScore($company),
        'price_competitiveness' => calculatePriceCompetitivenessScore($company),
        'airport_coverage' => calculateAirportCoverageScore($company),
        'fleet_quality' => calculateFleetQualityScore($company),
        'transparency' => calculateTransparencyScore($company),
        'refund_speed' => calculateRefundSpeedScore($company),
    ];

    $total = 0;
    foreach ($weights as $key => $weight) {
        $total += $scores[$key] * $weight;
    }

    return round($total, 1);
}

/**
 * Get all scores for a company
 */
function getAllScores(array $company): array {
    return [
        'overall' => calculateOverallScore($company),
        'financial_exposure' => calculateFinancialExposureScore($company),
        'deposit_flexibility' => calculateDepositFlexibilityScore($company),
        'price_competitiveness' => calculatePriceCompetitivenessScore($company),
        'airport_coverage' => calculateAirportCoverageScore($company),
        'fleet_quality' => calculateFleetQualityScore($company),
        'transparency' => calculateTransparencyScore($company),
        'refund_speed' => calculateRefundSpeedScore($company),
        'cross_border' => calculateCrossBorderScore($company),
        'customer_satisfaction' => calculateCustomerSatisfactionScore($company),
    ];
}

/**
 * Get score color class
 */
function scoreColor(float $score): string {
    if ($score >= 8.0) return 'score--excellent';
    if ($score >= 6.5) return 'score--good';
    if ($score >= 5.0) return 'score--average';
    if ($score >= 3.5) return 'score--poor';
    return 'score--bad';
}

/**
 * Get score label
 */
function scoreLabel(float $score): string {
    if ($score >= 8.0) return 'Excellent';
    if ($score >= 6.5) return 'Good';
    if ($score >= 5.0) return 'Average';
    if ($score >= 3.5) return 'Below Average';
    return 'Poor';
}

/**
 * Render score bar HTML
 */
function scoreBar(float $score, string $label = '', bool $animate = true): string {
    $percent = ($score / 10) * 100;
    $colorClass = scoreColor($score);
    $animClass = $animate ? ' score-bar--animate' : '';

    $html = '<div class="score-bar-wrapper">';
    if ($label) {
        $html .= '<div class="score-bar-label">' . htmlspecialchars($label) . '</div>';
    }
    $html .= '<div class="score-bar' . $animClass . '">';
    $html .= '<div class="score-bar__fill ' . $colorClass . '" style="width: ' . $percent . '%" data-score="' . $score . '"></div>';
    $html .= '</div>';
    $html .= '<div class="score-bar-value ' . $colorClass . '">' . number_format($score, 1) . '</div>';
    $html .= '</div>';

    return $html;
}

/**
 * Render score circle HTML
 */
function scoreCircle(float $score, string $label = ''): string {
    $colorClass = scoreColor($score);
    $html = '<div class="score-circle ' . $colorClass . '" data-score="' . $score . '">';
    $html .= '<div class="score-circle__value">' . number_format($score, 1) . '</div>';
    if ($label) {
        $html .= '<div class="score-circle__label">' . htmlspecialchars($label) . '</div>';
    }
    $html .= '</div>';
    return $html;
}

/**
 * Get ranked companies
 */
function getRankedCompanies(): array {
    if (!function_exists('getCompanies')) {
        require_once __DIR__ . '/functions.php';
    }
    $companies = getCompanies();
    usort($companies, function($a, $b) {
        return calculateOverallScore($b) <=> calculateOverallScore($a);
    });
    return $companies;
}
