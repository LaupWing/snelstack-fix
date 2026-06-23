/**
 * Slot — Save. Static: renders the is-layout-flex wrapper + its inner blocks.
 * Layout itself lives in slot.css, keyed on the slot identity class.
 */
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

export default function save() {
	const blockProps = useBlockProps.save({ className: 'is-layout-flex' });
	const innerProps = useInnerBlocksProps.save(blockProps);
	return <div {...innerProps} />;
}
