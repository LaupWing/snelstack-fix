<?php
/**
 * Page seeders — front page, blog, cases, websites.
 *
 * @package Snel
 */

defined('ABSPATH') || exit;

// ---------------------------------------------------------------------------
// Block content helpers
// ---------------------------------------------------------------------------

function snel_get_front_page_blocks(): string
{
    $flags   = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
    $h_class = 'font-semibold text-slate-950 text-2xl/tight md:text-3xl/tight lg:text-4xl/tight xl:text-5xl/tight';

    $stmt_h   = "Gebouwd voor groei.<br><span class='snel-muted'>Niet voor de showcase.</span>";
    $stmt_p   = 'Elk project begint met begrip van jouw business en doelen. Snel live, meetbaar resultaat en continu verbeteren.';
    $stmt_h_j = json_encode($stmt_h, $flags);
    $stmt_p_j = json_encode($stmt_p, $flags);

    $b = [];

    // ── Hero ──────────────────────────────────────────────────────────────────────
    $b[] = '<!-- wp:snel/hero {"size":"md"} -->'
        . "\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-eyebrow\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-eyebrow\"><!-- wp:snel/badge /--></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n"
        . "\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-middle\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-middle\"><!-- wp:snel/heading {\"level\":\"h1\",\"className\":\"" . $h_class . "\"} -->"
        . "\n<h1 class=\"wp-block-snel-heading snel-heading max-w-4xl snel-h-xl " . $h_class . "\"><strong>Slimmer werken.</strong>&nbsp;<span class=\"snel-muted\">Data extractie, n8n automatisering en custom software voor bedrijven die willen groeien.</span></h1>"
        . "\n<!-- /wp:snel/heading --></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n"
        . "\n<!-- wp:snel/slot {\"max\":2,\"orientation\":\"horizontal\",\"className\":\"snel-slot-lower\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-lower\"><!-- wp:snel/button-gradient /-->"
        . "\n<!-- wp:snel/button /--></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n<!-- /wp:snel/hero -->";

    // ── Stack showcase ─────────────────────────────────────────────────────────────
    $b[] = '<!-- wp:snel/stack-showcase {"size":"lg","disableTop":true,"disableBottom":true} /-->';

    // ── Partners ───────────────────────────────────────────────────────────────────
    $b[] = '<!-- wp:snel/partners {"animated":false,"count":6} /-->';

    // ── Panel dark ─────────────────────────────────────────────────────────────────
    $b[] = '<!-- wp:snel/panel {"theme":"dark","rounded":true} -->'
        . "\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-eyebrow\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-eyebrow\"><!-- wp:snel/badge-text {\"color\":\"teal\"} /--></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n"
        . "\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-middle\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-middle\"><!-- wp:snel/heading {\"size\":\"xl\",\"className\":\"" . $h_class . "\"} -->"
        . "\n<h2 class=\"wp-block-snel-heading snel-heading max-w-4xl snel-h-xl " . $h_class . "\">Data extractie, automatisering en software op maat</h2>"
        . "\n<!-- /wp:snel/heading --></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n"
        . "\n<!-- wp:snel/slot {\"max\":2,\"orientation\":\"horizontal\",\"className\":\"snel-slot-lower\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-lower\"><!-- wp:snel/paragraph {\"size\":\"lg\"} -->"
        . "\n<p class=\"wp-block-snel-paragraph snel-text max-w-4xl snel-text-lg\">Wij bouwen systemen die jouw data ophalen, processen draaien en software leveren op maat.&nbsp;<span class=\"snel-muted\">Geen generieke tools. Precies wat jij nodig hebt.</span></p>"
        . "\n<!-- /wp:snel/paragraph --></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n<!-- /wp:snel/panel -->";

    // ── Process ────────────────────────────────────────────────────────────────────
    $b[] = '<!-- wp:snel/process /-->';

    // ── Statement ──────────────────────────────────────────────────────────────────
    $b[] = '<!-- wp:snel/statement {"heading":' . $stmt_h_j . ',"paragraph":' . $stmt_p_j . '} /-->';

    // ── Panel light ────────────────────────────────────────────────────────────────
    $b[] = '<!-- wp:snel/panel {"rounded":true} -->'
        . "\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-eyebrow\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-eyebrow\"><!-- wp:snel/badge-text /--></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n"
        . "\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-middle\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-middle\"><!-- wp:snel/heading {\"size\":\"2xl\"} -->"
        . "\n<h2 class=\"wp-block-snel-heading snel-heading max-w-4xl snel-h-2xl\">Klaar om te <br><span class=\"snel-muted\">starten?</span></h2>"
        . "\n<!-- /wp:snel/heading --></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n"
        . "\n<!-- wp:snel/slot {\"max\":2,\"orientation\":\"horizontal\",\"className\":\"snel-slot-lower\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-lower\"><!-- wp:snel/button-gradient /--></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n<!-- /wp:snel/panel -->";

    // ── Cases ──────────────────────────────────────────────────────────────────────
    $b[] = '<!-- wp:snel/cases {"bg":"white","disableTop":true} /-->';

    // ── Posts ──────────────────────────────────────────────────────────────────────
    $b[] = '<!-- wp:snel/posts {"size":"md","disableTop":true} /-->';

    return implode("\n\n", $b);
}

