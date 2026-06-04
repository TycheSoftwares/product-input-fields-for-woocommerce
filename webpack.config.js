const path = require('path');
const { DefinePlugin } = require('webpack');
const defaultConfig = require('@wordpress/scripts/config/webpack.config');

module.exports = {
    ...defaultConfig,
    entry: {
		admin: path.resolve(__dirname, 'src/index.js'),
        product: path.resolve(__dirname, 'src/product.js'),
	},
    plugins: [
		...defaultConfig.plugins,
		new DefinePlugin({
			'process.env.PIF_WC_BUILD': JSON.stringify( process.env.PIF_WC_BUILD || 'false' ),
		}),
	],
	output: {
		path: path.resolve(__dirname, 'build'),
		filename: '[name].js',
	},
    externals: {
        ...defaultConfig.externals,
        '@wordpress/api-fetch': 'wp.apiFetch',
        '@wordpress/i18n': 'wp.i18n',
        '@wordpress/date': 'wp.date'
    },
    resolve: {
        alias: {
            ...defaultConfig.resolve?.alias,
            '@pif': path.resolve(__dirname, 'src/'),
        },
    },
};