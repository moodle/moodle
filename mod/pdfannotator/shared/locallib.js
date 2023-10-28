/**
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Rabea de Groot and Ahmad Obeid (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

function addDropdownNavigation(Y, __capabilities, __cmid) {

    // Select the general overview tab.
    let tabs = document.querySelectorAll('.nav.nav-tabs li a');
    var overviewtab;
    for (var tab in tabs) {
        if (tabs[tab].innerHTML === M.util.get_string('overview', 'pdfannotator')) {
            overviewtab = tabs[tab];
            break;
        }
    }
    overviewtab.classList.add('mydropbtn');

    // Create a dropdown navigation menu.
    var dropdown = document.createElement('div');
    dropdown.id = 'pdfannotator_dropdownnav';
    dropdown.classList.add('dropdown-content');

    if (__capabilities.viewquestions) {
        var nav1 = document.createElement('a');
        var img1 = "<div><span class='media-left'><i class='icon fa fa-unlock fa-fw'></i></span> ";
        nav1.href = 'view.php?action=overviewquestions&id=' + __cmid;
        nav1.innerHTML = img1 + "<span class='media-body'>" + M.util.get_string('questionstab', 'pdfannotator') + "</span>";
        dropdown.appendChild(nav1);
    }
    if (__capabilities.viewanswers) {
        var nav2 = document.createElement('a');
        var img2 = "<div><span class='media-left'><i class='icon fa fa-envelope fa-fw'></i></span> ";
        nav2.href = 'view.php?action=overviewanswers&id=' + __cmid;
        nav2.innerHTML = img2 + "<span class='media-body'>" + M.util.get_string('answerstab', 'pdfannotator') + "</span>";
        dropdown.appendChild(nav2);
    }
    if (__capabilities.viewposts) {
        var nav3 = document.createElement('a');
        var img3 = "<div><span class='media-left'><i class='icon fa fa-user fa-fw'></i></span> ";
        nav3.href = 'view.php?action=overviewownposts&id=' + __cmid;
        nav3.innerHTML = img3 + "<span class='media-body'>" + M.util.get_string('ownpoststab', 'pdfannotator') + "</span>";
        dropdown.appendChild(nav3);
    }
    if (__capabilities.viewreports) {
        var nav4 = document.createElement('a');
        var img4 = "<div><span class='media-left'><i class='icon fa fa-flag fa-fw'></i></span> "; // "<div><span class='media-left'><img src='" + M.util.image_url('flagged', 'pdfannotator') + "'></span> ";
        nav4.href = 'view.php?action=overviewreports&id=' + __cmid;
        nav4.innerHTML = img4 + "<span class='media-body'>" + M.util.get_string('reportstab', 'pdfannotator') + "</span>";
        dropdown.appendChild(nav4);

    }

    overviewtab.parentNode.append(dropdown);

    // Add an event listener (for opening the dropdown) to the overview tab.
    let mouseOverDropdownContent = false;
    // Hover on overview-tab => show dropdown.
    overviewtab.addEventListener("mouseover", function () {
        if (!dropdown.classList.contains('show')) {
            dropdown.classList.add('show');
        }
    });

    // Leaving overview-tab => hide dropdown. But not if hover on dropdown!
    overviewtab.addEventListener("mouseleave", function () {
        setTimeout(function () {
            if (!mouseOverDropdownContent) {
                $('#pdfannotator_dropdownnav').removeClass('show');
            }
        }, 0);
    });

    $('#pdfannotator_dropdownnav').mouseenter(function () {
        mouseOverDropdownContent = true;
    });
    $('#pdfannotator_dropdownnav').mouseleave(function () {
        $('#pdfannotator_dropdownnav').removeClass('show');
        mouseOverDropdownContent = false;
    });

}

function renderMathJax(node) {
    var counter = 0;
    let mathjax = function (node) {
        if (typeof (MathJax) !== "undefined") {
            MathJax.Hub.Queue(['Typeset', MathJax.Hub, node]);
        } else if (counter < 30) {
            counter++;
            setTimeout(mathjax, 100);
        } else {
        }
    };
    mathjax(node);
}

function fixCommentForm() {
    if ($('#comment-list-form').hasClass('fixtool')) {
        $('#comment-list-form').removeClass('fixtool');
        $('#comment-list-form').css("width", "");
        $('#comment-list-form').css("top", "");
    }

    var top = $('#comment-list-form').offset().top - parseFloat($('#comment-list-form').css('marginTop').replace(/auto/, 0));
    var fixedTop = $('#pdftoolbar').outerHeight();
    if ($('.fixed-top').length > 0) {
        fixedTop += $('.fixed-top').outerHeight();
    } else if ($('.navbar-static-top').length > 0) {
        fixedTop += $('.navbar-static-top').outerHeight();
    }
    var oldWidth = $('#comment-list-form').css('width');

    fixForm(top, fixedTop, oldWidth);

    $(window).scroll(function (event) {
        fixForm(top, fixedTop, oldWidth);
    });

    $(window).resize(function (event) {
        // Adjust width if form is fixed.
        if ($('#comment-list-form').hasClass('fixtool')) {
            $('#comment-list-form').removeClass('fixtool');
            $('#comment-list-form').css("width", "");
            oldWidth = $('#comment-list-form').css('width');
            document.getElementById("comment-list-form").style.width = oldWidth;
        } else {
            oldWidth = $('#comment-list-form').css('width');
        }
        // Fix form if window was resized so that the scroll event wasn't triggered.
        fixForm(top, fixedTop, oldWidth);
    });
}

function fixForm(top, fixedTop, oldWidth) {
    var y = $(this).scrollTop();
    if (y >= top + 1 - fixedTop) {
        $('#comment-list-form').addClass('fixtool');
        document.getElementById("comment-list-form").style.top = fixedTop + "px";
        document.getElementById("comment-list-form").style.width = oldWidth;
    } else {
        $('#comment-list-form').removeClass('fixtool');
        $('#comment-list-form').css("width", "");
        $('#comment-list-form').css("top", "");
    }
}

function closeComment() {
    document.querySelector('.comment-list-form').setAttribute('style', 'display:none');
    document.getElementById('commentSubmit').value = M.util.get_string('answerButton', 'pdfannotator');
    document.getElementById('myarea').value = "";
    document.querySelector('.comment-list-container').innerHTML = '';
}
var oldHeight = -1;
function makeFullScreen() {
    document.querySelector('body').classList.toggle('fullscreenWrapper');
    // If it is now in fullscreen, the image should be the collapse fullscreen image.
    // Else it should be the fullscreen image.
    if (document.querySelector('body').classList.contains('fullscreenWrapper')) {
        oldHeight = document.querySelector('#body-wrapper').style.height;
        let img = document.querySelector('img[title="' + M.util.get_string('fullscreen', 'pdfannotator') + '"]');
        img.title = M.util.get_string('fullscreenBack', 'pdfannotator');
        img.alt = M.util.get_string('fullscreenBack', 'pdfannotator');
        img.parentNode.title = M.util.get_string('fullscreenBack', 'pdfannotator');
        img.src = M.util.image_url('fullscreen_collapse', 'pdfannotator');
        var height = document.querySelector('html').getBoundingClientRect().height;
        document.querySelector('#body-wrapper').style.height = (height - 142) + 'px';
    } else {
        let img = document.querySelector('img[title="' + M.util.get_string('fullscreenBack', 'pdfannotator') + '"]');
        img.title = M.util.get_string('fullscreen', 'pdfannotator');
        img.alt = M.util.get_string('fullscreen', 'pdfannotator');
        img.parentNode.title = M.util.get_string('fullscreen', 'pdfannotator');
        img.src = M.util.image_url('fullscreen', 'pdfannotator');
        document.querySelector('#body-wrapper').style.height = oldHeight;
    }
}

/**
 * Check just one checkbox under the comment form
 * 
 */