function snel_get_blog_page_blocks(): string
{
    $b = [];

    // ── Intro ──────────────────────────────────────────────────────────────────────
    $b[] = '<!-- wp:snel/intro -->'
        . "\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-eyebrow\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-eyebrow\"><!-- wp:snel/badge-text /--></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n"
        . "\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-heading\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-heading\"><!-- wp:snel/heading {\"size\":\"3xl\",\"weight\":\"extrabold\"} -->"
        . "\n<h2 class=\"wp-block-snel-heading snel-heading max-w-4xl snel-h-3xl snel-hw-extrabold\">Idee&#235;n die&nbsp;<br><span class=\"snel-muted\">beweging brengen</span></h2>"
        . "\n<!-- /wp:snel/heading --></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n"
        . "\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-body\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-body\"><!-- wp:snel/paragraph {\"size\":\"lg\"} -->"
        . "\n<p class=\"wp-block-snel-paragraph snel-text max-w-4xl snel-text-lg\">We delen idee&#235;n, ervaringen en ontwikkelingen uit ons vakgebied. Praktisch, helder en bedoeld om jou verder te helpen in een wereld die continu verandert.</p>"
        . "\n<!-- /wp:snel/paragraph --></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n"
        . "\n<!-- wp:snel/slot {\"max\":2,\"orientation\":\"horizontal\",\"className\":\"snel-slot-cta\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-cta\"><!-- wp:snel/category-nav /--></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n<!-- /wp:snel/intro -->";

    // ── Posts archive ──────────────────────────────────────────────────────────────
    $b[] = '<!-- wp:snel/posts-archive /-->';

    return implode("\n\n", $b);
}

function snel_get_cases_page_blocks(): string
{
    $b = [];

    // ── Intro ──────────────────────────────────────────────────────────────────────
    $b[] = '<!-- wp:snel/intro -->'
        . "\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-eyebrow\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-eyebrow\"><!-- wp:snel/badge-text /--></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n"
        . "\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-heading\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-heading\"><!-- wp:snel/heading {\"size\":\"2xl\",\"weight\":\"extrabold\"} -->"
        . "\n<h2 class=\"wp-block-snel-heading snel-heading max-w-4xl snel-h-2xl snel-hw-extrabold\">Werk dat <br><span class=\"snel-muted\">voor zichzelf</span>&nbsp;<br>spreekt</h2>"
        . "\n<!-- /wp:snel/heading --></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n"
        . "\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-body\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-body\"><!-- wp:snel/paragraph -->"
        . "\n<p class=\"wp-block-snel-paragraph snel-text max-w-4xl snel-text-md\">Van <strong>custom WordPress-themes</strong> en <strong>SaaS-platforms</strong> tot <strong>n8n-automatiseringen</strong> en AI-tools. Elk project anders, dezelfde aanpak: vakmanschap eerst.</p>"
        . "\n<!-- /wp:snel/paragraph --></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n"
        . "\n<!-- wp:snel/slot {\"max\":2,\"orientation\":\"horizontal\",\"className\":\"snel-slot-cta\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-cta\"></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n<!-- /wp:snel/intro -->";

    // ── Cases grid ─────────────────────────────────────────────────────────────────
    $b[] = '<!-- wp:snel/cases {"bg":"white"} /-->';

    // ── Content ────────────────────────────────────────────────────────────────────
    $b[] = <<<'CONTENT'
<!-- wp:snel/content -->
<!-- wp:heading {"level":2} -->
<h2>Design, code en automatisering — in één</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Elk project hier is anders. Maar de aanpak is altijd hetzelfde: begrijpen wat er nodig is, snel bouwen en leveren wat werkt. Als senior developer én designer in één gaat er geen tijd verloren aan overdracht of teamvergaderingen.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>AI versnelt het proces. Niet als vervanging, maar als copiloot. Het resultaat: werk van hoog niveau, sneller geleverd en tegen een eerlijker tarief dan een bureau kan bieden.</p>
<!-- /wp:paragraph -->
<!-- /wp:snel/content -->
CONTENT;

    return implode("\n\n", $b);
}

