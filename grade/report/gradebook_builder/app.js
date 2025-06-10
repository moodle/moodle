$(document).ready(function() {
    // handle event: remove category button clicked
    $(document).on('click', 'span.remove-category-label', function (e) {
        var elem = $(e.currentTarget),
            table = elem.parent().parent().parent().parent().parent(),
            id = table.attr('id'),
            name = elem.parent().find('span:first').html();

        table.remove();

        readjustWeight(function() {
            $('div.control-group[data-categoryid="' + id + '"]').remove();
        });

        $('#add-item-category').children('option[value=' + id + ']').remove();
    });

    // handle event: grade item input change
    // Save item point value
    $(document).on('focusout', 'div.point-blank input', function (e) {
        var elem = $(e.currentTarget);
        var val = elem.val();
        // TODO: some kind of numeric evaluation?
    });

    // handle event: remove item span clicked
    $(document).on('click', 'span.remove-item-label', function (e) {
        $(e.currentTarget).parent().parent().parent().remove();
    });

    var category_tmpl = $('div#grade-category-tmpl').find('table'),
        item_tmpl = $('div#grade-item-tmpl').find('tr'),
        weight_tmpl = $('div#category-weight-tmpl').find('div:first'),
        target_weight = 100;

    function checkElemWeight(elem, weight) {
        if (isNaN(weight)) {
            elem.css('border', '1px solid red');
            return false;
        } else {
            elem.removeAttr('style');
            return true;
        }
    }

    function get_selected(selector) {
        return $(selector).children().filter(':selected').html();
    }

    function get_selected_value(selector) {
        return $(selector).children().filter(':selected').val();
    }

    function add_category(name, weight) {
        var category = category_tmpl.clone(),
            weights = weight_tmpl.clone(),
            id = new Date().getTime() + '';

        category.attr('name', name).attr('id', id);
        weights.attr('name', name).attr('data-categoryid', id);

        if (name === '') {
            return;
        }

        category.find('h4').replaceWith('<h4><span>' + name + '</span> <span class="label label-important remove-category-label">X</span></h4>');

        weights.find('span:first').replaceWith('<span>' + name + '</span>');

        $('div#grade-categories').append(category);

        $('select#add-item-category')
            .append('<option value="' + id + '">' + name + '</option>');

       // $('div#howto').replaceWith('<div class="help"><a href=https://moodle3.grok.lsu.edu/Article.aspx?articleid=18968 target="_blank" class="help">help</a></div>');

        readjustWeight(function(tw) {
            tw.append(weights);

            if (weight) {
                $('.control-group[data-categoryid="' + id + '"]')
                    .find('input').val(weight * 100);
            }
        });
    }

    function readjustWeight(callback) {
        var tw = $('#category-weights fieldset'),
            old_sep = Math.round(target_weight / tw.children().length);

        // manipulate size
        callback(tw);

        var sep = Math.round(target_weight / tw.children().length);
        tw.find('.input-mini').each(function(i, elem) {
            var val = $(elem).val();
            console.log(parseFloat(val));
            console.log(old_sep);
            console.log(sep);
            if (val === "0" || isNaN(val) || parseFloat(val) == old_sep) {
                $(elem).val(sep);
            }
        });
    }

    // Add grade category
    $('button#add-category').click(function(e) {
        e.preventDefault();

        var name = $('input#category-name').val();

        add_category(name);
    });

    function add_item(category_name, name, points, itemtype) {
        var category = $('table[name="' + category_name + '"]').find('tbody'),
            to_add = $('input#grade-item-num-add').val(),
            i = 0;

        for (; i < to_add; i++) {
            var item = item_tmpl.clone();

            if (!name) {
                var num = category.children().length + 1,
                    itemname = category_name + ' ' + num;
            } else {
                var itemname = name;
            }

            if (!itemtype) {
                var itemtype = get_selected_value('select#grade-itemtype');
            }

            item.find('span:first').replaceWith('<span data-itemtype="' + itemtype + '">' + itemname + ' <span class="label label-important remove-item-label">X</span></span>');

            if (points) {
                item.find('input.input-mini').val(points);
            }

            category.append(item);
        }
    }

    // Add grade item
    $('button#add-item').click(function(e) {
        e.preventDefault();

        var category_name = get_selected('select#add-item-category');

        add_item(category_name, '');
    });

    // Template name
    $("#template-toggle-input").on('click', function() {
        var s = $(this);

        s.html('<input value="' + s.text() + '"/>')
         .children("input").focus()
         .on('focusout', function() {
             var name = $(this).val().trim() == '' ? 'New Template' : $(this).val();
             s.html(name);
             $('#builder').children("input[name=name]").val(name);
         });

        return false;
    });

    


/*
    // Hover to show remove button for categories
    $('tr').live({
        mouseenter: function(e) {
            $(e.currentTarget).children().find('span.remove-category-label').show();
        },
        mouseleave: function(e) {
            $(e.currentTarget).children().find('span.remove-category-label').hide();
        }
    });

    // Hover to show remove button for items

    $('tr').live({
        mouseenter: function(e) {
            $(e.currentTarget).children().find('span.remove-item-label').show();
        },
        mouseleave: function(e) {
            $(e.currentTarget).children().find('span.remove-item-label').hide();
        }
    });
*/
    // Show or hide category weights
    $('select#grading-method').change(function(e) {
        var val = $('select#grading-method').val();

        if (val === "10") {
            $('form#category-weights').show();
        $('div#builder-start.container').addClass('taller');
        } else {
            $('form#category-weights').hide();
        $('div#builder-start.container').removeClass('taller');
        }

    });

    // Convert form to JSON and Submit on save button click
    $('form#builder').submit(function() {
        var gb = {},
            total_weight = 0,
            tw = $('#category-weights fieldset'),
            errors = [];

        gb['name'] = $(this).children('input[name=name]').val();
        gb['aggregation'] = $('select#grading-method').val();
        gb['categories'] = [];

        if (gb['aggregation'] === "10") {
            tw.find('.input-mini').each(function() {
                total_weight += isNaN($(this).val()) ? 0 : parseFloat($(this).val());
            });

            $('#category-weights h4').siblings('.error').remove();

            if (total_weight != 100) {
                $('#category-weights h4')
                    .after('<div class="error">Does not total 100%</div>');
                errors.push('total');
            }
        }

        $('div#builder-start').find('table').each(function() {
            var cat_obj = {},
                parent = $(this),
                id = parent.attr('id'),
                input = $('.control-group[data-categoryid=' + id + ']').find('input'),
                weight = input.val(),
                items = [];

            cat_obj['name'] = $(this).find('span:first').html();

            if (!checkElemWeight(input, weight)) {
                errors.push(cat_obj['name'] + ' bad weight');
                return;
            }

            cat_obj['weight'] = gb['aggregation'] === "10" ?
                parseFloat(weight) / 100.0 : 0;

            parent.find('td').each(function() {
                var gi_name = $(this).find('span:first').clone().children().remove().end().text().trim();
                var gi_itemtype = $(this).find('span:first').clone().attr('data-itemtype');
                var gi_points = $(this).find('input.input-mini').val();

                if (!checkElemWeight($(this).find('input.input-mini'), gi_points)) {
                    errors.push(gi_name + ' bad number');
                    return;
                }

                // Gather itemtype and itemmodule
                items.push({
                    'name': gi_name,
                    'grademax': gi_points,
                    'weight': gb['aggregation'] === "10" ? 1 : 0,
                    'itemtype': gi_itemtype == 'manual' ? 'manual' : 'mod',
                    'itemmodule': gi_itemtype == 'manual' ? '' : gi_itemtype,
                });
            });

            cat_obj['items'] = items;

            gb['categories'].push(cat_obj);
        });

        $('input[name="data"]').val(JSON.stringify(gb));
        return errors.length == 0;
    });

    // Launch change for wight mean
    $('select#grading-method').change();

    var gb_json = $('input[name="data"]').val();

    if (gb_json.length > 2) {
        var gb_obj = JSON.parse(gb_json);

        $('select#grading-method').val(gb_obj['aggregation']).change();

        $.each(gb_obj['categories'], function() {
            var cat_node = this;

            add_category(cat_node.name, cat_node.weight);

            $.each(cat_node['items'], function() {
                add_item(cat_node.name, this.name, this.grademax, this.itemmodule);
            });
        });
    }
});
