
M.form_listing = {};
M.form_listing.Y = null;
M.form_listing.instances = [];

/**
 * This fucntion is called for each listing on page.
 */
M.form_listing.init = function(Y, params) {
    if (params && params.hiddeninputid && params.elementid) {

        // Enable element that were hidden/displau for support of no-javascript
        // Display the form
        Y.one('#'+params.elementid).removeClass('hide');
        // Replace the radio buttons by a hidden input
        Y.one('#formlistinginputcontainer').setHTML('<input name='+params.inputname+' type=hidden id='+params.attributid+' value='+params.currentvalue+' />');

        var caption = Y.one('#'+params.elementid+'_caption');
        var allitems = Y.one('#'+params.elementid+'_all');
        var selecteditem = Y.one('#'+params.elementid+'_main');
        var hiddeninput = Y.one('#'+params.hiddeninputid);

        // Do not display the listing by default
        var show = 0;
        allitems.hide();

        // Refresh the main item + set the hidden input to its value
        var selectItem = function(e) {
            var index = this.get('id').replace(params.elementid+'_all_',"");;
            hiddeninput.set('value', items[index]);
            selecteditem.setHTML(params.items[items[index]].mainhtml)
        }

        // caption Onlick event to display/hide the listing
        var onClick = function(e) {
            if (!show) {
                allitems.show(true);
                show = 1;
                caption.setHTML(params.hideall);
            } else {
                allitems.hide(true);
                show = 0;
                caption.setHTML(params.showall);
            }
        };

        caption.on('click', onClick);

        // Fill the item rows with html + add event
        // PS: we need to save the items into a temporary "items[]" array because params.items keys could be
        //     url. This temporary items[] avoid not working calls like Y.one('#myitems_http:www.google.com').
        var items = [];
        var itemindex = 0;
        for (itemid in params.items) {
            items[itemindex] = itemid;
            
            // Add the row
            allitems.append("<div id="+params.elementid+'_all_'+itemindex+" class='formlistingrow'>" + params.items[itemid].rowhtml + "</div>");

            // Add click event to the row
            Y.one('#'+params.elementid+'_all_'+itemindex).on('click', selectItem);

            itemindex = itemindex + 1;
        }
    }
};
