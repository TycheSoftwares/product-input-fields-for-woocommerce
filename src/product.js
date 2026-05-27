/**
 * WordPress dependencies.
 */
import { createRoot } from 'react-dom/client';
import '@wordpress/components/build-style/style.css';

/**
 * External dependencies
 */
import { HashRouter } from 'react-router-dom';

/**
 * Internal dependencies.
 */
import App from './app';
import { FieldBuilder } from './screens';
import './app.scss';

window.addEventListener(
    'load',
    function () {
        const container = document.querySelector('#pif-product-settings');
        const root = createRoot(container);
        const productID = pif_product_vars.post_id;
        root.render(
                <HashRouter>
                    <FieldBuilder settingsData={[]} productID={productID} />
                </HashRouter>
        );
    },
    false
);