function snel_get_websites_page_blocks(): string
{
    $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    $cards = [
        ['icon' => 'graduation-cap',  'heading' => '10 jaar vakmanschap',  'body' => 'Full-stack development en design als dagelijks werk. Geen junior die leert op jouw project.'],
        ['icon' => 'cursor',          'heading' => 'Design én code in één', 'body' => 'Geen overdracht tussen designer en developer. Alles in één hoofd, één stijl, één workflow.'],
        ['icon' => 'brain',           'heading' => 'AI als copiloot',       'body' => 'AI versnelt mijn werk zonder in te leveren op kwaliteit. Jij profiteert van de snelheid.'],
        ['icon' => 'bolt',            'heading' => 'Sneller live',          'body' => 'Zonder teamvergaderingen en briefingrondes kom ik sneller tot een resultaat dat klopt.'],
        ['icon' => 'trending-up',     'heading' => 'Lagere kosten',         'body' => 'Geen bureauopslag, geen projectmanager, geen overhead. Wat je betaalt gaat naar het werk.'],
        ['icon' => 'message-circle',  'heading' => 'Direct contact',        'body' => 'Je werkt direct met mij. Geen account managers, geen tussenpersonen, geen vertraging.'],
    ];
    $cards_j = json_encode($cards, $flags);

    $stmt_h   = "Design en code.<br><span class='snel-muted'>Van één persoon.</span>";
    $stmt_p   = "Bij bureaus werkt een designer de visuals uit en geeft ze door aan een developer. Die overdracht kost tijd en gaat ten koste van kwaliteit. <span class='snel-accent'>Bij mij is dat dezelfde persoon met 10 jaar ervaring.</span><br><br>AI versnelt het proces. Niet als vervanger, maar als copiloot.";
    $stmt_h_j = json_encode($stmt_h, $flags);
    $stmt_p_j = json_encode($stmt_p, $flags);

    $b = [];

    // ── Intro ──────────────────────────────────────────────────────────────────────
    $b[] = '<!-- wp:snel/intro {"visual":"speed","showBeams":false,"showGradient":false} -->'
        . "\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-eyebrow\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-eyebrow\"><!-- wp:snel/badge-text {\"label\":\"Website op maat\",\"color\":\"sky\"} /--></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n"
        . "\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-middle\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-middle\"><!-- wp:snel/heading {\"size\":\"3xl\"} -->"
        . "\n<h2 class=\"wp-block-snel-heading snel-heading max-w-4xl snel-h-3xl\">Design en code. Door één persoon.</h2>"
        . "\n<!-- /wp:snel/heading --></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n"
        . "\n<!-- wp:snel/slot {\"max\":2,\"orientation\":\"horizontal\",\"className\":\"snel-slot-lower\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-lower\"><!-- wp:snel/paragraph -->"
        . "\n<p class=\"wp-block-snel-paragraph snel-text max-w-4xl snel-text-md\">Senior developer én designer in één. Met AI als copiloot lever ik sneller en scherper dan een heel bureau — voor een <strong>eerlijke prijs</strong>.</p>"
        . "\n<!-- /wp:snel/paragraph --></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n"
        . "\n<!-- wp:snel/slot {\"max\":2,\"orientation\":\"horizontal\",\"className\":\"snel-slot-cta\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-cta\"><!-- wp:snel/button-gradient /-->"
        . "\n<!-- wp:snel/button {\"variant\":\"filled\"} /--></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n<!-- /wp:snel/intro -->";

    // ── Panel canvas ───────────────────────────────────────────────────────────────
    $b[] = '<!-- wp:snel/panel {"theme":"canvas","rounded":true} -->'
        . "\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-eyebrow\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-eyebrow\"><!-- wp:snel/badge-text {\"color\":\"pink\"} /--></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n"
        . "\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-middle\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-middle\"><!-- wp:snel/heading {\"size\":\"3xl\"} -->"
        . "\n<h2 class=\"wp-block-snel-heading snel-heading max-w-4xl snel-h-3xl\">Waarom mijn aanpak werkt</h2>"
        . "\n<!-- /wp:snel/heading --></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n"
        . "\n<!-- wp:snel/slot {\"max\":2,\"orientation\":\"horizontal\",\"className\":\"snel-slot-lower\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-lower\"><!-- wp:snel/paragraph -->"
        . "\n<p class=\"wp-block-snel-paragraph snel-text max-w-4xl snel-text-md\">Geen overdracht tussen designer en developer. Geen teamvergaderingen. Geen marge op marge. Jij werkt direct met de persoon die jouw site bouwt.</p>"
        . "\n<!-- /wp:snel/paragraph --></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n<!-- /wp:snel/panel -->";

    // ── Statement ──────────────────────────────────────────────────────────────────
    $b[] = '<!-- wp:snel/statement {"heading":' . $stmt_h_j . ',"paragraph":' . $stmt_p_j . ',"bg":"canvas"} /-->';

    // ── Features ───────────────────────────────────────────────────────────────────
    $b[] = '<!-- wp:snel/features {"cards":' . $cards_j . '} /-->';

    return implode("\n\n", $b);
}

