<?php
/**
 * Case Slide — one slider item: image + optional label/value card.
 *
 * @var array $attributes
 */

defined('ABSPATH') || exit;

$image_id = (int) ($attributes['imageId'] ?? 0);
$img_url  = $image_id ? wp_get_attachment_image_url($image_id, 'full') : ($attributes['imageUrl'] ?? '');
if (! $img_url) {
    // No image picked: fall back to the post's featured image (seeded slides).
    $img_url = get_the_post_thumbnail_url(null, 'full');
}
$img_alt  = trim($attributes['imageAlt'] ?? '') ?: get_the_title();
$label    = trim($attributes['label'] ?? '');
$value    = trim($attributes['value'] ?? '');

if (! $img_url) {
    return;
}

// First rendered slide paints the LCP: eager + high priority, rest lazy.
static $snel_case_slide_first = true;
$is_first = $snel_case_slide_first;
$snel_case_slide_first = false;
?>
<div class="snel-case-slide relative w-full flex-none snap-center">
    <img
        src="<?php echo esc_url($img_url); ?>"
        alt="<?php echo esc_attr($img_alt); ?>"
        class="w-full h-full object-cover object-center aspect-[4/5] md:aspect-[16/9]"
        <?php echo $is_first ? 'loading="eager" fetchpriority="high"' : 'loading="lazy"'; ?>
    />

    <?php if ($label && $value) : ?>
    <div class="absolute bottom-0 left-0 right-0 z-10 p-4 lg:p-8">
        <div class="inline-flex rounded-xl p-4 lg:p-6 border border-white/10 bg-black/25 backdrop-blur-sm">
            <div>
                <span class="block text-xs font-normal text-white/50 sm:text-sm"><?php echo esc_html($label); ?></span>
                <span class="mt-1 block text-base font-normal text-white sm:text-lg md:text-xl lg:text-2xl"><?php echo esc_html($value); ?></span>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
