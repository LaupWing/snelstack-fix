<?php
/**
 * Services seeder — definitions, block builder, seed function.
 *
 * @package Snel
 */

defined('ABSPATH') || exit;

// ---------------------------------------------------------------------------
// Block builder
// ---------------------------------------------------------------------------

if (! function_exists('snel_svc_blocks')) {
    function snel_svc_blocks(array $d): string
    {
        $cards_json = json_encode($d['cards'], JSON_UNESCAPED_UNICODE);
        $flags      = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        $stmt_h_j   = json_encode($d['stmt_h'], $flags);
        $stmt_p_j   = json_encode($d['stmt_p'], $flags);

        $b = [];

        // ── Intro ─────────────────────────────────────────────────────────────
        $b[] = '<!-- wp:snel/intro {"visual":"' . $d['visual'] . '"} -->'
            // eyebrow
            . "\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-eyebrow\"} -->"
            . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-eyebrow\">"
            . "<!-- wp:snel/badge-text {\"label\":\"" . $d['badge'] . "\"} /-->"
            . "</div>"
            . "\n<!-- /wp:snel/slot -->"
            // heading
            . "\n\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-heading\"} -->"
            . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-heading\">"
            . "<!-- wp:snel/heading {\"level\":\"h1\",\"size\":\"xl\",\"weight\":\"extrabold\"} -->"
            . "\n<h1 class=\"wp-block-snel-heading snel-heading max-w-4xl snel-h-xl snel-hw-extrabold\">" . $d['intro_h'] . "</h1>"
            . "\n<!-- /wp:snel/heading -->"
            . "</div>"
            . "\n<!-- /wp:snel/slot -->"
            // body
            . "\n\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-body\"} -->"
            . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-body\">"
            . "<!-- wp:snel/paragraph {\"size\":\"md\"} -->"
            . "\n<p class=\"wp-block-snel-paragraph snel-text max-w-4xl snel-text-md\">" . $d['intro_b'] . "</p>"
            . "\n<!-- /wp:snel/paragraph -->"
            . "</div>"
            . "\n<!-- /wp:snel/slot -->"
            // cta
            . "\n\n<!-- wp:snel/slot {\"max\":2,\"orientation\":\"horizontal\",\"className\":\"snel-slot-cta\"} -->"
            . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-cta\">"
            . "<!-- wp:snel/button-gradient {\"url\":\"/contact\"} /-->"
            . "\n\n<!-- wp:snel/button {\"url\":\"/cases\"} /-->"
            . "</div>"
            . "\n<!-- /wp:snel/slot -->"
            . "\n<!-- /wp:snel/intro -->";

        // ── Panel (canvas, rounded) ────────────────────────────────────────────
        $b[] = '<!-- wp:snel/panel {"theme":"canvas","rounded":true} -->'
            // eyebrow
            . "\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-eyebrow\"} -->"
            . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-eyebrow\">"
            . "<!-- wp:snel/badge-text {\"label\":\"" . $d['panel_ey'] . "\",\"color\":\"teal\"} /-->"
            . "</div>"
            . "\n<!-- /wp:snel/slot -->"
            // middle heading
            . "\n\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-middle\"} -->"
            . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-middle\">"
            . "<!-- wp:snel/heading {\"size\":\"2xl\",\"weight\":\"extrabold\"} -->"
            . "\n<h2 class=\"wp-block-snel-heading snel-heading max-w-4xl snel-h-2xl snel-hw-extrabold\">" . $d['panel_h'] . "</h2>"
            . "\n<!-- /wp:snel/heading -->"
            . "</div>"
            . "\n<!-- /wp:snel/slot -->"
            // lower
            . "\n\n<!-- wp:snel/slot {\"max\":2,\"orientation\":\"horizontal\",\"className\":\"snel-slot-lower\"} -->"
            . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-lower\">"
            . "<!-- wp:snel/paragraph {\"size\":\"lg\"} -->"
            . "\n<p class=\"wp-block-snel-paragraph snel-text max-w-4xl snel-text-lg\">" . $d['panel_b'] . "</p>"
            . "\n<!-- /wp:snel/paragraph -->"
            . "</div>"
            . "\n<!-- /wp:snel/slot -->"
            . "\n<!-- /wp:snel/panel -->";

        // ── Features ──────────────────────────────────────────────────────────
        $b[] = '<!-- wp:snel/features {"cards":' . $cards_json . '} /-->';

        // ── Statement ─────────────────────────────────────────────────────────
        $b[] = '<!-- wp:snel/statement {"heading":' . $stmt_h_j . ',"paragraph":' . $stmt_p_j . ',"bg":"canvas"} /-->';

        // ── Partners ──────────────────────────────────────────────────────────
        $b[] = '<!-- wp:snel/partners /-->';

        // ── Content ───────────────────────────────────────────────────────────
        $b[] = '<!-- wp:snel/content -->'
            . "\n<!-- wp:heading -->"
            . "\n<h2 class=\"wp-block-heading\">" . $d['cont_h'] . "</h2>"
            . "\n<!-- /wp:heading -->"
            . "\n\n<!-- wp:paragraph -->"
            . "\n<p>" . $d['cont_b'] . "</p>"
            . "\n<!-- /wp:paragraph -->"
            . "\n\n<!-- wp:paragraph -->"
            . "\n<p>" . $d['cont_b2'] . "</p>"
            . "\n<!-- /wp:paragraph -->"
            . "\n<!-- /wp:snel/content -->";

        return implode("\n\n", $b);
    }
}

