/* jshint ignore:start */
define(['jquery', 'core/log'], function ($, log) {

    "use strict"; // jshint ;_;

    log.debug('Filter Generico Presets initialising');

    return {

        presetdata: false,

        dataitems: ['key', 'name', 'instructions', 'requirecss', 'requirejs', 'shim', 'defaults',
            'amd', 'body', 'bodyend', 'script', 'style', 'dataset', 'datasetvars', 'alternate', 'alternateend', 'version'],

        fetchcontrols: function (templateindex) {
            var controls = {};
            controls.key = document.getElementById('id_s_filter_generico_templatekey_' + templateindex);
            controls.name = document.getElementById('id_s_filter_generico_templatename_' + templateindex);
            controls.instructions = document.getElementById('id_s_filter_generico_templateinstructions_' + templateindex);
            controls.requirecss = document.getElementById('id_s_filter_generico_templaterequire_css_' + templateindex);
            controls.requirejs = document.getElementById('id_s_filter_generico_templaterequire_js_' + templateindex);
            controls.shim = document.getElementById('id_s_filter_generico_templaterequire_js_shim_' + templateindex);
            controls.defaults = document.getElementById('id_s_filter_generico_templatedefaults_' + templateindex);
            controls.amd = document.getElementById('id_s_filter_generico_template_amd_' + templateindex);
            controls.body = document.getElementById('id_s_filter_generico_template_' + templateindex);
            controls.bodyend = document.getElementById('id_s_filter_generico_templateend_' + templateindex);
            controls.script = document.getElementById('id_s_filter_generico_templatescript_' + templateindex);
            controls.style = document.getElementById('id_s_filter_generico_templatestyle_' + templateindex);
            controls.dataset = document.getElementById('id_s_filter_generico_dataset_' + templateindex);
            controls.datasetvars = document.getElementById('id_s_filter_generico_datasetvars_' + templateindex);
            controls.alternate = document.getElementById('id_s_filter_generico_templatealternate_' + templateindex);
            controls.alternateend = document.getElementById('id_s_filter_generico_templatealternate_end_' + templateindex);
            controls.presetdata = document.getElementById('id_s_filter_generico_presetdata_' + templateindex);
            controls.version = document.getElementById('id_s_filter_generico_templateversion_' + templateindex);
            return controls;
        },

        fetchjsonbundle: function (templateindex, controls) {
            var bundle = {};
            $.each(this.dataitems,
                function (index, item) {
                    bundle[item] = controls[item].value;
                }
            );
            var jsonbundle = JSON.stringify(bundle);
            return jsonbundle;
            window.open("data:text/json;charset=utf-8," + encodeURIComponent(bundlejson));
        },

        exportbundle: function (templateindex) {
            var controls = this.fetchcontrols(templateindex);
            if (controls.key.value == '') {
                return;
            }
            var jsonbundle = this.fetchjsonbundle(templateindex, controls);

            var pom = document.createElement('a');
            pom.setAttribute('href', "data:text/json;charset=utf-8," + encodeURIComponent(jsonbundle));
            pom.setAttribute('download', controls['key'].value + '.txt');

            if (document.createEvent) {
                var event = document.createEvent('MouseEvents');
                event.initEvent('click', true, true);
                pom.dispatchEvent(event);
            }
            else {
                pom.click();
            }
        },

        populateform: function (templateindex, presetindex, presetdata) {
            //get all our html controls
            var controls = this.fetchcontrols(templateindex);

            //what a rip off there was no selection!!!
            if (!presetindex && !presetdata) {
                return;
            }
            if (presetindex == 0 && presetdata) {
                //this is good, we have something from dopopulate
            } else {
                //this is a normal selection
                presetdata = this.presetdata;
            }

            $.each(this.dataitems,
                function (index, item) {
                    // log.debug(item + ':' + presetindex + ':' + presetdata[presetindex][item]);
                    //first check we have a data item for this control(old bundles don't have instructions or new fields etc)
                    //then set the data
                    if (presetdata[presetindex].hasOwnProperty(item)) {
                        controls[item].value = presetdata[presetindex][item];
                    } else {
                        switch (item) {
                            case 'amd':
                                controls[item].value = 0;
                                break;
                            default:
                                controls[item].value = '';
                        }
                    }
                }
            );

        },

        dopopulate: function (templateindex, templatedata) {
            this.populateform(templateindex, 0, new Array(templatedata));
        },

        // load all generico stuff and stash all our variables
        init: function (opts) {
            if (!this.presetdata) {
                var controlid = '#id_s_filter_generico_presetdata_' + opts['templateindex'];
                var presetcontrol = $(controlid).get(0);
                this.presetdata = JSON.parse(presetcontrol.value);
                $(controlid).remove();
            }

            var amdpresets = this;
            //handle the select box change event
            $("select[name='filter_generico/presets']").change(function () {
                amdpresets.populateform(opts['templateindex'], $(this).val());
            });

            //drag drop square events
            var ddsquareid = '#id_s_filter_generico_dragdropsquare_' + opts['templateindex']

            //export the current bundle
            $(ddsquareid).on("click", function (event) {
                amdpresets.exportbundle(opts['templateindex']);
            });


            //handle the drop event. First cancel dragevents which prevent drop firing
            $(ddsquareid).on("dragover", function (event) {
                event.preventDefault();
                event.stopPropagation();
                $(this).addClass('filter_generico_dragging');
            });

            $(ddsquareid).on("dragleave", function (event) {
                event.preventDefault();
                event.stopPropagation();
                $(this).removeClass('filter_generico_dragging');
            });

            $(ddsquareid).on('drop', function (event) {

                //stop the browser from opening the file
                event.preventDefault();
                //Now we need to get the files that were dropped
                //The normal method would be to use event.dataTransfer.files
                //but as jquery creates its own event object you ave to access 
                //the browser even through originalEvent.  which looks like this
                var files = event.originalEvent.dataTransfer.files;

                //if we have files, read and process them
                if (files.length) {
                    var f = files[0];
                    if (f) {
                        var r = new FileReader();
                        r.onload = function (e) {
                            var contents = e.target.result;
                            var templatedata = JSON.parse(contents);
                            if (templatedata.key) {
                                amdpresets.dopopulate(opts['templateindex'], templatedata);
                            }
                        }
                        r.readAsText(f);
                    } else {
                        alert("Failed to load file");
                    }//end of if f
                }//end of if files
                $(this).removeClass('filter_generico_dragging');
            });
        }//end of function

    }
});
/* jshint ignore:end */