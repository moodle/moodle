// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * JavaScript code for the gapfill question type.
 *
 * @copyright  2017 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(["jquery"], function($) {
  /**
   *
   * @param {string} text
   * @param {string} delimitchars
   */
  return function Item(text, delimitchars) {
    this.questionid = $("input[name=id]").val();
    this.settings = getSettings();
    this.gaptext = text;
    this.delimitchars = delimitchars;
    /* The l and r is for left and right */
    this.l = delimitchars.substr(0, 1);
    this.r = delimitchars.substr(1, 1);
    this.len = this.gaptext.length;
    this.startchar = this.gaptext.substring(0, 1);
    /* For checking if the end char is the right delimiter */
    this.endchar = this.gaptext.substring(this.len - 1, this.len);
    this.gaptextNodelim = "";
    this.feedback = {};
    this.instance = 0;
    this.feedback.correct = $("#id_corecteditable").html();
    this.feedback.incorrect = $("#id_incorrecteditable").html();
    Item.prototype.striptags = function(gaptext) {
      /* This is not a perfect way of stripping html but it may be good enough */
      if (gaptext === undefined) {
        return "";
      }
      var regex = /(<([^>]+)>)/gi;
      return gaptext.replace(regex, "");
    };
    /**
     * Pull data from hidden settings field on form
     * @returns {object} settings
     */
    function getSettings() {
      var settings = [];
      var settingsdata = $("[name='itemsettings']").val();
      if (settingsdata > "") {
        var obj = JSON.parse(settingsdata);
        for (var o in obj) {
          settings.push(obj[o]);
        }
      }
      return settings;
    }
    this.stripdelim = function() {
      if (this.startchar === this.l) {
        this.gaptextNodelim = this.gaptext.substring(1, this.len);
      }
      if (this.endchar === this.r) {
        var len = this.gaptextNodelim.length;
        this.gaptextNodelim = this.gaptextNodelim.substring(0, len - 1);
      }
      return this.gaptextNodelim;
    };
    var itemsettings = [];
    Item.prototype.getItemSettings = function(target) {
      var itemid = target.id;
      var underscore = itemid.indexOf("_");
      /* The instance, normally 0 but incremented if a gap has the same text as another
       * instance is not currently used*/
      this.instance = itemid.substr(underscore + 1);
      for (var set in this.settings) {
        text = this.stripdelim();
        if (this.settings[set].gaptext === text) {
          itemsettings = this.settings[set];
        }
      }
      return itemsettings;
    };
    this.updateJson = function(e) {
      var found = false;
      var id = e.target.id;
      for (var set in this.settings) {
        if (this.settings[set].gaptext === this.stripdelim()) {
          this.settings[set].correctfeedback = $(
            "#id_correcteditable"
          )[0].innerHTML;
          this.settings[set].incorrectfeedback = $(
            "#id_incorrecteditable"
          )[0].innerHTML;
          found = true;
        }
      }
      if (found === false) {
        /* If there is no record for this word add one */
        var itemsettings = {
          itemid: id,
          questionid: $("input[name=id]").val(),
          correctfeedback: $("#id_correcteditable").html(),
          incorrectfeedback: $("#id_incorrecteditable").html(),
          gaptext: this.stripdelim()
        };
        this.settings.push(itemsettings);
      }
      return JSON.stringify(this.settings);
    };
  };
});
