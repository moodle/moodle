define(['jquery','theme_crisp/magnific-popup'], function($) {
 
    return {
        init: function() {
            $(document).ready(function() {
              $('.video').magnificPopup({
                  type: 'iframe',
                  
                  
                  iframe: {
                     markup: '<div class="mfp-iframe-scaler">'+
                                '<div class="mfp-close"></div>'+
                                '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>'+
                                '<div class="mfp-title">Some caption</div>'+
                              '</div>'
                  },
                  callbacks: {
                    markupParse: function(template, values, item) {
                     values.title = item.el.attr('title');
                    }
                  },
                  patterns: {
                    youtube: {
                      index: 'youtube.com/', // String that detects type of video (in this case YouTube). Simply via url.indexOf(index).

                      id: 'v=', // String that splits URL in a two parts, second part should be %id%
                      // Or null - full URL will be returned
                      // Or a function that should return %id%, for example:
                      // id: function(url) { return 'parsed id'; }

                      src: '//www.youtube.com/embed/%id%?autoplay=1' // URL that will be set as a source for iframe.
                    }
                  }
                  
                });

                

  
                           /*$('.galleryzz').magnificPopup({
                                  delegate:'a',
                                  type: 'iframe',
                                  
                                  
                                  iframe: {
                                     markup: '<div class="mfp-iframe-scaler">'+
                                                '<div class="mfp-close"></div>'+
                                                '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>'+
                                                '<div class="mfp-title">Some caption</div>'+
                                              '</div>'
                                  },
                                  callbacks: {
                                    markupParse: function(template, values, item) {
                                     values.title = item.el.attr('title');
                                    }
                                  }
                             
                                  
                                  
                                });*/
            



              });
              

        }
    }
});