// ---------------------------------------------------------------------------
// Service definitions (inlined from seed-services.php + service-content.php)
// ---------------------------------------------------------------------------

function snel_get_service_definitions(): array
{
    return [
        [
            'menu_order' => 0,
            'nl' => [
                'title'   => 'Automatisering',
                'slug'    => 'automatisering',
                'icon'    => '⚡',
                'tagline' => 'Minder handwerk, meer resultaat',
                'headline'=> "Automatiseer.\nGroei sneller.",
                'visual'  => 'automation',
                'excerpt' => 'Repetitieve taken geautomatiseerd zodat jij je kunt focussen op groei.',
                'content' => snel_svc_blocks([
                    'visual'   => 'automation',
                    'badge'    => 'Automatisering',
                    'intro_h'  => "Repetitief werk.<br><span class=\"snel-muted\">Voortaan overbodig.</span>",
                    'intro_b'  => 'Van leadopvolging tot facturatie. Wij bouwen workflows die je bedrijfsprocessen end-to-end automatiseren met n8n en Claude.',
                    'panel_ey' => 'Hoe het werkt',
                    'panel_h'  => "Bouwen. <br>Testen. <br><span class=\"snel-muted\">Loslaten.</span>",
                    'panel_b'  => 'Wij analyseren je processen, identificeren de tijdvreters en bouwen workflows die zichzelf runnen. Jij doet het werk dat er echt toe doet.',
                    'cards'    => [
                        ['icon' => 'play-circle',  'heading' => 'Alles start vanzelf',          'body' => 'Zodra een lead binnenkomt, een formulier wordt ingevuld of een e-mail arriveert gaat de workflow direct van start.'],
                        ['icon' => 'brain',        'heading' => 'AI doet het denkwerk',         'body' => 'Aanvragen classificeren, e-mails samenvatten, klantdata verrijken — zonder dat jij er ook maar naar hoeft te kijken.'],
                        ['icon' => 'git-branch',   'heading' => 'Eén trigger, overal actie',    'body' => 'Eén event stuurt tegelijk een e-mail, update je CRM én stuurt een melding naar Slack. Alles tegelijk.'],
                        ['icon' => 'shield-check', 'heading' => 'Het gaat nooit stuk',          'body' => 'Loopt er iets mis? De workflow herstelt zichzelf en jij krijgt een melding. Geen verloren data, geen gemiste leads.'],
                        ['icon' => 'activity',     'heading' => 'Altijd inzicht',               'body' => 'Bekijk live hoeveel runs geslaagd zijn, hoeveel tijd er bespaard is en waar het proces op dit moment staat.'],
                        ['icon' => 'link-2',       'heading' => 'Werkt met wat je al gebruikt', 'body' => 'Van Notion tot HubSpot, van Gmail tot WhatsApp. Wij koppelen de tools die jij al dagelijks gebruikt.'],
                    ],
                    'stmt_h'  => "Handmatig werk <br><span class='snel-muted'>is minder groei.</span>",
                    'stmt_p'  => 'Bedrijven die automatiseren groeien sneller en maken minder fouten. Wij bouwen de workflows die dat mogelijk maken.',
                    'cont_h'  => 'Wat wij automatiseren',
                    'cont_b'  => 'Wij werken met n8n als primair automatiseringsplatform. Open-source, draait op je eigen server en geen transactiekosten. Van leadopvolging tot CRM-updates tot het automatisch genereren van rapporten.',
                    'cont_b2' => 'Elke workflow begint met een intake. Wij luisteren naar je processen, tekenen de flow uit en bouwen daarna pas. Zo weet je precies wat je krijgt voordat we beginnen.',
                ]),
            ],
            'en' => [
                'title'   => 'Automation',
                'slug'    => 'automation',
                'tagline' => 'Less manual work, more results',
                'headline'=> "Automate.\nGrow faster.",
                'excerpt' => 'Repetitive tasks automated so you can focus on growth.',
            ],
        ],
        [
            'menu_order' => 1,
            'nl' => [
                'title'   => 'Data & AI Consultancy',
                'slug'    => 'data-ai-consultancy',
                'icon'    => '🧠',
                'tagline' => 'Strategie en implementatie in een',
                'headline'=> "Wij kijken mee.\nJij groeit sneller.",
                'visual'  => 'ai',
                'excerpt' => 'We analyseren je data en processen, en bouwen dan precies wat werkt.',
                'content' => snel_svc_blocks([
                    'visual'   => 'ai',
                    'badge'    => 'Data & AI',
                    'intro_h'  => "Wij kijken mee.<br><span class=\"snel-muted\">Jij groeit sneller.</span>",
                    'intro_b'  => 'Geen generiek AI-advies. Wij analyseren jouw data, jouw processen en bouwen dan precies wat werkt voor jouw situatie.',
                    'panel_ey' => 'Onze aanpak',
                    'panel_h'  => "Analyse eerst.<br><span class=\"snel-muted\">Bouwen daarna.</span>",
                    'panel_b'  => 'Voordat we ook maar een regel code schrijven begrijpen we waar je data staat, hoe schoon het is en wat je er mee wil bereiken.',
                    'cards'    => [
                        ['icon' => 'search',         'heading' => 'Data audit',       'body' => 'Wij analyseren waar je data staat, hoe schoon het is en wat er mist.'],
                        ['icon' => 'brain',          'heading' => 'AI strategie',     'body' => 'Concreet advies over waar AI de grootste impact maakt in jouw processen.'],
                        ['icon' => 'hammer',         'heading' => 'Implementatie',    'body' => 'Van advies naar werkende oplossing. Wij bouwen het zelf.'],
                        ['icon' => 'database',       'heading' => 'Data cleanup',     'body' => 'Inconsistente, dubbele of verouderde data opgeschoond en gestructureerd.'],
                        ['icon' => 'link-2',         'heading' => 'Integraties',      'body' => 'Al je databronnen gekoppeld zodat AI altijd de meest actuele informatie heeft.'],
                        ['icon' => 'graduation-cap', 'heading' => 'Kennisoverdracht', 'body' => 'Jij leert hoe je het systeem beheert. Geen afhankelijkheid van ons.'],
                    ],
                    'stmt_h'  => "AI is pas nuttig<br><span class='snel-muted'>als de data klopt.</span>",
                    'stmt_p'  => 'De meeste AI-projecten mislukken niet door slechte modellen, maar door slechte data. Wij zorgen dat de basis op orde is voordat we iets bouwen.',
                    'cont_h'  => 'Voor wie is dit?',
                    'cont_b'  => 'Dit is voor bedrijven die weten dat er waarde in hun data zit, maar niet precies weten hoe ze die eruit halen. Of voor teams die AI willen inzetten maar niet weten waar te beginnen.',
                    'cont_b2' => 'Wij nemen je mee van nul naar een werkende AI-oplossing. Met concrete resultaten, geen vage beloftes.',
                ]),
            ],
            'en' => [
                'title'   => 'Data & AI Consultancy',
                'slug'    => 'data-ai-consultancy',
                'tagline' => 'Strategy and implementation in one',
                'headline'=> "We look along.\nYou grow faster.",
                'excerpt' => 'We analyse your data and processes, then build exactly what works.',
            ],
        ],
        [
            'menu_order' => 2,
            'nl' => [
                'title'   => 'Custom Software',
                'slug'    => 'custom-software',
                'icon'    => '💻',
                'tagline' => 'Jouw tool, jouw regels',
                'headline'=> "Software op maat.\nZonder compromis.",
                'visual'  => 'software',
                'excerpt' => 'Interne tools en dashboards die precies doen wat jij nodig hebt.',
                'content' => snel_svc_blocks([
                    'visual'   => 'software',
                    'badge'    => 'Custom Software',
                    'intro_h'  => "Software op maat.<br><span class=\"snel-muted\">Zonder compromis.</span>",
                    'intro_b'  => 'Generieke software dwingt je om je processen aan te passen aan het systeem. Wij bouwen het systeem dat past bij jouw processen.',
                    'panel_ey' => 'Wat wij bouwen',
                    'panel_h'  => "Van dashboard<br><span class=\"snel-muted\">tot platform.</span>",
                    'panel_b'  => 'Interne tools, klantportals, dashboards, API koppelingen. Gebouwd met Laravel of WordPress, afhankelijk van wat het beste past.',
                    'cards'    => [
                        ['icon' => 'layout-dashboard', 'heading' => 'Dashboards',    'body' => 'Realtime inzicht in je data. Gebouwd precies zoals jij het wil zien.'],
                        ['icon' => 'users',            'heading' => 'Klantportalen', 'body' => 'Geef klanten toegang tot hun eigen omgeving, facturen of projectstatus.'],
                        ['icon' => 'plug',             'heading' => 'API koppelingen','body' => 'Verbind met externe diensten, databases of bestaande software.'],
                        ['icon' => 'shield',           'heading' => 'Veilig',         'body' => 'Security-first development met best practices voor authenticatie en data.'],
                        ['icon' => 'smartphone',       'heading' => 'Responsive',     'body' => 'Werkt perfect op desktop, tablet en mobiel zonder concessies.'],
                        ['icon' => 'trending-up',      'heading' => 'Schaalbaar',     'body' => 'Architectuur die meeschaalt naarmate je bedrijf groeit.'],
                    ],
                    'stmt_h'  => "Van de plank<br><span class='snel-muted'>past nooit perfect.</span>",
                    'stmt_p'  => 'Generieke software dwingt je om je processen aan te passen aan het systeem. Custom software past zich aan aan jou en geeft je een concurrentievoordeel dat niemand kan kopiëren.',
                    'cont_h'  => 'Ons proces',
                    'cont_b'  => 'We starten met een intake om te begrijpen wat je nodig hebt. Daarna bouwen we iteratief: eerst een werkend prototype, dan verfijnen we op basis van je feedback.',
                    'cont_b2' => 'Wij werken met Laravel voor complexe applicaties en WordPress voor content-gedreven platforms. Altijd met Tailwind CSS en moderne development practices.',
                ]),
            ],
            'en' => [
                'title'   => 'Custom Software',
                'slug'    => 'custom-software',
                'tagline' => 'Your tool, your rules',
                'headline'=> "Custom software.\nNo compromise.",
                'excerpt' => 'Internal tools and dashboards that do exactly what you need.',
            ],
        ],
        [
            'menu_order' => 3,
            'nl' => [
                'title'   => 'Vibe Coding Redding',
                'slug'    => 'vibe-coding-redding',
                'icon'    => '🛟',
                'tagline' => 'AI-code stuk? Wij fixen het',
                'headline'=> "Vibe coding misgegaan?\nWij lossen het op.",
                'visual'  => 'vibe',
                'excerpt' => 'Je AI-gegenereerde code werkt niet meer. Wij duiken erin en fixen het snel.',
                'content' => snel_svc_blocks([
                    'visual'   => 'vibe',
                    'badge'    => 'Vibe Coding Rescue',
                    'intro_h'  => "Vibe coding misgegaan?<br><span class=\"snel-muted\">Wij lossen het op.</span>",
                    'intro_b'  => 'Cursor, Bolt en v0 zijn krachtige tools. Maar als er iets kapotgaat, heb je een echte developer nodig die begrijpt wat er onder de motorkap gebeurt.',
                    'panel_ey' => 'Hoe we te werk gaan',
                    'panel_h'  => "Diagnose.<br>Fix.<br><span class=\"snel-muted\">Uitleg.</span>",
                    'panel_b'  => 'Wij duiken in je code, vinden de oorzaak van het probleem en fixen het zonder andere dingen stuk te maken. Je krijgt ook uitleg zodat je het de volgende keer zelf herkent.',
                    'cards'    => [
                        ['icon' => 'search',          'heading' => 'Snelle diagnose', 'body' => 'Wij analyseren de foutmelding en vinden de oorzaak, ook als de AI dat niet kon.'],
                        ['icon' => 'zap',             'heading' => 'Binnen 24 uur',   'body' => 'De meeste problemen zijn binnen een dag opgelost. Geen lange wachttijden.'],
                        ['icon' => 'shield-check',    'heading' => 'Geen bijschade',  'body' => 'We fixen het probleem zonder andere onderdelen van je project te breken.'],
                        ['icon' => 'book-open',       'heading' => 'Uitleg erbij',    'body' => 'Je leert wat er fout was en hoe je het in de toekomst voorkomt.'],
                        ['icon' => 'test-tube',       'heading' => 'Getest',          'body' => 'Na de fix testen we grondig of alles werkt zoals verwacht.'],
                        ['icon' => 'message-circle',  'heading' => 'Direct contact',  'body' => 'Directe communicatie via WhatsApp of e-mail. Geen tickets of wachtrijen.'],
                    ],
                    'stmt_h'  => "AI schrijft code.<br><span class='snel-muted'>Jij neemt de consequenties.</span>",
                    'stmt_p'  => 'Vibe coding werkt totdat het dat niet meer doet. Dan heb je iemand nodig die de code echt begrijpt en snel kan handelen.',
                    'cont_h'  => 'Wat kunnen wij fixen?',
                    'cont_b'  => 'TypeErrors, API-fouten, build problemen, performance issues, authenticatie bugs, database queries die verkeerd lopen. Als het code is, kunnen wij het fixen.',
                    'cont_b2' => 'Wij werken met React, Next.js, Laravel, WordPress, Node.js en meer. Stuur ons je foutmelding en we vertellen je binnen een uur of we je kunnen helpen.',
                ]),
            ],
            'en' => [
                'title'   => 'Vibe Coding Rescue',
                'slug'    => 'vibe-coding-rescue',
                'tagline' => 'AI code broken? We fix it',
                'headline'=> "Vibe coding gone wrong?\nWe fix it.",
                'excerpt' => 'Your AI-generated code stopped working. We dive in and fix it fast.',
            ],
        ],
    ];
}

