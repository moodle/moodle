YUI.add('moodle-qtype_ddimagetoimage-question', function(Y) {
    var DDIMAGETOIMAGEQUESTIONNAME = 'ddimagetoimage_question';
    var DDIMAGETOIMAGE_QUESTION = function() {
        DDIMAGETOIMAGE_QUESTION.superclass.constructor.apply(this, arguments);
    };
    Y.extend(DDIMAGETOIMAGE_QUESTION, M.qtype_ddimagetoimage.dd_base_class, {
        initializer : function(params) {
            this.doc = this.doc_structure(this);
            this.poll_for_image_load(null, 1000, this.create_all_drag_and_drops);
            this.doc.bg_img().after('load', this.poll_for_image_load, this,
                                                    false, 1000, this.create_all_drag_and_drops);
            this.doc.drag_image_homes().after('load', this.poll_for_image_load, this,
                                                    false, 1000, this.create_all_drag_and_drops);
            Y.on('windowresize', this.reposition_drags_for_question, this);
        },
        create_all_drag_and_drops : function () {
            this.init_drops();
            this.update_padding_sizes_all();
            var i = 0;
            this.doc.drag_image_homes().each(function(dragimagehome){
                var dragimageno = 
                    +this.doc.get_classname_numeric_suffix(dragimagehome, 'dragimagehomes');
                var choice = +this.doc.get_classname_numeric_suffix(dragimagehome, 'choice');
                var group = +this.doc.get_classname_numeric_suffix(dragimagehome, 'group')
                var groupsize = this.doc.drop_zone_group(group).size();
                var dragnode = this.doc.clone_new_drag_image(i, dragimageno);
                i++;
                if (!this.get('readonly')) {
                    this.doc.draggable_for_question(dragnode, group, choice);
                }
                if (dragnode.hasClass('infinite')) {
                    var dragstocreate = groupsize - 1;
                    while (dragstocreate > 0) {
                        dragnode = this.doc.clone_new_drag_image(i, dragimageno);
                        i++;
                        if (!this.get('readonly')) {
                            this.doc.draggable_for_question(dragnode, group, choice);
                        }
                        dragstocreate--;
                    }
                }
            }, this);
            this.reposition_drags_for_question();
        },
        reposition_drags_for_question : function() {
            this.doc.drag_images().removeClass('placed');
            this.doc.drag_images().each (function (dragimage) {
                if (dragimage.dd !== undefined) {
                    dragimage.dd.detachAll('drag:start');
                }
            }, this);
            this.doc.drop_zones().each(function(dropzone) {
                var relativexy = dropzone.getData('xy');
                dropzone.setXY(this.convert_to_window_xy(relativexy));
                var inputcss = 'input#' + dropzone.getData('inputid');
                var input = this.doc.top_node().one(inputcss);
                var choice = input.get('value');
                if (choice !== "") {
                    var group = dropzone.getData('group');
                    var dragimage = null;
                    var dragimages = this.doc.top_node()
                                        .all('div.dragitemgroup'+group+' img.choice'+choice+'.drag');
                    dragimages.some(function (d) {
                        if (!d.hasClass('placed')) {
                            dragimage = d;
                            return true;
                        } else {
                            return false;
                        }
                    });
                    if (dragimage !== null) {
                        dragimage.setXY(dropzone.getXY());
                        dragimage.addClass('placed');
                        if (dragimage.dd !== undefined) {
                            dragimage.dd.once('drag:start', function (e, input) {
                                input.set('value', '');
                                e.target.get('node').removeClass('placed');
                            },this, input);
                        }
                    }
                }
            }, this);
            this.doc.drag_images().each(function(dragimage) {
                if (!dragimage.hasClass('placed')) {
                    var dragimagehome = this.doc.drag_image_home(dragimage.getData('dragimageno'));
                    dragimage.setXY(dragimagehome.getXY());
                }
            }, this);
        },
        init_drops : function () {
            var dropareas = this.doc.top_node().one('div.dropzones');
            var groupnodes = {};
            for (var groupno =1; groupno <= 8; groupno++) {
                var groupnode = Y.Node.create('<div class = "dropzonegroup'+groupno+'"></div>');
                dropareas.append(groupnode);
                groupnodes[groupno] = groupnode;
            }
            for (var dropno in this.get('drops')){
                var drop = this.get('drops')[dropno];
                var nodeclass = 'dropzone group'+drop.group+' place'+dropno;
                var title = drop.text.replace('"', '\"');
                var dropnodehtml = '<div title="'+ title +'" class="'+nodeclass+'">&nbsp;</div>';
                var dropnode = Y.Node.create(dropnodehtml);
                groupnodes[drop.group].append(dropnode);
                dropnode.setStyles({'opacity': 0.5});
                dropnode.setData('xy', drop.xy);
                dropnode.setData('place', dropno);
                dropnode.setData('inputid', drop.fieldname.replace(':', '_'));
                dropnode.setData('group', drop.group);
                var dropdd = new Y.DD.Drop({
                      node: dropnode});
                dropdd.on('drop:hit', function(e) {
                    var drag = e.drag.get('node');
                    var drop = e.drop.get('node');
                    if (+drop.getData('group') === drag.getData('group')){
                        var inputid = drop.getData('inputid');
                        var inputnode = Y.one('input#'+inputid);
                        inputnode.set('value', drag.getData('choice'));
                    }
                }, this);
            };
        }
    }, {NAME : DDIMAGETOIMAGEQUESTIONNAME, ATTRS : {}});
    M.qtype_ddimagetoimage = M.qtype_ddimagetoimage || {};
    M.qtype_ddimagetoimage.init_question = function(config) { 
        return new DDIMAGETOIMAGE_QUESTION(config);
    }
}, '@VERSION@', {
    requires:['moodle-qtype_ddimagetoimage-dd']
});