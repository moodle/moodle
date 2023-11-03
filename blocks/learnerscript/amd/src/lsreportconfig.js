define(['core/ajax',
    'jquery',
    'jqueryui'
], function(Ajax, $) {
    var lsreportconfig = {
        slideIndex: 1,
        currentSlide: function(currentSlideIndex) {
            lsreportconfig.lsreportslideshow(lsreportconfig.slideIndex = currentSlideIndex);
        },

        progressbar: $( "#progressbar" ),

        lsreportslideshow: function(currentSlideIndex) {
            var i;
            var slides = document.getElementsByClassName("mySlides");
            var dots = document.getElementsByClassName("demo");
            if (currentSlideIndex > slides.length) {lsreportconfig.slideIndex = 1}
            if (currentSlideIndex < 1) {lsreportconfig.slideIndex = slides.length}
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            slides[lsreportconfig.slideIndex-1].style.display = "block";
            setTimeout(function(){
                if (lsreportconfig.slideIndex >= slides.length) {
                    lsreportconfig.currentSlide(1);
                } else {
                    lsreportconfig.lsreportslideshow(lsreportconfig.slideIndex++) ;
                }
            }, 3000);
        },
        lsconfigimportprogress: function(args){
            var val = lsreportconfig.progressbar.progressbar( "value" ) || 0;
            var total = args.total;
            var current = args.current;
            var errorreportspositiondata = args.errorreportspositiondata;
            var lastreportposition = args.lastreportposition;
            var promise = Ajax.call([{
                methodname: 'block_learnerscript_importreports',
                    args: {
                        total: total,
                        current: current,
                        errorreportspositiondata: errorreportspositiondata,
                        lastreportposition: lastreportposition
                    },
                }], false);
            promise[0].done(function(response){
                resp = $.parseJSON(response);
                lsreportconfig.progressbar.progressbar("value", resp.percent);
                if(resp.percent < 100) {
                    if(resp.current && resp.current > 0) {
                        args.current = resp.current;
                    } else {
                        args.current = args.current + 1;
                    }
                    setTimeout(function(){
                        lsreportconfig.lsconfigimportprogress(args);
                    }, 500);
                }
            });
        },
        lsconfigresetprogress: function(step) {
            var promise = Ajax.call([{
                methodname: 'block_learnerscript_resetlsconfig',
                    args: {
                        step : step
                    },
                }], false);
            promise[0].done(function(response){
                resp = $.parseJSON(response);
                lsreportconfig.progressbar.progressbar("value", resp.percent);
                if (resp.percent == 100) {
                    window.location.href = M.cfg.wwwroot + '/blocks/learnerscript/lsconfig.php?import=1';
                }
                if (resp.percent < 100) {
                    if (resp.next && resp.next > 0) {
                        step = resp.next;
                    }
                    setTimeout(function(){
                        lsreportconfig.lsconfigresetprogress(step);
                    }, 500);
                }
            });
        },
        lsreportconfigimport: function (){
            var promise = Ajax.call([{
                methodname: 'block_learnerscript_lsreportconfigimport',
                    args: {
                    },
                }]);
            promise[0].done(function(response){
                return "Message from onbeforeunload handler";
            });
        }
    };
    return {
        init: function(args, status) {
            lsreportconfig.progressbar.progressbar({
                    value: false,
                    change: function() {
                    },
                    complete: function() {
                        $('#reportdashboardnav').show(500);
                    }
                });
            if ($('.mySlides').length > 0) {
                lsreportconfig.lsreportslideshow(1);
            }
            if (status == 'import') {
                lsreportconfig.lsconfigimportprogress(args);
            } else if (status == 'reset') {
                lsreportconfig.lsconfigresetprogress(1);
            }
            window.onbeforeunload = lsreportconfig.lsreportconfigimport;
        },
    };
});