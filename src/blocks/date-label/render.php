<?php
/**
 * Snel Date Label — publish (or modified) date of the post in scope.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

defined('ABSPATH') || exit;

$post_id = $block->context['postId'] ?? get_the_ID();
$format  = $attributes['format'] !== '' ? $attributes['format'] : get_option('date_format');

if ($post_id) {
	$display = ! empty($attributes['showModified'])
		? get_the_modified_date($format, $post_id)
		: get_the_date($format, $post_id);
	$machine = ! empty($attributes['showModified'])
		? get_the_modified_date('c', $post_id)
		: get_the_date('c', $post_id);
} else {
	// No post in scope (e.g. editor preview outside a post) — show today.
	$display = wp_date($format);
	$machine = wp_date('c');
}
?>
<time datetime="<?php echo esc_attr($machine); ?>" class="snel-date-label block text-sm uppercase tracking-wide font-semibold text-slate-400 antialiased"><?php echo esc_html($display); ?></time>
