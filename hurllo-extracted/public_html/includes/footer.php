</main><!-- /#main-content -->

<!-- Footer -->
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <!-- Column 1: Brand -->
            <div class="footer-col footer-col--brand">
                <a href="/" class="footer-logo">
                    <span class="logo-icon">⚡</span>
                    <span class="logo-text">Hurllo</span>
                </a>
                <p class="footer-tagline">South Africa's Car Hire Intelligence Platform. Not a booking site. Not an affiliate. Pure intelligence across <?= count(getCompanies()) ?> companies.</p>
                <div class="footer-badges">
                    <span class="footer-badge">🇿🇦 South Africa</span>
                    <span class="footer-badge">📊 Data-Driven</span>
                    <span class="footer-badge">🔍 Independent</span>
                </div>
            </div>

            <!-- Column 2: Directory -->
            <div class="footer-col">
                <h3 class="footer-heading">Company Directory</h3>
                <ul class="footer-links">
                    <?php
                    $footerCompanies = getCompanies();
                    foreach (array_slice($footerCompanies, 0, 8) as $fc):
                    ?>
                    <li><a href="<?= url('company', $fc['slug']) ?>"><?= htmlspecialchars($fc['name']) ?> Car Hire</a></li>
                    <?php endforeach; ?>
                    <li><a href="<?= url('directory') ?>">View All <?= count($footerCompanies) ?> Companies →</a></li>
                </ul>
            </div>

            <!-- Column 3: Intelligence -->
            <div class="footer-col">
                <h3 class="footer-heading">Intelligence Hub</h3>
                <ul class="footer-links">
                    <li><a href="<?= url('rankings') ?>">Company Rankings</a></li>
                    <li><a href="<?= url('compare') ?>">Compare Companies</a></li>
                    <li><a href="<?= url('vehicles') ?>">Vehicle Hub</a></li>
                    <li><a href="<?= url('directory', '', ['sort' => 'lowest_exposure']) ?>">Lowest Financial Exposure</a></li>
                    <li><a href="<?= url('directory', '', ['sort' => 'fastest_refund']) ?>">Fastest Refund</a></li>
                    <li><a href="<?= url('directory', '', ['sort' => 'lowest_price']) ?>">Lowest Price</a></li>
                    <li><a href="<?= url('directory', '', ['filter_cross_border' => '1']) ?>">Cross-Border Allowed</a></li>
                </ul>
            </div>

            <!-- Column 4: Education -->
            <div class="footer-col">
                <h3 class="footer-heading">Car Hire Guide</h3>
                <ul class="footer-links">
                    <li><a href="<?= url('education', '', ['topic' => 'how-car-hire-works']) ?>">How Car Hire Works</a></li>
                    <li><a href="<?= url('education', '', ['topic' => 'financial-exposure']) ?>">Financial Exposure Guide</a></li>
                    <li><a href="<?= url('education', '', ['topic' => 'refund-guide']) ?>">Refund Guide</a></li>
                    <li><a href="<?= url('education', '', ['topic' => 'cross-border']) ?>">Cross-Border Guide</a></li>
                    <li><a href="<?= url('education', '', ['topic' => 'ev-guide']) ?>">EV Rental Guide</a></li>
                    <li><a href="<?= url('education', '', ['topic' => 'seasonal-demand']) ?>">Seasonal Demand Guide</a></li>
                </ul>
            </div>
        </div>

        <!-- SEO Location Links -->
        <div class="footer-locations">
            <h3 class="footer-heading footer-heading--small">Car Hire by Location</h3>
            <div class="footer-location-grid">
                <a href="<?= url('directory', '', ['location' => 'johannesburg']) ?>">Johannesburg Car Hire</a>
                <a href="<?= url('directory', '', ['location' => 'cape-town']) ?>">Cape Town Car Hire</a>
                <a href="<?= url('directory', '', ['location' => 'durban']) ?>">Durban Car Hire</a>
                <a href="<?= url('directory', '', ['location' => 'or-tambo']) ?>">OR Tambo Airport Car Hire</a>
                <a href="<?= url('directory', '', ['location' => 'cape-town-airport']) ?>">Cape Town Airport Car Hire</a>
                <a href="<?= url('directory', '', ['location' => 'king-shaka']) ?>">King Shaka Airport Car Hire</a>
                <a href="<?= url('directory', '', ['location' => 'pretoria']) ?>">Pretoria Car Hire</a>
                <a href="<?= url('directory', '', ['location' => 'port-elizabeth']) ?>">Port Elizabeth Car Hire</a>
                <a href="<?= url('directory', '', ['location' => 'lanseria']) ?>">Lanseria Airport Car Hire</a>
                <a href="<?= url('directory', '', ['location' => 'bloemfontein']) ?>">Bloemfontein Car Hire</a>
                <a href="<?= url('directory', '', ['location' => 'east-london']) ?>">East London Car Hire</a>
                <a href="<?= url('directory', '', ['location' => 'kruger']) ?>">Kruger Park Car Hire</a>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="footer-bottom-left">
                <p>&copy; <?= date('Y') ?> Hurllo. All rights reserved. Independent intelligence platform.</p>
            </div>
            <div class="footer-bottom-right">
                <p class="footer-disclaimer">Hurllo is an independent intelligence and comparison platform. We do not process bookings or receive commissions. All data is independently researched.</p>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top -->
<button class="back-to-top" id="back-to-top" aria-label="Back to top">↑</button>

<script src="/assets/js/main.js"></script>
</body>
</html>