function checkOnlyOneCheckbox( Y ) {
    var radios = document.getElementsByClassName('pdfannotator-radio');
    var anonymousCheckbox = document.getElementById('anonymousCheckbox');
    var privateCheckbox = document.getElementById('privateCheckbox');
    var protectedCheckbox = document.getElementById('protectedCheckbox');
    if(anonymousCheckbox) {
        anonymousCheckbox.addEventListener('click', function(){
            if(anonymousCheckbox.checked) {
                if(privateCheckbox){
                    privateCheckbox.checked = false;
                }
                if(protectedCheckbox) {
                    protectedCheckbox.checked = false;
                }
            }
        });
    }

    if(privateCheckbox) {
        privateCheckbox.addEventListener('click', function(){
            if(privateCheckbox.checked) {
                if(anonymousCheckbox){
                    anonymousCheckbox.checked = false;
                }
                if(protectedCheckbox) {
                    protectedCheckbox.checked = false;
                }
            }
        });
    }

    if(protectedCheckbox) {
        protectedCheckbox.addEventListener('click', function(){
            if(protectedCheckbox.checked) {
                if(anonymousCheckbox){
                    anonymousCheckbox.checked = false;
                }
                if(privateCheckbox) {
                    privateCheckbox.checked = false;
                }
            }
        });
    }
}

function setTimeoutNotification(){
    setTimeout(function(){
        let notificationpanel = document.getElementById("user-notifications");
        while (notificationpanel.hasChildNodes()) {  
            notificationpanel.removeChild(notificationpanel.firstChild);
        } 
    }, 10000);
}