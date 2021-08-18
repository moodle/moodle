import Core from '@wiris/mathtype-integration-js-dev/src/core.src'
import Parser from '@wiris/mathtype-integration-js-dev/src/parser';
import Util from '@wiris/mathtype-integration-js-dev/src/util';
import Image from '@wiris/mathtype-integration-js-dev/src/image';
import Configuration from '@wiris/mathtype-integration-js-dev/src/configuration';
import Listeners from '@wiris/mathtype-integration-js-dev/src/listeners';
import IntegrationModel from '@wiris/mathtype-integration-js-dev/src/integrationmodel';
import { TinyMceIntegration, currentInstance, instances } from './editor_plugin.src';
import Latex from '@wiris/mathtype-integration-js-dev/src/latex';
import Test from '@wiris/mathtype-integration-js-dev/src/test';

// Expose WirisPlugin variable to the window.
window.WirisPlugin = {
    Core: Core,
    Parser: Parser,
    Image: Image,
    Util: Util,
    Configuration: Configuration,
    Listeners: Listeners,
    IntegrationModel: IntegrationModel,
    currentInstance: currentInstance,
    instances: instances,
    TinyMceIntegration: TinyMceIntegration,
    Latex: Latex,
    Test : Test
}
