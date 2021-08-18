import Core from './core.src';
import Parser from './parser';
import Util from './util';
import Image from './image';
import Configuration from './configuration';
import Listeners from './listeners';
import IntegrationModel from './integrationmodel';
import MathML from './mathml';
import Latex from './latex';
import Test from './test';

// Expose WirisPlugin variable to the window.
window.WirisPlugin = {
  Core,
  Parser,
  Image,
  MathML,
  Util,
  Configuration,
  Listeners,
  IntegrationModel,
  Latex,
  Test,
};
