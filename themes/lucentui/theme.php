<?php

/**
 * LucentUI Theme Configuration
 *
 * Designed for Paymenter.
 * Made by TariqIsHuman - https://zaqua.studio
 *
 * Version:
 * v2.0.3-7aa08bc51c6d6a26a4769247ecf9d846-true-1772245370-Lipeng
 */

return [
    'name' => 'LucentUI',
    'author' => 'AquaGeprek',
    'url' => 'https://docs.zaqua.studio/lucentui',
    'version' => 'v2.0.3',
    'description' => 'A modern and sleek theme for Paymenter, designed to enhance user experience with a focus on gaming and server hosting.',

    'settings' => [

        // ========================================================================
        // 1. GLOBAL SETTINGS & LAYOUT
        // ========================================================================
        [
            'name' => 'general_placeholder',
            'type' => 'placeholder',
            'label' => 'General & Layout',
            'description' => 'Configure the main look and feel of the theme.',
        ],
        [
            'name' => 'change_font_family',
            'label' => 'Font Family',
            'type' => 'select',
            'options' => [
                'Figtree' => 'Figtree (Default)',
                'Plus Jakarta Sans' => 'Plus Jakarta Sans',
                'Poppins' => 'Poppins',
                'Inter' => 'Inter',
                'Roboto' => 'Roboto',
                'Open Sans' => 'Open Sans',
                'IBM Plex Sans Thai' => 'IBM Plex Sans Thai',
                'Orbitron' => 'Orbitron',
            ],
            'default' => 'Figtree',
            'description' => 'Select the primary font family for the website.',
        ],
        [
            'name' => 'homepage_layout',
            'label' => 'Homepage Layout',
            'type' => 'select',
            'options' => [
                'modern' => 'Modern Layout',
                'business' => 'Business Layout',
                'portal' => 'Portal Layout',
            ],
            'default' => 'modern',
            'description' => 'Choose the structural layout for the homepage.',
        ],
        [
            'name' => 'navbar_layout',
            'label' => 'Navigation Bar Layout',
            'type' => 'select',
            'options' => [
                'lucent-left' => 'Left Aligned',
                'lucent-centered' => 'Centered',
            ],
            'default' => 'lucent-left',
        ],
        [
            'name' => 'sidebar_style',
            'label' => 'Sidebar Style',
            'type' => 'select',
            'options' => [
                'simple' => 'Simple',
                'fancy' => 'Fancy',
            ],
            'default' => 'fancy',
            'description' => 'Choose the style of the sidebar.',
        ],
        [
            'name' => 'show_full_logo',
            'label' => 'Show Full Logo',
            'type' => 'checkbox',
            'default' => true,
            'description' => 'If unchecked, only the icon/mark might be shown depending on layout.',
        ],
        [
            'name' => 'show_credits_in_navbar',
            'label' => 'Show Credits+ in Navbar',
            'type' => 'checkbox',
            'default' => true,
            'description' => 'Display user credits balance in the navigation bar (Requires Credits+ Module).',
        ],
        [
            'name' => 'show_language_currency',
            'label' => 'Show Currency in Navbar',
            'type' => 'checkbox',
            'default' => false,
            'description' => 'Display the selected language and currency in the navigation dropdown.',
        ],
        [
            'name' => 'disable_homepage',
            'label' => 'Disable Homepage',
            'type' => 'checkbox',
            'default' => false,
            'description' => 'Redirect all visitors directly to the dashboard/login.',
        ],
        [
            'name' => 'background_image_url',
            'label' => 'Global Background Image URL',
            'type' => 'text',
            'default' => '',
            'description' => 'Optional. Recommended size: 1920x1080.',
        ],
        [
            'name' => 'background_image_opacity',
            'label' => 'Background Opacity (%)',
            'type' => 'text',
            'default' => 30,
        ],
        [
            'name' => 'background_image_blur',
            'label' => 'Background Blur (px)',
            'type' => 'text',
            'default' => 5,
        ],

        // ========================================================================
        // 2. COLOR SCHEME
        // ========================================================================
        [
            'name' => 'colors_placeholder',
            'type' => 'placeholder',
            'label' => 'Theme Colors',
            'description' => 'Customize the color palette for both Light and Dark modes.',
        ],
        // Light Mode
        [
            'name' => 'primary',
            'label' => 'Primary Brand Color (Light)',
            'type' => 'color',
            'default' => 'hsl(229, 100%, 64%)',
        ],
        [
            'name' => 'secondary',
            'label' => 'Secondary Brand Color (Light)',
            'type' => 'color',
            'default' => 'hsl(237, 33%, 60%)',
        ],
        [
            'name' => 'neutral',
            'label' => 'Neutral/Borders (Light)',
            'type' => 'color',
            'default' => 'hsl(220, 25%, 85%)',
        ],
        [
            'name' => 'base',
            'label' => 'Text Base Color (Light)',
            'type' => 'color',
            'default' => 'hsl(0, 0%, 0%)',
        ],
        [
            'name' => 'background',
            'label' => 'Page Background (Light)',
            'type' => 'color',
            'default' => 'hsl(100, 100%, 100%)',
        ],
        [
            'name' => 'background-secondary',
            'label' => 'Card Background (Light)',
            'type' => 'color',
            'default' => 'hsl(0, 0%, 97%)',
        ],
        // Dark Mode
        [
            'name' => 'dark-primary',
            'label' => 'Primary Brand Color (Dark)',
            'type' => 'color',
            'default' => 'hsl(229, 100%, 64%)',
        ],
        [
            'name' => 'dark-secondary',
            'label' => 'Secondary Brand Color (Dark)',
            'type' => 'color',
            'default' => 'hsl(237, 33%, 60%)',
        ],
        [
            'name' => 'dark-neutral',
            'label' => 'Neutral/Borders (Dark)',
            'type' => 'color',
            'default' => 'hsl(0, 0%, 7%)',
        ],
        [
            'name' => 'dark-base',
            'label' => 'Text Base Color (Dark)',
            'type' => 'color',
            'default' => 'hsl(100, 100%, 100%)',
        ],
        [
            'name' => 'dark-background',
            'label' => 'Page Background (Dark)',
            'type' => 'color',
            'default' => 'hsl(240, 18%, 9%)',
        ],
        [
            'name' => 'dark-background-secondary',
            'label' => 'Card Background (Dark)',
            'type' => 'color',
            'default' => 'hsl(240, 13%, 11%)',
        ],

        // ========================================================================
        // 3. HOMEPAGE: HERO SECTION (Used by Modern & Business)
        // ========================================================================
        [
            'name' => 'hero_placeholder',
            'type' => 'placeholder',
            'label' => 'Hero Section',
            'description' => 'The main top section of your homepage.',
        ],
        [
            'name' => 'homepage_hero_badge',
            'label' => 'Badge Text (Pill)',
            'type' => 'text',
            'default' => 'Now 50% Off!',
        ],
        [
            'name' => 'homepage_hero_title1',
            'label' => 'Headline Part 1',
            'type' => 'text',
            'default' => 'The Best',
        ],
        [
            'name' => 'homepage_hero_title2',
            'label' => 'Headline Part 2 (Gradient/Color)',
            'type' => 'text',
            'default' => 'Hosting',
        ],
        // Business Rotating Titles
        [
            'name' => 'homepage_rotating_title1',
            'label' => 'Rotating Word 1 (Business Layout)',
            'type' => 'text',
            'default' => 'Infrastructure',
        ],
        [
            'name' => 'homepage_rotating_title2',
            'label' => 'Rotating Word 2 (Business Layout)',
            'type' => 'text',
            'default' => 'Cloud',
        ],
        [
            'name' => 'homepage_rotating_title3',
            'label' => 'Rotating Word 3 (Business Layout)',
            'type' => 'text',
            'default' => 'Server',
        ],
        [
            'name' => 'homepage_hero_desc',
            'label' => 'Hero Description',
            'type' => 'markdown',
            'default' => 'Experience next-gen game hosting with blazing-fast SSD performance, enterprise-grade security, and 24/7 expert support.',
        ],
        [
            'name' => 'homepage_hero_illustration_url',
            'label' => 'Hero Image URL (Modern Layout)',
            'type' => 'text',
            'default' => '',
            'description' => 'Leave empty to use default SVG.',
        ],
        [
            'name' => 'homepage_hero_illustration_alt',
            'label' => 'Hero Image Alt Text',
            'type' => 'text',
            'default' => 'Hero Illustration',
        ],
        [
            'name' => 'hero_trust_badge_1',
            'label' => 'Trust Badge 1 Text',
            'type' => 'text',
            'default' => 'Money-back',
        ],
        [
            'name' => 'hero_trust_badge_2',
            'label' => 'Trust Badge 2 Text',
            'type' => 'text',
            'default' => 'Instant Setup',
        ],
        [
            'name' => 'hero_trust_badge_3',
            'label' => 'Trust Badge 3 Text',
            'type' => 'text',
            'default' => '24/7 Support',
        ],
        [
            'name' => 'hero_cta_primary_text',
            'label' => 'Primary Button Text',
            'type' => 'text',
            'default' => 'Get Started',
        ],
        [
            'name' => 'hero_cta_secondary_text',
            'label' => 'Secondary Button Text',
            'type' => 'text',
            'default' => 'Features',
        ],

        // ========================================================================
        // 4. HOMEPAGE: STATISTICS (With Modern & Business variants)
        // ========================================================================
        [
            'name' => 'stats_placeholder',
            'type' => 'placeholder',
            'label' => 'Statistics Section',
        ],
        [
            'name' => 'stats_uptime_title',
            'label' => 'Stat 1 Title (Uptime)',
            'type' => 'text',
            'default' => 'Uptime',
        ],
        [
            'name' => 'stats_uptime_subtitle',
            'label' => 'Stat 1 Subtitle (Modern Layout)',
            'type' => 'text',
            'default' => 'Guaranteed',
        ],
        [
            'name' => 'stats_support_title',
            'label' => 'Stat 2 Title (Support)',
            'type' => 'text',
            'default' => 'Support',
        ],
        [
            'name' => 'stats_support_subtitle',
            'label' => 'Stat 2 Subtitle (Modern Layout)',
            'type' => 'text',
            'default' => 'Always available',
        ],
        [
            'name' => 'stats_users_title',
            'label' => 'Stat 3 Title (Users)',
            'type' => 'text',
            'default' => 'Customers',
        ],
        [
            'name' => 'stats_users_subtitle',
            'label' => 'Stat 3 Subtitle (Modern Layout)',
            'type' => 'text',
            'default' => 'Worldwide',
        ],
        [
            'name' => 'stats_servers_title',
            'label' => 'Stat 4 Title (Servers)',
            'type' => 'text',
            'default' => 'Servers',
        ],
        [
            'name' => 'stats_servers_subtitle',
            'label' => 'Stat 4 Subtitle (Modern Layout)',
            'type' => 'text',
            'default' => 'Running worldwide',
        ],
        [
            'name' => 'stats_locations_count',
            'label' => 'Stat 4 Value (Business Layout)',
            'type' => 'text',
            'default' => '15+',
            'description' => 'Replaces Server Count in Business Layout',
        ],

        // ========================================================================
        // 5. FEATURES SECTION (6 Features for Modern, 4 for Business)
        // ========================================================================
        [
            'name' => 'features_placeholder',
            'type' => 'placeholder',
            'label' => 'Features Section',
        ],
        [
            'name' => 'features_title',
            'label' => 'Section Title',
            'type' => 'text',
            'default' => 'Why Choose Us?',
        ],
        [
            'name' => 'features_subtitle',
            'label' => 'Section Subtitle',
            'type' => 'textarea',
            'default' => 'We\'ve built the most advanced game hosting platform.',
        ],
        // Feature 1
        [
            'name' => 'feature_1_title',
            'label' => 'Feature 1 Title',
            'type' => 'text',
            'default' => 'Game Security',
        ],
        [
            'name' => 'feature_1_desc',
            'label' => 'Feature 1 Description',
            'type' => 'textarea',
            'default' => 'Advanced DDoS protection and secure connections.',
        ],
        // Feature 2
        [
            'name' => 'feature_2_title',
            'label' => 'Feature 2 Title',
            'type' => 'text',
            'default' => 'Smart Analytics',
        ],
        [
            'name' => 'feature_2_desc',
            'label' => 'Feature 2 Description',
            'type' => 'textarea',
            'default' => 'Real-time performance monitoring and player analytics.',
        ],
        // Feature 3
        [
            'name' => 'feature_3_title',
            'label' => 'Feature 3 Title',
            'type' => 'text',
            'default' => 'Lightning Setup',
        ],
        [
            'name' => 'feature_3_desc',
            'label' => 'Feature 3 Description',
            'type' => 'textarea',
            'default' => 'Deploy your game servers in under 60 seconds.',
        ],
        // Feature 4
        [
            'name' => 'feature_4_title',
            'label' => 'Feature 4 Title',
            'type' => 'text',
            'default' => 'Extreme Performance',
        ],
        [
            'name' => 'feature_4_desc',
            'label' => 'Feature 4 Description',
            'type' => 'textarea',
            'default' => 'Premium NVMe SSD storage and high-frequency CPUs.',
        ],
        // Feature 5 (Needed for Modern Layout)
        [
            'name' => 'feature_5_title',
            'label' => 'Feature 5 Title',
            'type' => 'text',
            'default' => 'Global Reach',
        ],
        [
            'name' => 'feature_5_desc',
            'label' => 'Feature 5 Description',
            'type' => 'textarea',
            'default' => 'Datacenter locations across 6 continents.',
        ],
        // Feature 6 (Needed for Modern Layout)
        [
            'name' => 'feature_6_title',
            'label' => 'Feature 6 Title',
            'type' => 'text',
            'default' => 'Performance Optimization',
        ],
        [
            'name' => 'feature_6_desc',
            'label' => 'Feature 6 Description',
            'type' => 'textarea',
            'default' => 'Advanced optimization to improve server performance.',
        ],

        // ========================================================================
        // 6. PRICING SECTION (Business Layout Only)
        // ========================================================================
        [
            'name' => 'pricing_placeholder',
            'type' => 'placeholder',
            'label' => 'Pricing (Business Layout)',
            'description' => 'Configure manual pricing cards for the Business layout.',
        ],
        [
            'name' => 'homepage_show_pricing',
            'label' => 'Enable Manual Pricing',
            'type' => 'checkbox',
            'default' => true,
        ],
        [
            'name' => 'pricing_title',
            'label' => 'Pricing Title',
            'type' => 'text',
            'default' => 'Simple, Transparent Pricing',
        ],
        [
            'name' => 'pricing_subtitle',
            'label' => 'Pricing Subtitle',
            'type' => 'textarea',
            'default' => 'Choose the perfect plan for your business needs. Upgrade anytime.',
        ],
        [
            'name' => 'pricing_popular_plan',
            'label' => 'Most Popular Plan (1, 2, or 3)',
            'type' => 'select',
            'options' => [
                '1' => 'Plan 1',
                '2' => 'Plan 2',
                '3' => 'Plan 3',
                '0' => 'None'
            ],
            'default' => '2',
        ],
        // Plan 1
        [
            'name' => 'pricing_1_name',
            'label' => 'Plan 1 Name',
            'type' => 'text',
            'default' => 'Starter',
        ],
        [
            'name' => 'pricing_1_price',
            'label' => 'Plan 1 Price',
            'type' => 'text',
            'default' => '$9.99',
        ],
        [
            'name' => 'pricing_1_discount',
            'label' => 'Plan 1 Discount Badge',
            'type' => 'text',
            'default' => '',
        ],
        [
            'name' => 'pricing_1_desc',
            'label' => 'Plan 1 Description',
            'type' => 'text',
            'default' => 'Essential resources for small projects.',
        ],
        [
            'name' => 'pricing_1_url',
            'label' => 'Plan 1 Link URL',
            'type' => 'text',
            'default' => '#',
        ],
        [
            'name' => 'pricing_1_features',
            'label' => 'Plan 1 Features (Comma separated)',
            'type' => 'textarea',
            'default' => '1 vCPU Core, 2 GB RAM, 20 GB NVMe Storage, 1 TB Bandwidth',
        ],
        // Plan 2
        [
            'name' => 'pricing_2_name',
            'label' => 'Plan 2 Name',
            'type' => 'text',
            'default' => 'Business Pro',
        ],
        [
            'name' => 'pricing_2_price',
            'label' => 'Plan 2 Price',
            'type' => 'text',
            'default' => '$29.99',
        ],
        [
            'name' => 'pricing_2_discount',
            'label' => 'Plan 2 Discount Badge',
            'type' => 'text',
            'default' => 'Save 15%',
        ],
        [
            'name' => 'pricing_2_desc',
            'label' => 'Plan 2 Description',
            'type' => 'text',
            'default' => 'Power and reliability for growing businesses.',
        ],
        [
            'name' => 'pricing_2_url',
            'label' => 'Plan 2 Link URL',
            'type' => 'text',
            'default' => '#',
        ],
        [
            'name' => 'pricing_2_features',
            'label' => 'Plan 2 Features (Comma separated)',
            'type' => 'textarea',
            'default' => '4 vCPU Cores, 8 GB RAM, 100 GB NVMe Storage, Unmetered Bandwidth, Priority Support',
        ],
        // Plan 3
        [
            'name' => 'pricing_3_name',
            'label' => 'Plan 3 Name',
            'type' => 'text',
            'default' => 'Enterprise',
        ],
        [
            'name' => 'pricing_3_price',
            'label' => 'Plan 3 Price',
            'type' => 'text',
            'default' => '$99.99',
        ],
        [
            'name' => 'pricing_3_discount',
            'label' => 'Plan 3 Discount Badge',
            'type' => 'text',
            'default' => '',
        ],
        [
            'name' => 'pricing_3_desc',
            'label' => 'Plan 3 Description',
            'type' => 'text',
            'default' => 'Maximum performance for mission-critical apps.',
        ],
        [
            'name' => 'pricing_3_url',
            'label' => 'Plan 3 Link URL',
            'type' => 'text',
            'default' => '#',
        ],
        [
            'name' => 'pricing_3_features',
            'label' => 'Plan 3 Features (Comma separated)',
            'type' => 'textarea',
            'default' => 'Dedicated Resources, 32 GB RAM, 1 TB NVMe Storage, 10Gbps Uplink, Dedicated Account Manager',
        ],

        // ========================================================================
        // 7. LOCATIONS & INFRASTRUCTURE (Business Layout Only)
        // ========================================================================
        [
            'name' => 'infrastructure_placeholder',
            'type' => 'placeholder',
            'label' => 'Global Infrastructure',
        ],
        [
            'name' => 'locations_badge',
            'label' => 'Badge Text',
            'type' => 'text',
            'default' => 'Global Network',
        ],
        [
            'name' => 'locations_title',
            'label' => 'Section Title',
            'type' => 'text',
            'default' => 'Low Latency, Worldwide.',
        ],
        [
            'name' => 'locations_description',
            'label' => 'Description',
            'type' => 'textarea',
            'default' => 'Deploy your services closer to your customers. Our premium Tier-4 data centers ensure maximum speed and reliability wherever you are.',
        ],
        [
            'name' => 'locations_list',
            'label' => 'Locations List (Comma Separated)',
            'type' => 'textarea',
            'default' => 'New York, London, Singapore, Jakarta, Tokyo, Sydney',
        ],
        [
            'name' => 'locations_coordinates',
            'label' => 'Map Coordinates (Name:Left%:Top%)',
            'type' => 'textarea',
            'default' => 'New York:25:28,London:48:25,Singapore:75:48,Jakarta:76:51,Tokyo:85:26,Sydney:80:70',
            'description' => 'Format: CityName:LeftPercentage:TopPercentage. Separated by comma.',
        ],
        [
            'name' => 'locations_cta_text',
            'label' => 'Link Text',
            'type' => 'text',
            'default' => 'Check network status',
        ],
        [
            'name' => 'locations_help_link',
            'label' => 'Locations Help URL',
            'type' => 'text',
            'default' => '#',
            'description' => 'Overrides general help link for this specific button.',
        ],
        [
            'name' => 'locations_capacity_label',
            'label' => 'Capacity Label',
            'type' => 'text',
            'default' => 'Network Capacity',
        ],
        [
            'name' => 'locations_capacity_value',
            'label' => 'Capacity Value',
            'type' => 'text',
            'default' => '10 Tbps+ Backbone',
        ],
        [
            'name' => 'locations_status_label',
            'label' => 'Status Label',
            'type' => 'text',
            'default' => 'Status',
        ],
        [
            'name' => 'locations_status_value',
            'label' => 'Status Value',
            'type' => 'text',
            'default' => 'Operational',
        ],

        // ========================================================================
        // 8. SERVICES & CATEGORIES
        // ========================================================================
        [
            'name' => 'services_placeholder',
            'type' => 'placeholder',
            'label' => 'Services Section',
        ],
        [
            'name' => 'services_title',
            'label' => 'Section Title',
            'type' => 'text',
            'default' => 'Our Services',
        ],
        [
            'name' => 'services_subtitle',
            'label' => 'Section Subtitle',
            'type' => 'textarea',
            'default' => 'Choose from our range of hosting solutions',
        ],
        [
            'name' => 'show_category_description',
            'label' => 'Show Category Description',
            'type' => 'checkbox',
            'default' => true,
        ],
        [
            'name' => 'homepage_featured_category',
            'label' => 'Featured Category Slug',
            'type' => 'text',
            'default' => 'vps',
            'description' => 'For Business Layout highlighting.',
        ],
        [
            'name' => 'homepage_discount_text',
            'label' => 'Featured Discount Text',
            'type' => 'text',
            'default' => 'Save 20% on Yearly',
        ],
        [
            'name' => 'show_category_image_banner',
            'label' => 'Category Banner Image',
            'type' => 'checkbox',
            'default' => true,
        ],
        [
            'name' => 'small_images',
            'label' => 'Compact Product Images',
            'type' => 'checkbox',
            'default' => false,
        ],
        [
            'name' => 'direct_checkout',
            'label' => 'Direct Checkout',
            'type' => 'checkbox',
            'default' => false,
        ],

        // ========================================================================
        // 9. TESTIMONIALS
        // ========================================================================
        [
            'name' => 'testimonials_placeholder',
            'type' => 'placeholder',
            'label' => 'Testimonials Section',
        ],
        [
            'name' => 'testimonials_title',
            'label' => 'Section Title',
            'type' => 'text',
            'default' => 'What Customers Say',
        ],
        [
            'name' => 'testimonials_subtitle',
            'label' => 'Section Subtitle',
            'type' => 'textarea',
            'default' => 'Join thousands of satisfied customers',
        ],
        // Testimonial 1
        [
            'name' => 'testimonial_1_name',
            'label' => 'User 1 Name',
            'type' => 'text',
            'default' => 'John Doe',
        ],
        [
            'name' => 'testimonial_1_role',
            'label' => 'User 1 Role',
            'type' => 'text',
            'default' => 'CEO',
        ],
        [
            'name' => 'testimonial_1_quote',
            'label' => 'User 1 Quote',
            'type' => 'textarea',
            'default' => 'Great service!',
        ],
        // Testimonial 2
        [
            'name' => 'testimonial_2_name',
            'label' => 'User 2 Name',
            'type' => 'text',
            'default' => 'Sarah Smith',
        ],
        [
            'name' => 'testimonial_2_role',
            'label' => 'User 2 Role',
            'type' => 'text',
            'default' => 'Gamer',
        ],
        [
            'name' => 'testimonial_2_quote',
            'label' => 'User 2 Quote',
            'type' => 'textarea',
            'default' => 'Amazing performance.',
        ],
        // Testimonial 3
        [
            'name' => 'testimonial_3_name',
            'label' => 'User 3 Name',
            'type' => 'text',
            'default' => 'Mike Ross',
        ],
        [
            'name' => 'testimonial_3_role',
            'label' => 'User 3 Role',
            'type' => 'text',
            'default' => 'Developer',
        ],
        [
            'name' => 'testimonial_3_quote',
            'label' => 'User 3 Quote',
            'type' => 'textarea',
            'default' => 'Top notch support.',
        ],

        // ========================================================================
        // 10. FAQ (Modern & Standard Layouts use 6 items)
        // ========================================================================
        [
            'name' => 'faq_placeholder',
            'type' => 'placeholder',
            'label' => 'FAQ (Standard/Modern)',
            'description' => 'Questions for Modern and Portal layouts.',
        ],
        [
            'name' => 'faq_title',
            'label' => 'FAQ Title',
            'type' => 'text',
            'default' => 'Frequently Asked Questions',
        ],
        [
            'name' => 'faq_subtitle',
            'label' => 'FAQ Subtitle',
            'type' => 'textarea',
            'default' => 'Find answers to common questions about our services',
        ],
        [
            'name' => 'faq_1_question',
            'label' => 'Question 1',
            'type' => 'text',
            'default' => 'How quickly can I get my server up and running?',
        ],
        [
            'name' => 'faq_1_answer',
            'label' => 'Answer 1',
            'type' => 'textarea',
            'default' => 'Your server will be automatically deployed within 60 seconds.',
        ],
        [
            'name' => 'faq_2_question',
            'label' => 'Question 2',
            'type' => 'text',
            'default' => 'What payment methods do you accept?',
        ],
        [
            'name' => 'faq_2_answer',
            'label' => 'Answer 2',
            'type' => 'textarea',
            'default' => 'We accept all major payment methods including cards and crypto.',
        ],
        [
            'name' => 'faq_3_question',
            'label' => 'Question 3',
            'type' => 'text',
            'default' => 'Can I upgrade or downgrade my plan anytime?',
        ],
        [
            'name' => 'faq_3_answer',
            'label' => 'Answer 3',
            'type' => 'textarea',
            'default' => 'Yes! You can upgrade or downgrade your hosting plan at any time.',
        ],
        [
            'name' => 'faq_4_question',
            'label' => 'Question 4',
            'type' => 'text',
            'default' => 'Do you offer refunds if I\'m not satisfied?',
        ],
        [
            'name' => 'faq_4_answer',
            'label' => 'Answer 4',
            'type' => 'textarea',
            'default' => 'We offer a 30-day money-back guarantee.',
        ],
        [
            'name' => 'faq_5_question',
            'label' => 'Question 5',
            'type' => 'text',
            'default' => 'What kind of support do you provide?',
        ],
        [
            'name' => 'faq_5_answer',
            'label' => 'Answer 5',
            'type' => 'textarea',
            'default' => 'We provide 24/7 customer support through live chat and tickets.',
        ],
        [
            'name' => 'faq_6_question',
            'label' => 'Question 6',
            'type' => 'text',
            'default' => 'How do you handle DDoS attacks?',
        ],
        [
            'name' => 'faq_6_answer',
            'label' => 'Answer 6',
            'type' => 'textarea',
            'default' => 'All our servers come with enterprise-grade DDoS protection.',
        ],

        // ========================================================================
        // 11. FAQ (Business Layout uses 5 Specific Items)
        // ========================================================================
        [
            'name' => 'faq_business_placeholder',
            'type' => 'placeholder',
            'label' => 'FAQ (Business Layout)',
            'description' => 'Specific questions for Business Layout.',
        ],
        [
            'name' => 'faq_business_1_question',
            'label' => 'Business Q1',
            'type' => 'text',
            'default' => 'How long does deployment take?',
        ],
        [
            'name' => 'faq_business_1_answer',
            'label' => 'Business A1',
            'type' => 'textarea',
            'default' => 'Most services are deployed instantly after payment.',
        ],
        [
            'name' => 'faq_business_2_question',
            'label' => 'Business Q2',
            'type' => 'text',
            'default' => 'Do you offer DDoS protection?',
        ],
        [
            'name' => 'faq_business_2_answer',
            'label' => 'Business A2',
            'type' => 'textarea',
            'default' => 'Yes, all our services come with standard DDoS protection included free of charge.',
        ],
        [
            'name' => 'faq_business_3_question',
            'label' => 'Business Q3',
            'type' => 'text',
            'default' => 'Can I upgrade my plan later?',
        ],
        [
            'name' => 'faq_business_3_answer',
            'label' => 'Business A3',
            'type' => 'textarea',
            'default' => 'Absolutely. You can scale your resources up instantly.',
        ],
        [
            'name' => 'faq_business_4_question',
            'label' => 'Business Q4',
            'type' => 'text',
            'default' => 'Are you compliant with industry standards?',
        ],
        [
            'name' => 'faq_business_4_answer',
            'label' => 'Business A4',
            'type' => 'textarea',
            'default' => 'Yes, we follow strict industry standards for security and data protection.',
        ],
        [
            'name' => 'faq_business_5_question',
            'label' => 'Business Q5',
            'type' => 'text',
            'default' => 'How does billing work for enterprise plans?',
        ],
        [
            'name' => 'faq_business_5_answer',
            'label' => 'Business A5',
            'type' => 'textarea',
            'default' => 'We offer flexible billing options including monthly and annual payments.',
        ],

        // ========================================================================
        // 12. CALL TO ACTION & CARDS (Portal/General)
        // ========================================================================
        [
            'name' => 'cta_placeholder',
            'type' => 'placeholder',
            'label' => 'Call to Action & Cards',
        ],
        [
            'name' => 'cta_title',
            'label' => 'CTA Title',
            'type' => 'text',
            'default' => 'Ready to Get Started?',
        ],
        [
            'name' => 'cta_subtitle',
            'label' => 'CTA Subtitle',
            'type' => 'textarea',
            'default' => 'Join thousands of satisfied customers today',
        ],
        [
            'name' => 'cta_primary_text',
            'label' => 'CTA Primary Button',
            'type' => 'text',
            'default' => 'View Pricing',
        ],
        [
            'name' => 'cta_secondary_text',
            'label' => 'CTA Secondary Button',
            'type' => 'text',
            'default' => 'Contact Us',
        ],
        [
            'name' => 'cta_badge_1',
            'label' => 'Badge 1 Text (Business)',
            'type' => 'text',
            'default' => 'Trusted by 10,000+ users',
        ],
        [
            'name' => 'cta_badge_2',
            'label' => 'Badge 2 Text (Business)',
            'type' => 'text',
            'default' => '24/7 Security',
        ],
        // Quick Action Cards (Portal)
        [
            'name' => 'qa_placeholder',
            'type' => 'placeholder',
            'label' => 'Quick Action Cards (Portal)',
        ],
        [
            'name' => 'quick_actions_title',
            'label' => 'Section Title',
            'type' => 'text',
            'default' => 'Get Started in Seconds',
        ],
        [
            'name' => 'quick_actions_subtitle',
            'label' => 'Section Subtitle',
            'type' => 'text',
            'default' => 'Everything you need is just one click away',
        ],
        [
            'name' => 'buy_server_title',
            'label' => 'Buy Card Title',
            'type' => 'text',
            'default' => 'Buy Server',
        ],
        [
            'name' => 'buy_server_subtitle',
            'label' => 'Buy Card Subtitle',
            'type' => 'text',
            'default' => 'Get started',
        ],
        [
            'name' => 'buy_server_link',
            'label' => 'Buy Card URL',
            'type' => 'text',
            'default' => '#services',
        ],
        [
            'name' => 'help_title',
            'label' => 'Support Card Title',
            'type' => 'text',
            'default' => 'Support',
        ],
        [
            'name' => 'help_subtitle',
            'label' => 'Support Card Subtitle',
            'type' => 'text',
            'default' => '24/7 Help',
        ],
        [
            'name' => 'help_link',
            'label' => 'Support/Help URL',
            'type' => 'text',
            'default' => '#',
        ],
        [
            'name' => 'login_title',
            'label' => 'Login Card Title',
            'type' => 'text',
            'default' => 'Login',
        ],
        [
            'name' => 'login_subtitle',
            'label' => 'Login Card Subtitle',
            'type' => 'text',
            'default' => 'Access Panel',
        ],
        [
            'name' => 'dashboard_title',
            'label' => 'Dashboard Card Title',
            'type' => 'text',
            'default' => 'Dashboard',
        ],
        [
            'name' => 'dashboard_subtitle',
            'label' => 'Dashboard Card Subtitle',
            'type' => 'text',
            'default' => 'Manage',
        ],
        [
            'name' => 'docs_title',
            'label' => 'Docs Card Title',
            'type' => 'text',
            'default' => 'Docs',
        ],
        [
            'name' => 'docs_subtitle',
            'label' => 'Docs Card Subtitle',
            'type' => 'text',
            'default' => 'Learn more',
        ],
        [
            'name' => 'docs_link',
            'label' => 'Docs Card URL',
            'type' => 'text',
            'default' => '#',
        ],

        // ========================================================================
        // 13. AUTH & PAGES (Login/Register/Order)
        // ========================================================================
        [
            'name' => 'pages_placeholder',
            'type' => 'placeholder',
            'label' => 'Auth Pages',
        ],
        [
            'name' => 'login_title_page',
            'label' => 'Login Page Title',
            'type' => 'text',
            'default' => 'Welcome Back',
            'description' => 'Title displayed on the login page.',
        ],
        [
            'name' => 'login_subtitle_page',
            'label' => 'Login Page Subtitle',
            'type' => 'text',
            'default' => 'Please enter your credentials',
        ],
        [
            'name' => 'login_testimonial_text',
            'label' => 'Login Side Quote',
            'type' => 'textarea',
            'default' => 'This product changed the way our service works. Very intuitive and fast!',
        ],
        [
            'name' => 'login_testimonial_author',
            'label' => 'Quote Author',
            'type' => 'text',
            'default' => 'Satisfied User',
        ],
        // New Order
        [
            'name' => 'new_order_title',
            'label' => 'New Order Title',
            'type' => 'text',
            'default' => 'New Order',
        ],
        [
            'name' => 'new_order_subtitle',
            'label' => 'New Order Subtitle',
            'type' => 'text',
            'default' => 'Need more services?',
        ],
        [
            'name' => 'new_order_link',
            'label' => 'New Order Link URL',
            'type' => 'text',
            'default' => '',
        ],
        [
            'name' => 'register_subtitle',
            'label' => 'Register Page Subtitle',
            'type' => 'text',
            'default' => 'Save hundreds of hours building dashboards.',
        ],
        [
            'name' => 'register_testimonial_text',
            'label' => 'Register Quote',
            'type' => 'textarea',
            'default' => 'This product changed the way our service works.',
        ],
        [
            'name' => 'register_testimonial_author',
            'label' => 'Register Quote Author',
            'type' => 'text',
            'default' => 'Satisfied User',
        ],

        // ========================================================================
        // 14. BANNERS & NOTIFICATIONS
        // ========================================================================
        [
            'name' => 'notifications_placeholder',
            'type' => 'placeholder',
            'label' => 'Banners & Consent',
        ],
        [
            'name' => 'banner_enabled',
            'label' => 'Enable Dashboard Banner',
            'type' => 'checkbox',
            'default' => false,
        ],
        [
            'name' => 'banner_type',
            'label' => 'Banner Type',
            'type' => 'select',
            'options' => [
                'critical' => 'Critical (Red)',
                'warning' => 'Warning (Orange)',
                'info' => 'Info (Primary)',
                'success' => 'Success (Green)'
            ],
            'default' => 'info',
        ],
        [
            'name' => 'banner_message',
            'label' => 'Banner Message',
            'type' => 'textarea',
            'default' => 'Welcome to your dashboard.',
        ],
        [
            'name' => 'banner_dismissible',
            'label' => 'Is Dismissible?',
            'type' => 'checkbox',
            'default' => true,
        ],
        // Cookie Consent
        [
            'name' => 'enable_cookies',
            'label' => 'Enable Cookie Banner',
            'type' => 'checkbox',
            'default' => true,
        ],
        [
            'name' => 'cookie_title',
            'label' => 'Cookie Title',
            'type' => 'text',
            'default' => 'Cookie Usage',
        ],
        [
            'name' => 'cookie_message',
            'label' => 'Cookie Message',
            'type' => 'textarea',
            'default' => 'We use cookies to enhance your experience.',
        ],
        [
            'name' => 'cookie_button_text',
            'label' => 'Button Text',
            'type' => 'text',
            'default' => 'Accept Cookies',
        ],

        // ========================================================================
        // 15. SEO & SOCIALS & SECURITY
        // ========================================================================
        [
            'name' => 'seo_placeholder',
            'type' => 'placeholder',
            'label' => 'SEO, Socials & Security',
        ],
        [
            'name' => 'seo_title',
            'label' => 'SEO Title',
            'type' => 'text',
            'default' => 'Premium Game Hosting',
        ],
        [
            'name' => 'seo_description',
            'label' => 'Meta Description',
            'type' => 'textarea',
            'default' => 'Experience premium game hosting with 99.9% uptime.',
        ],
        [
            'name' => 'seo_keywords',
            'label' => 'Meta Keywords',
            'type' => 'textarea',
            'default' => 'game hosting, minecraft hosting, dedicated servers',
        ],
        [
            'name' => 'og_image',
            'label' => 'Open Graph Image URL',
            'type' => 'text',
            'default' => '',
            'description' => 'Image for social sharing (1200x630px).',
        ],
        // Footer Text
        [
            'name' => 'footer_text',
            'label' => 'Footer Description',
            'type' => 'markdown',
            'default' => 'Welcome to Paymenter!',
        ],
        // Links
        [
            'name' => 'privacy_policy_url',
            'label' => 'Privacy Policy URL',
            'type' => 'text',
        ],
        [
            'name' => 'terms_of_service_url',
            'label' => 'Terms of Service URL',
            'type' => 'text',
        ],
        [
            'name' => 'support_url',
            'label' => 'Support Page URL',
            'type' => 'text',
        ],
        [
            'name' => 'discord_url',
            'label' => 'Discord URL',
            'type' => 'text',
        ],
        [
            'name' => 'twitter_url',
            'label' => 'Twitter/X URL',
            'type' => 'text',
        ],
        [
            'name' => 'instagram_url',
            'label' => 'Instagram URL',
            'type' => 'text',
        ],
        [
            'name' => 'youtube_url',
            'label' => 'YouTube URL',
            'type' => 'text',
        ],
        [
            'name' => 'tiktok_url',
            'label' => 'TikTok URL',
            'type' => 'text',
        ],
        [
            'name' => 'github_url',
            'label' => 'GitHub URL',
            'type' => 'text',
        ],
        [
            'name' => 'control_panel_link',
            'label' => 'Ext. Control Panel URL',
            'type' => 'text',
        ],

        // ========================================================================
        // 16. CUSTOM CODE
        // ========================================================================
        [
            'name' => 'developer_placeholder',
            'type' => 'placeholder',
            'label' => 'Developer & Custom Code',
            'description' => 'Inject custom scripts and styles.',
        ],
        [
            'name' => 'custom_css',
            'label' => 'Custom CSS',
            'type' => 'textarea',
            'description' => 'CSS entered here will be wrapped in style tags.',
        ],
        [
            'name' => 'custom_js',
            'label' => 'Custom JavaScript',
            'type' => 'textarea',
            'description' => 'JS entered here will be wrapped in script tags.',
        ],
        [
            'name' => 'custom_head_html',
            'label' => 'HTML in <head>',
            'type' => 'textarea',
            'description' => 'Useful for Analytics/Meta tags.',
        ],
        [
            'name' => 'custom_body_top_html',
            'label' => 'HTML Start of <body>',
            'type' => 'textarea',
        ],
        [
            'name' => 'custom_body_bottom_html',
            'label' => 'HTML End of <body>',
            'type' => 'textarea',
            'description' => 'Useful for Chat widgets.',
        ],
        [
            'name' => 'custom_homepage_html',
            'label' => 'HTML Homepage Only',
            'type' => 'textarea',
        ],
        [
            'name' => 'custom_dashboard_html',
            'label' => 'HTML Dashboard Only',
            'type' => 'textarea',
        ],
    ],
];