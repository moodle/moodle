var wirisoverrideanswerinput = document.getElementsByName('wirisoverrideanswer');
wirisoverrideanswerinput[0].setAttribute('onkeyup', 'checkEmpty();');
wirisoverrideanswerinput[0].onkeyup = function(){checkEmpty();}; // for IE7

var correctanswerinput = document.getElementsByName('correctanswer');

if (wirisoverrideanswerinput[0].value != ""){
    correctanswerinput[0].disabled = true;
}

function checkEmpty(){
    if (trim(wirisoverrideanswerinput[0].value) != ""){
        correctanswerinput[0].disabled = true;
    }else{
        correctanswerinput[0].disabled = false;
    }
}

function trim(text){
    while (text.substring(0,1) == ' ') {
        text = text.substring(1, text.length);
    }
    while (text.substring(text.length - 1, text.length) == ' '){
        text = text.substring(0,text.length - 1);
    }
    return text;
}
