/**
 * Snel Paragraph — static save. Stores the rich HTML (bold/muted spans included).
 */
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { textClass } from './classes';

export default function save({ attributes }) {
	const { content, size, align, leading } = attributes;
	const blockProps = useBlockProps.save({ className: textClass(size, align, leading) });
	return <RichText.Content {...blockProps} tagName="p" value={content} />;
}
