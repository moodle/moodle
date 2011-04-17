<?php
    require_once($CFG->dirroot.'/mod/scorm/locallib.php');

    if (isset($userdata->status)) {
        if (!isset($userdata->{'cmi.exit'}) || (($userdata->{'cmi.exit'} == 'time-out') || ($userdata->{'cmi.exit'} == 'normal'))) {
                $userdata->entry = 'ab-initio';
        } else {
            if (isset($userdata->{'cmi.exit'}) && (($userdata->{'cmi.exit'} == 'suspend') || ($userdata->{'cmi.exit'} == 'logout'))) {
                $userdata->entry = 'resume';
            } else {
                $userdata->entry = '';
            }
        }
    }
    if (!isset($currentorg)) {
        $currentorg = '';
    }
?>

// Used need to debug cmi content (if you uncomment this, you must comment the definition inside SCORMapi1_3)
//var cmi = new Object();

//
// SCORM 1.3 API Implementation
//
function SCORMapi1_3() {
    // Standard Data Type Definition

    // language key has to be checked for language dependent strings
    var validLanguages = {'aa':'aa', 'ab':'ab', 'ae':'ae', 'af':'af', 'ak':'ak', 'am':'am', 'an':'an', 'ar':'ar', 'as':'as', 'av':'av', 'ay':'ay', 'az':'az',
                          'ba':'ba', 'be':'be', 'bg':'bg', 'bh':'bh', 'bi':'bi', 'bm':'bm', 'bn':'bn', 'bo':'bo', 'br':'br', 'bs':'bs',
                          'ca':'ca', 'ce':'ce', 'ch':'ch', 'co':'co', 'cr':'cr', 'cs':'cs', 'cu':'cu', 'cv':'cv', 'cy':'cy',
                          'da':'da', 'de':'de', 'dv':'dv', 'dz':'dz', 'ee':'ee', 'el':'el', 'en':'en', 'eo':'eo', 'es':'es', 'et':'et', 'eu':'eu',
                          'fa':'fa', 'ff':'ff', 'fi':'fi', 'fj':'fj', 'fo':'fo', 'fr':'fr', 'fy':'fy', 'ga':'ga', 'gd':'gd', 'gl':'gl', 'gn':'gn', 'gu':'gu', 'gv':'gv',
                          'ha':'ha', 'he':'he', 'hi':'hi', 'ho':'ho', 'hr':'hr', 'ht':'ht', 'hu':'hu', 'hy':'hy', 'hz':'hz',
                          'ia':'ia', 'id':'id', 'ie':'ie', 'ig':'ig', 'ii':'ii', 'ik':'ik', 'io':'io', 'is':'is', 'it':'it', 'iu':'iu',
                          'ja':'ja', 'jv':'jv', 'ka':'ka', 'kg':'kg', 'ki':'ki', 'kj':'kj', 'kk':'kk', 'kl':'kl', 'km':'km', 'kn':'kn', 'ko':'ko', 'kr':'kr', 'ks':'ks', 'ku':'ku', 'kv':'kv', 'kw':'kw', 'ky':'ky',
                          'la':'la', 'lb':'lb', 'lg':'lg', 'li':'li', 'ln':'ln', 'lo':'lo', 'lt':'lt', 'lu':'lu', 'lv':'lv',
                          'mg':'mg', 'mh':'mh', 'mi':'mi', 'mk':'mk', 'ml':'ml', 'mn':'mn', 'mo':'mo', 'mr':'mr', 'ms':'ms', 'mt':'mt', 'my':'my',
                          'na':'na', 'nb':'nb', 'nd':'nd', 'ne':'ne', 'ng':'ng', 'nl':'nl', 'nn':'nn', 'no':'no', 'nr':'nr', 'nv':'nv', 'ny':'ny',
                          'oc':'oc', 'oj':'oj', 'om':'om', 'or':'or', 'os':'os', 'pa':'pa', 'pi':'pi', 'pl':'pl', 'ps':'ps', 'pt':'pt',
                          'qu':'qu', 'rm':'rm', 'rn':'rn', 'ro':'ro', 'ru':'ru', 'rw':'rw',
                          'sa':'sa', 'sc':'sc', 'sd':'sd', 'se':'se', 'sg':'sg', 'sh':'sh', 'si':'si', 'sk':'sk', 'sl':'sl', 'sm':'sm', 'sn':'sn', 'so':'so', 'sq':'sq', 'sr':'sr', 'ss':'ss', 'st':'st', 'su':'su', 'sv':'sv', 'sw':'sw',
                          'ta':'ta', 'te':'te', 'tg':'tg', 'th':'th', 'ti':'ti', 'tk':'tk', 'tl':'tl', 'tn':'tn', 'to':'to', 'tr':'tr', 'ts':'ts', 'tt':'tt', 'tw':'tw', 'ty':'ty',
                          'ug':'ug', 'uk':'uk', 'ur':'ur', 'uz':'uz', 've':'ve', 'vi':'vi', 'vo':'vo',
                          'wa':'wa', 'wo':'wo', 'xh':'xh', 'yi':'yi', 'yo':'yo', 'za':'za', 'zh':'zh', 'zu':'zu',
                          'aar':'aar', 'abk':'abk', 'ave':'ave', 'afr':'afr', 'aka':'aka', 'amh':'amh', 'arg':'arg', 'ara':'ara', 'asm':'asm', 'ava':'ava', 'aym':'aym', 'aze':'aze',
                          'bak':'bak', 'bel':'bel', 'bul':'bul', 'bih':'bih', 'bis':'bis', 'bam':'bam', 'ben':'ben', 'tib':'tib', 'bod':'bod', 'bre':'bre', 'bos':'bos',
                          'cat':'cat', 'che':'che', 'cha':'cha', 'cos':'cos', 'cre':'cre', 'cze':'cze', 'ces':'ces', 'chu':'chu', 'chv':'chv', 'wel':'wel', 'cym':'cym',
                          'dan':'dan', 'ger':'ger', 'deu':'deu', 'div':'div', 'dzo':'dzo', 'ewe':'ewe', 'gre':'gre', 'ell':'ell', 'eng':'eng', 'epo':'epo', 'spa':'spa', 'est':'est', 'baq':'baq', 'eus':'eus', 'per':'per',
                          'fas':'fas', 'ful':'ful', 'fin':'fin', 'fij':'fij', 'fao':'fao', 'fre':'fre', 'fra':'fra', 'fry':'fry', 'gle':'gle', 'gla':'gla', 'glg':'glg', 'grn':'grn', 'guj':'guj', 'glv':'glv',
                          'hau':'hau', 'heb':'heb', 'hin':'hin', 'hmo':'hmo', 'hrv':'hrv', 'hat':'hat', 'hun':'hun', 'arm':'arm', 'hye':'hye', 'her':'her',
                          'ina':'ina', 'ind':'ind', 'ile':'ile', 'ibo':'ibo', 'iii':'iii', 'ipk':'ipk', 'ido':'ido', 'ice':'ice', 'isl':'isl', 'ita':'ita', 'iku':'iku',
                          'jpn':'jpn', 'jav':'jav', 'geo':'geo', 'kat':'kat', 'kon':'kon', 'kik':'kik', 'kua':'kua', 'kaz':'kaz', 'kal':'kal', 'khm':'khm', 'kan':'kan', 'kor':'kor', 'kau':'kau', 'kas':'kas', 'kur':'kur', 'kom':'kom', 'cor':'cor', 'kir':'kir',
                          'lat':'lat', 'ltz':'ltz', 'lug':'lug', 'lim':'lim', 'lin':'lin', 'lao':'lao', 'lit':'lit', 'lub':'lub', 'lav':'lav',
                          'mlg':'mlg', 'mah':'mah', 'mao':'mao', 'mri':'mri', 'mac':'mac', 'mkd':'mkd', 'mal':'mal', 'mon':'mon', 'mol':'mol', 'mar':'mar', 'may':'may', 'msa':'msa', 'mlt':'mlt', 'bur':'bur', 'mya':'mya',
                          'nau':'nau', 'nob':'nob', 'nde':'nde', 'nep':'nep', 'ndo':'ndo', 'dut':'dut', 'nld':'nld', 'nno':'nno', 'nor':'nor', 'nbl':'nbl', 'nav':'nav', 'nya':'nya',
                          'oci':'oci', 'oji':'oji', 'orm':'orm', 'ori':'ori', 'oss':'oss', 'pan':'pan', 'pli':'pli', 'pol':'pol', 'pus':'pus', 'por':'por', 'que':'que',
                          'roh':'roh', 'run':'run', 'rum':'rum', 'ron':'ron', 'rus':'rus', 'kin':'kin', 'san':'san', 'srd':'srd', 'snd':'snd', 'sme':'sme', 'sag':'sag', 'slo':'slo', 'sin':'sin', 'slk':'slk', 'slv':'slv', 'smo':'smo', 'sna':'sna', 'som':'som', 'alb':'alb', 'sqi':'sqi', 'srp':'srp', 'ssw':'ssw', 'sot':'sot', 'sun':'sun', 'swe':'swe', 'swa':'swa',
                          'tam':'tam', 'tel':'tel', 'tgk':'tgk', 'tha':'tha', 'tir':'tir', 'tuk':'tuk', 'tgl':'tgl', 'tsn':'tsn', 'ton':'ton', 'tur':'tur', 'tso':'tso', 'tat':'tat', 'twi':'twi', 'tah':'tah',
                          'uig':'uig', 'ukr':'ukr', 'urd':'urd', 'uzb':'uzb', 'ven':'ven', 'vie':'vie', 'vol':'vol', 'wln':'wln', 'wol':'wol', 'xho':'xho', 'yid':'yid', 'yor':'yor', 'zha':'zha', 'chi':'chi', 'zho':'zho', 'zul':'zul'};

    var CMIString200 = '^[\\u0000-\\uFFFF]{0,200}$';
    var CMIString250 = '^[\\u0000-\\uFFFF]{0,250}$';
    var CMIString1000 = '^[\\u0000-\\uFFFF]{0,1000}$';
    var CMIString4000 = '^[\\u0000-\\uFFFF]{0,4000}$';
    var CMIString64000 = '^[\\u0000-\\uFFFF]{0,64000}$';
    var CMILang = '^([a-zA-Z]{2,3}|i|x)(\-[a-zA-Z0-9\-]{2,8})?$|^$';
    var CMILangString250 = '^(\{lang=([a-zA-Z]{2,3}|i|x)(\-[a-zA-Z0-9\-]{2,8})?\})?([^\{].{0,250}$)?';
    var CMILangcr = '^((\{lang=([a-zA-Z]{2,3}|i|x)?(\-[a-zA-Z0-9\-]{2,8})?\}))(.*?)$';
    var CMILangString250cr = '^((\{lang=([a-zA-Z]{2,3}|i|x)?(\-[a-zA-Z0-9\-]{2,8})?\})?(.{0,250})?)?$';
    var CMILangString4000 = '^(\{lang=([a-zA-Z]{2,3}|i|x)(\-[a-zA-Z0-9\-]{2,8})?\})?([^\{].{0,4000}$)?';
    var CMITime = '^(19[7-9]{1}[0-9]{1}|20[0-2]{1}[0-9]{1}|203[0-8]{1})((-(0[1-9]{1}|1[0-2]{1}))((-(0[1-9]{1}|[1-2]{1}[0-9]{1}|3[0-1]{1}))(T([0-1]{1}[0-9]{1}|2[0-3]{1})((:[0-5]{1}[0-9]{1})((:[0-5]{1}[0-9]{1})((\\.[0-9]{1,2})((Z|([+|-]([0-1]{1}[0-9]{1}|2[0-3]{1})))(:[0-5]{1}[0-9]{1})?)?)?)?)?)?)?)?$';
    var CMITimespan = '^P(\\d+Y)?(\\d+M)?(\\d+D)?(T(((\\d+H)(\\d+M)?(\\d+(\.\\d{1,2})?S)?)|((\\d+M)(\\d+(\.\\d{1,2})?S)?)|((\\d+(\.\\d{1,2})?S))))?$';
    var CMIInteger = '^\\d+$';
    var CMISInteger = '^-?([0-9]+)$';
    var CMIDecimal = '^-?([0-9]{1,5})(\\.[0-9]{1,18})?$';
    var CMIIdentifier = '^\\S{0,250}[a-zA-Z0-9]$';
    var CMIShortIdentifier = '^[\\w\.]{1,250}$';
    var CMILongIdentifier = '^\\S{0,4000}$';
    var CMIFeedback = '^.*$'; // This must be redefined
    var CMIIndex = '[._](\\d+).';
    var CMIIndexStore = '.N(\\d+).';
    // Vocabulary Data Type Definition
    var CMICStatus = '^completed$|^incomplete$|^not attempted$|^unknown$';
    var CMISStatus = '^passed$|^failed$|^unknown$';
    var CMIExit = '^time-out$|^suspend$|^logout$|^normal$|^$';
    var CMIType = '^true-false$|^choice$|^(long-)?fill-in$|^matching$|^performance$|^sequencing$|^likert$|^numeric$|^other$';
    var CMIResult = '^correct$|^incorrect$|^unanticipated$|^neutral$|^-?([0-9]{1,4})(\\.[0-9]{1,18})?$';
    var NAVEvent = '^previous$|^continue$|^exit$|^exitAll$|^abandon$|^abandonAll$|^suspendAll$|^{target=\\S{0,200}[a-zA-Z0-9]}choice$';
    var NAVBoolean = '^unknown$|^true$|^false$';
    var NAVTarget = '^previous$|^continue$|^choice.{target=\\S{0,200}[a-zA-Z0-9]}$'
    // Children lists
    var cmi_children = '_version,comments_from_learner,comments_from_lms,completion_status,credit,entry,exit,interactions,launch_data,learner_id,learner_name,learner_preference,location,max_time_allowed,mode,objectives,progress_measure,scaled_passing_score,score,session_time,success_status,suspend_data,time_limit_action,total_time';
    var comments_children = 'comment,timestamp,location';
    var score_children = 'max,raw,scaled,min';
    var objectives_children = 'progress_measure,completion_status,success_status,description,score,id';
    var correct_responses_children = 'pattern';
    var student_data_children = 'mastery_score,max_time_allowed,time_limit_action';
    var student_preference_children = 'audio_level,audio_captioning,delivery_speed,language';
    var interactions_children = 'id,type,objectives,timestamp,correct_responses,weighting,learner_response,result,latency,description';
    // Data ranges
    var scaled_range = '-1#1';
    var audio_range = '0#*';
    var speed_range = '0#*';
    var text_range = '-1#1';
    var progress_range = '0#1';
    var learner_response = {
        'true-false':{'format':'^true$|^false$', 'max':1, 'delimiter':'', 'unique':false},
        'choice':{'format':CMIShortIdentifier, 'max':36, 'delimiter':'[,]', 'unique':true},
        'fill-in':{'format':CMILangString250, 'max':10, 'delimiter':'[,]', 'unique':false},
        'long-fill-in':{'format':CMILangString4000, 'max':1, 'delimiter':'', 'unique':false},
        'matching':{'format':CMIShortIdentifier, 'format2':CMIShortIdentifier, 'max':36, 'delimiter':'[,]', 'delimiter2':'[.]', 'unique':false},
        'performance':{'format':'^$|'+CMIShortIdentifier, 'format2':CMIDecimal+'|^$|'+CMIShortIdentifier, 'max':250, 'delimiter':'[,]', 'delimiter2':'[.]', 'unique':false},
        'sequencing':{'format':CMIShortIdentifier, 'max':36, 'delimiter':'[,]', 'unique':false},
        'likert':{'format':CMIShortIdentifier, 'max':1, 'delimiter':'', 'unique':false},
        'numeric':{'format':CMIDecimal, 'max':1, 'delimiter':'', 'unique':false},
        'other':{'format':CMIString4000, 'max':1, 'delimiter':'', 'unique':false}
    }

    var correct_responses = {
        'true-false':{'pre':'', 'max':1, 'delimiter':'', 'unique':false, 'duplicate':false,
                      'format':'^true$|^false$',
                      'limit':1},
        'choice':{'pre':'', 'max':36, 'delimiter':'[,]', 'unique':true, 'duplicate':false,
                  'format':CMIShortIdentifier},
//        'fill-in':{'pre':'^(((\{case_matters=(true|false)\})(\{order_matters=(true|false)\})?)|((\{order_matters=(true|false)\})(\{case_matters=(true|false)\})?))(.*?)$',
        'fill-in':{'pre':'',
                   'max':10, 'delimiter':'[,]', 'unique':false, 'duplicate':false,
                   'format':CMILangString250cr},
        'long-fill-in':{'pre':'^(\{case_matters=(true|false)\})?', 'max':1, 'delimiter':'', 'unique':false, 'duplicate':true,
                        'format':CMILangString4000},
        'matching':{'pre':'', 'max':36, 'delimiter':'[,]', 'delimiter2':'[.]', 'unique':false, 'duplicate':false,
                    'format':CMIShortIdentifier, 'format2':CMIShortIdentifier},
        'performance':{'pre':'^(\{order_matters=(true|false)\})?',
                       'max':250, 'delimiter':'[,]', 'delimiter2':'[.]', 'unique':false, 'duplicate':false,
                       'format':'^$|'+CMIShortIdentifier, 'format2':CMIDecimal+'|^$|'+CMIShortIdentifier},
        'sequencing':{'pre':'', 'max':36, 'delimiter':'[,]', 'unique':false, 'duplicate':false,
                      'format':CMIShortIdentifier},
        'likert':{'pre':'', 'max':1, 'delimiter':'', 'unique':false, 'duplicate':false,
                  'format':CMIShortIdentifier,
                  'limit':1},
        'numeric':{'pre':'', 'max':2, 'delimiter':'[:]', 'unique':false, 'duplicate':false,
                   'format':CMIDecimal,
                   'limit':1},
        'other':{'pre':'', 'max':1, 'delimiter':'', 'unique':false, 'duplicate':false,
                 'format':CMIString4000,
                 'limit':1}
    }

    // The SCORM 1.3 data model
    var datamodel =  {
        'cmi._children':{'defaultvalue':cmi_children, 'mod':'r'},
        'cmi._version':{'defaultvalue':'1.0', 'mod':'r'},
        'cmi.comments_from_learner._children':{'defaultvalue':comments_children, 'mod':'r'},
        'cmi.comments_from_learner._count':{'mod':'r', 'defaultvalue':'0'},
        'cmi.comments_from_learner.n.comment':{'format':CMILangString4000, 'mod':'rw'},
        'cmi.comments_from_learner.n.location':{'format':CMIString250, 'mod':'rw'},
        'cmi.comments_from_learner.n.timestamp':{'format':CMITime, 'mod':'rw'},
        'cmi.comments_from_lms._children':{'defaultvalue':comments_children, 'mod':'r'},
        'cmi.comments_from_lms._count':{'mod':'r', 'defaultvalue':'0'},
        'cmi.comments_from_lms.n.comment':{'format':CMILangString4000, 'mod':'r'},
        'cmi.comments_from_lms.n.location':{'format':CMIString250, 'mod':'r'},
        'cmi.comments_from_lms.n.timestamp':{'format':CMITime, 'mod':'r'},
        'cmi.completion_status':{'defaultvalue':'<?php echo isset($userdata->{'cmi.completion_status'})?$userdata->{'cmi.completion_status'}:'unknown' ?>', 'format':CMICStatus, 'mod':'rw'},
        'cmi.completion_threshold':{'defaultvalue':<?php echo isset($userdata->threshold)?'\''.$userdata->threshold.'\'':'null' ?>, 'mod':'r'},
        'cmi.credit':{'defaultvalue':'<?php echo isset($userdata->credit)?$userdata->credit:'' ?>', 'mod':'r'},
        'cmi.entry':{'defaultvalue':'<?php echo $userdata->entry ?>', 'mod':'r'},
        'cmi.exit':{'defaultvalue':'<?php echo isset($userdata->{'cmi.exit'})?$userdata->{'cmi.exit'}:'' ?>', 'format':CMIExit, 'mod':'w'},
        'cmi.interactions._children':{'defaultvalue':interactions_children, 'mod':'r'},
        'cmi.interactions._count':{'mod':'r', 'defaultvalue':'0'},
        'cmi.interactions.n.id':{'pattern':CMIIndex, 'format':CMILongIdentifier, 'mod':'rw'},
        'cmi.interactions.n.type':{'pattern':CMIIndex, 'format':CMIType, 'mod':'rw'},
        'cmi.interactions.n.objectives._count':{'pattern':CMIIndex, 'mod':'r', 'defaultvalue':'0'},
        'cmi.interactions.n.objectives.n.id':{'pattern':CMIIndex, 'format':CMILongIdentifier, 'mod':'rw'},
        'cmi.interactions.n.timestamp':{'pattern':CMIIndex, 'format':CMITime, 'mod':'rw'},
        'cmi.interactions.n.correct_responses._count':{'defaultvalue':'0', 'pattern':CMIIndex, 'mod':'r'},
        'cmi.interactions.n.correct_responses.n.pattern':{'pattern':CMIIndex, 'format':'CMIFeedback', 'mod':'rw'},
        'cmi.interactions.n.weighting':{'pattern':CMIIndex, 'format':CMIDecimal, 'mod':'rw'},
        'cmi.interactions.n.learner_response':{'pattern':CMIIndex, 'format':'CMIFeedback', 'mod':'rw'},
        'cmi.interactions.n.result':{'pattern':CMIIndex, 'format':CMIResult, 'mod':'rw'},
        'cmi.interactions.n.latency':{'pattern':CMIIndex, 'format':CMITimespan, 'mod':'rw'},
        'cmi.interactions.n.description':{'pattern':CMIIndex, 'format':CMILangString250, 'mod':'rw'},
        'cmi.launch_data':{'defaultvalue':<?php echo isset($userdata->datafromlms)?'\''.$userdata->datafromlms.'\'':'null' ?>, 'mod':'r'},
        'cmi.learner_id':{'defaultvalue':'<?php echo $userdata->student_id ?>', 'mod':'r'},
        'cmi.learner_name':{'defaultvalue':'<?php echo $userdata->student_name ?>', 'mod':'r'},
        'cmi.learner_preference._children':{'defaultvalue':student_preference_children, 'mod':'r'},
        'cmi.learner_preference.audio_level':{'defaultvalue':'1', 'format':CMIDecimal, 'range':audio_range, 'mod':'rw'},
        'cmi.learner_preference.language':{'defaultvalue':'', 'format':CMILang, 'mod':'rw'},
        'cmi.learner_preference.delivery_speed':{'defaultvalue':'1', 'format':CMIDecimal, 'range':speed_range, 'mod':'rw'},
        'cmi.learner_preference.audio_captioning':{'defaultvalue':'0', 'format':CMISInteger, 'range':text_range, 'mod':'rw'},
        'cmi.location':{'defaultvalue':<?php echo isset($userdata->{'cmi.location'})?'\''.$userdata->{'cmi.location'}.'\'':'null' ?>, 'format':CMIString1000, 'mod':'rw'},
        'cmi.max_time_allowed':{'defaultvalue':<?php echo isset($userdata->maxtimeallowed)?'\''.$userdata->maxtimeallowed.'\'':'null' ?>, 'mod':'r'},
        'cmi.mode':{'defaultvalue':'<?php echo $userdata->mode ?>', 'mod':'r'},
        'cmi.objectives._children':{'defaultvalue':objectives_children, 'mod':'r'},
        'cmi.objectives._count':{'mod':'r', 'defaultvalue':'0'},
        'cmi.objectives.n.id':{'pattern':CMIIndex, 'format':CMILongIdentifier, 'mod':'rw'},
        'cmi.objectives.n.score._children':{'defaultvalue':score_children, 'pattern':CMIIndex, 'mod':'r'},
        'cmi.objectives.n.score.scaled':{'defaultvalue':null, 'pattern':CMIIndex, 'format':CMIDecimal, 'range':scaled_range, 'mod':'rw'},
        'cmi.objectives.n.score.raw':{'defaultvalue':null, 'pattern':CMIIndex, 'format':CMIDecimal, 'mod':'rw'},
        'cmi.objectives.n.score.min':{'defaultvalue':null, 'pattern':CMIIndex, 'format':CMIDecimal, 'mod':'rw'},
        'cmi.objectives.n.score.max':{'defaultvalue':null, 'pattern':CMIIndex, 'format':CMIDecimal, 'mod':'rw'},
        'cmi.objectives.n.success_status':{'defaultvalue':'unknown', 'pattern':CMIIndex, 'format':CMISStatus, 'mod':'rw'},
        'cmi.objectives.n.completion_status':{'defaultvalue':'unknown', 'pattern':CMIIndex, 'format':CMICStatus, 'mod':'rw'},
        'cmi.objectives.n.progress_measure':{'defaultvalue':null, 'format':CMIDecimal, 'range':progress_range, 'mod':'rw'},
        'cmi.objectives.n.description':{'pattern':CMIIndex, 'format':CMILangString250, 'mod':'rw'},
        'cmi.progress_measure':{'defaultvalue':<?php echo isset($userdata->{'cmi.progess_measure'})?'\''.$userdata->{'cmi.progress_measure'}.'\'':'null' ?>, 'format':CMIDecimal, 'range':progress_range, 'mod':'rw'},
        'cmi.scaled_passing_score':{'defaultvalue':<?php echo isset($userdata->{'cmi.scaled_passing_score'})?'\''.$userdata->{'cmi.scaled_passing_score'}.'\'':'null' ?>, 'format':CMIDecimal, 'range':scaled_range, 'mod':'r'},
        'cmi.score._children':{'defaultvalue':score_children, 'mod':'r'},
        'cmi.score.scaled':{'defaultvalue':<?php echo isset($userdata->{'cmi.score.scaled'})?'\''.$userdata->{'cmi.score.scaled'}.'\'':'null' ?>, 'format':CMIDecimal, 'range':scaled_range, 'mod':'rw'},
        'cmi.score.raw':{'defaultvalue':<?php echo isset($userdata->{'cmi.score.raw'})?'\''.$userdata->{'cmi.score.raw'}.'\'':'null' ?>, 'format':CMIDecimal, 'mod':'rw'},
        'cmi.score.min':{'defaultvalue':<?php echo isset($userdata->{'cmi.score.min'})?'\''.$userdata->{'cmi.score.min'}.'\'':'null' ?>, 'format':CMIDecimal, 'mod':'rw'},
        'cmi.score.max':{'defaultvalue':<?php echo isset($userdata->{'cmi.score.max'})?'\''.$userdata->{'cmi.score.max'}.'\'':'null' ?>, 'format':CMIDecimal, 'mod':'rw'},
        'cmi.session_time':{'format':CMITimespan, 'mod':'w', 'defaultvalue':'PT0H0M0S'},
        'cmi.success_status':{'defaultvalue':'<?php echo isset($userdata->{'cmi.success_status'})?$userdata->{'cmi.success_status'}:'unknown' ?>', 'format':CMISStatus, 'mod':'rw'},
        'cmi.suspend_data':{'defaultvalue':<?php echo isset($userdata->{'cmi.suspend_data'})?'\''.$userdata->{'cmi.suspend_data'}.'\'':'null' ?>, 'format':CMIString64000, 'mod':'rw'},
        'cmi.time_limit_action':{'defaultvalue':<?php echo isset($userdata->timelimitaction)?'\''.$userdata->timelimitaction.'\'':'null' ?>, 'mod':'r'},
        'cmi.total_time':{'defaultvalue':'<?php echo isset($userdata->{'cmi.total_time'})?$userdata->{'cmi.total_time'}:'PT0H0M0S' ?>', 'mod':'r'},
        'adl.nav.request':{'defaultvalue':'_none_', 'format':NAVEvent, 'mod':'rw'}
    };
    //
    // Datamodel inizialization
    //
        var cmi = new Object();
        cmi.comments_from_learner = new Object();
        cmi.comments_from_learner._count = 0;
        cmi.comments_from_lms = new Object();
        cmi.comments_from_lms._count = 0;
        cmi.interactions = new Object();
        cmi.interactions._count = 0;
        cmi.learner_preference = new Object();
        cmi.objectives = new Object();
        cmi.objectives._count = 0;
        cmi.score = new Object();

    // Navigation Object
    var adl = new Object();
        adl.nav = new Object();
        adl.nav.request_valid = new Array();

    for (element in datamodel) {
        if (element.match(/\.n\./) == null) {
            if ((typeof eval('datamodel["'+element+'"].defaultvalue')) != 'undefined') {
                eval(element+' = datamodel["'+element+'"].defaultvalue;');
            } else {
                eval(element+' = "";');
            }
        }
    }

<?php
    // reconstitute objectives, comments_from_learner and comments_from_lms
    scorm_reconstitute_array_element($scorm->version, $userdata, 'cmi.objectives', array('score'));
    scorm_reconstitute_array_element($scorm->version, $userdata, 'cmi.interactions', array('objectives', 'correct_responses'));
    scorm_reconstitute_array_element($scorm->version, $userdata, 'cmi.comments_from_learner', array());
    scorm_reconstitute_array_element($scorm->version, $userdata, 'cmi.comments_from_lms', array());
?>

    if (cmi.completion_status == '') {
        cmi.completion_status = 'not attempted';
    }

    //
    // API Methods definition
    //
    var Initialized = false;
    var Terminated = false;
    var diagnostic = "";
    var errorCode = "0";

    function Initialize (param) {
        errorCode = "0";
        if (param == "") {
            if ((!Initialized) && (!Terminated)) {
                Initialized = true;
                errorCode = "0";
                <?php
                    if (scorm_debugging($scorm)) {
                        echo 'LogAPICall("Initialize", param, "", errorCode);';
                    }
                ?>
                return "true";
            } else {
                if (Initialized) {
                    errorCode = "103";
                } else {
                    errorCode = "104";
                }
            }
        } else {
            errorCode = "201";
        }
        <?php
            if (scorm_debugging($scorm)) {
                echo 'LogAPICall("Initialize", param, "", errorCode);';
            }
        ?>
        return "false";
    }


<?php
    // pull in the TOC callback
    include_once($CFG->dirroot.'/mod/scorm/datamodels/callback.js.php');
 ?>


    function Terminate (param) {
        errorCode = "0";
        if (param == "") {
            if ((Initialized) && (!Terminated)) {
                var AJAXResult = StoreData(cmi,true);
                <?php
                    if (scorm_debugging($scorm)) {
                        echo 'LogAPICall("Terminate", "AJAXResult", AJAXResult, 0);';
                    }
                ?>
                result = ('true' == AJAXResult) ? 'true' : 'false';
                errorCode = ('true' == result)? '0' : '101'; // General exception for any AJAX fault
                <?php
                    if (scorm_debugging($scorm)) {
                        echo 'LogAPICall("Terminate", "result", result, errorCode);';
                    }
                ?>
                if ('true' == result) {
                    Initialized = false;
                    Terminated = true;
                    if (adl.nav.request != '_none_') {
                        switch (adl.nav.request) {
                            case 'continue':
                                setTimeout('scorm_get_next();',500);
                            break;
                            case 'previous':
                                setTimeout('scorm_get_prev();',500);
                            break;
                            case 'choice':
                            break;
                            case 'exit':
                            break;
                            case 'exitAll':
                            break;
                            case 'abandon':
                            break;
                            case 'abandonAll':
                            break;
                        }
                    } else {
                        if (<?php echo $scorm->auto ?> == 1) {
                            setTimeout('scorm_get_next();',500);
                        }
                    }
                    // trigger TOC update
                    var sURL = "<?php echo $CFG->wwwroot; ?>" + "/mod/scorm/prereqs.php?a=<?php echo $scorm->id ?>&scoid=<?php echo $scoid ?>&attempt=<?php echo $attempt ?>&mode=<?php echo $mode ?>&currentorg=<?php echo $currentorg ?>&sesskey=<?php echo sesskey(); ?>";
                    YAHOO.util.Connect.asyncRequest('GET', sURL, this.connectPrereqCallback, null);
                } else {
                    diagnostic = "Failure calling the Terminate remote callback: the server replied with HTTP Status " + AJAXResult;
                }
                return result;
            } else {
                if (Terminated) {
                    errorCode = "113";
                } else {
                    errorCode = "112";
                }
            }
        } else {
            errorCode = "201";
        }
        <?php
            if (scorm_debugging($scorm)) {
                echo 'LogAPICall("Terminate", param, "", errorCode);';
            }
        ?>
        return "false";
    }

    function GetValue (element) {
        errorCode = "0";
        diagnostic = "";
        if ((Initialized) && (!Terminated)) {
            if (element !="") {
                var expression = new RegExp(CMIIndex,'g');
                var elementmodel = String(element).replace(expression,'.n.');
                if ((typeof eval('datamodel["'+elementmodel+'"]')) != "undefined") {
                    if (eval('datamodel["'+elementmodel+'"].mod') != 'w') {

                        element = String(element).replace(/\.(\d+)\./, ".N$1.");
                        element = element.replace(/\.(\d+)\./, ".N$1.");

                        var elementIndexes = element.split('.');
                        var subelement = element.substr(0,3);
                        var i = 1;
                        while ((i < elementIndexes.length) && (typeof eval(subelement) != "undefined")) {
                            subelement += '.'+elementIndexes[i++];
                        }

                        if (subelement == element) {

                            if ((typeof eval(subelement) != "undefined") && (eval(subelement) != null)) {
                                errorCode = "0";
                                <?php
                                    if (scorm_debugging($scorm)) {
                                        echo 'LogAPICall("GetValue", element, eval(element), 0);';
                                    }
                                ?>
                                return eval(element);
                            } else {
                                errorCode = "403";
                            }
                        } else {
                            errorCode = "301";
                        }
                    } else {
                        //errorCode = eval('datamodel["'+elementmodel+'"].readerror');
                        errorCode = "405";
                    }
                } else {
                    var childrenstr = '._children';
                    var countstr = '._count';
                    var parentmodel = '';
                    if (elementmodel.substr(elementmodel.length-childrenstr.length,elementmodel.length) == childrenstr) {
                        parentmodel = elementmodel.substr(0,elementmodel.length-childrenstr.length);
                        if ((typeof eval('datamodel["'+parentmodel+'"]')) != "undefined") {
                            errorCode = "301";
                            diagnostic = "Data Model Element Does Not Have Children";
                        } else {
                            errorCode = "401";
                        }
                    } else if (elementmodel.substr(elementmodel.length-countstr.length,elementmodel.length) == countstr) {
                        parentmodel = elementmodel.substr(0,elementmodel.length-countstr.length);
                        if ((typeof eval('datamodel["'+parentmodel+'"]')) != "undefined") {
                            errorCode = "301";
                            diagnostic = "Data Model Element Cannot Have Count";
                        } else {
                            errorCode = "401";
                        }
                    } else {
                        parentmodel = 'adl.nav.request_valid.';
                        if (element.substr(0,parentmodel.length) == parentmodel) {
                            if (element.substr(parentmodel.length).match(NAVTarget) == null) {
                                errorCode = "301";
                            } else {
                                if (adl.nav.request == element.substr(parentmodel.length)) {
                                    return "true";
                                } else if (adl.nav.request == '_none_') {
                                    return "unknown";
                                } else {
                                    return "false";
                                }
                            }
                        } else {
                            errorCode = "401";
                        }
                    }
                }
            } else {
                errorCode = "301";
            }
        } else {
            if (Terminated) {
                errorCode = "123";
            } else {
                errorCode = "122";
            }
        }
        <?php
            if (scorm_debugging($scorm)) {
                echo 'LogAPICall("GetValue", element, "", errorCode);';
            }
        ?>
        return "";
    }

    function SetValue (element,value) {
        errorCode = "0";
        diagnostic = "";
        if ((Initialized) && (!Terminated)) {
            if (element != "") {
                var expression = new RegExp(CMIIndex,'g');
                var elementmodel = String(element).replace(expression,'.n.');
                if ((typeof eval('datamodel["'+elementmodel+'"]')) != "undefined") {
                    if (eval('datamodel["'+elementmodel+'"].mod') != 'r') {
                        if (eval('datamodel["'+elementmodel+'"].format') != 'CMIFeedback') {
                            expression = new RegExp(eval('datamodel["'+elementmodel+'"].format'));
                        } else {
                            // cmi.interactions.n.type depending format accept everything at this stage
                            expression = new RegExp(CMIFeedback);
                        }
                        value = value+'';
                        var matches = value.match(expression);
                        if ((matches != null) && ((matches.join('').length > 0) || (value.length == 0))) {
                            // Value match dataelement format

                            if (element != elementmodel) {
                                //This is a dynamic datamodel element

                                var elementIndexes = element.split('.');
                                var subelement = 'cmi';
                                var parentelement = 'cmi';
                                for (var i=1;(i < elementIndexes.length-1) && (errorCode=="0");i++) {
                                    var elementIndex = elementIndexes[i];
                                    if (elementIndexes[i+1].match(/^\d+$/)) {
                                        if ((parseInt(elementIndexes[i+1]) > 0) && (elementIndexes[i+1].charAt(0) == 0)) {
                                            // Index has a leading 0 (zero), this is not a number
                                            errorCode = "351";
                                        }
                                        parentelement = subelement+'.'+elementIndex;
                                        if ((typeof eval(parentelement) == "undefined") || (typeof eval(parentelement+'._count') == "undefined")) {
                                            errorCode="408";
                                        } else {
                                            if (elementIndexes[i+1] > eval(parentelement+'._count')) {
                                                errorCode = "351";
                                                diagnostic = "Data Model Element Collection Set Out Of Order";
                                            }
                                            subelement = subelement.concat('.'+elementIndex+'.N'+elementIndexes[i+1]);
                                            i++;

                                            if (((typeof eval(subelement)) == "undefined") && (i < elementIndexes.length-2)) {
                                                errorCode="408";
                                            }
                                        }
                                    } else {
                                        subelement = subelement.concat('.'+elementIndex);
                                    }
                                }

                                if (errorCode == "0") {
                                    // Till now it's a real datamodel element

                                    element = subelement.concat('.'+elementIndexes[elementIndexes.length-1]);

                                    if ((typeof eval(subelement)) == "undefined") {
                                        switch (elementmodel) {
                                            case 'cmi.objectives.n.id':
                                                if (!duplicatedID(element,parentelement,value)) {
                                                    if (elementIndexes[elementIndexes.length-2] == eval(parentelement+'._count')) {
                                                        eval(parentelement+'._count++;');
                                                        eval(subelement+' = new Object();');
                                                        var subobject = eval(subelement);
                                                        subobject.success_status = datamodel["cmi.objectives.n.success_status"].defaultvalue;
                                                        subobject.completion_status = datamodel["cmi.objectives.n.completion_status"].defaultvalue;
                                                        subobject.progress_measure = datamodel["cmi.objectives.n.progress_measure"].defaultvalue;
                                                        subobject.score = new Object();
                                                        subobject.score._children = score_children;
                                                        subobject.score.scaled = datamodel["cmi.objectives.n.score.scaled"].defaultvalue;
                                                        subobject.score.raw = datamodel["cmi.objectives.n.score.raw"].defaultvalue;
                                                        subobject.score.min = datamodel["cmi.objectives.n.score.min"].defaultvalue;
                                                        subobject.score.max = datamodel["cmi.objectives.n.score.max"].defaultvalue;
                                                    }
                                                } else {
                                                    errorCode="351";
                                                    diagnostic = "Data Model Element ID Already Exists";
                                                }
                                            break;
                                            case 'cmi.interactions.n.id':
                                                if (elementIndexes[elementIndexes.length-2] == eval(parentelement+'._count')) {
                                                    eval(parentelement+'._count++;');
                                                    eval(subelement+' = new Object();');
                                                    var subobject = eval(subelement);
                                                    subobject.objectives = new Object();
                                                    subobject.objectives._count = 0;
                                                }
                                            break;
                                            case 'cmi.interactions.n.objectives.n.id':
                                                if (typeof eval(parentelement) != "undefined") {
                                                    if (!duplicatedID(element,parentelement,value)) {
                                                        if (elementIndexes[elementIndexes.length-2] == eval(parentelement+'._count')) {
                                                            eval(parentelement+'._count++;');
                                                            eval(subelement+' = new Object();');
                                                        }
                                                    } else {
                                                        errorCode="351";
                                                        diagnostic = "Data Model Element ID Already Exists";
                                                    }
                                                } else {
                                                    errorCode="408";
                                                }
                                            break;
                                            case 'cmi.interactions.n.correct_responses.n.pattern':
                                                if (typeof eval(parentelement) != "undefined") {
                                                    // Use cmi.interactions.n.type value to check the right dataelement format
                                                    if (elementIndexes[elementIndexes.length-2] == eval(parentelement+'._count')) {
                                                        var interactiontype = eval(String(parentelement).replace('correct_responses','type'));
                                                        var interactioncount = eval(parentelement+'._count');
                                                        // trap duplicate values, which is not allowed for type choice
                                                        if (interactiontype == 'choice') {
                                                            for (var i=0; (i < interactioncount) && (errorCode=="0"); i++) {
                                                               if (eval(parentelement+'.N'+i+'.pattern') == value) {
                                                                   errorCode = "351";
                                                               }
                                                            }
                                                        }
                                                        if ((typeof correct_responses[interactiontype].limit == 'undefined') ||
                                                            (eval(parentelement+'._count') < correct_responses[interactiontype].limit)) {
                                                            var nodes = new Array();
                                                            if (correct_responses[interactiontype].delimiter != '') {
                                                                nodes = value.split(correct_responses[interactiontype].delimiter);
                                                            } else {
                                                                nodes[0] = value;
                                                            }
                                                            if ((nodes.length > 0) && (nodes.length <= correct_responses[interactiontype].max)) {
                                                                errorCode = CRcheckValueNodes (element, interactiontype, nodes, value, errorCode);
                                                            } else if (nodes.length > correct_responses[interactiontype].max) {
                                                                errorCode = "351";
                                                                diagnostic = "Data Model Element Pattern Too Long";
                                                            }
                                                            if ((errorCode == "0") && ((correct_responses[interactiontype].duplicate == false) ||
                                                               (!duplicatedPA(element,parentelement,value))) || (errorCode == "0" && value == "")) {
                                                               eval(parentelement+'._count++;');
                                                               eval(subelement+' = new Object();');
                                                            } else {
                                                                if (errorCode == "0") {
                                                                    errorCode="351";
                                                                    diagnostic = "Data Model Element Pattern Already Exists";
                                                                }
                                                            }
                                                        } else {
                                                            errorCode="351";
                                                            diagnostic = "Data Model Element Collection Limit Reached";
                                                        }
                                                    } else {
                                                        errorCode="351";
                                                        diagnostic = "Data Model Element Collection Set Out Of Order";
                                                    }
                                                } else {
                                                    errorCode="408";
                                                }
                                            break;
                                            default:
                                                if ((parentelement != 'cmi.objectives') && (parentelement != 'cmi.interactions') && (typeof eval(parentelement) != "undefined")) {
                                                    if (elementIndexes[elementIndexes.length-2] == eval(parentelement+'._count')) {
                                                        eval(parentelement+'._count++;');
                                                        eval(subelement+' = new Object();');
                                                    } else {
                                                        errorCode="351";
                                                        diagnostic = "Data Model Element Collection Set Out Of Order";
                                                    }
                                                } else {
                                                    errorCode="408";
                                                }
                                            break;
                                        }
                                    } else {
                                        switch (elementmodel) {
                                            case 'cmi.objectives.n.id':
                                                if (eval(element) != value) {
                                                    errorCode = "351";
                                                    diagnostic = "Write Once Violation";
                                                }
                                            break;
                                            case 'cmi.interactions.n.objectives.n.id':
                                                if (duplicatedID(element,parentelement,value)) {
                                                    errorCode = "351";
                                                    diagnostic = "Data Model Element ID Already Exists";
                                                }
                                            break;
                                            case 'cmi.interactions.n.type':
                                                var subobject = eval(subelement);
                                                subobject.correct_responses = new Object();
                                                subobject.correct_responses._count = 0;
                                            break;
                                            case 'cmi.interactions.n.learner_response':
                                                if (typeof eval(subelement+'.type') == "undefined") {
                                                    errorCode="408";
                                                } else {
                                                    // Use cmi.interactions.n.type value to check the right dataelement format
                                                    interactiontype = eval(subelement+'.type');
                                                    var nodes = new Array();
                                                    if (learner_response[interactiontype].delimiter != '') {
                                                        nodes = value.split(learner_response[interactiontype].delimiter);
                                                    } else {
                                                        nodes[0] = value;
                                                    }
                                                    if ((nodes.length > 0) && (nodes.length <= learner_response[interactiontype].max)) {
                                                        expression = new RegExp(learner_response[interactiontype].format);
                                                        for (var i=0; (i < nodes.length) && (errorCode=="0"); i++) {
                                                            if (typeof learner_response[interactiontype].delimiter2 != 'undefined') {
                                                                values = nodes[i].split(learner_response[interactiontype].delimiter2);
                                                                if (values.length == 2) {
                                                                    matches = values[0].match(expression);
                                                                    if (matches == null) {
                                                                        errorCode = "406";
                                                                    } else {
                                                                        var expression2 = new RegExp(learner_response[interactiontype].format2);
                                                                        matches = values[1].match(expression2);
                                                                        if (matches == null) {
                                                                            errorCode = "406";
                                                                        }
                                                                    }
                                                                } else {
                                                                    errorCode = "406";
                                                                }
                                                            } else {
                                                                matches = nodes[i].match(expression);
                                                                if (matches == null) {
                                                                    errorCode = "406";
                                                                } else {
                                                                    if ((nodes[i] != '') && (learner_response[interactiontype].unique)) {
                                                                        for (var j=0; (j<i) && (errorCode=="0"); j++) {
                                                                            if (nodes[i] == nodes[j]) {
                                                                                errorCode = "406";
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    } else if (nodes.length > learner_response[interactiontype].max) {
                                                        errorCode = "351";
                                                        diagnostic = "Data Model Element Pattern Too Long";
                                                    }
                                                }
                                             break;
                                         case 'cmi.interactions.n.correct_responses.n.pattern':
	                                         subel= subelement.split('.');
                                             subel1= 'cmi.interactions.'+subel[2];

                                                if (typeof eval(subel1+'.type') == "undefined") {
                                                    errorCode="408";
                                                } else {
                                                    // Use cmi.interactions.n.type value to check the right //dataelement format
                                                    var interactiontype = eval(subel1+'.type');
                                                    var interactioncount = eval(parentelement+'._count');
                                                    // trap duplicate values, which is not allowed for type choice
                                                    if (interactiontype == 'choice') {
                                                        for (var i=0; (i < interactioncount) && (errorCode=="0"); i++) {
                                                           if (eval(parentelement+'.N'+i+'.pattern') == value) {
                                                               errorCode = "351";
                                                           }
                                                        }
                                                    }
                                                    var nodes = new Array();
                                                    if (correct_responses[interactiontype].delimiter != '') {
                                                        nodes = value.split(correct_responses[interactiontype].delimiter);
                                                    } else {
                                                        nodes[0] = value;
                                                    }

                                                    if ((nodes.length > 0) && (nodes.length <= correct_responses[interactiontype].max)) {
                                                        errorCode = CRcheckValueNodes (element, interactiontype, nodes, value, errorCode);
                                                    } else if (nodes.length > correct_responses[interactiontype].max) {
                                                        errorCode = "351";
                                                        diagnostic = "Data Model Element Pattern Too Long";
                                                    }
                                                }
                                             break;
                                        }
                                    }
                                }
                            }
                            //Store data
                            if (errorCode == "0") {

                                if ((typeof eval('datamodel["'+elementmodel+'"].range')) != "undefined") {
                                    range = eval('datamodel["'+elementmodel+'"].range');
                                    ranges = range.split('#');
                                    value = value*1.0;
                                    if (value >= ranges[0]) {
                                        if ((ranges[1] == '*') || (value <= ranges[1])) {
                                            eval(element+'=value;');
                                            errorCode = "0";
                                            <?php
                                                if (scorm_debugging($scorm)) {
                                                    echo 'LogAPICall("SetValue", element, value, errorCode);';
                                                }
                                            ?>
                                            return "true";
                                        } else {
                                            errorCode = '407';
                                        }
                                    } else {
                                        errorCode = '407';
                                    }
                                } else {
                                    eval(element+'=value;');
                                    errorCode = "0";
                                    <?php
                                        if (scorm_debugging($scorm)) {
                                            echo 'LogAPICall("SetValue", element, value, errorCode);';
                                        }
                                    ?>
                                    return "true";
                                }
                            }
                        } else {
                            errorCode = "406";
                        }
                    } else {
                        errorCode = "404";
                    }
                } else {
                    errorCode = "401"
                }
            } else {
                errorCode = "351";
            }
        } else {
            if (Terminated) {
                errorCode = "133";
            } else {
                errorCode = "132";
            }
        }
        <?php
            if (scorm_debugging($scorm)) {
                echo 'LogAPICall("SetValue", element, value, errorCode);';
            }
        ?>
        return "false";
    }


    function CRremovePrefixes (node) {
        // check for prefixes lang, case, order
        // case and then order
        var seenOrder = false;
        var seenCase = false;
        var seenLang = false;
        var errorCode = "0";
        while (matches = node.match('^(\{(lang|case_matters|order_matters)=([^\}]+)\})')) {
            switch (matches[2]) {
                case 'lang':
                    // check for language prefix on each node
                    langmatches = node.match(CMILangcr);
                    if (langmatches != null) {
                        lang = langmatches[3];
                        // check that language string definition is valid
                        if (lang.length > 0 && lang != undefined) {
                            if (validLanguages[lang.toLowerCase()] == undefined) {
                                errorCode = "406";
                            }
                        }
                    }
                    seenLang = true;
                break;

                case 'case_matters':
                    // check for correct case answer
                    if (! seenLang && ! seenOrder && ! seenCase) {
                        if (matches[3] != 'true' && matches[3] != 'false') {
                            errorCode = "406";
                        }
                    }
                    seenCase = true;
                break;

                case 'order_matters':
                    // check for correct case answer
                    if (! seenCase && ! seenLang && ! seenOrder) {
                        if (matches[3] != 'true' && matches[3] != 'false') {
                            errorCode = "406";
                        }
                    }
                    seenOrder = true;
                break;

                default:
                break;
            }
            node = node.substr(matches[1].length);
        }
        return {'errorCode': errorCode, 'node': node};
    }


    function CRcheckValueNodes(element, interactiontype, nodes, value, errorCode) {
        expression = new RegExp(correct_responses[interactiontype].format);
        for (var i=0; (i < nodes.length) && (errorCode=="0"); i++) {
            if (interactiontype.match('^(fill-in|long-fill-in|matching|performance|sequencing)$')) {
                result = CRremovePrefixes(nodes[i]);
                errorCode = result.errorCode;
                nodes[i] = result.node;
            }

            // check for prefix on each node
            if (correct_responses[interactiontype].pre != '') {
                matches = nodes[i].match(correct_responses[interactiontype].pre);
                if (matches != null) {
                    nodes[i] = nodes[i].substr(matches[1].length);
                }
            }

            if (correct_responses[interactiontype].delimiter2 != undefined) {
                values = nodes[i].split(correct_responses[interactiontype].delimiter2);
                if (values.length == 2) {
                    matches = values[0].match(expression);
                    if (matches == null) {
                        errorCode = "406";
                    } else {
                        var expression2 = new RegExp(correct_responses[interactiontype].format2);
                        matches = values[1].match(expression2);
                        if (matches == null) {
                            errorCode = "406";
                        }
                    }
                } else {
                     errorCode = "406";
                }
            } else {
                matches = nodes[i].match(expression);
                //if ((matches == null) || (matches.join('').length == 0)) {
                if ((matches == null && value != "")||(matches == null && interactiontype=="true-false")){
                    errorCode = "406";
                } else {
                    // numeric range - left must be <= right
                    if (interactiontype == 'numeric' && nodes.length > 1) {
                        if (parseFloat(nodes[0]) > parseFloat(nodes[1])) {
                            errorCode = "406";
                        }
                    } else {
                        if ((nodes[i] != '') && (correct_responses[interactiontype].unique)) {
                            for (var j=0; (j < i) && (errorCode=="0"); j++) {
                                if (nodes[i] == nodes[j]) {
                                    errorCode = "406";
                                }
                            }
                        }
                    }
                }
            }
        } // end of for each nodes
        return errorCode;
    }


    function Commit (param) {
        errorCode = "0";
        if (param == "") {
            if ((Initialized) && (!Terminated)) {
                var AJAXResult = StoreData(cmi,false);
                <?php
                    if (scorm_debugging($scorm)) {
                        echo 'LogAPICall("Commit", "AJAXResult", AJAXResult, 0);';
                    }
                ?>
                var result = ('true' == AJAXResult) ? 'true' : 'false';
                errorCode = ('true' == result)? '0' : '101'; // General exception for any AJAX fault
                <?php
                    if (scorm_debugging($scorm)) {
                        echo 'LogAPICall("Commit", "result", result, errorCode);';
                    }
                ?>
                if ('false' == result) {
                    diagnostic = "Failure calling the Commit remote callback: the server replied with HTTP Status " + AJAXResult;
                }
                return result;
            } else {
                if (Terminated) {
                    errorCode = "143";
                } else {
                    errorCode = "142";
                }
            }
        } else {
            errorCode = "201";
        }
        <?php
            if (scorm_debugging($scorm)) {
                echo 'LogAPICall("Commit", param, "", errorCode);';
            }
        ?>
        return "false";
    }

    function GetLastError () {
    <?php
        if (scorm_debugging($scorm)) {
            echo 'LogAPICall("GetLastError", "", "", errorCode);';
        }
    ?>
        return errorCode;
    }

    function GetErrorString (param) {
        if (param != "") {
            var errorString = "";
            switch(param) {
                case "0":
                    errorString = "No error";
                break;
                case "101":
                    errorString = "General exception";
                break;
                case "102":
                    errorString = "General Inizialization Failure";
                break;
                case "103":
                    errorString = "Already Initialized";
                break;
                case "104":
                    errorString = "Content Instance Terminated";
                break;
                case "111":
                    errorString = "General Termination Failure";
                break;
                case "112":
                    errorString = "Termination Before Inizialization";
                break;
                case "113":
                    errorString = "Termination After Termination";
                break;
                case "122":
                    errorString = "Retrieve Data Before Initialization";
                break;
                case "123":
                    errorString = "Retrieve Data After Termination";
                break;
                case "132":
                    errorString = "Store Data Before Inizialization";
                break;
                case "133":
                    errorString = "Store Data After Termination";
                break;
                case "142":
                    errorString = "Commit Before Inizialization";
                break;
                case "143":
                    errorString = "Commit After Termination";
                break;
                case "201":
                    errorString = "General Argument Error";
                break;
                case "301":
                    errorString = "General Get Failure";
                break;
                case "351":
                    errorString = "General Set Failure";
                break;
                case "391":
                    errorString = "General Commit Failure";
                break;
                case "401":
                    errorString = "Undefinited Data Model";
                break;
                case "402":
                    errorString = "Unimplemented Data Model Element";
                break;
                case "403":
                    errorString = "Data Model Element Value Not Initialized";
                break;
                case "404":
                    errorString = "Data Model Element Is Read Only";
                break;
                case "405":
                    errorString = "Data Model Element Is Write Only";
                break;
                case "406":
                    errorString = "Data Model Element Type Mismatch";
                break;
                case "407":
                    errorString = "Data Model Element Value Out Of Range";
                break;
                case "408":
                    errorString = "Data Model Dependency Not Established";
                break;
            }
            <?php
            if (scorm_debugging($scorm)) {
                echo 'LogAPICall("GetErrorString", param,  errorString, 0);';
            }
             ?>
            return errorString;
        } else {
           <?php
            if (scorm_debugging($scorm)) {
                echo 'LogAPICall("GetErrorString", param,  "No error string found!", 0);';
            }
             ?>
            return "";
        }
    }

    function GetDiagnostic (param) {
        if (diagnostic != "") {
            <?php
                if (scorm_debugging($scorm)) {
                    echo 'LogAPICall("GetDiagnostic", param, diagnostic, 0);';
                }
            ?>
            return diagnostic;
        }
        <?php
            if (scorm_debugging($scorm)) {
                echo 'LogAPICall("GetDiagnostic", param, param, 0);';
            }
        ?>
        return param;
    }

    function duplicatedID (element, parent, value) {
        var found = false;
        var elements = eval(parent+'._count');
        for (var n=0;(n < elements) && (!found);n++) {
            if ((parent+'.N'+n+'.id' != element) && (eval(parent+'.N'+n+'.id') == value)) {
                found = true;
            }
        }
        return found;
    }

    function duplicatedPA (element, parent, value) {
        var found = false;
        var elements = eval(parent+'._count');
        for (var n=0;(n < elements) && (!found);n++) {
            if ((parent+'.N'+n+'.pattern' != element) && (eval(parent+'.N'+n+'.pattern') == value)) {
                found = true;
            }
        }
        return found;
    }

    function getElementModel(element) {
        if (typeof datamodel[element] != "undefined") {
            return element;
        } else {
            var expression = new RegExp(CMIIndex,'g');
            var elementmodel = String(element).replace(expression,'.n.');
            if (typeof datamodel[elementmodel] != "undefined") {
                return elementmodel;
            }
        }
        return false;
    }

    function AddTime (first, second) {
        <?php
//            if (scorm_debugging($scorm)) {
//                echo 'alert("AddTime: "+first+" + "+second);';
//            }
        ?>
        var timestring = 'P';
        var matchexpr = /^P((\d+)Y)?((\d+)M)?((\d+)D)?(T((\d+)H)?((\d+)M)?((\d+(\.\d{1,2})?)S)?)?$/;
        var firstarray = first.match(matchexpr);
        var secondarray = second.match(matchexpr);
        if ((firstarray != null) && (secondarray != null)) {
            var firstsecs=0;
            if(parseFloat(firstarray[13],10)>0){ firstsecs=parseFloat(firstarray[13],10); }
            var secondsecs=0;
            if(parseFloat(secondarray[13],10)>0){ secondsecs=parseFloat(secondarray[13],10); }
            var secs = firstsecs+secondsecs;  //Seconds
            var change = Math.floor(secs/60);
            secs = Math.round((secs-(change*60))*100)/100;
            var firstmins=0;
            if(parseInt(firstarray[11],10)>0){ firstmins=parseInt(firstarray[11],10); }
            var secondmins=0;
            if(parseInt(secondarray[11],10)>0){ secondmins=parseInt(secondarray[11],10); }
            var mins = firstmins+secondmins+change;   //Minutes
            change = Math.floor(mins / 60);
            mins = Math.round(mins-(change*60));
            var firsthours=0;
            if(parseInt(firstarray[9],10)>0){ firsthours=parseInt(firstarray[9],10); }
            var secondhours=0;
            if(parseInt(secondarray[9],10)>0){ secondhours=parseInt(secondarray[9],10); }
            var hours = firsthours+secondhours+change; //Hours
            change = Math.floor(hours/24);
            hours = Math.round(hours-(change*24));
            var firstdays=0;
            if(parseInt(firstarray[6],10)>0){ firstdays=parseInt(firstarray[6],10); }
            var seconddays=0;
            if(parseInt(secondarray[6],10)>0){ firstdays=parseInt(secondarray[6],10); }
            var days = Math.round(firstdays+seconddays+change); // Days
            var firstmonths=0;
            if(parseInt(firstarray[4],10)>0){ firstmonths=parseInt(firstarray[4],10); }
            var secondmonths=0;
            if(parseInt(secondarray[4],10)>0){ secondmonths=parseInt(secondarray[4],10); }
            var months = Math.round(firstmonths+secondmonths);
            var firstyears=0;
            if(parseInt(firstarray[2],10)>0){ firstyears=parseInt(firstarray[2],10); }
            var secondyears=0;
            if(parseInt(secondarray[2],10)>0){ secondyears=parseInt(secondarray[2],10); }
            var years = Math.round(firstyears+secondyears);
        }
        if (years > 0) {
            timestring += years + 'Y';
        }
        if (months > 0) {
            timestring += months + 'M';
        }
        if (days > 0) {
            timestring += days + 'D';
        }
        if ((hours > 0) || (mins > 0) || (secs > 0)) {
            timestring += 'T';
            if (hours > 0) {
                timestring += hours + 'H';
            }
            if (mins > 0) {
                timestring += mins + 'M';
            }
            if (secs > 0) {
                timestring += secs + 'S';
            }
        }
        return timestring;
    }

    function TotalTime() {
        var total_time = AddTime(cmi.total_time, cmi.session_time);
        return '&'+underscore('cmi.total_time')+'='+encodeURIComponent(total_time);
    }

    function CollectData(data,parent) {
        var datastring = '';
        for (property in data) {
            if (typeof data[property] == 'object') {
                datastring += CollectData(data[property],parent+'.'+property);
            } else {
                var element = parent+'.'+property;
                var expression = new RegExp(CMIIndexStore,'g');
                var elementmodel = String(element).replace(expression,'.n.');
                if ((typeof eval('datamodel["'+elementmodel+'"]')) != "undefined") {
                    if (eval('datamodel["'+elementmodel+'"].mod') != 'r') {
                        var elementstring = '&'+underscore(element)+'='+encodeURIComponent(data[property]);
                        if ((typeof eval('datamodel["'+elementmodel+'"].defaultvalue')) != "undefined") {
                            if (eval('datamodel["'+elementmodel+'"].defaultvalue') != data[property] || eval('typeof(datamodel["'+elementmodel+'"].defaultvalue)') != typeof(data[property])) {
                                datastring += elementstring;
                            }
                        } else {
                            datastring += elementstring;
                        }
                    }
                }
            }
        }
        return datastring;
    }

    function StoreData(data,storetotaltime) {
        var datastring = '';
        if (storetotaltime) {
            if (cmi.mode == 'normal') {
                if (cmi.credit == 'credit') {
                    if ((cmi.completion_threshold != null) && (cmi.progress_measure != null)) {
                        if (cmi.progress_measure >= cmi.completion_threshold) {
                            cmi.completion_status = 'completed';
                        } else {
                            cmi.completion_status = 'incomplete';
                        }
                    }
                    if ((cmi.scaled_passed_score != null) && (cmi.score.scaled != '')) {
                        if (cmi.score.scaled >= cmi.scaled_passed_score) {
                            cmi.success_status = 'passed';
                        } else {
                            cmi.success_status = 'failed';
                        }
                    }
                }
            }
            datastring += TotalTime();
        }
        datastring += CollectData(data,'cmi');
        var element = 'adl.nav.request';
        var navrequest = eval(element) != datamodel[element].defaultvalue ? '&'+underscore(element)+'='+encodeURIComponent(eval(element)) : '';
        datastring += navrequest;
        datastring += '&attempt=<?php echo $attempt ?>';
        datastring += '&scoid=<?php echo $scoid ?>';
        var myRequest = NewHttpReq();
        var result = DoRequest(myRequest,"<?php p($CFG->wwwroot) ?>/mod/scorm/datamodel.php","id=<?php p($id) ?>&a=<?php p($a) ?>&sesskey=<?php echo sesskey() ?>"+datastring);
        var results = String(result).split('\n');
        if ((results.length > 2) && (navrequest != '')) {
            eval(results[2]);
        }
        errorCode = results[1];
        return results[0];
    }

    this.Initialize = Initialize;
    this.Terminate = Terminate;
    this.GetValue = GetValue;
    this.SetValue = SetValue;
    this.Commit = Commit;
    this.GetLastError = GetLastError;
    this.GetErrorString = GetErrorString;
    this.GetDiagnostic = GetDiagnostic;
    this.version = '1.0';
}

var API_1484_11 = new SCORMapi1_3();

<?php
// pull in the debugging utilities
if (scorm_debugging($scorm)) {
    include_once($CFG->dirroot.'/mod/scorm/datamodels/debug.js.php');
    echo 'AppendToLog("Moodle SCORM 1.3 API Loaded, Activity: '.$scorm->name.', SCO: '.$sco->identifier.'", 0);';
}
 ?>