// ---------------------------------------------------------------------------
// Seeder functions
// ---------------------------------------------------------------------------

function snel_seed_services(): int
{
    global $wpdb;

    $definitions = snel_get_service_definitions();

    // Wipe existing services so reseed is always clean.
    $old = get_posts(['post_type' => 'service', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids']);
    foreach ($old as $id) {
        wp_delete_post($id, true);
    }

    $count = 0;

    foreach ($definitions as $def) {
        $nl    = $def['nl'];
        $order = $def['menu_order'];

        $nl_id = wp_insert_post([
            'post_type'    => 'service',
            'post_status'  => 'publish',
            'post_title'   => $nl['title'],
            'post_name'    => $nl['slug'],
            'post_excerpt' => $nl['excerpt'],
            'post_content' => $nl['content'] ?? '',
            'menu_order'   => $order,
        ]);

        if (is_wp_error($nl_id)) {
            continue;
        }

        $wpdb->update($wpdb->posts, ['post_name' => $nl['slug']], ['ID' => $nl_id]);
        snel_set_service_meta($nl_id, $nl);
        $count++;
    }

    flush_rewrite_rules();

    return $count;
}

function snel_set_service_meta(int $post_id, array $data, array $fallback = []): void
{
    update_post_meta($post_id, '_service_icon',     $data['icon']     ?? ($fallback['icon']     ?? ''));
    update_post_meta($post_id, '_service_tagline',  $data['tagline']  ?? ($fallback['tagline']  ?? ''));
    update_post_meta($post_id, '_service_headline', $data['headline'] ?? ($fallback['headline'] ?? ''));
    update_post_meta($post_id, '_service_visual',   $data['visual']   ?? ($fallback['visual']   ?? ''));
}
