import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps } from '@wordpress/block-editor';

export default function Edit({ attributes }) {
	return (
		<div {...useBlockProps()}>
			<ServerSideRender block="snel/cases" attributes={attributes} />
		</div>
	);
}
