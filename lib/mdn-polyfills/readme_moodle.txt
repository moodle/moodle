The steps are essentially:
1) Install mdn-polyfills package

    npm install --no-save  mdn-polyfills

2) Join them all together:

    cd node_modules/mdn-polyfills
    cat CustomEvent.* Element.* Function.* HTMLCanvasElement.* MouseEvent.* Node.prototype.* NodeList.* > ../../lib/mdn-polyfills/polyfill.js

3) Uninstall the package again

    npm uninstall --no-save mdn-polyfills
