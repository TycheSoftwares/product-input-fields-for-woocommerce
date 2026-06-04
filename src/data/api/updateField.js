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
const updateField = async ( fieldID, fieldData, productID ) => {
    try {
        console.log( "Updating field with ID: ", fieldID, " and data: ", fieldData, " for product ID: ", productID );
        if ( productID ) {
            const response = await apiFetch({ path: `/pif/v1/products/${productID}/fields/${fieldID}`, method: 'PUT', data: fieldData });
            return response;
        } else {
            const response = await apiFetch({ path: `/pif/v1/fields/${fieldID}`, method: 'PUT', data: fieldData });
            return response;
        }
    } catch (error) {
        return [];
    }
};

export default updateField;