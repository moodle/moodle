/* eslint-disable no-console */

import ServiceProvider from './serviceprovider';

export default class Test {
  static init() {
    Test.testServices();
  }

  static testServices() {
    let data;
    console.log('Testing configuration service...');
    console.log(ServiceProvider.getService('configurationjs', '', 'get'));
    console.log('Testing showimage service...');
    data = [];
    data.mml = '<math xmlns="http://www.w3.org/1998/Math/MathML"><msup><mi>x</mi><mn>2</mn></msup></math>';
    console.log(ServiceProvider.getService('showimage', data));
    console.log('Testing createimage service...');
    data = [];
    data.mml = '<math xmlns="http://www.w3.org/1998/Math/MathML"><msup><mi>x</mi><mn>2</mn></msup></math>';
    console.log(ServiceProvider.getService('createimage', data, 'post'));
    console.log('Testing MathML2Latex service...');
    data = [];
    data.service = 'mathml2latex';
    data.mml = '<math xmlns="http://www.w3.org/1998/Math/MathML"><msup><mi>x</mi><mn>2</mn></msup></math>';
    console.log(ServiceProvider.getService('service', data));
    console.log('Testing Latex2MathML service...');
    data = [];
    data.service = 'latex2mathml';
    data.latex = 'x^2';
    console.log(ServiceProvider.getService('service', data));
    console.log('Testing Mathml2Accesible service...');
    data = [];
    data.service = 'mathml2accessible';
    data.mml = '<math xmlns="http://www.w3.org/1998/Math/MathML"><msup><mi>x</mi><mn>2</mn></msup></math>';
    console.log(ServiceProvider.getService('service', data));
  }
}
