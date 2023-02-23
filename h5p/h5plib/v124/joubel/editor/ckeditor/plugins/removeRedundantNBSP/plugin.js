/*
 * Remove &nbsp; entities which were inserted ie. when removing a space and
 * immediately inputting a space.
 *
 * NB: We could also set config.basicEntities to false, but this is stongly
 * adviced against since this also does not turn ie. < into &lt;.
 * @link http://stackoverflow.com/a/16468264/328272
 *
 * Based on StackOverflow answer.
 * @link http://stackoverflow.com/a/14549010/328272
 */
CKEDITOR.plugins.add('removeRedundantNBSP', {
  afterInit: function(editor) {
    var config = editor.config,
      dataProcessor = editor.dataProcessor,
      htmlFilter = dataProcessor && dataProcessor.htmlFilter;

    if (htmlFilter) {
      htmlFilter.addRules({
        text: function(text) {
          return text.replace(/(\w)&nbsp;/gi, '$1 ');
        }
      }, {
        applyToAll: true,
        excludeNestedEditable: true
      });
    }
  }
});