/**
 * Slot — internal layout container with a hard max-block cap.
 *
 * Uses WP's `is-layout-flex` so the editor doesn't auto-center children (its
 * margin-inline:auto rule skips is-layout-flex). The actual flex layout lives in
 * slot.css, keyed on the slot identity class (snel-slot-*).
 *
 * `max` (0 = unlimited) hides the appender once full, enforcing "1 block" /
 * "1–2 blocks" per slot.
 */
import { useBlockProps, useInnerBlocksProps, InnerBlocks } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';

export default function Edit({ clientId, attributes }) {
	const { max, orientation } = attributes;
	const count = useSelect((select) => select('core/block-editor').getBlockCount(clientId), [clientId]);
	const atMax = max > 0 && count >= max;

	const blockProps = useBlockProps({ className: 'is-layout-flex' });
	const innerProps = useInnerBlocksProps(blockProps, {
		templateLock: false,
		orientation: orientation === 'horizontal' ? 'horizontal' : 'vertical',
		renderAppender: atMax ? false : InnerBlocks.ButtonBlockAppender,
	});

	return <div {...innerProps} />;
}
