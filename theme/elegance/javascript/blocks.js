/* Clicking the Header of the block, hide/show the block's content
    Credit to Itamar Zadok (https://moodle.org/mod/forum/discuss.php?d=218799#p982036)
*/

YUI().use("node-base","node-event-simulate", function(Y) {
    var btn1_Click = function(e)
    {
        var targetBlock = e.target.ancestor('.block', true);

        // Don't run if in editing mode
        if (targetBlock.hasClass('block_with_controls')) {
            return false;
        }
        
        var hiderShow = targetBlock.one('.block-hider-show');
        var hiderHide = targetBlock.one('.block-hider-hide');

        // If propogating from simulation, halt
        if (e.target == hiderShow || e.target == hiderHide) {
            e.halt(true);
            return false;
        }

        if (targetBlock.hasClass('hidden')) {
            // Try show
            if (hiderShow) {
                hiderShow.simulate('click');
            }
        } else {
            // Try hide
            if (hiderHide) {
                hiderHide.simulate('click');
            }
        }
        return false;

    };
    Y.on("click", btn1_Click, ".header .title");
});