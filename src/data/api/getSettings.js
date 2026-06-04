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
const getSettings = async (params = '') => {
    try {
        const response = await apiFetch({ path: `/pif/v1/general_settings/${params}` });
        return response;
    } catch (error) {
        return [];
    }
};

export default getSettings;