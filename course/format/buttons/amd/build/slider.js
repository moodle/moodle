define(['jquery'], function($) {

    return {
        init: function() {

          var sectionStartPos = $('.tab-pane.active #topics').position();
          var topicStartPos = $('.tab-pane.active #topics').position();

          function move(e, sP, direction){
            var cP = e.position(); //current position
            var wS = (e.width())/(e.children().length); // width step;
            var hS = (e.height())/(e.children().length); // height step;

            if (direction === 'left'){
              if(cP.left < sP.left){
                var wPos = cP.left + wS;
                if(wPos>0){wPos=0}
                e.css({'transform': 'translateX('+ wPos +'px)'});
              }
            } else if (direction === 'right') {
              var sR = $('.slide-right').position().left;

              if(cP.left + e.width() > sR){
                var wPos = cP.left - wS;
                e.css({'transform': 'translateX('+ wPos +'px)'});
              }
            } else if (direction === 'top'){
              if(cP.top < sP.top){
                var hPos = cP.top + hS;
                if(hPos>0){hPos=0}
                e.css({'transform': 'translateY('+ hPos +'px)'});
              }
            } else if (direction === 'bottom') {
              var sB = $('.slide-bottom').position().top;

              if(cP.top + e.height() > sB){
                var hPos = cP.top - sP.top - hS;
                var bMax = (sB-sP.top)-e.height();
                console.log(bMax);
                if(hPos<bMax){hPos=sB-e.height()}
                e.css({'transform': 'translateY('+ hPos +'px)'});
              }
            }
          }

          $('.slide-tabs.slide-left').on('click', function(){
            move($('#sections'), sectionStartPos, 'left');
          });
          $('.slide-tabs.slide-right').on('click', function(){
            move($('#sections'), sectionStartPos, 'right');
          });
          $('.slide-tabs.slide-top').on('click', function(){
            move($('.tab-pane.active #topics'), topicStartPos, 'top');
          });
          $('.slide-tabs.slide-bottom').on('click', function(){
            move($('.tab-pane.active #topics'), topicStartPos, 'bottom');
          });

        }
    };
});
