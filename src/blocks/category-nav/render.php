<?php
/**
 * Category Nav — scrollable category filter bar with archive links.
 * Active state is auto-detected: highlights current category on archive pages.
 * Overflow is scrollable without a scrollbar; view.js toggles chevron buttons.
 *
 * @var array $attributes
 */

defined('ABSPATH') || exit;

$all_label = $attributes['allLabel'] ?? 'Alle';
$post_type = $attributes['postType'] ?? 'post';

// Get all categories that have published posts of this post type.
$cats = get_categories([
    'taxonomy'   => 'category',
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC',
]);

if (empty($cats)) {
    return;
}

// For 'post' the archive is the posts page (Settings → Reading), which the
// translations plugin keeps language-aware. Don't hardcode /blog/ as a fallback —
// that ignores both a renamed posts page and the language prefix.
$posts_page  = (int) get_option('page_for_posts');
$archive_url = ($post_type === 'post' && $posts_page)
    ? get_permalink($posts_page)
    : (get_post_type_archive_link($post_type) ?: home_url('/'));
$current_cat_id = is_category() ? get_queried_object_id() : 0;

$inactive_class = 'scroll-mx-2 snap-start shrink-0 whitespace-nowrap font-medium antialiased text-sm px-4 h-11 flex items-center transition duration-300 rounded-md text-slate-700 hover:bg-white hover:shadow-[0px_4px_8px_rgba(34,42,53,0.05),0px_0px_0px_1px_rgba(34,42,53,0.04),0px_1px_5px_-4px_rgba(19,19,22,0.7)]';
$active_class   = 'scroll-mx-2 snap-start shrink-0 whitespace-nowrap font-medium antialiased text-sm px-4 h-11 flex items-center transition duration-300 rounded-md bg-slate-950 text-teal-400';

$btn_class = 'snel-catnav-btn absolute top-1/2 -translate-y-1/2 flex h-11 w-7 items-center justify-center text-slate-400 hover:text-slate-900';
?>
<div class="snel-category-nav relative w-max min-w-0 max-w-full">

    <button type="button"
            class="<?php echo esc_attr($btn_class); ?> snel-catnav-prev -left-7"
            aria-label="<?php echo esc_attr(snel__('Scroll naar links')); ?>">
        <svg viewBox="0 0 20 20" fill="currentColor" class="size-5"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L8.832 10l3.938 3.71a.75.75 0 1 1-1.04 1.08l-4.5-4.25a.75.75 0 0 1 0-1.08l4.5-4.25a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd"/></svg>
    </button>

    <nav class="snel-catnav-track flex overflow-x-auto snap-x w-full rounded-lg bg-slate-100 p-1 space-x-1">

        <a href="<?php echo esc_url($archive_url); ?>"
           <?php if ($current_cat_id === 0) : ?>aria-current="page"<?php endif; ?>
           class="<?php echo esc_attr($current_cat_id === 0 ? $active_class : $inactive_class); ?>">
            <?php echo esc_html($all_label); ?>
        </a>

        <?php foreach ($cats as $cat) :
            $is_active = $current_cat_id === $cat->term_id;
        ?>
        <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>"
           <?php if ($is_active) : ?>aria-current="page"<?php endif; ?>
           class="<?php echo esc_attr($is_active ? $active_class : $inactive_class); ?>">
            <?php echo esc_html($cat->name); ?>
        </a>
        <?php endforeach; ?>

    </nav>

    <button type="button"
            class="<?php echo esc_attr($btn_class); ?> snel-catnav-next -right-7"
            aria-label="<?php echo esc_attr(snel__('Scroll naar rechts')); ?>">
        <svg viewBox="0 0 20 20" fill="currentColor" class="size-5"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd"/></svg>
    </button>

</div>
