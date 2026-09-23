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
const deleteField = async ( fieldID, productID ) => {
    try {
        console.log( "Deleting field with ID: ", fieldID );
        if ( productID ) {
            const response = await apiFetch({ path: `/pif/v1/products/${productID}/fields/${fieldID}`, method: 'DELETE' });
            return response;
        } else {
            const response = await apiFetch({ path: `/pif/v1/fields/${fieldID}`, method: 'DELETE' });
            return response;
        }
    } catch (error) {
        return [];
    }
};

export default deleteField;