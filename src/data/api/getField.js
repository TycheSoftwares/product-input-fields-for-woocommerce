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
const getField = async (id, productID) => {
    try {
        if ( productID ) {
            const response = await apiFetch({ path: `/pif/v1/products/${productID}/fields/${id}`, method: 'GET' });
            return response;
        } else {
            const response = await apiFetch({ path: `/pif/v1/fields/${id}`, method: 'GET' });
            return response;
        }
    } catch (error) {
        return [];
    }
};

export default getField;