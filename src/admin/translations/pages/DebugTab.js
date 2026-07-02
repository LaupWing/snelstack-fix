import { useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { ChevronRight, ChevronDown } from 'lucide-react';

function Section( { title, count, children } ) {
    const [ open, setOpen ] = useState( false );
    return (
        <div className="border border-gray-200 rounded-lg mb-2 overflow-hidden">
            <button
                onClick={ () => setOpen( ! open ) }
                className="w-full flex items-center gap-2 px-4 py-2.5 text-left text-sm font-semibold text-gray-700 hover:bg-gray-50"
            >
                { open ? <ChevronDown className="w-4 h-4" /> : <ChevronRight className="w-4 h-4" /> }
                <span>{ title }</span>
                { count !== undefined && (
                    <span className="ml-1 text-xs font-normal text-gray-400">({ count })</span>
                ) }
            </button>
            { open && (
                <div className="px-4 pb-4 border-t border-gray-100">
                    { children }
                </div>
            ) }
        </div>
    );
}

function Json( { data } ) {
    return (
        <pre
            className="text-xs bg-gray-50 border border-gray-100 rounded p-3 mt-3 overflow-auto"
            style={ { maxHeight: 420, fontFamily: 'monospace', whiteSpace: 'pre' } }
        >
            { JSON.stringify( data, null, 2 ) }
        </pre>
    );
}

export default function DebugTab() {
    const cfg = window.snelTranslations || {};
    const [ data, setData ] = useState( null );
    const [ error, setError ] = useState( '' );

    useEffect( () => {
        fetch( `${ cfg.restUrl }/debug`, { headers: { 'X-WP-Nonce': cfg.nonce } } )
            .then( ( r ) => r.json() )
            .then( setData )
            .catch( () => setError( __( 'Could not load debug data.', 'snel' ) ) );
    }, [] );

    if ( error ) {
        return <p className="text-sm text-red-600">{ error }</p>;
    }
    if ( ! data ) {
        return <p className="text-sm text-gray-500">{ __( 'Loading…', 'snel' ) }</p>;
    }

    const groups = data.translationGroups || [];

    return (
        <div className="max-w-3xl">
            <p className="text-sm text-gray-500 mb-4">
                { __( 'Read-only view of the current translation data in the database — for debugging, so you can inspect it without opening SQL.', 'snel' ) }
            </p>

            <Section title={ __( 'Languages config', 'snel' ) }>
                <Json data={ data.languagesConfig } />
            </Section>

            <Section title={ __( 'Default / enabled', 'snel' ) }>
                <Json data={ { defaultLang: data.defaultLang, enabledLangs: data.enabledLangs } } />
            </Section>

            <Section title={ __( 'Translation groups', 'snel' ) } count={ groups.length }>
                <Json data={ groups } />
            </Section>

            <Section title={ __( 'Theme strings', 'snel' ) }>
                <Json data={ data.themeStrings } />
            </Section>
        </div>
    );
}
