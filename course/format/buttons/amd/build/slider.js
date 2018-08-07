define(['jquery'], function($) {

    return {
        init: function() {
          // init sliders
          function horizontalSliderInit(){
            $('.slider.sections').slick({
              dots: false,
              autoplay: false,
              arrows:true,
              slidesToShow: 4,
              slidesToScroll: 1,
              responsive: [
                {
                  breakpoint: 1200,
                  settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    infinite: true,
                    dots: false
                  }
                },
                {
                  breakpoint: 992,
                  settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                  }
                },
                {
                  breakpoint: 576,
                  settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                  }
                }
              ]
            });
          }
          function verticalSliderInit(){
            $('.slider.labels').slick({
              dots: false,
              autoplay: false,
              vertical: true,
              slidesToShow: 4,
              slidesToScroll: 1,
              verticalSwiping: true,
              arrows:true,
              responsive: [
                {
                  breakpoint: 1200,
                  settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    infinite: true,
                    dots: false
                  }
                },
                {
                  breakpoint: 992,
                  settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                  }
                },
                {
                  breakpoint: 576,
                  settings: 'unslick'
                }
              ]
            });
          }
          // init sliders
          horizontalSliderInit();
          verticalSliderInit();
          // event Listeners
          var horizontals = $('.slider.sections .nav-item');
          var verticals = $('.slider.labels .nav-item');
          for (var i=0; i<horizontals.length; i++){
            var item = horizontals[i];
            item.addEventListener('click',function(){
              var equils = document.querySelector('[data-appearence="'+this.dataset.position+'"]');
              var index = parseInt(this.dataset.position, 10)-1;
              var btns = document.querySelectorAll('li');
              for(var j=0; j<btns.length;j++){
                if (j == index){
                  btns[j].classList +='slick-active';
                  btns[j].childNodes[0].click();
                } else {
                  btns[j].classList = '';
                }
              }
            });
          }
          for (var i=0; i<verticals.length; i++){
            var item = verticals[i];
            item.addEventListener('click',function(){
              var equils = document.querySelector('[data-text="'+this.dataset.appearence+'"]');
              equils.classList.toggle("d-none");
            });
          }
          console.log('script is working!');
        }
    };
});
