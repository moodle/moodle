import SimpleSAMLLogout from './logout.js';

$(document).ready(function () {
    new SimpleSAMLLogout($('body').attr('id'));
});