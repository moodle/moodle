/*
 * @package    tool_models
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /**
  * @module tool_models/log_info
  */
define(['jquery', 'core/str', 'core/modal_factory'], function($, str, ModalFactory) {

    return {

        /**
         * Prepares a modal info for a log's results.
         * @access public
         * @param {int} id
         * @return {String} HTML info
         */
        loadInfo : function(id, info) {

            var link = $('[data-model-log-id="' + id + '"]');
            str.get_string('loginfo', 'tool_models').done(function(langString) {

                var bodyInfo = $("<ul>");
                for (var i in info) {
                    bodyInfo.append("<li>" + info[i] + "</li>");
                }
                bodyInfo.append("</ul>");
                ModalFactory.create({
                    title: langString,
                    body: bodyInfo.html(),
                    large: true,
                }, link);
            });
        }
    };
});
