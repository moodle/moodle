var require = {
    baseUrl : '[BASEURL]',
    // We only support AMD modules with an explicit define() statement.
    enforceDefine: true,
    skipDataMain: true,
    waitSeconds : 0,

    paths: {
        jquery: '[JSURL]lib/jquery/jquery-3.2.1.min[JSEXT]',
        jqueryui: '[JSURL]lib/jquery/ui-1.12.1/jquery-ui.min[JSEXT]',
        jqueryprivate: '[JSURL]lib/requirejs/jquery-private[JSEXT]'
    },

    // Custom jquery config map.
    map: {
      // '*' means all modules will get 'jqueryprivate'
      // for their 'jquery' dependency.
      '*': { jquery: 'jqueryprivate' },
      // Stub module for 'process'. This is a workaround for a bug in MathJax (see MDL-60458).
      '*': { process: 'core/first' },

      // 'jquery-private' wants the real jQuery module
      // though. If this line was not here, there would
      // be an unresolvable cyclic dependency.
      jqueryprivate: { jquery: 'jquery' }
    }
};
