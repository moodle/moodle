$('.blockpanelbutton').click(function () {
    var blockslideropen = localStorage.getItem('blockslideropen');

    if (blockslideropen == 1) {
        localStorage.setItem('blockslideropen', 0);
    } else {
        localStorage.setItem('blockslideropen', 1);
    }

});

if (localStorage.getItem('blockslideropen') == 1) {
    $('#blockslider').addClass('show');
}
