import {call as fetchMany} from 'core/ajax';

let aiConfig = null;

/**
 * Make request for retrieving the purpose configuration for current tenant.
 *
 * @param {string} tenant the tenant identifier or null, if the tenant of the user should be used
 */
const fetchAiConfig = (tenant = null) => fetchMany([{
    methodname: 'local_ai_manager_get_ai_config',
    args: {
        tenant
    },
}])[0];

const fetchPurposeOptions = (purpose) => fetchMany([{
    methodname: 'local_ai_manager_get_purpose_options',
    args: {
        purpose
    },
}])[0];

/**
 * Executes the call to store input value.
 *
 * @param {string} tenant the tenant identifier or null, if the tenant of the user should be used
 */
export const getAiConfig = async(tenant = null) => {
    if (aiConfig === null) {
        aiConfig = await fetchAiConfig(tenant);
    }
    return aiConfig;
};

export const getPurposeOptions = async(purpose) => {
    return await fetchPurposeOptions(purpose);
};
