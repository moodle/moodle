/**
 * If this script is loaded as part of the CKEditor assets, it will enable the language plugin.
 */
(function ($) {
    $(document).ready(function () {
        if (!window.CKEDITOR) {
            return;
        }
        return;
        // Register our plugin
        CKEDITOR.plugins.addExternal('language', '/h5p/js/ckeditor/plugins/language');
        H5PEditor.HtmlAddons = H5PEditor.HtmlAddons || {};
        H5PEditor.HtmlAddons.text = H5PEditor.HtmlAddons.text || {};
        H5PEditor.HtmlAddons.text.bidi = function (config, tags) {
            // Add the plugin.
            config.extraPlugins = (config.extraPlugins ? ',' : '') + 'language';

            // Add plugin to toolbar.
            config.toolbar.push({
                name: "bidi",
                items: ['Language']
            });
        };
    });
})(H5P.jQuery);
