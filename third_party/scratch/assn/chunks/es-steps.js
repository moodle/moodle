(window["webpackJsonpGUI"] = window["webpackJsonpGUI"] || []).push([["es-steps"],{

/***/ "./src/lib/libraries/decks/es-steps.js":
/*!*********************************************!*\
  !*** ./src/lib/libraries/decks/es-steps.js ***!
  \*********************************************/
/*! exports provided: esImages */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "esImages", function() { return esImages; });
/* harmony import */ var _steps_intro_1_move_es_gif__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./steps/intro-1-move.es.gif */ "./src/lib/libraries/decks/steps/intro-1-move.es.gif");
/* harmony import */ var _steps_intro_2_say_es_gif__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./steps/intro-2-say.es.gif */ "./src/lib/libraries/decks/steps/intro-2-say.es.gif");
/* harmony import */ var _steps_intro_3_green_flag_es_gif__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./steps/intro-3-green-flag.es.gif */ "./src/lib/libraries/decks/steps/intro-3-green-flag.es.gif");
/* harmony import */ var _steps_speech_add_extension_es_gif__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./steps/speech-add-extension.es.gif */ "./src/lib/libraries/decks/steps/speech-add-extension.es.gif");
/* harmony import */ var _steps_speech_say_something_es_png__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./steps/speech-say-something.es.png */ "./src/lib/libraries/decks/steps/speech-say-something.es.png");
/* harmony import */ var _steps_speech_set_voice_es_png__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./steps/speech-set-voice.es.png */ "./src/lib/libraries/decks/steps/speech-set-voice.es.png");
/* harmony import */ var _steps_speech_move_around_es_png__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./steps/speech-move-around.es.png */ "./src/lib/libraries/decks/steps/speech-move-around.es.png");
/* harmony import */ var _steps_pick_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./steps/pick-backdrop.LTR.gif */ "./src/lib/libraries/decks/steps/pick-backdrop.LTR.gif");
/* harmony import */ var _steps_speech_add_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./steps/speech-add-sprite.LTR.gif */ "./src/lib/libraries/decks/steps/speech-add-sprite.LTR.gif");
/* harmony import */ var _steps_speech_song_es_png__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./steps/speech-song.es.png */ "./src/lib/libraries/decks/steps/speech-song.es.png");
/* harmony import */ var _steps_speech_change_color_es_png__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./steps/speech-change-color.es.png */ "./src/lib/libraries/decks/steps/speech-change-color.es.png");
/* harmony import */ var _steps_speech_spin_es_png__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./steps/speech-spin.es.png */ "./src/lib/libraries/decks/steps/speech-spin.es.png");
/* harmony import */ var _steps_speech_grow_shrink_es_png__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./steps/speech-grow-shrink.es.png */ "./src/lib/libraries/decks/steps/speech-grow-shrink.es.png");
/* harmony import */ var _steps_cn_show_character_LTR_gif__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./steps/cn-show-character.LTR.gif */ "./src/lib/libraries/decks/steps/cn-show-character.LTR.gif");
/* harmony import */ var _steps_cn_say_es_png__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./steps/cn-say.es.png */ "./src/lib/libraries/decks/steps/cn-say.es.png");
/* harmony import */ var _steps_cn_glide_es_png__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./steps/cn-glide.es.png */ "./src/lib/libraries/decks/steps/cn-glide.es.png");
/* harmony import */ var _steps_cn_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./steps/cn-pick-sprite.LTR.gif */ "./src/lib/libraries/decks/steps/cn-pick-sprite.LTR.gif");
/* harmony import */ var _steps_cn_collect_es_png__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./steps/cn-collect.es.png */ "./src/lib/libraries/decks/steps/cn-collect.es.png");
/* harmony import */ var _steps_add_variable_es_gif__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./steps/add-variable.es.gif */ "./src/lib/libraries/decks/steps/add-variable.es.gif");
/* harmony import */ var _steps_cn_score_es_png__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./steps/cn-score.es.png */ "./src/lib/libraries/decks/steps/cn-score.es.png");
/* harmony import */ var _steps_cn_backdrop_es_png__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./steps/cn-backdrop.es.png */ "./src/lib/libraries/decks/steps/cn-backdrop.es.png");
/* harmony import */ var _steps_add_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ./steps/add-sprite.LTR.gif */ "./src/lib/libraries/decks/steps/add-sprite.LTR.gif");
/* harmony import */ var _steps_name_pick_letter_LTR_gif__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! ./steps/name-pick-letter.LTR.gif */ "./src/lib/libraries/decks/steps/name-pick-letter.LTR.gif");
/* harmony import */ var _steps_name_play_sound_es_png__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! ./steps/name-play-sound.es.png */ "./src/lib/libraries/decks/steps/name-play-sound.es.png");
/* harmony import */ var _steps_name_pick_letter2_LTR_gif__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! ./steps/name-pick-letter2.LTR.gif */ "./src/lib/libraries/decks/steps/name-pick-letter2.LTR.gif");
/* harmony import */ var _steps_name_change_color_es_png__WEBPACK_IMPORTED_MODULE_25__ = __webpack_require__(/*! ./steps/name-change-color.es.png */ "./src/lib/libraries/decks/steps/name-change-color.es.png");
/* harmony import */ var _steps_name_spin_es_png__WEBPACK_IMPORTED_MODULE_26__ = __webpack_require__(/*! ./steps/name-spin.es.png */ "./src/lib/libraries/decks/steps/name-spin.es.png");
/* harmony import */ var _steps_name_grow_es_png__WEBPACK_IMPORTED_MODULE_27__ = __webpack_require__(/*! ./steps/name-grow.es.png */ "./src/lib/libraries/decks/steps/name-grow.es.png");
/* harmony import */ var _steps_music_pick_instrument_LTR_gif__WEBPACK_IMPORTED_MODULE_28__ = __webpack_require__(/*! ./steps/music-pick-instrument.LTR.gif */ "./src/lib/libraries/decks/steps/music-pick-instrument.LTR.gif");
/* harmony import */ var _steps_music_play_sound_es_png__WEBPACK_IMPORTED_MODULE_29__ = __webpack_require__(/*! ./steps/music-play-sound.es.png */ "./src/lib/libraries/decks/steps/music-play-sound.es.png");
/* harmony import */ var _steps_music_make_song_es_png__WEBPACK_IMPORTED_MODULE_30__ = __webpack_require__(/*! ./steps/music-make-song.es.png */ "./src/lib/libraries/decks/steps/music-make-song.es.png");
/* harmony import */ var _steps_music_make_beat_es_png__WEBPACK_IMPORTED_MODULE_31__ = __webpack_require__(/*! ./steps/music-make-beat.es.png */ "./src/lib/libraries/decks/steps/music-make-beat.es.png");
/* harmony import */ var _steps_music_make_beatbox_es_png__WEBPACK_IMPORTED_MODULE_32__ = __webpack_require__(/*! ./steps/music-make-beatbox.es.png */ "./src/lib/libraries/decks/steps/music-make-beatbox.es.png");
/* harmony import */ var _steps_chase_game_add_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_33__ = __webpack_require__(/*! ./steps/chase-game-add-backdrop.LTR.gif */ "./src/lib/libraries/decks/steps/chase-game-add-backdrop.LTR.gif");
/* harmony import */ var _steps_chase_game_add_sprite1_LTR_gif__WEBPACK_IMPORTED_MODULE_34__ = __webpack_require__(/*! ./steps/chase-game-add-sprite1.LTR.gif */ "./src/lib/libraries/decks/steps/chase-game-add-sprite1.LTR.gif");
/* harmony import */ var _steps_chase_game_right_left_es_png__WEBPACK_IMPORTED_MODULE_35__ = __webpack_require__(/*! ./steps/chase-game-right-left.es.png */ "./src/lib/libraries/decks/steps/chase-game-right-left.es.png");
/* harmony import */ var _steps_chase_game_up_down_es_png__WEBPACK_IMPORTED_MODULE_36__ = __webpack_require__(/*! ./steps/chase-game-up-down.es.png */ "./src/lib/libraries/decks/steps/chase-game-up-down.es.png");
/* harmony import */ var _steps_chase_game_add_sprite2_LTR_gif__WEBPACK_IMPORTED_MODULE_37__ = __webpack_require__(/*! ./steps/chase-game-add-sprite2.LTR.gif */ "./src/lib/libraries/decks/steps/chase-game-add-sprite2.LTR.gif");
/* harmony import */ var _steps_chase_game_move_randomly_es_png__WEBPACK_IMPORTED_MODULE_38__ = __webpack_require__(/*! ./steps/chase-game-move-randomly.es.png */ "./src/lib/libraries/decks/steps/chase-game-move-randomly.es.png");
/* harmony import */ var _steps_chase_game_play_sound_es_png__WEBPACK_IMPORTED_MODULE_39__ = __webpack_require__(/*! ./steps/chase-game-play-sound.es.png */ "./src/lib/libraries/decks/steps/chase-game-play-sound.es.png");
/* harmony import */ var _steps_chase_game_change_score_es_png__WEBPACK_IMPORTED_MODULE_40__ = __webpack_require__(/*! ./steps/chase-game-change-score.es.png */ "./src/lib/libraries/decks/steps/chase-game-change-score.es.png");
/* harmony import */ var _steps_pop_game_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_41__ = __webpack_require__(/*! ./steps/pop-game-pick-sprite.LTR.gif */ "./src/lib/libraries/decks/steps/pop-game-pick-sprite.LTR.gif");
/* harmony import */ var _steps_pop_game_play_sound_es_png__WEBPACK_IMPORTED_MODULE_42__ = __webpack_require__(/*! ./steps/pop-game-play-sound.es.png */ "./src/lib/libraries/decks/steps/pop-game-play-sound.es.png");
/* harmony import */ var _steps_pop_game_change_score_es_png__WEBPACK_IMPORTED_MODULE_43__ = __webpack_require__(/*! ./steps/pop-game-change-score.es.png */ "./src/lib/libraries/decks/steps/pop-game-change-score.es.png");
/* harmony import */ var _steps_pop_game_random_position_es_png__WEBPACK_IMPORTED_MODULE_44__ = __webpack_require__(/*! ./steps/pop-game-random-position.es.png */ "./src/lib/libraries/decks/steps/pop-game-random-position.es.png");
/* harmony import */ var _steps_pop_game_change_color_es_png__WEBPACK_IMPORTED_MODULE_45__ = __webpack_require__(/*! ./steps/pop-game-change-color.es.png */ "./src/lib/libraries/decks/steps/pop-game-change-color.es.png");
/* harmony import */ var _steps_pop_game_reset_score_es_png__WEBPACK_IMPORTED_MODULE_46__ = __webpack_require__(/*! ./steps/pop-game-reset-score.es.png */ "./src/lib/libraries/decks/steps/pop-game-reset-score.es.png");
/* harmony import */ var _steps_animate_char_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_47__ = __webpack_require__(/*! ./steps/animate-char-pick-sprite.LTR.gif */ "./src/lib/libraries/decks/steps/animate-char-pick-sprite.LTR.gif");
/* harmony import */ var _steps_animate_char_say_something_es_png__WEBPACK_IMPORTED_MODULE_48__ = __webpack_require__(/*! ./steps/animate-char-say-something.es.png */ "./src/lib/libraries/decks/steps/animate-char-say-something.es.png");
/* harmony import */ var _steps_animate_char_add_sound_es_png__WEBPACK_IMPORTED_MODULE_49__ = __webpack_require__(/*! ./steps/animate-char-add-sound.es.png */ "./src/lib/libraries/decks/steps/animate-char-add-sound.es.png");
/* harmony import */ var _steps_animate_char_talk_es_png__WEBPACK_IMPORTED_MODULE_50__ = __webpack_require__(/*! ./steps/animate-char-talk.es.png */ "./src/lib/libraries/decks/steps/animate-char-talk.es.png");
/* harmony import */ var _steps_animate_char_move_es_png__WEBPACK_IMPORTED_MODULE_51__ = __webpack_require__(/*! ./steps/animate-char-move.es.png */ "./src/lib/libraries/decks/steps/animate-char-move.es.png");
/* harmony import */ var _steps_animate_char_jump_es_png__WEBPACK_IMPORTED_MODULE_52__ = __webpack_require__(/*! ./steps/animate-char-jump.es.png */ "./src/lib/libraries/decks/steps/animate-char-jump.es.png");
/* harmony import */ var _steps_animate_char_change_color_es_png__WEBPACK_IMPORTED_MODULE_53__ = __webpack_require__(/*! ./steps/animate-char-change-color.es.png */ "./src/lib/libraries/decks/steps/animate-char-change-color.es.png");
/* harmony import */ var _steps_story_pick_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_54__ = __webpack_require__(/*! ./steps/story-pick-backdrop.LTR.gif */ "./src/lib/libraries/decks/steps/story-pick-backdrop.LTR.gif");
/* harmony import */ var _steps_story_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_55__ = __webpack_require__(/*! ./steps/story-pick-sprite.LTR.gif */ "./src/lib/libraries/decks/steps/story-pick-sprite.LTR.gif");
/* harmony import */ var _steps_story_say_something_es_png__WEBPACK_IMPORTED_MODULE_56__ = __webpack_require__(/*! ./steps/story-say-something.es.png */ "./src/lib/libraries/decks/steps/story-say-something.es.png");
/* harmony import */ var _steps_story_pick_sprite2_LTR_gif__WEBPACK_IMPORTED_MODULE_57__ = __webpack_require__(/*! ./steps/story-pick-sprite2.LTR.gif */ "./src/lib/libraries/decks/steps/story-pick-sprite2.LTR.gif");
/* harmony import */ var _steps_story_flip_es_gif__WEBPACK_IMPORTED_MODULE_58__ = __webpack_require__(/*! ./steps/story-flip.es.gif */ "./src/lib/libraries/decks/steps/story-flip.es.gif");
/* harmony import */ var _steps_story_conversation_es_png__WEBPACK_IMPORTED_MODULE_59__ = __webpack_require__(/*! ./steps/story-conversation.es.png */ "./src/lib/libraries/decks/steps/story-conversation.es.png");
/* harmony import */ var _steps_story_pick_backdrop2_LTR_gif__WEBPACK_IMPORTED_MODULE_60__ = __webpack_require__(/*! ./steps/story-pick-backdrop2.LTR.gif */ "./src/lib/libraries/decks/steps/story-pick-backdrop2.LTR.gif");
/* harmony import */ var _steps_story_switch_backdrop_es_png__WEBPACK_IMPORTED_MODULE_61__ = __webpack_require__(/*! ./steps/story-switch-backdrop.es.png */ "./src/lib/libraries/decks/steps/story-switch-backdrop.es.png");
/* harmony import */ var _steps_story_hide_character_es_png__WEBPACK_IMPORTED_MODULE_62__ = __webpack_require__(/*! ./steps/story-hide-character.es.png */ "./src/lib/libraries/decks/steps/story-hide-character.es.png");
/* harmony import */ var _steps_story_show_character_es_png__WEBPACK_IMPORTED_MODULE_63__ = __webpack_require__(/*! ./steps/story-show-character.es.png */ "./src/lib/libraries/decks/steps/story-show-character.es.png");
/* harmony import */ var _steps_video_add_extension_es_gif__WEBPACK_IMPORTED_MODULE_64__ = __webpack_require__(/*! ./steps/video-add-extension.es.gif */ "./src/lib/libraries/decks/steps/video-add-extension.es.gif");
/* harmony import */ var _steps_video_pet_es_png__WEBPACK_IMPORTED_MODULE_65__ = __webpack_require__(/*! ./steps/video-pet.es.png */ "./src/lib/libraries/decks/steps/video-pet.es.png");
/* harmony import */ var _steps_video_animate_es_png__WEBPACK_IMPORTED_MODULE_66__ = __webpack_require__(/*! ./steps/video-animate.es.png */ "./src/lib/libraries/decks/steps/video-animate.es.png");
/* harmony import */ var _steps_video_pop_es_png__WEBPACK_IMPORTED_MODULE_67__ = __webpack_require__(/*! ./steps/video-pop.es.png */ "./src/lib/libraries/decks/steps/video-pop.es.png");
/* harmony import */ var _steps_fly_choose_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_68__ = __webpack_require__(/*! ./steps/fly-choose-backdrop.LTR.gif */ "./src/lib/libraries/decks/steps/fly-choose-backdrop.LTR.gif");
/* harmony import */ var _steps_fly_choose_character_LTR_png__WEBPACK_IMPORTED_MODULE_69__ = __webpack_require__(/*! ./steps/fly-choose-character.LTR.png */ "./src/lib/libraries/decks/steps/fly-choose-character.LTR.png");
/* harmony import */ var _steps_fly_say_something_es_png__WEBPACK_IMPORTED_MODULE_70__ = __webpack_require__(/*! ./steps/fly-say-something.es.png */ "./src/lib/libraries/decks/steps/fly-say-something.es.png");
/* harmony import */ var _steps_fly_make_interactive_es_png__WEBPACK_IMPORTED_MODULE_71__ = __webpack_require__(/*! ./steps/fly-make-interactive.es.png */ "./src/lib/libraries/decks/steps/fly-make-interactive.es.png");
/* harmony import */ var _steps_fly_object_to_collect_LTR_png__WEBPACK_IMPORTED_MODULE_72__ = __webpack_require__(/*! ./steps/fly-object-to-collect.LTR.png */ "./src/lib/libraries/decks/steps/fly-object-to-collect.LTR.png");
/* harmony import */ var _steps_fly_flying_heart_es_png__WEBPACK_IMPORTED_MODULE_73__ = __webpack_require__(/*! ./steps/fly-flying-heart.es.png */ "./src/lib/libraries/decks/steps/fly-flying-heart.es.png");
/* harmony import */ var _steps_fly_select_flyer_LTR_png__WEBPACK_IMPORTED_MODULE_74__ = __webpack_require__(/*! ./steps/fly-select-flyer.LTR.png */ "./src/lib/libraries/decks/steps/fly-select-flyer.LTR.png");
/* harmony import */ var _steps_fly_keep_score_es_png__WEBPACK_IMPORTED_MODULE_75__ = __webpack_require__(/*! ./steps/fly-keep-score.es.png */ "./src/lib/libraries/decks/steps/fly-keep-score.es.png");
/* harmony import */ var _steps_fly_choose_scenery_LTR_gif__WEBPACK_IMPORTED_MODULE_76__ = __webpack_require__(/*! ./steps/fly-choose-scenery.LTR.gif */ "./src/lib/libraries/decks/steps/fly-choose-scenery.LTR.gif");
/* harmony import */ var _steps_fly_move_scenery_es_png__WEBPACK_IMPORTED_MODULE_77__ = __webpack_require__(/*! ./steps/fly-move-scenery.es.png */ "./src/lib/libraries/decks/steps/fly-move-scenery.es.png");
/* harmony import */ var _steps_fly_switch_costume_es_png__WEBPACK_IMPORTED_MODULE_78__ = __webpack_require__(/*! ./steps/fly-switch-costume.es.png */ "./src/lib/libraries/decks/steps/fly-switch-costume.es.png");
/* harmony import */ var _steps_pong_add_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_79__ = __webpack_require__(/*! ./steps/pong-add-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/pong-add-backdrop.LTR.png");
/* harmony import */ var _steps_pong_add_ball_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_80__ = __webpack_require__(/*! ./steps/pong-add-ball-sprite.LTR.png */ "./src/lib/libraries/decks/steps/pong-add-ball-sprite.LTR.png");
/* harmony import */ var _steps_pong_bounce_around_es_png__WEBPACK_IMPORTED_MODULE_81__ = __webpack_require__(/*! ./steps/pong-bounce-around.es.png */ "./src/lib/libraries/decks/steps/pong-bounce-around.es.png");
/* harmony import */ var _steps_pong_add_a_paddle_LTR_gif__WEBPACK_IMPORTED_MODULE_82__ = __webpack_require__(/*! ./steps/pong-add-a-paddle.LTR.gif */ "./src/lib/libraries/decks/steps/pong-add-a-paddle.LTR.gif");
/* harmony import */ var _steps_pong_move_the_paddle_es_png__WEBPACK_IMPORTED_MODULE_83__ = __webpack_require__(/*! ./steps/pong-move-the-paddle.es.png */ "./src/lib/libraries/decks/steps/pong-move-the-paddle.es.png");
/* harmony import */ var _steps_pong_select_ball_LTR_png__WEBPACK_IMPORTED_MODULE_84__ = __webpack_require__(/*! ./steps/pong-select-ball.LTR.png */ "./src/lib/libraries/decks/steps/pong-select-ball.LTR.png");
/* harmony import */ var _steps_pong_add_code_to_ball_es_png__WEBPACK_IMPORTED_MODULE_85__ = __webpack_require__(/*! ./steps/pong-add-code-to-ball.es.png */ "./src/lib/libraries/decks/steps/pong-add-code-to-ball.es.png");
/* harmony import */ var _steps_pong_choose_score_es_png__WEBPACK_IMPORTED_MODULE_86__ = __webpack_require__(/*! ./steps/pong-choose-score.es.png */ "./src/lib/libraries/decks/steps/pong-choose-score.es.png");
/* harmony import */ var _steps_pong_insert_change_score_es_png__WEBPACK_IMPORTED_MODULE_87__ = __webpack_require__(/*! ./steps/pong-insert-change-score.es.png */ "./src/lib/libraries/decks/steps/pong-insert-change-score.es.png");
/* harmony import */ var _steps_pong_reset_score_es_png__WEBPACK_IMPORTED_MODULE_88__ = __webpack_require__(/*! ./steps/pong-reset-score.es.png */ "./src/lib/libraries/decks/steps/pong-reset-score.es.png");
/* harmony import */ var _steps_pong_add_line_LTR_gif__WEBPACK_IMPORTED_MODULE_89__ = __webpack_require__(/*! ./steps/pong-add-line.LTR.gif */ "./src/lib/libraries/decks/steps/pong-add-line.LTR.gif");
/* harmony import */ var _steps_pong_game_over_es_png__WEBPACK_IMPORTED_MODULE_90__ = __webpack_require__(/*! ./steps/pong-game-over.es.png */ "./src/lib/libraries/decks/steps/pong-game-over.es.png");
/* harmony import */ var _steps_imagine_type_what_you_want_es_png__WEBPACK_IMPORTED_MODULE_91__ = __webpack_require__(/*! ./steps/imagine-type-what-you-want.es.png */ "./src/lib/libraries/decks/steps/imagine-type-what-you-want.es.png");
/* harmony import */ var _steps_imagine_click_green_flag_es_png__WEBPACK_IMPORTED_MODULE_92__ = __webpack_require__(/*! ./steps/imagine-click-green-flag.es.png */ "./src/lib/libraries/decks/steps/imagine-click-green-flag.es.png");
/* harmony import */ var _steps_imagine_choose_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_93__ = __webpack_require__(/*! ./steps/imagine-choose-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/imagine-choose-backdrop.LTR.png");
/* harmony import */ var _steps_imagine_choose_any_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_94__ = __webpack_require__(/*! ./steps/imagine-choose-any-sprite.LTR.png */ "./src/lib/libraries/decks/steps/imagine-choose-any-sprite.LTR.png");
/* harmony import */ var _steps_imagine_fly_around_es_png__WEBPACK_IMPORTED_MODULE_95__ = __webpack_require__(/*! ./steps/imagine-fly-around.es.png */ "./src/lib/libraries/decks/steps/imagine-fly-around.es.png");
/* harmony import */ var _steps_imagine_choose_another_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_96__ = __webpack_require__(/*! ./steps/imagine-choose-another-sprite.LTR.png */ "./src/lib/libraries/decks/steps/imagine-choose-another-sprite.LTR.png");
/* harmony import */ var _steps_imagine_left_right_es_png__WEBPACK_IMPORTED_MODULE_97__ = __webpack_require__(/*! ./steps/imagine-left-right.es.png */ "./src/lib/libraries/decks/steps/imagine-left-right.es.png");
/* harmony import */ var _steps_imagine_up_down_es_png__WEBPACK_IMPORTED_MODULE_98__ = __webpack_require__(/*! ./steps/imagine-up-down.es.png */ "./src/lib/libraries/decks/steps/imagine-up-down.es.png");
/* harmony import */ var _steps_imagine_change_costumes_es_png__WEBPACK_IMPORTED_MODULE_99__ = __webpack_require__(/*! ./steps/imagine-change-costumes.es.png */ "./src/lib/libraries/decks/steps/imagine-change-costumes.es.png");
/* harmony import */ var _steps_imagine_glide_to_point_es_png__WEBPACK_IMPORTED_MODULE_100__ = __webpack_require__(/*! ./steps/imagine-glide-to-point.es.png */ "./src/lib/libraries/decks/steps/imagine-glide-to-point.es.png");
/* harmony import */ var _steps_imagine_grow_shrink_es_png__WEBPACK_IMPORTED_MODULE_101__ = __webpack_require__(/*! ./steps/imagine-grow-shrink.es.png */ "./src/lib/libraries/decks/steps/imagine-grow-shrink.es.png");
/* harmony import */ var _steps_imagine_choose_another_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_102__ = __webpack_require__(/*! ./steps/imagine-choose-another-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/imagine-choose-another-backdrop.LTR.png");
/* harmony import */ var _steps_imagine_switch_backdrops_es_png__WEBPACK_IMPORTED_MODULE_103__ = __webpack_require__(/*! ./steps/imagine-switch-backdrops.es.png */ "./src/lib/libraries/decks/steps/imagine-switch-backdrops.es.png");
/* harmony import */ var _steps_imagine_record_a_sound_es_gif__WEBPACK_IMPORTED_MODULE_104__ = __webpack_require__(/*! ./steps/imagine-record-a-sound.es.gif */ "./src/lib/libraries/decks/steps/imagine-record-a-sound.es.gif");
/* harmony import */ var _steps_imagine_choose_sound_es_png__WEBPACK_IMPORTED_MODULE_105__ = __webpack_require__(/*! ./steps/imagine-choose-sound.es.png */ "./src/lib/libraries/decks/steps/imagine-choose-sound.es.png");
/* harmony import */ var _steps_add_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_106__ = __webpack_require__(/*! ./steps/add-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/add-backdrop.LTR.png");
/* harmony import */ var _steps_add_effects_es_png__WEBPACK_IMPORTED_MODULE_107__ = __webpack_require__(/*! ./steps/add-effects.es.png */ "./src/lib/libraries/decks/steps/add-effects.es.png");
/* harmony import */ var _steps_hide_show_es_png__WEBPACK_IMPORTED_MODULE_108__ = __webpack_require__(/*! ./steps/hide-show.es.png */ "./src/lib/libraries/decks/steps/hide-show.es.png");
/* harmony import */ var _steps_switch_costumes_es_png__WEBPACK_IMPORTED_MODULE_109__ = __webpack_require__(/*! ./steps/switch-costumes.es.png */ "./src/lib/libraries/decks/steps/switch-costumes.es.png");
/* harmony import */ var _steps_change_size_es_png__WEBPACK_IMPORTED_MODULE_110__ = __webpack_require__(/*! ./steps/change-size.es.png */ "./src/lib/libraries/decks/steps/change-size.es.png");
/* harmony import */ var _steps_spin_turn_es_png__WEBPACK_IMPORTED_MODULE_111__ = __webpack_require__(/*! ./steps/spin-turn.es.png */ "./src/lib/libraries/decks/steps/spin-turn.es.png");
/* harmony import */ var _steps_spin_point_in_direction_es_png__WEBPACK_IMPORTED_MODULE_112__ = __webpack_require__(/*! ./steps/spin-point-in-direction.es.png */ "./src/lib/libraries/decks/steps/spin-point-in-direction.es.png");
/* harmony import */ var _steps_record_a_sound_sounds_tab_es_png__WEBPACK_IMPORTED_MODULE_113__ = __webpack_require__(/*! ./steps/record-a-sound-sounds-tab.es.png */ "./src/lib/libraries/decks/steps/record-a-sound-sounds-tab.es.png");
/* harmony import */ var _steps_record_a_sound_click_record_es_png__WEBPACK_IMPORTED_MODULE_114__ = __webpack_require__(/*! ./steps/record-a-sound-click-record.es.png */ "./src/lib/libraries/decks/steps/record-a-sound-click-record.es.png");
/* harmony import */ var _steps_record_a_sound_press_record_button_es_png__WEBPACK_IMPORTED_MODULE_115__ = __webpack_require__(/*! ./steps/record-a-sound-press-record-button.es.png */ "./src/lib/libraries/decks/steps/record-a-sound-press-record-button.es.png");
/* harmony import */ var _steps_record_a_sound_choose_sound_es_png__WEBPACK_IMPORTED_MODULE_116__ = __webpack_require__(/*! ./steps/record-a-sound-choose-sound.es.png */ "./src/lib/libraries/decks/steps/record-a-sound-choose-sound.es.png");
/* harmony import */ var _steps_record_a_sound_play_your_sound_es_png__WEBPACK_IMPORTED_MODULE_117__ = __webpack_require__(/*! ./steps/record-a-sound-play-your-sound.es.png */ "./src/lib/libraries/decks/steps/record-a-sound-play-your-sound.es.png");
/* harmony import */ var _steps_move_arrow_keys_left_right_es_png__WEBPACK_IMPORTED_MODULE_118__ = __webpack_require__(/*! ./steps/move-arrow-keys-left-right.es.png */ "./src/lib/libraries/decks/steps/move-arrow-keys-left-right.es.png");
/* harmony import */ var _steps_move_arrow_keys_up_down_es_png__WEBPACK_IMPORTED_MODULE_119__ = __webpack_require__(/*! ./steps/move-arrow-keys-up-down.es.png */ "./src/lib/libraries/decks/steps/move-arrow-keys-up-down.es.png");
/* harmony import */ var _steps_glide_around_back_and_forth_es_png__WEBPACK_IMPORTED_MODULE_120__ = __webpack_require__(/*! ./steps/glide-around-back-and-forth.es.png */ "./src/lib/libraries/decks/steps/glide-around-back-and-forth.es.png");
/* harmony import */ var _steps_glide_around_point_es_png__WEBPACK_IMPORTED_MODULE_121__ = __webpack_require__(/*! ./steps/glide-around-point.es.png */ "./src/lib/libraries/decks/steps/glide-around-point.es.png");
/* harmony import */ var _steps_code_cartoon_01_say_something_es_png__WEBPACK_IMPORTED_MODULE_122__ = __webpack_require__(/*! ./steps/code-cartoon-01-say-something.es.png */ "./src/lib/libraries/decks/steps/code-cartoon-01-say-something.es.png");
/* harmony import */ var _steps_code_cartoon_02_animate_es_png__WEBPACK_IMPORTED_MODULE_123__ = __webpack_require__(/*! ./steps/code-cartoon-02-animate.es.png */ "./src/lib/libraries/decks/steps/code-cartoon-02-animate.es.png");
/* harmony import */ var _steps_code_cartoon_03_select_different_character_LTR_png__WEBPACK_IMPORTED_MODULE_124__ = __webpack_require__(/*! ./steps/code-cartoon-03-select-different-character.LTR.png */ "./src/lib/libraries/decks/steps/code-cartoon-03-select-different-character.LTR.png");
/* harmony import */ var _steps_code_cartoon_04_use_minus_sign_es_png__WEBPACK_IMPORTED_MODULE_125__ = __webpack_require__(/*! ./steps/code-cartoon-04-use-minus-sign.es.png */ "./src/lib/libraries/decks/steps/code-cartoon-04-use-minus-sign.es.png");
/* harmony import */ var _steps_code_cartoon_05_grow_shrink_es_png__WEBPACK_IMPORTED_MODULE_126__ = __webpack_require__(/*! ./steps/code-cartoon-05-grow-shrink.es.png */ "./src/lib/libraries/decks/steps/code-cartoon-05-grow-shrink.es.png");
/* harmony import */ var _steps_code_cartoon_06_select_another_different_character_LTR_png__WEBPACK_IMPORTED_MODULE_127__ = __webpack_require__(/*! ./steps/code-cartoon-06-select-another-different-character.LTR.png */ "./src/lib/libraries/decks/steps/code-cartoon-06-select-another-different-character.LTR.png");
/* harmony import */ var _steps_code_cartoon_07_jump_es_png__WEBPACK_IMPORTED_MODULE_128__ = __webpack_require__(/*! ./steps/code-cartoon-07-jump.es.png */ "./src/lib/libraries/decks/steps/code-cartoon-07-jump.es.png");
/* harmony import */ var _steps_code_cartoon_08_change_scenes_es_png__WEBPACK_IMPORTED_MODULE_129__ = __webpack_require__(/*! ./steps/code-cartoon-08-change-scenes.es.png */ "./src/lib/libraries/decks/steps/code-cartoon-08-change-scenes.es.png");
/* harmony import */ var _steps_code_cartoon_09_glide_around_es_png__WEBPACK_IMPORTED_MODULE_130__ = __webpack_require__(/*! ./steps/code-cartoon-09-glide-around.es.png */ "./src/lib/libraries/decks/steps/code-cartoon-09-glide-around.es.png");
/* harmony import */ var _steps_code_cartoon_10_change_costumes_es_png__WEBPACK_IMPORTED_MODULE_131__ = __webpack_require__(/*! ./steps/code-cartoon-10-change-costumes.es.png */ "./src/lib/libraries/decks/steps/code-cartoon-10-change-costumes.es.png");
/* harmony import */ var _steps_code_cartoon_11_choose_more_characters_LTR_png__WEBPACK_IMPORTED_MODULE_132__ = __webpack_require__(/*! ./steps/code-cartoon-11-choose-more-characters.LTR.png */ "./src/lib/libraries/decks/steps/code-cartoon-11-choose-more-characters.LTR.png");
/* harmony import */ var _steps_talking_2_choose_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_133__ = __webpack_require__(/*! ./steps/talking-2-choose-sprite.LTR.png */ "./src/lib/libraries/decks/steps/talking-2-choose-sprite.LTR.png");
/* harmony import */ var _steps_talking_3_say_something_es_png__WEBPACK_IMPORTED_MODULE_134__ = __webpack_require__(/*! ./steps/talking-3-say-something.es.png */ "./src/lib/libraries/decks/steps/talking-3-say-something.es.png");
/* harmony import */ var _steps_talking_4_choose_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_135__ = __webpack_require__(/*! ./steps/talking-4-choose-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/talking-4-choose-backdrop.LTR.png");
/* harmony import */ var _steps_talking_5_switch_backdrop_es_png__WEBPACK_IMPORTED_MODULE_136__ = __webpack_require__(/*! ./steps/talking-5-switch-backdrop.es.png */ "./src/lib/libraries/decks/steps/talking-5-switch-backdrop.es.png");
/* harmony import */ var _steps_talking_6_choose_another_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_137__ = __webpack_require__(/*! ./steps/talking-6-choose-another-sprite.LTR.png */ "./src/lib/libraries/decks/steps/talking-6-choose-another-sprite.LTR.png");
/* harmony import */ var _steps_talking_7_move_around_es_png__WEBPACK_IMPORTED_MODULE_138__ = __webpack_require__(/*! ./steps/talking-7-move-around.es.png */ "./src/lib/libraries/decks/steps/talking-7-move-around.es.png");
/* harmony import */ var _steps_talking_8_choose_another_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_139__ = __webpack_require__(/*! ./steps/talking-8-choose-another-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/talking-8-choose-another-backdrop.LTR.png");
/* harmony import */ var _steps_talking_9_animate_es_png__WEBPACK_IMPORTED_MODULE_140__ = __webpack_require__(/*! ./steps/talking-9-animate.es.png */ "./src/lib/libraries/decks/steps/talking-9-animate.es.png");
/* harmony import */ var _steps_talking_10_choose_third_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_141__ = __webpack_require__(/*! ./steps/talking-10-choose-third-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/talking-10-choose-third-backdrop.LTR.png");
/* harmony import */ var _steps_talking_11_choose_sound_es_gif__WEBPACK_IMPORTED_MODULE_142__ = __webpack_require__(/*! ./steps/talking-11-choose-sound.es.gif */ "./src/lib/libraries/decks/steps/talking-11-choose-sound.es.gif");
/* harmony import */ var _steps_talking_12_dance_moves_es_png__WEBPACK_IMPORTED_MODULE_143__ = __webpack_require__(/*! ./steps/talking-12-dance-moves.es.png */ "./src/lib/libraries/decks/steps/talking-12-dance-moves.es.png");
/* harmony import */ var _steps_talking_13_ask_and_answer_es_png__WEBPACK_IMPORTED_MODULE_144__ = __webpack_require__(/*! ./steps/talking-13-ask-and-answer.es.png */ "./src/lib/libraries/decks/steps/talking-13-ask-and-answer.es.png");
// Intro


 // Text to Speech










 // Cartoon Network








 // Add sprite

 // Animate a name






 // Make Music





 // Chase-Game









 // Clicker-Game (Pop Game)







 // Animate A Character








 // Tell A Story










 // Video Sensing




 // Make it Fly












 // Pong













 // Imagine a World















 // Add a Backdrop

 // Add Effects

 // Hide and Show

 // Switch Costumes

 // Change Size

 // Spin


 // Record a Sound





 // Use Arrow Keys


 // Glide Around


 // Code a Cartoon











 // Talking Tales














var esImages = {
  // Intro
  introMove: _steps_intro_1_move_es_gif__WEBPACK_IMPORTED_MODULE_0__["default"],
  introSay: _steps_intro_2_say_es_gif__WEBPACK_IMPORTED_MODULE_1__["default"],
  introGreenFlag: _steps_intro_3_green_flag_es_gif__WEBPACK_IMPORTED_MODULE_2__["default"],
  // Text to Speech
  speechAddExtension: _steps_speech_add_extension_es_gif__WEBPACK_IMPORTED_MODULE_3__["default"],
  speechSaySomething: _steps_speech_say_something_es_png__WEBPACK_IMPORTED_MODULE_4__["default"],
  speechSetVoice: _steps_speech_set_voice_es_png__WEBPACK_IMPORTED_MODULE_5__["default"],
  speechMoveAround: _steps_speech_move_around_es_png__WEBPACK_IMPORTED_MODULE_6__["default"],
  speechAddBackdrop: _steps_pick_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_7__["default"],
  speechAddSprite: _steps_speech_add_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_8__["default"],
  speechSong: _steps_speech_song_es_png__WEBPACK_IMPORTED_MODULE_9__["default"],
  speechChangeColor: _steps_speech_change_color_es_png__WEBPACK_IMPORTED_MODULE_10__["default"],
  speechSpin: _steps_speech_spin_es_png__WEBPACK_IMPORTED_MODULE_11__["default"],
  speechGrowShrink: _steps_speech_grow_shrink_es_png__WEBPACK_IMPORTED_MODULE_12__["default"],
  // Cartoon Network
  cnShowCharacter: _steps_cn_show_character_LTR_gif__WEBPACK_IMPORTED_MODULE_13__["default"],
  cnSay: _steps_cn_say_es_png__WEBPACK_IMPORTED_MODULE_14__["default"],
  cnGlide: _steps_cn_glide_es_png__WEBPACK_IMPORTED_MODULE_15__["default"],
  cnPickSprite: _steps_cn_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_16__["default"],
  cnCollect: _steps_cn_collect_es_png__WEBPACK_IMPORTED_MODULE_17__["default"],
  cnVariable: _steps_add_variable_es_gif__WEBPACK_IMPORTED_MODULE_18__["default"],
  cnScore: _steps_cn_score_es_png__WEBPACK_IMPORTED_MODULE_19__["default"],
  cnBackdrop: _steps_cn_backdrop_es_png__WEBPACK_IMPORTED_MODULE_20__["default"],
  // Add sprite
  addSprite: _steps_add_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_21__["default"],
  // Animate a name
  namePickLetter: _steps_name_pick_letter_LTR_gif__WEBPACK_IMPORTED_MODULE_22__["default"],
  namePlaySound: _steps_name_play_sound_es_png__WEBPACK_IMPORTED_MODULE_23__["default"],
  namePickLetter2: _steps_name_pick_letter2_LTR_gif__WEBPACK_IMPORTED_MODULE_24__["default"],
  nameChangeColor: _steps_name_change_color_es_png__WEBPACK_IMPORTED_MODULE_25__["default"],
  nameSpin: _steps_name_spin_es_png__WEBPACK_IMPORTED_MODULE_26__["default"],
  nameGrow: _steps_name_grow_es_png__WEBPACK_IMPORTED_MODULE_27__["default"],
  // Make-Music
  musicPickInstrument: _steps_music_pick_instrument_LTR_gif__WEBPACK_IMPORTED_MODULE_28__["default"],
  musicPlaySound: _steps_music_play_sound_es_png__WEBPACK_IMPORTED_MODULE_29__["default"],
  musicMakeSong: _steps_music_make_song_es_png__WEBPACK_IMPORTED_MODULE_30__["default"],
  musicMakeBeat: _steps_music_make_beat_es_png__WEBPACK_IMPORTED_MODULE_31__["default"],
  musicMakeBeatbox: _steps_music_make_beatbox_es_png__WEBPACK_IMPORTED_MODULE_32__["default"],
  // Chase-Game
  chaseGameAddBackdrop: _steps_chase_game_add_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_33__["default"],
  chaseGameAddSprite1: _steps_chase_game_add_sprite1_LTR_gif__WEBPACK_IMPORTED_MODULE_34__["default"],
  chaseGameRightLeft: _steps_chase_game_right_left_es_png__WEBPACK_IMPORTED_MODULE_35__["default"],
  chaseGameUpDown: _steps_chase_game_up_down_es_png__WEBPACK_IMPORTED_MODULE_36__["default"],
  chaseGameAddSprite2: _steps_chase_game_add_sprite2_LTR_gif__WEBPACK_IMPORTED_MODULE_37__["default"],
  chaseGameMoveRandomly: _steps_chase_game_move_randomly_es_png__WEBPACK_IMPORTED_MODULE_38__["default"],
  chaseGamePlaySound: _steps_chase_game_play_sound_es_png__WEBPACK_IMPORTED_MODULE_39__["default"],
  chaseGameAddVariable: _steps_add_variable_es_gif__WEBPACK_IMPORTED_MODULE_18__["default"],
  chaseGameChangeScore: _steps_chase_game_change_score_es_png__WEBPACK_IMPORTED_MODULE_40__["default"],
  // Make-A-Pop/Clicker Game
  popGamePickSprite: _steps_pop_game_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_41__["default"],
  popGamePlaySound: _steps_pop_game_play_sound_es_png__WEBPACK_IMPORTED_MODULE_42__["default"],
  popGameAddScore: _steps_add_variable_es_gif__WEBPACK_IMPORTED_MODULE_18__["default"],
  popGameChangeScore: _steps_pop_game_change_score_es_png__WEBPACK_IMPORTED_MODULE_43__["default"],
  popGameRandomPosition: _steps_pop_game_random_position_es_png__WEBPACK_IMPORTED_MODULE_44__["default"],
  popGameChangeColor: _steps_pop_game_change_color_es_png__WEBPACK_IMPORTED_MODULE_45__["default"],
  popGameResetScore: _steps_pop_game_reset_score_es_png__WEBPACK_IMPORTED_MODULE_46__["default"],
  // Animate A Character
  animateCharPickBackdrop: _steps_pick_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_7__["default"],
  animateCharPickSprite: _steps_animate_char_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_47__["default"],
  animateCharSaySomething: _steps_animate_char_say_something_es_png__WEBPACK_IMPORTED_MODULE_48__["default"],
  animateCharAddSound: _steps_animate_char_add_sound_es_png__WEBPACK_IMPORTED_MODULE_49__["default"],
  animateCharTalk: _steps_animate_char_talk_es_png__WEBPACK_IMPORTED_MODULE_50__["default"],
  animateCharMove: _steps_animate_char_move_es_png__WEBPACK_IMPORTED_MODULE_51__["default"],
  animateCharJump: _steps_animate_char_jump_es_png__WEBPACK_IMPORTED_MODULE_52__["default"],
  animateCharChangeColor: _steps_animate_char_change_color_es_png__WEBPACK_IMPORTED_MODULE_53__["default"],
  // Tell A Story
  storyPickBackdrop: _steps_story_pick_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_54__["default"],
  storyPickSprite: _steps_story_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_55__["default"],
  storySaySomething: _steps_story_say_something_es_png__WEBPACK_IMPORTED_MODULE_56__["default"],
  storyPickSprite2: _steps_story_pick_sprite2_LTR_gif__WEBPACK_IMPORTED_MODULE_57__["default"],
  storyFlip: _steps_story_flip_es_gif__WEBPACK_IMPORTED_MODULE_58__["default"],
  storyConversation: _steps_story_conversation_es_png__WEBPACK_IMPORTED_MODULE_59__["default"],
  storyPickBackdrop2: _steps_story_pick_backdrop2_LTR_gif__WEBPACK_IMPORTED_MODULE_60__["default"],
  storySwitchBackdrop: _steps_story_switch_backdrop_es_png__WEBPACK_IMPORTED_MODULE_61__["default"],
  storyHideCharacter: _steps_story_hide_character_es_png__WEBPACK_IMPORTED_MODULE_62__["default"],
  storyShowCharacter: _steps_story_show_character_es_png__WEBPACK_IMPORTED_MODULE_63__["default"],
  // Video Sensing
  videoAddExtension: _steps_video_add_extension_es_gif__WEBPACK_IMPORTED_MODULE_64__["default"],
  videoPet: _steps_video_pet_es_png__WEBPACK_IMPORTED_MODULE_65__["default"],
  videoAnimate: _steps_video_animate_es_png__WEBPACK_IMPORTED_MODULE_66__["default"],
  videoPop: _steps_video_pop_es_png__WEBPACK_IMPORTED_MODULE_67__["default"],
  // Make it Fly
  flyChooseBackdrop: _steps_fly_choose_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_68__["default"],
  flyChooseCharacter: _steps_fly_choose_character_LTR_png__WEBPACK_IMPORTED_MODULE_69__["default"],
  flySaySomething: _steps_fly_say_something_es_png__WEBPACK_IMPORTED_MODULE_70__["default"],
  flyMoveArrows: _steps_fly_make_interactive_es_png__WEBPACK_IMPORTED_MODULE_71__["default"],
  flyChooseObject: _steps_fly_object_to_collect_LTR_png__WEBPACK_IMPORTED_MODULE_72__["default"],
  flyFlyingObject: _steps_fly_flying_heart_es_png__WEBPACK_IMPORTED_MODULE_73__["default"],
  flySelectFlyingSprite: _steps_fly_select_flyer_LTR_png__WEBPACK_IMPORTED_MODULE_74__["default"],
  flyAddScore: _steps_add_variable_es_gif__WEBPACK_IMPORTED_MODULE_18__["default"],
  flyKeepScore: _steps_fly_keep_score_es_png__WEBPACK_IMPORTED_MODULE_75__["default"],
  flyAddScenery: _steps_fly_choose_scenery_LTR_gif__WEBPACK_IMPORTED_MODULE_76__["default"],
  flyMoveScenery: _steps_fly_move_scenery_es_png__WEBPACK_IMPORTED_MODULE_77__["default"],
  flySwitchLooks: _steps_fly_switch_costume_es_png__WEBPACK_IMPORTED_MODULE_78__["default"],
  // Pong
  pongAddBackdrop: _steps_pong_add_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_79__["default"],
  pongAddBallSprite: _steps_pong_add_ball_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_80__["default"],
  pongBounceAround: _steps_pong_bounce_around_es_png__WEBPACK_IMPORTED_MODULE_81__["default"],
  pongAddPaddle: _steps_pong_add_a_paddle_LTR_gif__WEBPACK_IMPORTED_MODULE_82__["default"],
  pongMoveThePaddle: _steps_pong_move_the_paddle_es_png__WEBPACK_IMPORTED_MODULE_83__["default"],
  pongSelectBallSprite: _steps_pong_select_ball_LTR_png__WEBPACK_IMPORTED_MODULE_84__["default"],
  pongAddMoreCodeToBall: _steps_pong_add_code_to_ball_es_png__WEBPACK_IMPORTED_MODULE_85__["default"],
  pongAddAScore: _steps_add_variable_es_gif__WEBPACK_IMPORTED_MODULE_18__["default"],
  pongChooseScoreFromMenu: _steps_pong_choose_score_es_png__WEBPACK_IMPORTED_MODULE_86__["default"],
  pongInsertChangeScoreBlock: _steps_pong_insert_change_score_es_png__WEBPACK_IMPORTED_MODULE_87__["default"],
  pongResetScore: _steps_pong_reset_score_es_png__WEBPACK_IMPORTED_MODULE_88__["default"],
  pongAddLineSprite: _steps_pong_add_line_LTR_gif__WEBPACK_IMPORTED_MODULE_89__["default"],
  pongGameOver: _steps_pong_game_over_es_png__WEBPACK_IMPORTED_MODULE_90__["default"],
  // Imagine a World
  imagineTypeWhatYouWant: _steps_imagine_type_what_you_want_es_png__WEBPACK_IMPORTED_MODULE_91__["default"],
  imagineClickGreenFlag: _steps_imagine_click_green_flag_es_png__WEBPACK_IMPORTED_MODULE_92__["default"],
  imagineChooseBackdrop: _steps_imagine_choose_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_93__["default"],
  imagineChooseSprite: _steps_imagine_choose_any_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_94__["default"],
  imagineFlyAround: _steps_imagine_fly_around_es_png__WEBPACK_IMPORTED_MODULE_95__["default"],
  imagineChooseAnotherSprite: _steps_imagine_choose_another_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_96__["default"],
  imagineLeftRight: _steps_imagine_left_right_es_png__WEBPACK_IMPORTED_MODULE_97__["default"],
  imagineUpDown: _steps_imagine_up_down_es_png__WEBPACK_IMPORTED_MODULE_98__["default"],
  imagineChangeCostumes: _steps_imagine_change_costumes_es_png__WEBPACK_IMPORTED_MODULE_99__["default"],
  imagineGlideToPoint: _steps_imagine_glide_to_point_es_png__WEBPACK_IMPORTED_MODULE_100__["default"],
  imagineGrowShrink: _steps_imagine_grow_shrink_es_png__WEBPACK_IMPORTED_MODULE_101__["default"],
  imagineChooseAnotherBackdrop: _steps_imagine_choose_another_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_102__["default"],
  imagineSwitchBackdrops: _steps_imagine_switch_backdrops_es_png__WEBPACK_IMPORTED_MODULE_103__["default"],
  imagineRecordASound: _steps_imagine_record_a_sound_es_gif__WEBPACK_IMPORTED_MODULE_104__["default"],
  imagineChooseSound: _steps_imagine_choose_sound_es_png__WEBPACK_IMPORTED_MODULE_105__["default"],
  // Add a Backdrop
  addBackdrop: _steps_add_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_106__["default"],
  // Add Effects
  addEffects: _steps_add_effects_es_png__WEBPACK_IMPORTED_MODULE_107__["default"],
  // Hide and Show
  hideAndShow: _steps_hide_show_es_png__WEBPACK_IMPORTED_MODULE_108__["default"],
  // Switch Costumes
  switchCostumes: _steps_switch_costumes_es_png__WEBPACK_IMPORTED_MODULE_109__["default"],
  // Change Size
  changeSize: _steps_change_size_es_png__WEBPACK_IMPORTED_MODULE_110__["default"],
  // Spin
  spinTurn: _steps_spin_turn_es_png__WEBPACK_IMPORTED_MODULE_111__["default"],
  spinPointInDirection: _steps_spin_point_in_direction_es_png__WEBPACK_IMPORTED_MODULE_112__["default"],
  // Record a Sound
  recordASoundSoundsTab: _steps_record_a_sound_sounds_tab_es_png__WEBPACK_IMPORTED_MODULE_113__["default"],
  recordASoundClickRecord: _steps_record_a_sound_click_record_es_png__WEBPACK_IMPORTED_MODULE_114__["default"],
  recordASoundPressRecordButton: _steps_record_a_sound_press_record_button_es_png__WEBPACK_IMPORTED_MODULE_115__["default"],
  recordASoundChooseSound: _steps_record_a_sound_choose_sound_es_png__WEBPACK_IMPORTED_MODULE_116__["default"],
  recordASoundPlayYourSound: _steps_record_a_sound_play_your_sound_es_png__WEBPACK_IMPORTED_MODULE_117__["default"],
  // Use Arrow Keys
  moveArrowKeysLeftRight: _steps_move_arrow_keys_left_right_es_png__WEBPACK_IMPORTED_MODULE_118__["default"],
  moveArrowKeysUpDown: _steps_move_arrow_keys_up_down_es_png__WEBPACK_IMPORTED_MODULE_119__["default"],
  // Glide Around
  glideAroundBackAndForth: _steps_glide_around_back_and_forth_es_png__WEBPACK_IMPORTED_MODULE_120__["default"],
  glideAroundPoint: _steps_glide_around_point_es_png__WEBPACK_IMPORTED_MODULE_121__["default"],
  // Code a Cartoon
  codeCartoonSaySomething: _steps_code_cartoon_01_say_something_es_png__WEBPACK_IMPORTED_MODULE_122__["default"],
  codeCartoonAnimate: _steps_code_cartoon_02_animate_es_png__WEBPACK_IMPORTED_MODULE_123__["default"],
  codeCartoonSelectDifferentCharacter: _steps_code_cartoon_03_select_different_character_LTR_png__WEBPACK_IMPORTED_MODULE_124__["default"],
  codeCartoonUseMinusSign: _steps_code_cartoon_04_use_minus_sign_es_png__WEBPACK_IMPORTED_MODULE_125__["default"],
  codeCartoonGrowShrink: _steps_code_cartoon_05_grow_shrink_es_png__WEBPACK_IMPORTED_MODULE_126__["default"],
  codeCartoonSelectDifferentCharacter2: _steps_code_cartoon_06_select_another_different_character_LTR_png__WEBPACK_IMPORTED_MODULE_127__["default"],
  codeCartoonJump: _steps_code_cartoon_07_jump_es_png__WEBPACK_IMPORTED_MODULE_128__["default"],
  codeCartoonChangeScenes: _steps_code_cartoon_08_change_scenes_es_png__WEBPACK_IMPORTED_MODULE_129__["default"],
  codeCartoonGlideAround: _steps_code_cartoon_09_glide_around_es_png__WEBPACK_IMPORTED_MODULE_130__["default"],
  codeCartoonChangeCostumes: _steps_code_cartoon_10_change_costumes_es_png__WEBPACK_IMPORTED_MODULE_131__["default"],
  codeCartoonChooseMoreCharacters: _steps_code_cartoon_11_choose_more_characters_LTR_png__WEBPACK_IMPORTED_MODULE_132__["default"],
  // Talking Tales
  talesAddExtension: _steps_speech_add_extension_es_gif__WEBPACK_IMPORTED_MODULE_3__["default"],
  talesChooseSprite: _steps_talking_2_choose_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_133__["default"],
  talesSaySomething: _steps_talking_3_say_something_es_png__WEBPACK_IMPORTED_MODULE_134__["default"],
  talesAskAnswer: _steps_talking_13_ask_and_answer_es_png__WEBPACK_IMPORTED_MODULE_144__["default"],
  talesChooseBackdrop: _steps_talking_4_choose_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_135__["default"],
  talesSwitchBackdrop: _steps_talking_5_switch_backdrop_es_png__WEBPACK_IMPORTED_MODULE_136__["default"],
  talesChooseAnotherSprite: _steps_talking_6_choose_another_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_137__["default"],
  talesMoveAround: _steps_talking_7_move_around_es_png__WEBPACK_IMPORTED_MODULE_138__["default"],
  talesChooseAnotherBackdrop: _steps_talking_8_choose_another_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_139__["default"],
  talesAnimateTalking: _steps_talking_9_animate_es_png__WEBPACK_IMPORTED_MODULE_140__["default"],
  talesChooseThirdBackdrop: _steps_talking_10_choose_third_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_141__["default"],
  talesChooseSound: _steps_talking_11_choose_sound_es_gif__WEBPACK_IMPORTED_MODULE_142__["default"],
  talesDanceMoves: _steps_talking_12_dance_moves_es_png__WEBPACK_IMPORTED_MODULE_143__["default"]
};


/***/ }),

/***/ "./src/lib/libraries/decks/steps/add-effects.es.png":
/*!**********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/add-effects.es.png ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/9c26bc01562608a60630bdd2ceb11f32.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/add-variable.es.gif":
/*!***********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/add-variable.es.gif ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/8fb623c09ceb089ea8acacc2ec6c7560.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/animate-char-add-sound.es.png":
/*!*********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/animate-char-add-sound.es.png ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/4c54923789c89dce72395bef6ce514ce.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/animate-char-change-color.es.png":
/*!************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/animate-char-change-color.es.png ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/da4cb769d1503b2f7d7223f56f82fe71.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/animate-char-jump.es.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/animate-char-jump.es.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/37363439655b5cda1fcc98c8d018dc82.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/animate-char-move.es.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/animate-char-move.es.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/206b89658ebe53f886571a12d496d64e.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/animate-char-say-something.es.png":
/*!*************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/animate-char-say-something.es.png ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/38ceac5fc82ca7d2bc3c9ae6b1350e7e.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/animate-char-talk.es.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/animate-char-talk.es.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/500edd000a8cfba95a709178728f4278.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/change-size.es.png":
/*!**********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/change-size.es.png ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/2046eb5a880c1b5171bb16265b19f3f5.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/chase-game-change-score.es.png":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/chase-game-change-score.es.png ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/50f8e36eb9cfbb4376a8667b72fade55.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/chase-game-move-randomly.es.png":
/*!***********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/chase-game-move-randomly.es.png ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/4387108addb53b9f5aa6f2274432bad3.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/chase-game-play-sound.es.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/chase-game-play-sound.es.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/a5f4d9e78765a909e424de2dfb7a02de.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/chase-game-right-left.es.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/chase-game-right-left.es.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/86918d0d7571ef57c865ad010267a870.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/chase-game-up-down.es.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/chase-game-up-down.es.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/73b9a823b21e6c901f27fe2f25b9dfc5.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/cn-backdrop.es.png":
/*!**********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/cn-backdrop.es.png ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/0dfdb6c646b37d38ced2102454492287.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/cn-collect.es.png":
/*!*********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/cn-collect.es.png ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/7f418ff99191360e0cd33dad2a9e4fe9.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/cn-glide.es.png":
/*!*******************************************************!*\
  !*** ./src/lib/libraries/decks/steps/cn-glide.es.png ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/7e8c4958d571d8d370759a36aacb4caf.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/cn-say.es.png":
/*!*****************************************************!*\
  !*** ./src/lib/libraries/decks/steps/cn-say.es.png ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/99af0bbe04cf954c4875b264bc38d90f.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/cn-score.es.png":
/*!*******************************************************!*\
  !*** ./src/lib/libraries/decks/steps/cn-score.es.png ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/78ef7d8f66eb4f0d9861466b61c793db.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-01-say-something.es.png":
/*!****************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-01-say-something.es.png ***!
  \****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/a6eb9f486bcb0347cc92694b35141e8f.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-02-animate.es.png":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-02-animate.es.png ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/43cd0bc9a6d278db9e0e9cd15a373a47.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-04-use-minus-sign.es.png":
/*!*****************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-04-use-minus-sign.es.png ***!
  \*****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/55285610fac7cc6a614a8a127aff09b7.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-05-grow-shrink.es.png":
/*!**************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-05-grow-shrink.es.png ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/03baef80be53e5e780281cccb058d67a.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-07-jump.es.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-07-jump.es.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/69ec4f5798b8a8d518a14cc79ef52ada.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-08-change-scenes.es.png":
/*!****************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-08-change-scenes.es.png ***!
  \****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/1de71b8ae8e313180e83eaaa61caf2a9.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-09-glide-around.es.png":
/*!***************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-09-glide-around.es.png ***!
  \***************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/3b17e72eee56e45ce2380d7580bb9f70.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-10-change-costumes.es.png":
/*!******************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-10-change-costumes.es.png ***!
  \******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/036f04aa622d6ea76fd8cd04329edc40.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/fly-flying-heart.es.png":
/*!***************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/fly-flying-heart.es.png ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/5435fa00f34d90a507e551cab758f329.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/fly-keep-score.es.png":
/*!*************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/fly-keep-score.es.png ***!
  \*************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/f8b6111d457861c24252b9fb5d4b3621.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/fly-make-interactive.es.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/fly-make-interactive.es.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/4594c45c2d65d077d6095b8d0cb13e05.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/fly-move-scenery.es.png":
/*!***************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/fly-move-scenery.es.png ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/2f43f431ff39f0e18caff2f8a8330b1a.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/fly-say-something.es.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/fly-say-something.es.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/0bd3c9446f1562476707c2fae66b74a7.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/fly-switch-costume.es.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/fly-switch-costume.es.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/46b0393e300996e290af821c545cb0e4.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/glide-around-back-and-forth.es.png":
/*!**************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/glide-around-back-and-forth.es.png ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/b4cd6a8718c0a3569eedaef38ad8b695.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/glide-around-point.es.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/glide-around-point.es.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/a8158f6473de6bfb0c384c432c6e2214.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/hide-show.es.png":
/*!********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/hide-show.es.png ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/db5e495097b57462c1e6ae9070e535c2.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-change-costumes.es.png":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-change-costumes.es.png ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/af761cfdb27a304daaca2c88beb65217.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-choose-sound.es.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-choose-sound.es.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/5ccdf542fc6ede1f4033edf18b28a15a.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-click-green-flag.es.png":
/*!***********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-click-green-flag.es.png ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/21244ae3ad8474219ab618f96f2c661c.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-fly-around.es.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-fly-around.es.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/728002ed5c423b96771a8bde9188b89c.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-glide-to-point.es.png":
/*!*********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-glide-to-point.es.png ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/b2059ee7397ab8398c21bdccef400c28.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-grow-shrink.es.png":
/*!******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-grow-shrink.es.png ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/8152f4cdece9f0c0e0ffbaa6727ecbba.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-left-right.es.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-left-right.es.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/5862593dcedfc3fb8e34d98d26b6dcb2.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-record-a-sound.es.gif":
/*!*********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-record-a-sound.es.gif ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/2ca2bbe3cb2700c2880d9d7ed3fc3704.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-switch-backdrops.es.png":
/*!***********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-switch-backdrops.es.png ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/f0394669b7028b2c817a09ad71984576.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-type-what-you-want.es.png":
/*!*************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-type-what-you-want.es.png ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/368a71d976b8e9914e958fea15bd3f3b.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-up-down.es.png":
/*!**************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-up-down.es.png ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/3fa7ccde1e3b7bb2b4c3864bd57b0053.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/intro-1-move.es.gif":
/*!***********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/intro-1-move.es.gif ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/412b172ffa19dda8d8421f01e37d54e5.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/intro-2-say.es.gif":
/*!**********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/intro-2-say.es.gif ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/76866b5df3cfa65ec290df419e38e813.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/intro-3-green-flag.es.gif":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/intro-3-green-flag.es.gif ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/7e93964f784d75a411ade3d972f78706.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/move-arrow-keys-left-right.es.png":
/*!*************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/move-arrow-keys-left-right.es.png ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/0630ad80d5b331550638cac237840cba.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/move-arrow-keys-up-down.es.png":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/move-arrow-keys-up-down.es.png ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/521721f0ba0f2a982bd8fc6937582cad.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/music-make-beat.es.png":
/*!**************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/music-make-beat.es.png ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/ff90f96e209a9a11f628bfb50e68a3b5.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/music-make-beatbox.es.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/music-make-beatbox.es.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/4ea34670c1003140dfb2b83ec942e460.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/music-make-song.es.png":
/*!**************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/music-make-song.es.png ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/4f86cb7e898efb63e99ca344329f203b.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/music-play-sound.es.png":
/*!***************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/music-play-sound.es.png ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/bf7242e7567f7fb77c849c53f246e2ef.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/name-change-color.es.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/name-change-color.es.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/0a00e132242adbab45c6802d4f3a08b9.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/name-grow.es.png":
/*!********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/name-grow.es.png ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/2bf12e6aa555d6d855aecb7351110302.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/name-play-sound.es.png":
/*!**************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/name-play-sound.es.png ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/b2d73f971fb49501ad5daa5a334ecb94.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/name-spin.es.png":
/*!********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/name-spin.es.png ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/6e10760f698fe29a240c86ad3a0551a8.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-add-code-to-ball.es.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-add-code-to-ball.es.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/4b569f1d27f096f4e8b4650a454d5b9a.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-bounce-around.es.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-bounce-around.es.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/a42c362e1cfe30b35031ff7775a052b3.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-choose-score.es.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-choose-score.es.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/9a6edbde3ea5fd8f06f78ecc28c468c4.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-game-over.es.png":
/*!*************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-game-over.es.png ***!
  \*************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/1fb88a4c37b4ade4e78645999b4ac51a.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-insert-change-score.es.png":
/*!***********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-insert-change-score.es.png ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/5115986f817f6f28fcc2bbd9870d4e78.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-move-the-paddle.es.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-move-the-paddle.es.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/1323327ab6ee8e62ec55efc0b4cf8277.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-reset-score.es.png":
/*!***************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-reset-score.es.png ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/2acd2cadb83b496a9c56fa9e79ce3359.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pop-game-change-color.es.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pop-game-change-color.es.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/18f294ca5633bad50ee97b1b26a463f3.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pop-game-change-score.es.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pop-game-change-score.es.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/7b5c58c346e5b576eb31363c307a1434.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pop-game-play-sound.es.png":
/*!******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pop-game-play-sound.es.png ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/9e0236849554b5315ce5d1da375d5e7f.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pop-game-random-position.es.png":
/*!***********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pop-game-random-position.es.png ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/d5b45fbf35aa792cb7718e1d0cd1d001.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pop-game-reset-score.es.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pop-game-reset-score.es.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/8b0bafd1f08d0bfe3d989ec9d573f0e2.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/record-a-sound-choose-sound.es.png":
/*!**************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/record-a-sound-choose-sound.es.png ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/07e48c8a87dcb25587d7fd773027cf3f.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/record-a-sound-click-record.es.png":
/*!**************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/record-a-sound-click-record.es.png ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/111fb6088d9d7aac20ed282e323093d1.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/record-a-sound-play-your-sound.es.png":
/*!*****************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/record-a-sound-play-your-sound.es.png ***!
  \*****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/fc16c0021d2816559a0b04fc9e7c7f63.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/record-a-sound-press-record-button.es.png":
/*!*********************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/record-a-sound-press-record-button.es.png ***!
  \*********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/59f238b155c894b41179c4614219de20.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/record-a-sound-sounds-tab.es.png":
/*!************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/record-a-sound-sounds-tab.es.png ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/12ef1f87e3cb2170630640e480031fa6.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-add-extension.es.gif":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-add-extension.es.gif ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/b5b1881e70be792201d475b1ee6e31ff.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-change-color.es.png":
/*!******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-change-color.es.png ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/94019237ccd0d69948da4ecdd7a5cc00.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-grow-shrink.es.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-grow-shrink.es.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/29c9aee887e0715f54d8935873d8d098.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-move-around.es.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-move-around.es.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/2989fa196a6e6cf582622f05486517ba.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-say-something.es.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-say-something.es.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/00798a63e5bd5f2b710ada8d56f55867.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-set-voice.es.png":
/*!***************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-set-voice.es.png ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/b559ad39944ba5fb0993cc0a24d44e5e.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-song.es.png":
/*!**********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-song.es.png ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/d9de76259d61bdec5e36c7d503f1c9d9.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-spin.es.png":
/*!**********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-spin.es.png ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/8c13d09f7243a5acf162c1c3824ea98f.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/spin-point-in-direction.es.png":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/spin-point-in-direction.es.png ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/894d3077a7253848503cd6645c715de7.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/spin-turn.es.png":
/*!********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/spin-turn.es.png ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/d36b6658fc71d5e72f8c3f93baecfe19.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/story-conversation.es.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/story-conversation.es.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/7b411344a0bc023842ecd797449fdbb3.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/story-flip.es.gif":
/*!*********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/story-flip.es.gif ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/e2cb2db2adc3decfac804eb00de25436.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/story-hide-character.es.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/story-hide-character.es.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/491c33c48b0a4cdc21b660e6c4e60785.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/story-say-something.es.png":
/*!******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/story-say-something.es.png ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/42106dd53674da406c7a98a50c2bb6e9.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/story-show-character.es.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/story-show-character.es.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/548e0211d3fb3ddb5db3e51d69ce2aeb.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/story-switch-backdrop.es.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/story-switch-backdrop.es.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/a8114133c2207b50a0b01f5ecb724b60.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/switch-costumes.es.png":
/*!**************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/switch-costumes.es.png ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/d94b3c566eeb52d2079cb34c73e74096.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-11-choose-sound.es.gif":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-11-choose-sound.es.gif ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/6efa7885333ec78d6566e80b19af3b80.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-12-dance-moves.es.png":
/*!*********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-12-dance-moves.es.png ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/15bee7b18e56e66cd01237f8dfb07797.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-13-ask-and-answer.es.png":
/*!************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-13-ask-and-answer.es.png ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/b2870e6914e59ba30932d5a42cd0bfac.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-3-say-something.es.png":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-3-say-something.es.png ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/df0144781d730388235a29597b3b5cbc.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-5-switch-backdrop.es.png":
/*!************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-5-switch-backdrop.es.png ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/ac7450c84ddd5a26010ad45c49849766.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-7-move-around.es.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-7-move-around.es.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/dad02a640241a78832f96dbb21816012.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-9-animate.es.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-9-animate.es.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/12532201def2d362731889a40dbd279c.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/video-add-extension.es.gif":
/*!******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/video-add-extension.es.gif ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/c1d4179e594597b875ae83585cbe1f08.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/video-animate.es.png":
/*!************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/video-animate.es.png ***!
  \************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/dbf7f523d74c7274241ee1686b90a8c9.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/video-pet.es.png":
/*!********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/video-pet.es.png ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/8c6ef008eaf10240acb80a5ced58c280.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/video-pop.es.png":
/*!********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/video-pop.es.png ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/463a37b5c6487c88d747c698be479634.png");

/***/ })

}]);
//# sourceMappingURL=es-steps.js.map