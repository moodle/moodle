jQuery.fn.liveUpdate = function (list) {
    list = jQuery(list);

    if (list.length) {
        var rows = list.children('a'),
        cache = rows.map(function () {
            return jQuery(this).text().toLowerCase();
        });

        this.keyup(filter).keyup().parents('form').submit(function () {
            return false;
        });
    }

    return this;

    function filter()
    {
        var term = jQuery.trim(jQuery(this).val().toLowerCase()), scores = [];

        if (!term) {
            rows.show();
        } else {
            rows.hide();

            cache.each(function (i) {
                var score = this.score(term);
                if (score > 0) {
                    scores.push([score, i]);
                }
            });

            jQuery.each(
                scores.sort(function (a, b) {
                    return b[0] - a[0];
                }),
                function () {
                    jQuery(rows[ this[1] ]).show();
                }
            );
        }
    }
};
