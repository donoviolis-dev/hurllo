<?php
$topic = preg_replace('/[^a-z0-9\-]/', '', $_GET['topic'] ?? '');

$topics = [
    'how-car-hire-works' => [
        'title' => 'How Car Hire Works in South Africa',
        'icon' => '🚗',
        'content' => [
            'intro' => 'Renting a car in South Africa involves several key steps and financial commitments that every traveller should understand before signing any agreement.',
            'sections' => [
                [
                    'heading' => 'The Booking Process',
                    'body' => 'Car hire in South Africa can be booked directly through company websites, at airport counters, or via third-party platforms. Always compare prices across multiple channels as rates can vary significantly. Direct bookings often offer more flexibility for modifications and cancellations.'
                ],
                [
                    'heading' => 'What You Need to Rent',
                    'body' => 'To rent a car in South Africa you typically need: a valid driver\'s licence (international licence recommended for foreign visitors), a credit card for the deposit (some companies accept debit cards), your passport or ID, and proof of accommodation or travel itinerary.'
                ],
                [
                    'heading' => 'Understanding the Rental Agreement',
                    'body' => 'The rental agreement is a legally binding contract. Key items to check include: the daily rate, mileage policy (unlimited vs per-km), fuel policy (full-to-full is standard), insurance inclusions, excess amount, and any additional driver fees.'
                ],
                [
                    'heading' => 'Collecting and Returning the Vehicle',
                    'body' => 'Always inspect the vehicle thoroughly before driving away and document any existing damage with photos. Return the vehicle with a full tank (unless pre-paid fuel option was selected) and at the agreed time to avoid additional charges.'
                ]
            ]
        ]
    ],
    'financial-exposure' => [
        'title' => 'Understanding Financial Exposure in Car Hire',
        'icon' => '💰',
        'content' => [
            'intro' => 'Financial exposure is the total amount of money you could potentially lose or have held during a car hire. Understanding this is critical to making an informed decision.',
            'sections' => [
                [
                    'heading' => 'What is a Security Deposit?',
                    'body' => 'A security deposit is a refundable amount held on your credit card at the start of the rental. In South Africa, deposits typically range from R1,500 to R5,000 depending on the company and vehicle class. This money is not charged — it is blocked on your card and released after the vehicle is returned undamaged.'
                ],
                [
                    'heading' => 'What is an Excess?',
                    'body' => 'The excess (also called a deductible) is the maximum amount you would pay in the event of an accident or damage to the vehicle. South African excesses typically range from R10,000 to R30,000. This is separate from the deposit and represents your maximum liability.'
                ],
                [
                    'heading' => 'Calculating Your Total Exposure',
                    'body' => 'Your total financial exposure = Deposit + Excess. For example, if a company charges a R3,000 deposit and has a R20,000 excess, your maximum exposure is R23,000. Use our Risk Simulator on the homepage to calculate your specific exposure.'
                ],
                [
                    'heading' => 'How to Reduce Financial Exposure',
                    'body' => 'Options to reduce exposure include: purchasing Collision Damage Waiver (CDW) insurance, choosing companies with lower deposits and excesses, using a credit card with built-in rental car insurance, or purchasing standalone travel insurance that covers rental car excess.'
                ]
            ]
        ]
    ],
    'refund-guide' => [
        'title' => 'Car Hire Deposit Refund Guide South Africa',
        'icon' => '⏱️',
        'content' => [
            'intro' => 'Getting your deposit back after a car hire can be frustrating if you don\'t know what to expect. Here\'s everything you need to know about deposit refunds in South Africa.',
            'sections' => [
                [
                    'heading' => 'How Long Does a Refund Take?',
                    'body' => 'Deposit refund times in South Africa vary significantly by company. The best companies refund within 2-4 business days. Average companies take 5-7 business days. Some companies can take up to 14 business days. Always check the refund policy before booking.'
                ],
                [
                    'heading' => 'Why Refunds Are Delayed',
                    'body' => 'Common reasons for delayed refunds include: vehicle inspection taking time, toll fee reconciliation, fuel charge processing, damage assessment, and administrative processing. Some companies batch process refunds weekly.'
                ],
                [
                    'heading' => 'How to Speed Up Your Refund',
                    'body' => 'To get your deposit back faster: return the vehicle on time, ensure the tank is full, document the return with photos, get a written confirmation of return, and follow up with the company if the refund hasn\'t arrived within their stated timeframe.'
                ],
                [
                    'heading' => 'What to Do If Your Refund Is Late',
                    'body' => 'If your deposit hasn\'t been refunded within the stated timeframe: contact the company\'s customer service, escalate to a manager if needed, contact your bank to dispute the hold, and as a last resort, contact the Consumer Protection Act authorities.'
                ]
            ]
        ]
    ],
    'cross-border' => [
        'title' => 'Cross-Border Car Hire Guide South Africa',
        'icon' => '🌍',
        'content' => [
            'intro' => 'Taking a rental car across South Africa\'s borders requires specific permissions and documentation. Not all companies allow cross-border travel, and those that do have specific requirements.',
            'sections' => [
                [
                    'heading' => 'Which Countries Can You Drive To?',
                    'body' => 'The most commonly permitted cross-border destinations from South Africa include: Namibia, Botswana, Lesotho, Swaziland (Eswatini), Zimbabwe, and Mozambique. Each company has different permitted countries, so always confirm before booking.'
                ],
                [
                    'heading' => 'Required Documentation',
                    'body' => 'For cross-border travel you typically need: a cross-border letter from the rental company (usually costs R500-R1,500), your passport, the vehicle\'s registration papers, proof of insurance valid in the destination country, and sometimes a carnet de passage for certain countries.'
                ],
                [
                    'heading' => 'Additional Costs',
                    'body' => 'Cross-border travel usually incurs additional fees including: cross-border permit fee (R500-R1,500), additional insurance premium, and sometimes a higher excess for cross-border travel. Always get a full cost breakdown before agreeing.'
                ],
                [
                    'heading' => 'Important Restrictions',
                    'body' => 'Be aware that: some companies prohibit cross-border travel entirely, some vehicles (especially luxury or premium) may not be permitted cross-border, and driving on unpaved roads may void your insurance in some countries.'
                ]
            ]
        ]
    ],
    'ev-guide' => [
        'title' => 'Electric Vehicle Car Hire Guide South Africa',
        'icon' => '⚡',
        'content' => [
            'intro' => 'Electric vehicle (EV) rentals are emerging in South Africa, but the market is still developing. Here\'s what you need to know before renting an EV in South Africa.',
            'sections' => [
                [
                    'heading' => 'EV Availability in South Africa',
                    'body' => 'EV rental availability in South Africa is currently limited, primarily available in major cities like Johannesburg and Cape Town. The most common EV rental options include the BMW i3, Nissan Leaf, and some Tesla models at premium rental companies.'
                ],
                [
                    'heading' => 'Charging Infrastructure',
                    'body' => 'South Africa\'s EV charging infrastructure is growing but still limited compared to Europe. Major cities have public charging stations, but rural areas and game reserves may have very limited or no charging options. Always plan your route carefully.'
                ],
                [
                    'heading' => 'Range Considerations',
                    'body' => 'South Africa\'s vast distances make range a critical consideration. Most EVs available for rental have a range of 200-400km per charge. For long-distance travel or game drives, a traditional petrol or diesel vehicle may be more practical.'
                ],
                [
                    'heading' => 'EV Rental Costs',
                    'body' => 'EV rentals in South Africa typically cost 20-40% more than equivalent petrol vehicles. However, electricity costs are significantly lower than petrol, so total trip costs may be comparable for shorter distances.'
                ]
            ]
        ]
    ],
    'seasonal-demand' => [
        'title' => 'Seasonal Demand Guide for Car Hire South Africa',
        'icon' => '📅',
        'content' => [
            'intro' => 'Car hire prices and availability in South Africa fluctuate significantly throughout the year. Understanding seasonal demand can help you save money and secure the vehicle you want.',
            'sections' => [
                [
                    'heading' => 'Peak Season (December - January)',
                    'body' => 'December and January are the busiest months for car hire in South Africa. School holidays, Christmas, and New Year drive extremely high demand. Prices can be 30-50% higher than off-peak. Book at least 4-6 weeks in advance for popular vehicle classes.'
                ],
                [
                    'heading' => 'High Season (July School Holidays)',
                    'body' => 'The July school holidays create a secondary peak in demand. SUVs and 7-seaters are particularly in demand for family travel. Book 3-4 weeks in advance to secure your preferred vehicle.'
                ],
                [
                    'heading' => 'Shoulder Season (March-May, September-November)',
                    'body' => 'These months offer a good balance of reasonable prices and good weather. September-October is particularly good for the Garden Route and Namaqualand wildflowers. Prices are typically 10-20% lower than peak season.'
                ],
                [
                    'heading' => 'Low Season (May-June)',
                    'body' => 'May and June offer the best deals on car hire in South Africa. Prices can be 20-30% lower than peak season. However, some coastal areas experience cold and wet weather. Kruger Park is excellent in winter as animals gather at water sources.'
                ]
            ]
        ]
    ],
    'tourist-guide' => [
        'title' => 'Complete Guide to Renting a Car in South Africa as a Tourist',
        'icon' => '🛂',
        'content' => [
            'intro' => 'Everything international visitors need to know before picking up a car in South Africa. From licence requirements to insurance options, this guide covers the essentials.',
            'sections' => [
                [
                    'heading' => 'Licence Requirements for International Visitors',
                    'body' => 'You can drive in South Africa with a valid foreign driving licence for up to 12 months. However, an International Driving Permit (IDP) is strongly recommended and required by many rental companies. The IDP serves as a certified translation of your licence and is recognised by all major rental companies. If your licence is not in English, carrying an official translation is also advisable. Some companies may also require an apostille or notarised translation depending on your country of origin.'
                ],
                [
                    'heading' => 'Age Restrictions and Young Driver Surcharges',
                    'body' => 'The minimum age to rent a car in South Africa is typically 21 years, though some premium providers may require drivers to be 23 or 25. Young drivers between 21-24 often pay a daily surcharge ranging from R150 to R500. Drivers over 75 may also face restrictions or additional screening. If you\'re under 25, consider budget-friendly economy cars as surcharges are usually lower on smaller vehicles. Some companies offer "young driver insurance" packages that can help manage these costs.'
                ],
                [
                    'heading' => 'Credit vs Debit Card Deposits for International Travellers',
                    'body' => 'Most South African rental companies require a credit card for the security deposit. The deposit is a pre-authorisation hold on your card, not an actual charge, and is released upon safe return of the vehicle. Debit cards are accepted at some smaller companies but typically require a higher deposit or proof of return travel arrangements. International visitors should notify their bank before travelling to prevent fraud alerts from blocking transactions. Consider carrying a secondary credit card as backup.'
                ],
                [
                    'heading' => 'What to Check at the Rental Counter',
                    'body' => 'Before driving away, thoroughly inspect the vehicle and document any existing damage with photos from multiple angles. Check tyre pressure, spare tyre availability, and ensure all lights are working. Confirm fuel policy (full-to-full is standard) and understand your excess amount. Ask for the emergency contact number and clarify what to do in case of an accident or breakdown. Request a written confirmation of any pre-existing damage to avoid disputes later.'
                ],
                [
                    'heading' => 'Insurance and Excess Reduction Options',
                    'body' => 'Basic insurance is usually included but has an excess (deductible) amount. You can reduce or eliminate this excess by purchasing Collision Damage Waiver (CDW) or Excess Reduction products, typically costing R50-R300 per day. Some travel credit cards offer complimentary rental car insurance — check your card benefits before purchasing additional coverage. Third-party excess insurance from providers like iCarhireinsurance can be cheaper than company options but may have specific conditions.'
                ],
                [
                    'heading' => 'Driving on South African Roads',
                    'body' => 'South Africans drive on the left side of the road. Most rental cars are left-hand drive, which takes adjustment for visitors from right-hand drive countries. Speed limits are 60km/h in urban areas, 100km/h on provincial roads, and 120km/h on highways. Gauteng uses electronic toll gates (e-tolls) — ask your rental company about e-tag availability. Be cautious of pedestrians, minibus taxis, and animals on rural roads. Keep vehicle doors locked in traffic and avoid driving at night in unfamiliar areas.'
                ]
            ]
        ]
    ],
    'road-trip-guide' => [
        'title' => 'South Africa Road Trip Guide for Tourists',
        'icon' => '🗺️',
        'content' => [
            'intro' => 'Plan the perfect self-drive holiday with our route guide for South Africa\'s most iconic roads. From the Garden Route to Kruger, here\'s everything you need to know.',
            'sections' => [
                [
                    'heading' => 'The Garden Route',
                    'body' => 'South Africa\'s most famous road trip stretches 300km from Mossel Bay to Storms River along the N2 highway. The route passes through George, Knysna, and Plettenberg Bay. Key stops include the Cango Caves near Oudtshoorn, the Knysna Heads cliff viewpoint, and Tsitsikamma National Park for canopy walks and hiking. The best time to drive the Garden Route is December to February when weather is warm and rainfall is minimal. Allow at least 4-5 days to fully explore the region. Book accommodation in advance during peak season as places fill quickly.'
                ],
                [
                    'heading' => 'Cape Peninsula Day Trip',
                    'body' => 'A classic day trip from Cape Town takes you along Chapman\'s Peak Drive (toll road) to Cape Point and back via Boulders Beach. The route covers approximately 250km and can be done in a single day. Stop at the penguins at Boulders Beach, explore the Cape of Good Hope nature reserve, and enjoy seafood in Kalk Bay. An economy car is sufficient for this trip, but a SUV offers better views from higher vantage points. Start early (around 8am) to avoid traffic and have more time at each stop.'
                ],
                [
                    'heading' => 'Panorama Route and Kruger National Park',
                    'body' => 'From Johannesburg, the drive to Kruger (approximately 4-5 hours) takes you past the scenic Panorama Route. Key stops include God\'s Window, Bourke\'s Luck Potholes, and the Three Rondavels. In Kruger, you can self-drive on well-maintained tar roads or opt for guided game drives. A standard sedan can handle all main roads, but a SUV provides better clearance for wildlife viewing from elevated viewpoints. The dry winter season (May to September) offers the best game viewing as animals congregate around water sources.'
                ],
                [
                    'heading' => 'The Drakensberg from Durban',
                    'body' => 'The Drakensberg mountains are a 3-4 hour drive from Durban via the N3 highway. The Central Drakensberg area offers hiking, horse riding, and scenic drives. For the adventurous, the Sani Pass into Lesotho requires a 4x4 vehicle and should only be attempted in good weather. The Olivie\'s Hoek Pass provides dramatic mountain scenery. Winter (June to August) can bring snow to higher elevations, creating stunning views but potentially closing some passes. Summer brings lush green landscapes but afternoon thunderstorms are common.'
                ],
                [
                    'heading' => 'Tips: Fuel Stations, Rest Stops, and Road Conditions',
                    'body' => 'Fuel stations are plentiful on major routes but can be sparse in remote areas. Always fill up before entering national parks or rural regions. Major petrol station chains like Engen, BP, and Shell have convenience stores and rest areas. South Africa\'s N-roads (national roads) are generally excellent, while R-roads (regional) vary in quality. The N1, N2, and N3 are toll roads — budget for e-tolls around Johannesburg and manual toll booths elsewhere. Download offline maps as mobile signal can be limited in some areas.'
                ]
            ]
        ]
    ],
    'deposit-guide-tourists' => [
        'title' => 'Car Hire Deposits in South Africa — A Tourist\'s Guide',
        'icon' => '💳',
        'content' => [
            'intro' => 'Deposits are the biggest financial surprise for international visitors. Here\'s exactly what to expect when renting a car in South Africa.',
            'sections' => [
                [
                    'heading' => 'Why Deposits Exist and How They Work',
                    'body' => 'A security deposit is a pre-authorisation hold placed on your credit card when you collect the vehicle. It\'s not an actual charge — the money remains in your account but becomes unavailable until released. The hold covers the rental company\'s potential costs for damage, fuel shortages, or traffic violations. The amount varies by company and vehicle class but typically ranges from R3,000 to R15,000. The hold usually appears as a pending transaction on your account and can take 1-21 days to release after returning the vehicle.'
                ],
                [
                    'heading' => 'Typical Deposit Ranges in South Africa',
                    'body' => 'For economy vehicles, expect deposits between R3,000 and R8,000. Mid-size sedans typically require R5,000 to R12,000. SUVs and 4x4s can demand R10,000 to R20,000. Luxury vehicles may require deposits of R15,000 to R30,000 or more. Major international brands (Avis, Hertz, Europcar) tend to be on the higher end, while local companies may be more flexible. The deposit amount often correlates with the vehicle\'s value and the company\'s risk assessment. Always ask for the exact deposit amount before confirming your booking.'
                ],
                [
                    'heading' => 'Credit Card Hold vs Actual Charge',
                    'body' => 'It\'s crucial to understand the difference between a hold and a charge. A hold is temporary and reduces your available credit, while an actual charge reduces your balance. Most reputable companies only place a hold, not a charge. However, if the vehicle is returned with damage or fuel missing, the company may convert the hold to a charge. Ensure you have enough credit available — the hold plus your rental cost should not exceed 80% of your credit limit to avoid declined transactions. Some companies use dynamic holds that adjust based on your rental duration.'
                ],
                [
                    'heading' => 'Debit Card Policies: Which Companies Allow It',
                    'body' => 'Major international rental companies (Avis, Hertz, Europcar, Budget) primarily require credit cards. However, some accept debit cards with conditions: a higher deposit (often R10,000+), proof of return travel arrangements, additional identification, or a prepaid fuel voucher. Local and regional companies like Tempest, First Car Rental, and Bluu are generally more flexible with debit cards. Using a debit card may also mean you\'re not eligible for certain insurance products. If you only have a debit card, research companies thoroughly and call ahead to confirm their specific requirements.'
                ],
                [
                    'heading' => 'How Long Refunds Take (and Why They Vary)',
                    'body' => 'Refund times vary dramatically between companies. Fastest providers (typically local brands) refund within 1-3 business days. Average companies take 5-7 business days. Slower companies (often multinationals with complex approval processes) can take 10-21 business days. Refund delays occur because: the company must complete a vehicle inspection, process any outstanding toll or fuel charges, wait for the bank\'s processing cycle, or use weekly batch processing. To speed things up, return the car during business hours, ensure the tank is full, and get a return confirmation receipt.'
                ],
                [
                    'heading' => 'How to Protect Yourself from Excess Charges',
                    'body' => 'To minimise excess charges: thoroughly document pre-existing damage with photos, understand what\'s covered by basic insurance, consider purchasing excess reduction (CDW), check if your credit card provides rental coverage, inspect the car carefully before signing, and ask for clarification on ambiguous items. Common excess charge sources include: fuel discrepancies, late return fees, additional driver fees, cross-border violations, and damage to tyres or undercarriage. Keep all receipts and documentation until your refund is processed. If you believe a charge is unfair, escalate to customer service with your evidence.'
                ]
            ]
        ]
    ]
];

