The steps are essentially:
1) Install mdn-polyfills and url-polyfill packages

    npm install --no-save mdn-polyfills url-polyfill

2) Join them all together:

    cd node_modules/mdn-polyfills
    cat CustomEvent.* Element.* Function.* HTMLCanvasElement.* MouseEvent.* Node.prototype.* NodeList.* > ../../lib/mdn-polyfills/polyfill.js
    cd ../url-polyfill/
    cat url-polyfill.min.js >> ../../lib/mdn-polyfills/polyfill.js

3) Uninstall the packages again

    npm uninstall --no-save mdn-polyfills url-polyfill
