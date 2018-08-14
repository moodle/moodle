define(['jquery','format_buttons/slick'], function($, slick) {

  function initDefaults(){
    var currentSection = checkStorage('lastSection'),
        currentLabel = checkStorage('lastLabel');
        // console.log("currentSection "+currentSection);
        // console.log("currentLabel "+currentLabel);

    if (currentSection == 1){
      var check = document.querySelector('.slider.sections .nav-item');
      if(check.dataset.section !== currentSection){
        currentSection = check.dataset.section;
      }
    }
    // console.log("currentSection "+currentSection);
    if (currentLabel == 1){
      var check = document.querySelector('#section'+currentSection+' .label-item');
      if(check.dataset.label !== currentLabel){
        localStorage.setItem('lastLabel', check.dataset.label);
        currentLabel = check.dataset.label;
      }
    }

    initSlider($('.slider.sections'));
    sectionsEvents();
    $('.slider.sections .nav-item[data-section="'+currentSection+'"]').toggleClass('active');
    $('#section' + currentSection).toggleClass('d-none');
    initSlider($('#section'+currentSection+' .slider.labels'),0);
    labelsEvents(currentSection);
  }

  function sectionsEvents(){
    var sections = $('.slider.sections .nav-item');
    for (var i = 0; i < sections.length; i++) {
      var item = sections[i];
      item.addEventListener('click', function() {
        loopActive(sections, item);
        $('.slider.sections .nav-item[data-section="'+this.dataset.section+'"]').toggleClass('active');
        loop($('.section-content'));
        $('#section' + this.dataset.section).toggleClass('d-none');
        addToStorage('lastSection', this.dataset.section);
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
        addToStorage('lastLabel', this.dataset.label);
        loopActive(labels, item);
        $('[data-label="' + this.dataset.label + '"]').toggleClass('active');
        var equils = $('[data-label-content="' + this.dataset.label + '"]');
        loop($('#section' + currentSection + ' .label-content'));
        $('[data-label-content="' + this.dataset.label + '"]').toggleClass('d-none');
      });
    }
    var checkLabel = document.querySelector('#section' + currentSection + ' .nav-item.active');
    if (checkLabel == null){
      var check = document.querySelector('#section'+currentSection+' .label-item');
      check.classList += ' active';
      var equils = $('[data-label-content="' + check.dataset.label + '"]');
      loop($('#section' + currentSection + ' .label-content'));
      $('[data-label-content="' + check.dataset.label + '"]').toggleClass('d-none');
    }
  }

  // if elem only - horizontal, 2 attr - vertical;
  function initSlider(elem, vert){
    var dir, resp=[], brakepoints= [1200, 992, 540], brp, slides=4;
    (document.dir == "rtl")?dir = true:dir = false;
    (vert !== undefined)?vert = true:vert = false;
    // responsiveness / dropdown on xs vert
    if (vert){
      for (var i=0; i<brakepoints.length; i++){
        if (brakepoints[i] == 540){
          // add dropdown touch event
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

  function loopActive(htmlCollection, currentActive){
    for(var i=0; i<htmlCollection.length;i++){
      var elem = htmlCollection[i];
      if (htmlCollection[i].classList.contains('active')){
        htmlCollection[i].classList.remove('active');
      }
    }
  }

  function loop (htmlCollection){
    for(var i=0; i<htmlCollection.length;i++){
      var elem = htmlCollection[i];
      if (!htmlCollection[i].classList.contains('d-none')){
        htmlCollection[i].classList += " d-none";
      }
    }
  }

  function checkStorage(key){
    if (localStorage.getItem(key)){
      return localStorage.getItem(key);
    } else {
       localStorage.setItem(key, 1);
       return 1;
    }
  }

  function addToStorage(key, value){
    if (localStorage.getItem( key )){
      localStorage.setItem(key, value);
    }
  }

  // bottom prev|next buttons for labels on xs breakpoint
  // function xsButtons(){
  //   var btns = '<button class="label-prev" onclick=""></button>';
  //   var block = document.createElement('div');
  // }
  // function slideLabel(dir){
  //   if (dir == 'prev'){
  //     labelSlider.slick('slickPrev');
  //   } else if (dir == 'next'){
  //     labelSlider.slick('slickNext');
  //   }
  // }


    return {
        init: function() {
          initDefaults();
        }
    };
});
