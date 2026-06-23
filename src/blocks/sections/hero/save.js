/**
 * Hero 2 — Save. Dynamic: frame rendered by render.php; persist the slots only.
 */
import { InnerBlocks } from '@wordpress/block-editor';

export default function save() {
	return <InnerBlocks.Content />;
}
