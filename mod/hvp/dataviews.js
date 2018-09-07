(function ($) {

    /**
   * Creates a new dataview.
   *
   * @private
   * @param {object} dataView Structure
   * @param {string} dataView.source AJAX URL for data view
   * @param {object[]} dataView.headers Header text and props
   * @param {boolean[]} dataView.filters Which fields to allow filtering for
   * @param {object} dataView.order Default order by and direction
   * @param {object} dataView.l10n Translations
   * @param {Element} wrapper Where in the DOM should the dataview be appended
   * @param {function} loaded Callback for when the dataview is ready
   */
    var createDataView = function (dataView, wrapper, loaded) {
        new H5PDataView(
        wrapper,
        dataView.source,
        dataView.headers,
        dataView.l10n,
        undefined,
        dataView.filters,
        loaded,
        dataView.order
        );
    };

    // Create data views when page is done loading.
    $(document).ready(function () {
        for (var id in H5PIntegration.dataViews) {
            if (!H5PIntegration.dataViews.hasOwnProperty(id)) {
                continue;
            }

            var wrapper = $('#' + id).get(0);
            if (wrapper !== undefined) {
                createDataView(H5PIntegration.dataViews[id], wrapper);
            }
        }
    });
})(H5P.jQuery);
