/**
 * Panel — Save.
 *
 * Dynamic block: the frame is rendered by render.php. We only persist the
 * InnerBlocks markup, which WordPress passes to render.php as $content.
 */
import { InnerBlocks } from '@wordpress/block-editor';

export default function save() {
	return <InnerBlocks.Content />;
}
