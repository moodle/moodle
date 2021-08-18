import Core from './src/core.src.js';
import Parser from './src/parser.js';
import Util from './src/util.js';
import Image from './src/image.js';
import Configuration from './src/configuration.js';
import Listeners from './src/listeners';
import backwardsLib from './src/backwardslib.js';
import polyfills from './src/polyfills.js';
import IntegrationModel from './src/integrationmodel.js';
import MathML from './src/mathml.js';
import Latex from './src/latex';

// Expose WirisPlugin variable to the window.
window.WirisPlugin = {
    Core: Core,
    Parser: Parser,
    Image: Image,
    MathML: MathML,
    Util: Util,
    Configuration: Configuration,
    Listeners: Listeners,
    IntegrationModel: IntegrationModel,
    Latex: Latex
}
