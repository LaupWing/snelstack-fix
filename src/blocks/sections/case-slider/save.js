/**
 * Case Slider — Save.
 *
 * Dynamic block: the slider shell is rendered by render.php. We only persist
 * the InnerBlocks (snel/case-slide) markup, passed to render.php as $content.
 */
import { InnerBlocks } from '@wordpress/block-editor';

export default function save() {
	return <InnerBlocks.Content />;
}
