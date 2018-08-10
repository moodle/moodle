define(['jquery','format_buttons/slick'], function($, slick) {

  function initDefaults(){
    var currentSection = 1;
    initSlider($('.slider.sections'));
    sectionsEvents();
    initSlider($('#section'+currentSection+' .slider.labels'),0);
  }

  function sectionsEvents(){
    var sections = $('.slider.sections .nav-item');
    for (var i = 0; i < sections.length; i++) {
      var item = sections[i];
      item.addEventListener('click', function() {
        loop($('.section-content'));
        $('#section' + this.dataset.section).toggleClass('d-none');
        unslickLabels();
        initSlider($('#section' + this.dataset.section + ' .slider.labels'),0);
        labelsEvents(this.dataset.section);
      });
    }
  }

  function labelsEvents(currentSection){
    var labels = $('#section' + currentSection + ' .nav-item');
    for (var i = 0; i < labels.length; i++) {
      var item = labels[i];
      item.addEventListener('click', function() {
        console.log(this);
        var equils = $('[data-label-content="' + this.dataset.label + '"]');
        loop($('#section' + currentSection + ' .label-content'));
        $('[data-label-content="' + this.dataset.label + '"]').toggleClass('d-none');
      });
    }
  }

  // if elem only - horizontal, 2 attr - vertical;
  function initSlider(elem, vert){
    var dir, resp=[], brakepoints= [1200, 992, 540], brp, slides=4;
    (document.dir == "rtl")?dir = true:dir = false;
    (vert !== undefined)?vert = true:vert = false;
    // responsiveness / dropdown on xs vert
    if (vert){
      console.log(elem);
      for (var i=0; i<brakepoints.length; i++){
        if (brakepoints[i] == 540){
          brp = {
            breakpoint: brakepoints[i],
            settings: 'unslick'
          };
          resp.push(brp);
        } else {
          brp = {
            breakpoint: brakepoints[i],
            settings: {
              slidesToShow: slides,
              slidesToScroll: 1,
            }
          };
          resp.push(brp);
        }
      }
    } else {
      for (var i=0; i<brakepoints.length; i++){
        brp = {
          breakpoint: brakepoints[i],
          settings: {
            slidesToShow: --slides,
            slidesToScroll: 1,
          }
        };
        resp.push(brp);
      }
    }

    var slickConfig = {
      dots: false,
      autoplay: false,
      arrows: true,
      vertical: vert,
      verticalSwiping: vert,
      rtl: dir,
      slidesToShow: 4,
      slidesToScroll: 1,
      responsive:resp,
    };
    // console.log("rtl:"+slickConfig.rtl);
    // console.log("vert:"+slickConfig.vertical);
    // console.log("resp:"+slickConfig.responsive);
    // console.log($.isArray(slickConfig.responsive));
    elem.slick(slickConfig);
  }

  function unslickLabels(){
      $('.labels.slick-initialized').slick('unslick');
  }

  function loop (htmlCollection){
    for(var i=0; i<htmlCollection.length;i++){
      var elem = htmlCollection[i];
      if (!htmlCollection[i].classList.contains('d-none')){
        htmlCollection[i].classList += " d-none";
      }
    }
  }


    return {
        init: function() {
          initDefaults();
        }
    };
});
