define(['jquery'], function($) {
 
    return {
        init: function() {

                $(document).ready(function() {
     
                  $('#owl-slide1').owlCarousel({
                      autoplay:true,
                      autoplayTimeout:10000,
                      responsive: false,
                      loop:true,
                      nav:true,
                      navText: ["<i class='fa fa-arrow-circle-left fa-2x' aria-hidden='true'></i>","<i class='fa fa-arrow-circle-right fa-2x' aria-hidden='true'></i>"],
                      items:1,

                  });
                 

                  $("#owl-slide2").owlCarousel({
                 
                      autoplay: true, 
                      autoplayTimeout:6000,
                      responsive: false,
                      loop:true,
                      nav:false,
                      items:1,
                 
                  });

                 
                });

        }
    }
});

