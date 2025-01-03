import {call as fetchMany} from 'core/ajax';

/**
 * Call to store input value
 * @param {string} purpose
 * @param {string} prompt
 * @param {array} options
 * @returns {mixed}
 */
const execMakeRequest = (
    purpose,
    prompt,
    options
) => fetchMany([{
    methodname: 'local_ai_manager_post_query',
    args: {
        purpose,
        prompt,
        options
    },
}])[0];

/**
 * Executes the call to store input value.
 * @param {string} purpose
 * @param {string} prompt
 * @param {array} options
 * @returns {mixed}
 */
export const makeRequest = async(purpose, prompt, options = {}) => {
    return execMakeRequest(purpose, prompt, JSON.stringify(options));
};
