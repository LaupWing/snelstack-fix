import { useBlockProps, useInnerBlocksProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import SectionControl, { getSectionStyle, getSectionClass } from '../../components/SectionControl';

const ALLOWED_BLOCKS = [
    'core/paragraph',
    'core/heading',
    'core/list',
    'core/image',
    'core/quote',
    'core/separator',
    'core/table',
    'core/code',
    'core/preformatted',
    'core/html',
    'core/embed',
];

export default function Edit({ attributes, setAttributes }) {
    const { bg, size, paddingSize, disableTop, disableBottom } = attributes;
    const isDark = bg === 'dark' || bg === 'canvas';

    const blockProps = useBlockProps({
        className: `snel-content ${getSectionClass(bg)}`,
        style: getSectionStyle(bg),
    });

    const innerProps = useInnerBlocksProps(
        {
            className: `mx-auto w-full max-w-3xl px-4 py-8 md:px-8 prose prose-slate max-w-none${isDark ? ' prose-invert' : ''}${size === 'lg' ? ' lg:prose-lg' : ''}`,
        },
        {
            allowedBlocks: ALLOWED_BLOCKS,
            template: [['core/paragraph', { placeholder: 'Schrijf hier je content...' }]],
        }
    );

    return (
        <>
            <InspectorControls>
                <SectionControl
                    value={bg} onChange={(v) => setAttributes({ bg: v })}
                    size={paddingSize} onSizeChange={(v) => setAttributes({ paddingSize: v })}
                    disableTop={disableTop} onDisableTopChange={(v) => setAttributes({ disableTop: v })}
                    disableBottom={disableBottom} onDisableBottomChange={(v) => setAttributes({ disableBottom: v })}
                />
                <PanelBody title={__('Typografie', 'snel')} initialOpen={false}>
                    <SelectControl
                        label={__('Tekstgrootte', 'snel')}
                        value={size}
                        options={[
                            { label: 'Standaard', value: 'base' },
                            { label: 'Groot (lg)',  value: 'lg'   },
                        ]}
                        onChange={(v) => setAttributes({ size: v })}
                        __nextHasNoMarginBottom
                    />
                </PanelBody>
            </InspectorControls>

            <section {...blockProps}>
                <div {...innerProps} />
            </section>
        </>
    );
}
