import {getTinyMCE} from 'editor_tiny/loader';

import {component} from './common';
import * as Configuration from './configuration';
import * as Config from 'core/config';

export const baseUrl = `${Config.wwwroot}/lib/editor/tiny/plugins/wiris/js`;

export default new Promise(async(resolve, reject) => {
    // eslint-disable-next-line no-unused-vars
    const [tinyMCE] = await Promise.all([
        getTinyMCE(),
    ]);

    const head = document.querySelector('head');
    let script = head.querySelector('script[data-mathtype="mathtype"]');
    // If plugin.min.js file is already loaded, execute the init and resolve the promise
    if (script) {
        resolve([`${component}/plugin`, Configuration]);
    }

    // Load plugin.min.js to the head
    script = document.createElement('script');
    script.dataset.mathtype = 'mathtype';
    script.src = `${baseUrl}/plugin.min.js`;
    script.async = true;

    script.addEventListener('load', () => {
        resolve([`${component}/plugin`, Configuration]);
    }, false);

    script.addEventListener('error', (err) => {
        reject(err);
    }, false);

    head.append(script);
});