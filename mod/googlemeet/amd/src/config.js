define([], function() {
  window.requirejs.config({
    paths: {
      'gapi': 'https://apis.google.com/js/api',
    },
    shim: {
      'gapi': {exports: 'gapi'},
    }
  });
});