// ---------------------------------------------------------------------------
// Seeder functions
// ---------------------------------------------------------------------------

function snel_seed_cases_page(bool $wipe = false): bool
{
    $content  = snel_get_cases_page_blocks();
    $existing = get_posts([
        'post_type'   => 'page',
        'name'        => 'cases',
        'post_status' => 'any',
        'numberposts' => 1,
        'fields'      => 'ids',
    ]);

    if ($existing && ! $wipe) return false;

    if ($existing && $wipe) {
        $result = wp_update_post(['ID' => $existing[0], 'post_content' => $content, 'post_status' => 'publish']);
        return ! is_wp_error($result);
    }

    $page_id = wp_insert_post([
        'post_type'    => 'page',
        'post_title'   => 'Cases',
        'post_name'    => 'cases',
        'post_content' => $content,
        'post_status'  => 'publish',
    ]);

    return ! is_wp_error($page_id);
}

function snel_seed_blog_page(bool $wipe = false): bool
{
    $content  = snel_get_blog_page_blocks();
    $existing = get_posts([
        'post_type'   => 'page',
        'name'        => 'blog',
        'post_status' => 'any',
        'numberposts' => 1,
        'fields'      => 'ids',
    ]);

    if ($existing && ! $wipe) return false;

    if ($existing && $wipe) {
        $result = wp_update_post(['ID' => $existing[0], 'post_content' => $content, 'post_status' => 'publish']);
        return ! is_wp_error($result);
    }

    $page_id = wp_insert_post([
        'post_type'    => 'page',
        'post_title'   => 'Blog',
        'post_name'    => 'blog',
        'post_content' => $content,
        'post_status'  => 'publish',
    ]);

    return ! is_wp_error($page_id);
}

