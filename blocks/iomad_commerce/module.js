window.iomad = window.iomad || {};

iomad.increaseCounter = function(inputname) {
    YUI().use('node', function(Y) {
        var counter = Y.one('input[name="' + inputname + '"]');
        counter.set('value', parseInt(counter.get('value')) + 1);
    });
}

iomad.addLicenseBlock = function(button) {
    YUI().use('node', function(Y) {
        var currency = Y.one('input[name="currency"]').get('value');
        var table = Y.one('#licenseblockstable');
        var tbody = table.one('tbody');
        var n = tbody.all('tr').size() + 1;

        var lastrow = table.one('tr.lastrow');
        var wasr0 = false;
        if (lastrow) wasr0 = table.one('tr.lastrow').removeClass('lastrow').hasClass('r0');

        var tr = document.createElement('tr');
        tr.className = 'lastrow ' + (wasr0 ? 'r1' : 'r0');

        var inputs = {
            'block_start_': { prefix: '' },
            'block_price_': { prefix: currency },
            'block_valid_': { prefix: '' },
            'block_shelflife_': { prefix: '' }
        };
        for (var k in inputs) {
            var td = document.createElement('td');
            td.innerHTML = inputs[k].prefix + '<input name="' + k + n + '" type="text" value="" size="5" />';
            tr.appendChild(td);
        }

        var strdelete = Y.one('#deleteText').get('innerText');
        var td = document.createElement('td');
        td.innerHTML = '<a href="#" onclick="iomad.removeLicenseBlock(this)">' + strdelete + '</a>';
        tr.appendChild(td);

        tbody.appendChild(tr);
        
        iomad.increaseCounter('blockPrices');
    });
};

iomad.removeLicenseBlock = function(button) {
    YUI().use('node', function(Y) {
        var node = Y.one(button);
        while (node && node.get("tagName").toUpperCase() != "TR")
            node = node.get("parentNode");
        if (node) node.remove();

        iomad.increaseCounter('deletedBlockPrices');
    });
};

iomad.onSelectTag = function(select) {
    if (select.selectedIndex > 0) {
        var tag = select.options[select.selectedIndex].value;
        YUI().use('node', function(Y) {
            var input = Y.one('#id_tags'),
                tags = [],
                text = input.get('value');
            if (text.replace(/\s/g, '').length) {
                tags = text.split(/\s*,\s*/);
            }
            if (tags.indexOf(tag) == -1) {
                tags.push(tag);
            }
            tags = tags.sort(function(A, B) {
                var a = A.toLowerCase(), b = B.toLowerCase();
                if (a == b) return 0;
                return a < b ? -1 : 1;
            });
            input.set('value', tags.join(", "));
        });
        select.selectedIndex = 0;
    }
};
