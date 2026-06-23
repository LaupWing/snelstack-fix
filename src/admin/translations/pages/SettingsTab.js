import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { SelectControl, Button } from '@wordpress/components';

export default function SettingsTab() {
    const data = window.snelTranslations || {};
    const langs = data.languages || []; // [{ code, label, default, enabled }]

    const [ defaultLang, setDefaultLang ] = useState(
        data.defaultLang || ( langs[ 0 ] && langs[ 0 ].code ) || ''
    );
    const [ enabled, setEnabled ] = useState(
        langs.filter( ( l ) => l.enabled ).map( ( l ) => l.code )
    );
    const [ busy, setBusy ] = useState( false );
    const [ status, setStatus ] = useState( '' );

    const isOn = ( code ) => code === defaultLang || enabled.includes( code );

    const toggle = ( code ) => {
        if ( code === defaultLang ) return; // default is always on
        setEnabled( ( prev ) =>
            prev.includes( code ) ? prev.filter( ( c ) => c !== code ) : [ ...prev, code ]
        );
    };

    const save = async () => {
        setBusy( true );
        setStatus( '' );
        // Default must always be enabled.
        const enabledLangs = Array.from( new Set( [ ...enabled, defaultLang ] ) );
        try {
            const res = await fetch( `${ data.restUrl }/settings`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': data.nonce },
                body: JSON.stringify( { defaultLang, enabledLangs } ),
            } );
            const json = await res.json();
            setStatus( json && json.success ? __( 'Saved.', 'snel' ) : __( 'Could not save.', 'snel' ) );
        } catch ( e ) {
            setStatus( __( 'Request failed.', 'snel' ) );
        }
        setBusy( false );
    };

    // The default dropdown only offers enabled languages.
    const defaultOptions = langs
        .filter( ( l ) => isOn( l.code ) )
        .map( ( l ) => ( { label: l.label, value: l.code } ) );

    return (
        <div className="max-w-xl">
            {/* Enabled languages */}
            <div className="mb-6">
                <p className="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">
                    { __( 'Enabled languages', 'snel' ) }
                </p>
                <div className="flex flex-wrap gap-x-5 gap-y-2">
                    { langs.map( ( l ) => (
                        <label
                            key={ l.code }
                            className={ `inline-flex items-center gap-2 text-sm ${
                                l.code === defaultLang ? 'text-gray-400' : 'text-gray-700 cursor-pointer'
                            }` }
                        >
                            <input
                                type="checkbox"
                                checked={ isOn( l.code ) }
                                disabled={ l.code === defaultLang }
                                onChange={ () => toggle( l.code ) }
                            />
                            <strong>{ l.label }{ l.code === defaultLang ? ' · src' : '' }</strong>
                        </label>
                    ) ) }
                </div>
                <p className="text-xs text-gray-400 mt-2">
                    { __( 'Turn languages on/off. The default (source) language is always on. Add new languages in inc/translations/config/languages.php.', 'snel' ) }
                </p>
            </div>

            {/* Default language */}
            <SelectControl
                label={ __( 'Default (source) language', 'snel' ) }
                value={ defaultLang }
                options={ defaultOptions }
                onChange={ setDefaultLang }
                __nextHasNoMarginBottom
            />
            <p className="text-xs text-gray-400 mt-1 mb-4">
                { __( 'The language content is written in — no URL prefix; all others get one (e.g. /en/).', 'snel' ) }
            </p>

            { data.translationsExist && (
                <p className="text-sm mb-4 p-2.5 rounded" style={ { background: '#fcf3e3', borderLeft: '4px solid #dba617' } }>
                    <strong>⚠ { __( 'Translations already exist.', 'snel' ) }</strong>{ ' ' }
                    { __( 'Changing the default language moves every URL (the prefix shifts) — best done before launch.', 'snel' ) }
                </p>
            ) }

            <Button variant="primary" onClick={ save } isBusy={ busy } disabled={ busy }>
                { __( 'Save', 'snel' ) }
            </Button>
            { status && <span className="ml-3 text-sm text-gray-600">{ status }</span> }
        </div>
    );
}