$currentTopic = $topics[$topic] ?? null;

// Breadcrumbs
$crumbs = [
    ['label' => 'Home', 'url' => '/'],
    ['label' => 'Car Hire Guide', 'url' => url('education')]
];
if ($currentTopic) {
    $crumbs[] = ['label' => $currentTopic['title']];
}
echo breadcrumbs($crumbs);

// FAQ Schema
if ($currentTopic):
?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        <?php foreach ($currentTopic['content']['sections'] as $i => $section): ?>
        {
            "@type": "Question",
            "name": "<?= htmlspecialchars($section['heading']) ?>",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "<?= htmlspecialchars($section['body']) ?>"
            }
        }<?= $i < count($currentTopic['content']['sections']) - 1 ? ',' : '' ?>
        <?php endforeach; ?>
    ]
}
</script>
<?php endif; ?>

<section class="page-hero page-hero--education">
    <div class="container">
        <h1 class="page-title"><?= $currentTopic ? htmlspecialchars($currentTopic['title']) : 'Car Hire Guide South Africa' ?></h1>
        <p class="page-subtitle">Expert intelligence on South African car hire — deposits, excess, cross-border, and more.</p>
    </div>
</section>

<section class="section education-section">
    <div class="container">
        <div class="education-layout">
            
            <!-- Sidebar -->
            <aside class="education-sidebar">
                <div class="topic-nav">
                    <h3>Guide Topics</h3>
                    <ul>
                        <?php foreach ($topics as $topicKey => $topicData): ?>
                        <li>
                            <a href="<?= url('education', '', ['topic' => $topicKey]) ?>" class="topic-link <?= $topic === $topicKey ? 'topic-link--active' : '' ?>">
                                <?= $topicData['icon'] ?> <?= htmlspecialchars($topicData['title']) ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </aside>
            
            <!-- Main Content -->
            <div class="education-main">
                <?php if ($currentTopic): ?>
                <div class="education-article">
                    <div class="article-header">
                        <span class="article-icon"><?= $currentTopic['icon'] ?></span>
                        <h2><?= htmlspecialchars($currentTopic['title']) ?></h2>
                    </div>
                    <p class="article-intro"><?= htmlspecialchars($currentTopic['content']['intro']) ?></p>
                    
                    <?php foreach ($currentTopic['content']['sections'] as $section): ?>
                    <div class="article-section">
                        <h3><?= htmlspecialchars($section['heading']) ?></h3>
                        <p><?= htmlspecialchars($section['body']) ?></p>
                    </div>
                    <?php endforeach; ?>
                    
                    <div class="article-cta">
                        <h3>Ready to Compare Companies?</h3>
                        <p>Use our intelligence platform to find the best car hire company for your needs.</p>
                        <div class="article-cta-buttons">
                            <a href="<?= url('directory') ?>" class="btn btn--primary">Browse Directory</a>
                            <a href="<?= url('compare') ?>" class="btn btn--outline">Compare Companies</a>
                        </div>
                    </div>
                </div>
                
                <?php else: ?>
                <!-- Topic Overview -->
                <div class="topics-overview">
                    <h2>Complete Car Hire Guide for South Africa</h2>
                    <p>Everything you need to know about renting a car in South Africa — from deposits and excess to cross-border travel and seasonal demand.</p>
                    
                    <div class="topics-grid">
                        <?php foreach ($topics as $topicKey => $topicData): ?>
                        <a href="<?= url('education', '', ['topic' => $topicKey]) ?>" class="topic-card">
                            <div class="topic-card__icon"><?= $topicData['icon'] ?></div>
                            <h3 class="topic-card__title"><?= htmlspecialchars($topicData['title']) ?></h3>
                            <p class="topic-card__intro"><?= htmlspecialchars(truncate($topicData['content']['intro'], 100)) ?></p>
                            <span class="topic-card__link">Read Guide →</span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
