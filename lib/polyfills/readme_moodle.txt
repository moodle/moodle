The steps are essentially:
1) Install required packages

    npm install --no-save mdn-polyfills url-polyfill regenerator-runtime core-js-bundle

2) Join them all together:

    cd node_modules/mdn-polyfills
    cat CustomEvent.* Element.* Function.* HTMLCanvasElement.* MouseEvent.* Node.prototype.* NodeList.* > ../../lib/polyfills/polyfill.js

    cd ../url-polyfill/
    cat url-polyfill.min.js >> ../../lib/polyfills/polyfill.js

    cd ../regenerator-runtime
    cat runtime.js >> ../../lib/polyfills/polyfill.js

    cd ../core-js-bundle
    cat minified.js >> ../../lib/polyfills/polyfill.js
    sed -i '/\/\/\# sourceMappingURL=minified.js.map/d' ../../lib/polyfills/polyfill.js

3) Uninstall the packages again

    npm uninstall --no-save mdn-polyfills url-polyfill regenerator-runtime core-js-bundle
