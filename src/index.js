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
import './app.scss';

window.addEventListener(
    'load',
    function () {
        const container = document.querySelector('#product-input-fields-for-woocommerce');
        const root = createRoot(container);
        root.render(
                <HashRouter>
                    <App />
                </HashRouter>
        );
    },
    false
);
