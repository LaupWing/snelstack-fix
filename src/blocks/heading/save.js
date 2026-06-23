/**
 * Snel Heading — static save. Stores the rich HTML (bold/muted spans included).
 */
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { headingClass } from './classes';

export default function save({ attributes }) {
	const { content, level, size, weight, align, gradient } = attributes;
	const blockProps = useBlockProps.save({ className: headingClass(level, size, weight, align, gradient) });
	return <RichText.Content {...blockProps} tagName={level} value={content} />;
}
