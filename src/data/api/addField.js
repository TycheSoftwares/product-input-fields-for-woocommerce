/**
 * WordPress dependencies.
 */
import apiFetch from '@wordpress/api-fetch';

/**
 * Fetches rules from the WordPress REST API.
 * 
 * @param {string} params Query parameters appended to the API path for filtering or specific requests.
 * @returns {Promise<null|Object[]>} 
 */
const addField = async (fieldData, productID) => {
    if ( productID ) {
        return await apiFetch({ path: `/pif/v1/products/${productID}/fields`, method: 'POST', data: fieldData });
    } else {
        return await apiFetch({ path: `/pif/v1/fields`, method: 'POST', data: fieldData });
    }
};

export default addField;