function snel_seed_websites_page(bool $wipe = false): bool
{
    $content  = snel_get_websites_page_blocks();
    $existing = get_posts([
        'post_type'   => 'page',
        'name'        => 'websites',
        'post_status' => 'any',
        'numberposts' => 1,
        'fields'      => 'ids',
    ]);

    if ($existing && ! $wipe) return false;

    if ($existing && $wipe) {
        $result = wp_update_post(['ID' => $existing[0], 'post_content' => $content, 'post_status' => 'publish']);
        return ! is_wp_error($result);
    }

    $page_id = wp_insert_post([
        'post_type'    => 'page',
        'post_title'   => 'Websites',
        'post_name'    => 'websites',
        'post_content' => $content,
        'post_status'  => 'publish',
    ]);

    return ! is_wp_error($page_id);
}

function snel_get_contact_page_blocks(): string
{
    $h_class = 'font-semibold text-slate-950 text-2xl/tight md:text-3xl/tight lg:text-4xl/tight xl:text-5xl/tight';

    $b = [];

    // ── Hero ──────────────────────────────────────────────────────────────────────
    $b[] = '<!-- wp:snel/hero {"size":"md"} -->'
        . "\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-eyebrow\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-eyebrow\"><!-- wp:snel/badge-text /--></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n"
        . "\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-middle\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-middle\"><!-- wp:snel/heading {\"level\":\"h1\",\"className\":\"" . $h_class . "\"} -->"
        . "\n<h1 class=\"wp-block-snel-heading snel-heading max-w-4xl snel-h-xl " . $h_class . "\">Vertel ons over<br><span class=\"snel-muted\">jouw project.</span></h1>"
        . "\n<!-- /wp:snel/heading --></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n"
        . "\n<!-- wp:snel/slot {\"max\":2,\"orientation\":\"horizontal\",\"className\":\"snel-slot-lower\"} -->"
        . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-lower\"><!-- wp:snel/paragraph {\"size\":\"lg\"} -->"
        . "\n<p class=\"wp-block-snel-paragraph snel-text max-w-4xl snel-text-lg\">We reageren binnen één werkdag. Liever direct bellen?&nbsp;<span class=\"snel-muted\">Dat kan ook.</span></p>"
        . "\n<!-- /wp:snel/paragraph --></div>"
        . "\n<!-- /wp:snel/slot -->"
        . "\n<!-- /wp:snel/hero -->";

    // ── Contact form ───────────────────────────────────────────────────────────────
    $b[] = '<!-- wp:snel/contact-form /-->';

    return implode("\n\n", $b);
}

function snel_seed_contact_page(bool $wipe = false): bool
{
    $content  = snel_get_contact_page_blocks();
    $existing = get_posts([
        'post_type'   => 'page',
        'name'        => 'contact',
        'post_status' => 'any',
        'numberposts' => 1,
        'fields'      => 'ids',
    ]);

    if ($existing && ! $wipe) return false;

    if ($existing && $wipe) {
        $result = wp_update_post(['ID' => $existing[0], 'post_content' => $content, 'post_status' => 'publish']);
        return ! is_wp_error($result);
    }

    $page_id = wp_insert_post([
        'post_type'    => 'page',
        'post_title'   => 'Contact',
        'post_name'    => 'contact',
        'post_content' => $content,
        'post_status'  => 'publish',
        'page_template' => 'page-contact.php',
    ]);

    return ! is_wp_error($page_id);
}

function snel_seed_front_page(bool $wipe = false): bool
{
    $content       = snel_get_front_page_blocks();
    $page_on_front = (int) get_option('page_on_front');

    if ($page_on_front && ! $wipe) {
        return false;
    }

    if ($page_on_front && $wipe) {
        $result = wp_update_post([
            'ID'           => $page_on_front,
            'post_content' => $content,
            'post_status'  => 'publish',
        ]);
        return ! is_wp_error($result);
    }

    $page_id = wp_insert_post([
        'post_type'    => 'page',
        'post_title'   => 'Home',
        'post_name'    => 'home',
        'post_content' => $content,
        'post_status'  => 'publish',
    ]);

    if (is_wp_error($page_id)) return false;

    update_option('show_on_front', 'page');
    update_option('page_on_front', $page_id);

    return true;
}
