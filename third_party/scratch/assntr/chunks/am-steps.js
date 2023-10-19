(window["webpackJsonpGUI"] = window["webpackJsonpGUI"] || []).push([["am-steps"],{

/***/ "./src/lib/libraries/decks/am-steps.js":
/*!*********************************************!*\
  !*** ./src/lib/libraries/decks/am-steps.js ***!
  \*********************************************/
/*! exports provided: amImages */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "amImages", function() { return amImages; });
/* harmony import */ var _steps_intro_1_move_am_gif__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./steps/intro-1-move.am.gif */ "./src/lib/libraries/decks/steps/intro-1-move.am.gif");
/* harmony import */ var _steps_intro_2_say_am_gif__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./steps/intro-2-say.am.gif */ "./src/lib/libraries/decks/steps/intro-2-say.am.gif");
/* harmony import */ var _steps_intro_3_green_flag_am_gif__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./steps/intro-3-green-flag.am.gif */ "./src/lib/libraries/decks/steps/intro-3-green-flag.am.gif");
/* harmony import */ var _steps_speech_add_extension_am_gif__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./steps/speech-add-extension.am.gif */ "./src/lib/libraries/decks/steps/speech-add-extension.am.gif");
/* harmony import */ var _steps_speech_say_something_am_png__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./steps/speech-say-something.am.png */ "./src/lib/libraries/decks/steps/speech-say-something.am.png");
/* harmony import */ var _steps_speech_set_voice_am_png__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./steps/speech-set-voice.am.png */ "./src/lib/libraries/decks/steps/speech-set-voice.am.png");
/* harmony import */ var _steps_speech_move_around_am_png__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./steps/speech-move-around.am.png */ "./src/lib/libraries/decks/steps/speech-move-around.am.png");
/* harmony import */ var _steps_pick_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./steps/pick-backdrop.LTR.gif */ "./src/lib/libraries/decks/steps/pick-backdrop.LTR.gif");
/* harmony import */ var _steps_speech_add_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./steps/speech-add-sprite.LTR.gif */ "./src/lib/libraries/decks/steps/speech-add-sprite.LTR.gif");
/* harmony import */ var _steps_speech_song_am_png__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./steps/speech-song.am.png */ "./src/lib/libraries/decks/steps/speech-song.am.png");
/* harmony import */ var _steps_speech_change_color_am_png__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./steps/speech-change-color.am.png */ "./src/lib/libraries/decks/steps/speech-change-color.am.png");
/* harmony import */ var _steps_speech_spin_am_png__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./steps/speech-spin.am.png */ "./src/lib/libraries/decks/steps/speech-spin.am.png");
/* harmony import */ var _steps_speech_grow_shrink_am_png__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./steps/speech-grow-shrink.am.png */ "./src/lib/libraries/decks/steps/speech-grow-shrink.am.png");
/* harmony import */ var _steps_cn_show_character_LTR_gif__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./steps/cn-show-character.LTR.gif */ "./src/lib/libraries/decks/steps/cn-show-character.LTR.gif");
/* harmony import */ var _steps_cn_say_am_png__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./steps/cn-say.am.png */ "./src/lib/libraries/decks/steps/cn-say.am.png");
/* harmony import */ var _steps_cn_glide_am_png__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./steps/cn-glide.am.png */ "./src/lib/libraries/decks/steps/cn-glide.am.png");
/* harmony import */ var _steps_cn_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./steps/cn-pick-sprite.LTR.gif */ "./src/lib/libraries/decks/steps/cn-pick-sprite.LTR.gif");
/* harmony import */ var _steps_cn_collect_am_png__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./steps/cn-collect.am.png */ "./src/lib/libraries/decks/steps/cn-collect.am.png");
/* harmony import */ var _steps_add_variable_am_gif__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./steps/add-variable.am.gif */ "./src/lib/libraries/decks/steps/add-variable.am.gif");
/* harmony import */ var _steps_cn_score_am_png__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./steps/cn-score.am.png */ "./src/lib/libraries/decks/steps/cn-score.am.png");
/* harmony import */ var _steps_cn_backdrop_am_png__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./steps/cn-backdrop.am.png */ "./src/lib/libraries/decks/steps/cn-backdrop.am.png");
/* harmony import */ var _steps_add_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ./steps/add-sprite.LTR.gif */ "./src/lib/libraries/decks/steps/add-sprite.LTR.gif");
/* harmony import */ var _steps_name_pick_letter_LTR_gif__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! ./steps/name-pick-letter.LTR.gif */ "./src/lib/libraries/decks/steps/name-pick-letter.LTR.gif");
/* harmony import */ var _steps_name_play_sound_am_png__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! ./steps/name-play-sound.am.png */ "./src/lib/libraries/decks/steps/name-play-sound.am.png");
/* harmony import */ var _steps_name_pick_letter2_LTR_gif__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! ./steps/name-pick-letter2.LTR.gif */ "./src/lib/libraries/decks/steps/name-pick-letter2.LTR.gif");
/* harmony import */ var _steps_name_change_color_am_png__WEBPACK_IMPORTED_MODULE_25__ = __webpack_require__(/*! ./steps/name-change-color.am.png */ "./src/lib/libraries/decks/steps/name-change-color.am.png");
/* harmony import */ var _steps_name_spin_am_png__WEBPACK_IMPORTED_MODULE_26__ = __webpack_require__(/*! ./steps/name-spin.am.png */ "./src/lib/libraries/decks/steps/name-spin.am.png");
/* harmony import */ var _steps_name_grow_am_png__WEBPACK_IMPORTED_MODULE_27__ = __webpack_require__(/*! ./steps/name-grow.am.png */ "./src/lib/libraries/decks/steps/name-grow.am.png");
/* harmony import */ var _steps_music_pick_instrument_LTR_gif__WEBPACK_IMPORTED_MODULE_28__ = __webpack_require__(/*! ./steps/music-pick-instrument.LTR.gif */ "./src/lib/libraries/decks/steps/music-pick-instrument.LTR.gif");
/* harmony import */ var _steps_music_play_sound_am_png__WEBPACK_IMPORTED_MODULE_29__ = __webpack_require__(/*! ./steps/music-play-sound.am.png */ "./src/lib/libraries/decks/steps/music-play-sound.am.png");
/* harmony import */ var _steps_music_make_song_am_png__WEBPACK_IMPORTED_MODULE_30__ = __webpack_require__(/*! ./steps/music-make-song.am.png */ "./src/lib/libraries/decks/steps/music-make-song.am.png");
/* harmony import */ var _steps_music_make_beat_am_png__WEBPACK_IMPORTED_MODULE_31__ = __webpack_require__(/*! ./steps/music-make-beat.am.png */ "./src/lib/libraries/decks/steps/music-make-beat.am.png");
/* harmony import */ var _steps_music_make_beatbox_am_png__WEBPACK_IMPORTED_MODULE_32__ = __webpack_require__(/*! ./steps/music-make-beatbox.am.png */ "./src/lib/libraries/decks/steps/music-make-beatbox.am.png");
/* harmony import */ var _steps_chase_game_add_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_33__ = __webpack_require__(/*! ./steps/chase-game-add-backdrop.LTR.gif */ "./src/lib/libraries/decks/steps/chase-game-add-backdrop.LTR.gif");
/* harmony import */ var _steps_chase_game_add_sprite1_LTR_gif__WEBPACK_IMPORTED_MODULE_34__ = __webpack_require__(/*! ./steps/chase-game-add-sprite1.LTR.gif */ "./src/lib/libraries/decks/steps/chase-game-add-sprite1.LTR.gif");
/* harmony import */ var _steps_chase_game_right_left_am_png__WEBPACK_IMPORTED_MODULE_35__ = __webpack_require__(/*! ./steps/chase-game-right-left.am.png */ "./src/lib/libraries/decks/steps/chase-game-right-left.am.png");
/* harmony import */ var _steps_chase_game_up_down_am_png__WEBPACK_IMPORTED_MODULE_36__ = __webpack_require__(/*! ./steps/chase-game-up-down.am.png */ "./src/lib/libraries/decks/steps/chase-game-up-down.am.png");
/* harmony import */ var _steps_chase_game_add_sprite2_LTR_gif__WEBPACK_IMPORTED_MODULE_37__ = __webpack_require__(/*! ./steps/chase-game-add-sprite2.LTR.gif */ "./src/lib/libraries/decks/steps/chase-game-add-sprite2.LTR.gif");
/* harmony import */ var _steps_chase_game_move_randomly_am_png__WEBPACK_IMPORTED_MODULE_38__ = __webpack_require__(/*! ./steps/chase-game-move-randomly.am.png */ "./src/lib/libraries/decks/steps/chase-game-move-randomly.am.png");
/* harmony import */ var _steps_chase_game_play_sound_am_png__WEBPACK_IMPORTED_MODULE_39__ = __webpack_require__(/*! ./steps/chase-game-play-sound.am.png */ "./src/lib/libraries/decks/steps/chase-game-play-sound.am.png");
/* harmony import */ var _steps_chase_game_change_score_am_png__WEBPACK_IMPORTED_MODULE_40__ = __webpack_require__(/*! ./steps/chase-game-change-score.am.png */ "./src/lib/libraries/decks/steps/chase-game-change-score.am.png");
/* harmony import */ var _steps_pop_game_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_41__ = __webpack_require__(/*! ./steps/pop-game-pick-sprite.LTR.gif */ "./src/lib/libraries/decks/steps/pop-game-pick-sprite.LTR.gif");
/* harmony import */ var _steps_pop_game_play_sound_am_png__WEBPACK_IMPORTED_MODULE_42__ = __webpack_require__(/*! ./steps/pop-game-play-sound.am.png */ "./src/lib/libraries/decks/steps/pop-game-play-sound.am.png");
/* harmony import */ var _steps_pop_game_change_score_am_png__WEBPACK_IMPORTED_MODULE_43__ = __webpack_require__(/*! ./steps/pop-game-change-score.am.png */ "./src/lib/libraries/decks/steps/pop-game-change-score.am.png");
/* harmony import */ var _steps_pop_game_random_position_am_png__WEBPACK_IMPORTED_MODULE_44__ = __webpack_require__(/*! ./steps/pop-game-random-position.am.png */ "./src/lib/libraries/decks/steps/pop-game-random-position.am.png");
/* harmony import */ var _steps_pop_game_change_color_am_png__WEBPACK_IMPORTED_MODULE_45__ = __webpack_require__(/*! ./steps/pop-game-change-color.am.png */ "./src/lib/libraries/decks/steps/pop-game-change-color.am.png");
/* harmony import */ var _steps_pop_game_reset_score_am_png__WEBPACK_IMPORTED_MODULE_46__ = __webpack_require__(/*! ./steps/pop-game-reset-score.am.png */ "./src/lib/libraries/decks/steps/pop-game-reset-score.am.png");
/* harmony import */ var _steps_animate_char_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_47__ = __webpack_require__(/*! ./steps/animate-char-pick-sprite.LTR.gif */ "./src/lib/libraries/decks/steps/animate-char-pick-sprite.LTR.gif");
/* harmony import */ var _steps_animate_char_say_something_am_png__WEBPACK_IMPORTED_MODULE_48__ = __webpack_require__(/*! ./steps/animate-char-say-something.am.png */ "./src/lib/libraries/decks/steps/animate-char-say-something.am.png");
/* harmony import */ var _steps_animate_char_add_sound_am_png__WEBPACK_IMPORTED_MODULE_49__ = __webpack_require__(/*! ./steps/animate-char-add-sound.am.png */ "./src/lib/libraries/decks/steps/animate-char-add-sound.am.png");
/* harmony import */ var _steps_animate_char_talk_am_png__WEBPACK_IMPORTED_MODULE_50__ = __webpack_require__(/*! ./steps/animate-char-talk.am.png */ "./src/lib/libraries/decks/steps/animate-char-talk.am.png");
/* harmony import */ var _steps_animate_char_move_am_png__WEBPACK_IMPORTED_MODULE_51__ = __webpack_require__(/*! ./steps/animate-char-move.am.png */ "./src/lib/libraries/decks/steps/animate-char-move.am.png");
/* harmony import */ var _steps_animate_char_jump_am_png__WEBPACK_IMPORTED_MODULE_52__ = __webpack_require__(/*! ./steps/animate-char-jump.am.png */ "./src/lib/libraries/decks/steps/animate-char-jump.am.png");
/* harmony import */ var _steps_animate_char_change_color_am_png__WEBPACK_IMPORTED_MODULE_53__ = __webpack_require__(/*! ./steps/animate-char-change-color.am.png */ "./src/lib/libraries/decks/steps/animate-char-change-color.am.png");
/* harmony import */ var _steps_story_pick_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_54__ = __webpack_require__(/*! ./steps/story-pick-backdrop.LTR.gif */ "./src/lib/libraries/decks/steps/story-pick-backdrop.LTR.gif");
/* harmony import */ var _steps_story_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_55__ = __webpack_require__(/*! ./steps/story-pick-sprite.LTR.gif */ "./src/lib/libraries/decks/steps/story-pick-sprite.LTR.gif");
/* harmony import */ var _steps_story_say_something_am_png__WEBPACK_IMPORTED_MODULE_56__ = __webpack_require__(/*! ./steps/story-say-something.am.png */ "./src/lib/libraries/decks/steps/story-say-something.am.png");
/* harmony import */ var _steps_story_pick_sprite2_LTR_gif__WEBPACK_IMPORTED_MODULE_57__ = __webpack_require__(/*! ./steps/story-pick-sprite2.LTR.gif */ "./src/lib/libraries/decks/steps/story-pick-sprite2.LTR.gif");
/* harmony import */ var _steps_story_flip_am_gif__WEBPACK_IMPORTED_MODULE_58__ = __webpack_require__(/*! ./steps/story-flip.am.gif */ "./src/lib/libraries/decks/steps/story-flip.am.gif");
/* harmony import */ var _steps_story_conversation_am_png__WEBPACK_IMPORTED_MODULE_59__ = __webpack_require__(/*! ./steps/story-conversation.am.png */ "./src/lib/libraries/decks/steps/story-conversation.am.png");
/* harmony import */ var _steps_story_pick_backdrop2_LTR_gif__WEBPACK_IMPORTED_MODULE_60__ = __webpack_require__(/*! ./steps/story-pick-backdrop2.LTR.gif */ "./src/lib/libraries/decks/steps/story-pick-backdrop2.LTR.gif");
/* harmony import */ var _steps_story_switch_backdrop_am_png__WEBPACK_IMPORTED_MODULE_61__ = __webpack_require__(/*! ./steps/story-switch-backdrop.am.png */ "./src/lib/libraries/decks/steps/story-switch-backdrop.am.png");
/* harmony import */ var _steps_story_hide_character_am_png__WEBPACK_IMPORTED_MODULE_62__ = __webpack_require__(/*! ./steps/story-hide-character.am.png */ "./src/lib/libraries/decks/steps/story-hide-character.am.png");
/* harmony import */ var _steps_story_show_character_am_png__WEBPACK_IMPORTED_MODULE_63__ = __webpack_require__(/*! ./steps/story-show-character.am.png */ "./src/lib/libraries/decks/steps/story-show-character.am.png");
/* harmony import */ var _steps_video_add_extension_am_gif__WEBPACK_IMPORTED_MODULE_64__ = __webpack_require__(/*! ./steps/video-add-extension.am.gif */ "./src/lib/libraries/decks/steps/video-add-extension.am.gif");
/* harmony import */ var _steps_video_pet_am_png__WEBPACK_IMPORTED_MODULE_65__ = __webpack_require__(/*! ./steps/video-pet.am.png */ "./src/lib/libraries/decks/steps/video-pet.am.png");
/* harmony import */ var _steps_video_animate_am_png__WEBPACK_IMPORTED_MODULE_66__ = __webpack_require__(/*! ./steps/video-animate.am.png */ "./src/lib/libraries/decks/steps/video-animate.am.png");
/* harmony import */ var _steps_video_pop_am_png__WEBPACK_IMPORTED_MODULE_67__ = __webpack_require__(/*! ./steps/video-pop.am.png */ "./src/lib/libraries/decks/steps/video-pop.am.png");
/* harmony import */ var _steps_fly_choose_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_68__ = __webpack_require__(/*! ./steps/fly-choose-backdrop.LTR.gif */ "./src/lib/libraries/decks/steps/fly-choose-backdrop.LTR.gif");
/* harmony import */ var _steps_fly_choose_character_LTR_png__WEBPACK_IMPORTED_MODULE_69__ = __webpack_require__(/*! ./steps/fly-choose-character.LTR.png */ "./src/lib/libraries/decks/steps/fly-choose-character.LTR.png");
/* harmony import */ var _steps_fly_say_something_am_png__WEBPACK_IMPORTED_MODULE_70__ = __webpack_require__(/*! ./steps/fly-say-something.am.png */ "./src/lib/libraries/decks/steps/fly-say-something.am.png");
/* harmony import */ var _steps_fly_make_interactive_am_png__WEBPACK_IMPORTED_MODULE_71__ = __webpack_require__(/*! ./steps/fly-make-interactive.am.png */ "./src/lib/libraries/decks/steps/fly-make-interactive.am.png");
/* harmony import */ var _steps_fly_object_to_collect_LTR_png__WEBPACK_IMPORTED_MODULE_72__ = __webpack_require__(/*! ./steps/fly-object-to-collect.LTR.png */ "./src/lib/libraries/decks/steps/fly-object-to-collect.LTR.png");
/* harmony import */ var _steps_fly_flying_heart_am_png__WEBPACK_IMPORTED_MODULE_73__ = __webpack_require__(/*! ./steps/fly-flying-heart.am.png */ "./src/lib/libraries/decks/steps/fly-flying-heart.am.png");
/* harmony import */ var _steps_fly_select_flyer_LTR_png__WEBPACK_IMPORTED_MODULE_74__ = __webpack_require__(/*! ./steps/fly-select-flyer.LTR.png */ "./src/lib/libraries/decks/steps/fly-select-flyer.LTR.png");
/* harmony import */ var _steps_fly_keep_score_am_png__WEBPACK_IMPORTED_MODULE_75__ = __webpack_require__(/*! ./steps/fly-keep-score.am.png */ "./src/lib/libraries/decks/steps/fly-keep-score.am.png");
/* harmony import */ var _steps_fly_choose_scenery_LTR_gif__WEBPACK_IMPORTED_MODULE_76__ = __webpack_require__(/*! ./steps/fly-choose-scenery.LTR.gif */ "./src/lib/libraries/decks/steps/fly-choose-scenery.LTR.gif");
/* harmony import */ var _steps_fly_move_scenery_am_png__WEBPACK_IMPORTED_MODULE_77__ = __webpack_require__(/*! ./steps/fly-move-scenery.am.png */ "./src/lib/libraries/decks/steps/fly-move-scenery.am.png");
/* harmony import */ var _steps_fly_switch_costume_am_png__WEBPACK_IMPORTED_MODULE_78__ = __webpack_require__(/*! ./steps/fly-switch-costume.am.png */ "./src/lib/libraries/decks/steps/fly-switch-costume.am.png");
/* harmony import */ var _steps_pong_add_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_79__ = __webpack_require__(/*! ./steps/pong-add-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/pong-add-backdrop.LTR.png");
/* harmony import */ var _steps_pong_add_ball_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_80__ = __webpack_require__(/*! ./steps/pong-add-ball-sprite.LTR.png */ "./src/lib/libraries/decks/steps/pong-add-ball-sprite.LTR.png");
/* harmony import */ var _steps_pong_bounce_around_am_png__WEBPACK_IMPORTED_MODULE_81__ = __webpack_require__(/*! ./steps/pong-bounce-around.am.png */ "./src/lib/libraries/decks/steps/pong-bounce-around.am.png");
/* harmony import */ var _steps_pong_add_a_paddle_LTR_gif__WEBPACK_IMPORTED_MODULE_82__ = __webpack_require__(/*! ./steps/pong-add-a-paddle.LTR.gif */ "./src/lib/libraries/decks/steps/pong-add-a-paddle.LTR.gif");
/* harmony import */ var _steps_pong_move_the_paddle_am_png__WEBPACK_IMPORTED_MODULE_83__ = __webpack_require__(/*! ./steps/pong-move-the-paddle.am.png */ "./src/lib/libraries/decks/steps/pong-move-the-paddle.am.png");
/* harmony import */ var _steps_pong_select_ball_LTR_png__WEBPACK_IMPORTED_MODULE_84__ = __webpack_require__(/*! ./steps/pong-select-ball.LTR.png */ "./src/lib/libraries/decks/steps/pong-select-ball.LTR.png");
/* harmony import */ var _steps_pong_add_code_to_ball_am_png__WEBPACK_IMPORTED_MODULE_85__ = __webpack_require__(/*! ./steps/pong-add-code-to-ball.am.png */ "./src/lib/libraries/decks/steps/pong-add-code-to-ball.am.png");
/* harmony import */ var _steps_pong_choose_score_am_png__WEBPACK_IMPORTED_MODULE_86__ = __webpack_require__(/*! ./steps/pong-choose-score.am.png */ "./src/lib/libraries/decks/steps/pong-choose-score.am.png");
/* harmony import */ var _steps_pong_insert_change_score_am_png__WEBPACK_IMPORTED_MODULE_87__ = __webpack_require__(/*! ./steps/pong-insert-change-score.am.png */ "./src/lib/libraries/decks/steps/pong-insert-change-score.am.png");
/* harmony import */ var _steps_pong_reset_score_am_png__WEBPACK_IMPORTED_MODULE_88__ = __webpack_require__(/*! ./steps/pong-reset-score.am.png */ "./src/lib/libraries/decks/steps/pong-reset-score.am.png");
/* harmony import */ var _steps_pong_add_line_LTR_gif__WEBPACK_IMPORTED_MODULE_89__ = __webpack_require__(/*! ./steps/pong-add-line.LTR.gif */ "./src/lib/libraries/decks/steps/pong-add-line.LTR.gif");
/* harmony import */ var _steps_pong_game_over_am_png__WEBPACK_IMPORTED_MODULE_90__ = __webpack_require__(/*! ./steps/pong-game-over.am.png */ "./src/lib/libraries/decks/steps/pong-game-over.am.png");
/* harmony import */ var _steps_imagine_type_what_you_want_am_png__WEBPACK_IMPORTED_MODULE_91__ = __webpack_require__(/*! ./steps/imagine-type-what-you-want.am.png */ "./src/lib/libraries/decks/steps/imagine-type-what-you-want.am.png");
/* harmony import */ var _steps_imagine_click_green_flag_am_png__WEBPACK_IMPORTED_MODULE_92__ = __webpack_require__(/*! ./steps/imagine-click-green-flag.am.png */ "./src/lib/libraries/decks/steps/imagine-click-green-flag.am.png");
/* harmony import */ var _steps_imagine_choose_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_93__ = __webpack_require__(/*! ./steps/imagine-choose-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/imagine-choose-backdrop.LTR.png");
/* harmony import */ var _steps_imagine_choose_any_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_94__ = __webpack_require__(/*! ./steps/imagine-choose-any-sprite.LTR.png */ "./src/lib/libraries/decks/steps/imagine-choose-any-sprite.LTR.png");
/* harmony import */ var _steps_imagine_fly_around_am_png__WEBPACK_IMPORTED_MODULE_95__ = __webpack_require__(/*! ./steps/imagine-fly-around.am.png */ "./src/lib/libraries/decks/steps/imagine-fly-around.am.png");
/* harmony import */ var _steps_imagine_choose_another_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_96__ = __webpack_require__(/*! ./steps/imagine-choose-another-sprite.LTR.png */ "./src/lib/libraries/decks/steps/imagine-choose-another-sprite.LTR.png");
/* harmony import */ var _steps_imagine_left_right_am_png__WEBPACK_IMPORTED_MODULE_97__ = __webpack_require__(/*! ./steps/imagine-left-right.am.png */ "./src/lib/libraries/decks/steps/imagine-left-right.am.png");
/* harmony import */ var _steps_imagine_up_down_am_png__WEBPACK_IMPORTED_MODULE_98__ = __webpack_require__(/*! ./steps/imagine-up-down.am.png */ "./src/lib/libraries/decks/steps/imagine-up-down.am.png");
/* harmony import */ var _steps_imagine_change_costumes_am_png__WEBPACK_IMPORTED_MODULE_99__ = __webpack_require__(/*! ./steps/imagine-change-costumes.am.png */ "./src/lib/libraries/decks/steps/imagine-change-costumes.am.png");
/* harmony import */ var _steps_imagine_glide_to_point_am_png__WEBPACK_IMPORTED_MODULE_100__ = __webpack_require__(/*! ./steps/imagine-glide-to-point.am.png */ "./src/lib/libraries/decks/steps/imagine-glide-to-point.am.png");
/* harmony import */ var _steps_imagine_grow_shrink_am_png__WEBPACK_IMPORTED_MODULE_101__ = __webpack_require__(/*! ./steps/imagine-grow-shrink.am.png */ "./src/lib/libraries/decks/steps/imagine-grow-shrink.am.png");
/* harmony import */ var _steps_imagine_choose_another_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_102__ = __webpack_require__(/*! ./steps/imagine-choose-another-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/imagine-choose-another-backdrop.LTR.png");
/* harmony import */ var _steps_imagine_switch_backdrops_am_png__WEBPACK_IMPORTED_MODULE_103__ = __webpack_require__(/*! ./steps/imagine-switch-backdrops.am.png */ "./src/lib/libraries/decks/steps/imagine-switch-backdrops.am.png");
/* harmony import */ var _steps_imagine_record_a_sound_am_gif__WEBPACK_IMPORTED_MODULE_104__ = __webpack_require__(/*! ./steps/imagine-record-a-sound.am.gif */ "./src/lib/libraries/decks/steps/imagine-record-a-sound.am.gif");
/* harmony import */ var _steps_imagine_choose_sound_am_png__WEBPACK_IMPORTED_MODULE_105__ = __webpack_require__(/*! ./steps/imagine-choose-sound.am.png */ "./src/lib/libraries/decks/steps/imagine-choose-sound.am.png");
/* harmony import */ var _steps_add_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_106__ = __webpack_require__(/*! ./steps/add-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/add-backdrop.LTR.png");
/* harmony import */ var _steps_add_effects_am_png__WEBPACK_IMPORTED_MODULE_107__ = __webpack_require__(/*! ./steps/add-effects.am.png */ "./src/lib/libraries/decks/steps/add-effects.am.png");
/* harmony import */ var _steps_hide_show_am_png__WEBPACK_IMPORTED_MODULE_108__ = __webpack_require__(/*! ./steps/hide-show.am.png */ "./src/lib/libraries/decks/steps/hide-show.am.png");
/* harmony import */ var _steps_switch_costumes_am_png__WEBPACK_IMPORTED_MODULE_109__ = __webpack_require__(/*! ./steps/switch-costumes.am.png */ "./src/lib/libraries/decks/steps/switch-costumes.am.png");
/* harmony import */ var _steps_change_size_am_png__WEBPACK_IMPORTED_MODULE_110__ = __webpack_require__(/*! ./steps/change-size.am.png */ "./src/lib/libraries/decks/steps/change-size.am.png");
/* harmony import */ var _steps_spin_turn_am_png__WEBPACK_IMPORTED_MODULE_111__ = __webpack_require__(/*! ./steps/spin-turn.am.png */ "./src/lib/libraries/decks/steps/spin-turn.am.png");
/* harmony import */ var _steps_spin_point_in_direction_am_png__WEBPACK_IMPORTED_MODULE_112__ = __webpack_require__(/*! ./steps/spin-point-in-direction.am.png */ "./src/lib/libraries/decks/steps/spin-point-in-direction.am.png");
/* harmony import */ var _steps_record_a_sound_sounds_tab_am_png__WEBPACK_IMPORTED_MODULE_113__ = __webpack_require__(/*! ./steps/record-a-sound-sounds-tab.am.png */ "./src/lib/libraries/decks/steps/record-a-sound-sounds-tab.am.png");
/* harmony import */ var _steps_record_a_sound_click_record_am_png__WEBPACK_IMPORTED_MODULE_114__ = __webpack_require__(/*! ./steps/record-a-sound-click-record.am.png */ "./src/lib/libraries/decks/steps/record-a-sound-click-record.am.png");
/* harmony import */ var _steps_record_a_sound_press_record_button_am_png__WEBPACK_IMPORTED_MODULE_115__ = __webpack_require__(/*! ./steps/record-a-sound-press-record-button.am.png */ "./src/lib/libraries/decks/steps/record-a-sound-press-record-button.am.png");
/* harmony import */ var _steps_record_a_sound_choose_sound_am_png__WEBPACK_IMPORTED_MODULE_116__ = __webpack_require__(/*! ./steps/record-a-sound-choose-sound.am.png */ "./src/lib/libraries/decks/steps/record-a-sound-choose-sound.am.png");
/* harmony import */ var _steps_record_a_sound_play_your_sound_am_png__WEBPACK_IMPORTED_MODULE_117__ = __webpack_require__(/*! ./steps/record-a-sound-play-your-sound.am.png */ "./src/lib/libraries/decks/steps/record-a-sound-play-your-sound.am.png");
/* harmony import */ var _steps_move_arrow_keys_left_right_am_png__WEBPACK_IMPORTED_MODULE_118__ = __webpack_require__(/*! ./steps/move-arrow-keys-left-right.am.png */ "./src/lib/libraries/decks/steps/move-arrow-keys-left-right.am.png");
/* harmony import */ var _steps_move_arrow_keys_up_down_am_png__WEBPACK_IMPORTED_MODULE_119__ = __webpack_require__(/*! ./steps/move-arrow-keys-up-down.am.png */ "./src/lib/libraries/decks/steps/move-arrow-keys-up-down.am.png");
/* harmony import */ var _steps_glide_around_back_and_forth_am_png__WEBPACK_IMPORTED_MODULE_120__ = __webpack_require__(/*! ./steps/glide-around-back-and-forth.am.png */ "./src/lib/libraries/decks/steps/glide-around-back-and-forth.am.png");
/* harmony import */ var _steps_glide_around_point_am_png__WEBPACK_IMPORTED_MODULE_121__ = __webpack_require__(/*! ./steps/glide-around-point.am.png */ "./src/lib/libraries/decks/steps/glide-around-point.am.png");
/* harmony import */ var _steps_code_cartoon_01_say_something_am_png__WEBPACK_IMPORTED_MODULE_122__ = __webpack_require__(/*! ./steps/code-cartoon-01-say-something.am.png */ "./src/lib/libraries/decks/steps/code-cartoon-01-say-something.am.png");
/* harmony import */ var _steps_code_cartoon_02_animate_am_png__WEBPACK_IMPORTED_MODULE_123__ = __webpack_require__(/*! ./steps/code-cartoon-02-animate.am.png */ "./src/lib/libraries/decks/steps/code-cartoon-02-animate.am.png");
/* harmony import */ var _steps_code_cartoon_03_select_different_character_LTR_png__WEBPACK_IMPORTED_MODULE_124__ = __webpack_require__(/*! ./steps/code-cartoon-03-select-different-character.LTR.png */ "./src/lib/libraries/decks/steps/code-cartoon-03-select-different-character.LTR.png");
/* harmony import */ var _steps_code_cartoon_04_use_minus_sign_am_png__WEBPACK_IMPORTED_MODULE_125__ = __webpack_require__(/*! ./steps/code-cartoon-04-use-minus-sign.am.png */ "./src/lib/libraries/decks/steps/code-cartoon-04-use-minus-sign.am.png");
/* harmony import */ var _steps_code_cartoon_05_grow_shrink_am_png__WEBPACK_IMPORTED_MODULE_126__ = __webpack_require__(/*! ./steps/code-cartoon-05-grow-shrink.am.png */ "./src/lib/libraries/decks/steps/code-cartoon-05-grow-shrink.am.png");
/* harmony import */ var _steps_code_cartoon_06_select_another_different_character_LTR_png__WEBPACK_IMPORTED_MODULE_127__ = __webpack_require__(/*! ./steps/code-cartoon-06-select-another-different-character.LTR.png */ "./src/lib/libraries/decks/steps/code-cartoon-06-select-another-different-character.LTR.png");
/* harmony import */ var _steps_code_cartoon_07_jump_am_png__WEBPACK_IMPORTED_MODULE_128__ = __webpack_require__(/*! ./steps/code-cartoon-07-jump.am.png */ "./src/lib/libraries/decks/steps/code-cartoon-07-jump.am.png");
/* harmony import */ var _steps_code_cartoon_08_change_scenes_am_png__WEBPACK_IMPORTED_MODULE_129__ = __webpack_require__(/*! ./steps/code-cartoon-08-change-scenes.am.png */ "./src/lib/libraries/decks/steps/code-cartoon-08-change-scenes.am.png");
/* harmony import */ var _steps_code_cartoon_09_glide_around_am_png__WEBPACK_IMPORTED_MODULE_130__ = __webpack_require__(/*! ./steps/code-cartoon-09-glide-around.am.png */ "./src/lib/libraries/decks/steps/code-cartoon-09-glide-around.am.png");
/* harmony import */ var _steps_code_cartoon_10_change_costumes_am_png__WEBPACK_IMPORTED_MODULE_131__ = __webpack_require__(/*! ./steps/code-cartoon-10-change-costumes.am.png */ "./src/lib/libraries/decks/steps/code-cartoon-10-change-costumes.am.png");
/* harmony import */ var _steps_code_cartoon_11_choose_more_characters_LTR_png__WEBPACK_IMPORTED_MODULE_132__ = __webpack_require__(/*! ./steps/code-cartoon-11-choose-more-characters.LTR.png */ "./src/lib/libraries/decks/steps/code-cartoon-11-choose-more-characters.LTR.png");
/* harmony import */ var _steps_talking_2_choose_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_133__ = __webpack_require__(/*! ./steps/talking-2-choose-sprite.LTR.png */ "./src/lib/libraries/decks/steps/talking-2-choose-sprite.LTR.png");
/* harmony import */ var _steps_talking_3_say_something_am_png__WEBPACK_IMPORTED_MODULE_134__ = __webpack_require__(/*! ./steps/talking-3-say-something.am.png */ "./src/lib/libraries/decks/steps/talking-3-say-something.am.png");
/* harmony import */ var _steps_talking_4_choose_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_135__ = __webpack_require__(/*! ./steps/talking-4-choose-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/talking-4-choose-backdrop.LTR.png");
/* harmony import */ var _steps_talking_5_switch_backdrop_am_png__WEBPACK_IMPORTED_MODULE_136__ = __webpack_require__(/*! ./steps/talking-5-switch-backdrop.am.png */ "./src/lib/libraries/decks/steps/talking-5-switch-backdrop.am.png");
/* harmony import */ var _steps_talking_6_choose_another_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_137__ = __webpack_require__(/*! ./steps/talking-6-choose-another-sprite.LTR.png */ "./src/lib/libraries/decks/steps/talking-6-choose-another-sprite.LTR.png");
/* harmony import */ var _steps_talking_7_move_around_am_png__WEBPACK_IMPORTED_MODULE_138__ = __webpack_require__(/*! ./steps/talking-7-move-around.am.png */ "./src/lib/libraries/decks/steps/talking-7-move-around.am.png");
/* harmony import */ var _steps_talking_8_choose_another_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_139__ = __webpack_require__(/*! ./steps/talking-8-choose-another-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/talking-8-choose-another-backdrop.LTR.png");
/* harmony import */ var _steps_talking_9_animate_am_png__WEBPACK_IMPORTED_MODULE_140__ = __webpack_require__(/*! ./steps/talking-9-animate.am.png */ "./src/lib/libraries/decks/steps/talking-9-animate.am.png");
/* harmony import */ var _steps_talking_10_choose_third_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_141__ = __webpack_require__(/*! ./steps/talking-10-choose-third-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/talking-10-choose-third-backdrop.LTR.png");
/* harmony import */ var _steps_talking_11_choose_sound_am_gif__WEBPACK_IMPORTED_MODULE_142__ = __webpack_require__(/*! ./steps/talking-11-choose-sound.am.gif */ "./src/lib/libraries/decks/steps/talking-11-choose-sound.am.gif");
/* harmony import */ var _steps_talking_12_dance_moves_am_png__WEBPACK_IMPORTED_MODULE_143__ = __webpack_require__(/*! ./steps/talking-12-dance-moves.am.png */ "./src/lib/libraries/decks/steps/talking-12-dance-moves.am.png");
/* harmony import */ var _steps_talking_13_ask_and_answer_am_png__WEBPACK_IMPORTED_MODULE_144__ = __webpack_require__(/*! ./steps/talking-13-ask-and-answer.am.png */ "./src/lib/libraries/decks/steps/talking-13-ask-and-answer.am.png");
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














var amImages = {
  // Intro
  introMove: _steps_intro_1_move_am_gif__WEBPACK_IMPORTED_MODULE_0__["default"],
  introSay: _steps_intro_2_say_am_gif__WEBPACK_IMPORTED_MODULE_1__["default"],
  introGreenFlag: _steps_intro_3_green_flag_am_gif__WEBPACK_IMPORTED_MODULE_2__["default"],
  // Text to Speech
  speechAddExtension: _steps_speech_add_extension_am_gif__WEBPACK_IMPORTED_MODULE_3__["default"],
  speechSaySomething: _steps_speech_say_something_am_png__WEBPACK_IMPORTED_MODULE_4__["default"],
  speechSetVoice: _steps_speech_set_voice_am_png__WEBPACK_IMPORTED_MODULE_5__["default"],
  speechMoveAround: _steps_speech_move_around_am_png__WEBPACK_IMPORTED_MODULE_6__["default"],
  speechAddBackdrop: _steps_pick_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_7__["default"],
  speechAddSprite: _steps_speech_add_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_8__["default"],
  speechSong: _steps_speech_song_am_png__WEBPACK_IMPORTED_MODULE_9__["default"],
  speechChangeColor: _steps_speech_change_color_am_png__WEBPACK_IMPORTED_MODULE_10__["default"],
  speechSpin: _steps_speech_spin_am_png__WEBPACK_IMPORTED_MODULE_11__["default"],
  speechGrowShrink: _steps_speech_grow_shrink_am_png__WEBPACK_IMPORTED_MODULE_12__["default"],
  // Cartoon Network
  cnShowCharacter: _steps_cn_show_character_LTR_gif__WEBPACK_IMPORTED_MODULE_13__["default"],
  cnSay: _steps_cn_say_am_png__WEBPACK_IMPORTED_MODULE_14__["default"],
  cnGlide: _steps_cn_glide_am_png__WEBPACK_IMPORTED_MODULE_15__["default"],
  cnPickSprite: _steps_cn_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_16__["default"],
  cnCollect: _steps_cn_collect_am_png__WEBPACK_IMPORTED_MODULE_17__["default"],
  cnVariable: _steps_add_variable_am_gif__WEBPACK_IMPORTED_MODULE_18__["default"],
  cnScore: _steps_cn_score_am_png__WEBPACK_IMPORTED_MODULE_19__["default"],
  cnBackdrop: _steps_cn_backdrop_am_png__WEBPACK_IMPORTED_MODULE_20__["default"],
  // Add sprite
  addSprite: _steps_add_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_21__["default"],
  // Animate a name
  namePickLetter: _steps_name_pick_letter_LTR_gif__WEBPACK_IMPORTED_MODULE_22__["default"],
  namePlaySound: _steps_name_play_sound_am_png__WEBPACK_IMPORTED_MODULE_23__["default"],
  namePickLetter2: _steps_name_pick_letter2_LTR_gif__WEBPACK_IMPORTED_MODULE_24__["default"],
  nameChangeColor: _steps_name_change_color_am_png__WEBPACK_IMPORTED_MODULE_25__["default"],
  nameSpin: _steps_name_spin_am_png__WEBPACK_IMPORTED_MODULE_26__["default"],
  nameGrow: _steps_name_grow_am_png__WEBPACK_IMPORTED_MODULE_27__["default"],
  // Make-Music
  musicPickInstrument: _steps_music_pick_instrument_LTR_gif__WEBPACK_IMPORTED_MODULE_28__["default"],
  musicPlaySound: _steps_music_play_sound_am_png__WEBPACK_IMPORTED_MODULE_29__["default"],
  musicMakeSong: _steps_music_make_song_am_png__WEBPACK_IMPORTED_MODULE_30__["default"],
  musicMakeBeat: _steps_music_make_beat_am_png__WEBPACK_IMPORTED_MODULE_31__["default"],
  musicMakeBeatbox: _steps_music_make_beatbox_am_png__WEBPACK_IMPORTED_MODULE_32__["default"],
  // Chase-Game
  chaseGameAddBackdrop: _steps_chase_game_add_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_33__["default"],
  chaseGameAddSprite1: _steps_chase_game_add_sprite1_LTR_gif__WEBPACK_IMPORTED_MODULE_34__["default"],
  chaseGameRightLeft: _steps_chase_game_right_left_am_png__WEBPACK_IMPORTED_MODULE_35__["default"],
  chaseGameUpDown: _steps_chase_game_up_down_am_png__WEBPACK_IMPORTED_MODULE_36__["default"],
  chaseGameAddSprite2: _steps_chase_game_add_sprite2_LTR_gif__WEBPACK_IMPORTED_MODULE_37__["default"],
  chaseGameMoveRandomly: _steps_chase_game_move_randomly_am_png__WEBPACK_IMPORTED_MODULE_38__["default"],
  chaseGamePlaySound: _steps_chase_game_play_sound_am_png__WEBPACK_IMPORTED_MODULE_39__["default"],
  chaseGameAddVariable: _steps_add_variable_am_gif__WEBPACK_IMPORTED_MODULE_18__["default"],
  chaseGameChangeScore: _steps_chase_game_change_score_am_png__WEBPACK_IMPORTED_MODULE_40__["default"],
  // Make-A-Pop/Clicker Game
  popGamePickSprite: _steps_pop_game_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_41__["default"],
  popGamePlaySound: _steps_pop_game_play_sound_am_png__WEBPACK_IMPORTED_MODULE_42__["default"],
  popGameAddScore: _steps_add_variable_am_gif__WEBPACK_IMPORTED_MODULE_18__["default"],
  popGameChangeScore: _steps_pop_game_change_score_am_png__WEBPACK_IMPORTED_MODULE_43__["default"],
  popGameRandomPosition: _steps_pop_game_random_position_am_png__WEBPACK_IMPORTED_MODULE_44__["default"],
  popGameChangeColor: _steps_pop_game_change_color_am_png__WEBPACK_IMPORTED_MODULE_45__["default"],
  popGameResetScore: _steps_pop_game_reset_score_am_png__WEBPACK_IMPORTED_MODULE_46__["default"],
  // Animate A Character
  animateCharPickBackdrop: _steps_pick_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_7__["default"],
  animateCharPickSprite: _steps_animate_char_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_47__["default"],
  animateCharSaySomething: _steps_animate_char_say_something_am_png__WEBPACK_IMPORTED_MODULE_48__["default"],
  animateCharAddSound: _steps_animate_char_add_sound_am_png__WEBPACK_IMPORTED_MODULE_49__["default"],
  animateCharTalk: _steps_animate_char_talk_am_png__WEBPACK_IMPORTED_MODULE_50__["default"],
  animateCharMove: _steps_animate_char_move_am_png__WEBPACK_IMPORTED_MODULE_51__["default"],
  animateCharJump: _steps_animate_char_jump_am_png__WEBPACK_IMPORTED_MODULE_52__["default"],
  animateCharChangeColor: _steps_animate_char_change_color_am_png__WEBPACK_IMPORTED_MODULE_53__["default"],
  // Tell A Story
  storyPickBackdrop: _steps_story_pick_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_54__["default"],
  storyPickSprite: _steps_story_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_55__["default"],
  storySaySomething: _steps_story_say_something_am_png__WEBPACK_IMPORTED_MODULE_56__["default"],
  storyPickSprite2: _steps_story_pick_sprite2_LTR_gif__WEBPACK_IMPORTED_MODULE_57__["default"],
  storyFlip: _steps_story_flip_am_gif__WEBPACK_IMPORTED_MODULE_58__["default"],
  storyConversation: _steps_story_conversation_am_png__WEBPACK_IMPORTED_MODULE_59__["default"],
  storyPickBackdrop2: _steps_story_pick_backdrop2_LTR_gif__WEBPACK_IMPORTED_MODULE_60__["default"],
  storySwitchBackdrop: _steps_story_switch_backdrop_am_png__WEBPACK_IMPORTED_MODULE_61__["default"],
  storyHideCharacter: _steps_story_hide_character_am_png__WEBPACK_IMPORTED_MODULE_62__["default"],
  storyShowCharacter: _steps_story_show_character_am_png__WEBPACK_IMPORTED_MODULE_63__["default"],
  // Video Sensing
  videoAddExtension: _steps_video_add_extension_am_gif__WEBPACK_IMPORTED_MODULE_64__["default"],
  videoPet: _steps_video_pet_am_png__WEBPACK_IMPORTED_MODULE_65__["default"],
  videoAnimate: _steps_video_animate_am_png__WEBPACK_IMPORTED_MODULE_66__["default"],
  videoPop: _steps_video_pop_am_png__WEBPACK_IMPORTED_MODULE_67__["default"],
  // Make it Fly
  flyChooseBackdrop: _steps_fly_choose_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_68__["default"],
  flyChooseCharacter: _steps_fly_choose_character_LTR_png__WEBPACK_IMPORTED_MODULE_69__["default"],
  flySaySomething: _steps_fly_say_something_am_png__WEBPACK_IMPORTED_MODULE_70__["default"],
  flyMoveArrows: _steps_fly_make_interactive_am_png__WEBPACK_IMPORTED_MODULE_71__["default"],
  flyChooseObject: _steps_fly_object_to_collect_LTR_png__WEBPACK_IMPORTED_MODULE_72__["default"],
  flyFlyingObject: _steps_fly_flying_heart_am_png__WEBPACK_IMPORTED_MODULE_73__["default"],
  flySelectFlyingSprite: _steps_fly_select_flyer_LTR_png__WEBPACK_IMPORTED_MODULE_74__["default"],
  flyAddScore: _steps_add_variable_am_gif__WEBPACK_IMPORTED_MODULE_18__["default"],
  flyKeepScore: _steps_fly_keep_score_am_png__WEBPACK_IMPORTED_MODULE_75__["default"],
  flyAddScenery: _steps_fly_choose_scenery_LTR_gif__WEBPACK_IMPORTED_MODULE_76__["default"],
  flyMoveScenery: _steps_fly_move_scenery_am_png__WEBPACK_IMPORTED_MODULE_77__["default"],
  flySwitchLooks: _steps_fly_switch_costume_am_png__WEBPACK_IMPORTED_MODULE_78__["default"],
  // Pong
  pongAddBackdrop: _steps_pong_add_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_79__["default"],
  pongAddBallSprite: _steps_pong_add_ball_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_80__["default"],
  pongBounceAround: _steps_pong_bounce_around_am_png__WEBPACK_IMPORTED_MODULE_81__["default"],
  pongAddPaddle: _steps_pong_add_a_paddle_LTR_gif__WEBPACK_IMPORTED_MODULE_82__["default"],
  pongMoveThePaddle: _steps_pong_move_the_paddle_am_png__WEBPACK_IMPORTED_MODULE_83__["default"],
  pongSelectBallSprite: _steps_pong_select_ball_LTR_png__WEBPACK_IMPORTED_MODULE_84__["default"],
  pongAddMoreCodeToBall: _steps_pong_add_code_to_ball_am_png__WEBPACK_IMPORTED_MODULE_85__["default"],
  pongAddAScore: _steps_add_variable_am_gif__WEBPACK_IMPORTED_MODULE_18__["default"],
  pongChooseScoreFromMenu: _steps_pong_choose_score_am_png__WEBPACK_IMPORTED_MODULE_86__["default"],
  pongInsertChangeScoreBlock: _steps_pong_insert_change_score_am_png__WEBPACK_IMPORTED_MODULE_87__["default"],
  pongResetScore: _steps_pong_reset_score_am_png__WEBPACK_IMPORTED_MODULE_88__["default"],
  pongAddLineSprite: _steps_pong_add_line_LTR_gif__WEBPACK_IMPORTED_MODULE_89__["default"],
  pongGameOver: _steps_pong_game_over_am_png__WEBPACK_IMPORTED_MODULE_90__["default"],
  // Imagine a World
  imagineTypeWhatYouWant: _steps_imagine_type_what_you_want_am_png__WEBPACK_IMPORTED_MODULE_91__["default"],
  imagineClickGreenFlag: _steps_imagine_click_green_flag_am_png__WEBPACK_IMPORTED_MODULE_92__["default"],
  imagineChooseBackdrop: _steps_imagine_choose_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_93__["default"],
  imagineChooseSprite: _steps_imagine_choose_any_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_94__["default"],
  imagineFlyAround: _steps_imagine_fly_around_am_png__WEBPACK_IMPORTED_MODULE_95__["default"],
  imagineChooseAnotherSprite: _steps_imagine_choose_another_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_96__["default"],
  imagineLeftRight: _steps_imagine_left_right_am_png__WEBPACK_IMPORTED_MODULE_97__["default"],
  imagineUpDown: _steps_imagine_up_down_am_png__WEBPACK_IMPORTED_MODULE_98__["default"],
  imagineChangeCostumes: _steps_imagine_change_costumes_am_png__WEBPACK_IMPORTED_MODULE_99__["default"],
  imagineGlideToPoint: _steps_imagine_glide_to_point_am_png__WEBPACK_IMPORTED_MODULE_100__["default"],
  imagineGrowShrink: _steps_imagine_grow_shrink_am_png__WEBPACK_IMPORTED_MODULE_101__["default"],
  imagineChooseAnotherBackdrop: _steps_imagine_choose_another_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_102__["default"],
  imagineSwitchBackdrops: _steps_imagine_switch_backdrops_am_png__WEBPACK_IMPORTED_MODULE_103__["default"],
  imagineRecordASound: _steps_imagine_record_a_sound_am_gif__WEBPACK_IMPORTED_MODULE_104__["default"],
  imagineChooseSound: _steps_imagine_choose_sound_am_png__WEBPACK_IMPORTED_MODULE_105__["default"],
  // Add a Backdrop
  addBackdrop: _steps_add_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_106__["default"],
  // Add Effects
  addEffects: _steps_add_effects_am_png__WEBPACK_IMPORTED_MODULE_107__["default"],
  // Hide and Show
  hideAndShow: _steps_hide_show_am_png__WEBPACK_IMPORTED_MODULE_108__["default"],
  // Switch Costumes
  switchCostumes: _steps_switch_costumes_am_png__WEBPACK_IMPORTED_MODULE_109__["default"],
  // Change Size
  changeSize: _steps_change_size_am_png__WEBPACK_IMPORTED_MODULE_110__["default"],
  // Spin
  spinTurn: _steps_spin_turn_am_png__WEBPACK_IMPORTED_MODULE_111__["default"],
  spinPointInDirection: _steps_spin_point_in_direction_am_png__WEBPACK_IMPORTED_MODULE_112__["default"],
  // Record a Sound
  recordASoundSoundsTab: _steps_record_a_sound_sounds_tab_am_png__WEBPACK_IMPORTED_MODULE_113__["default"],
  recordASoundClickRecord: _steps_record_a_sound_click_record_am_png__WEBPACK_IMPORTED_MODULE_114__["default"],
  recordASoundPressRecordButton: _steps_record_a_sound_press_record_button_am_png__WEBPACK_IMPORTED_MODULE_115__["default"],
  recordASoundChooseSound: _steps_record_a_sound_choose_sound_am_png__WEBPACK_IMPORTED_MODULE_116__["default"],
  recordASoundPlayYourSound: _steps_record_a_sound_play_your_sound_am_png__WEBPACK_IMPORTED_MODULE_117__["default"],
  // Use Arrow Keys
  moveArrowKeysLeftRight: _steps_move_arrow_keys_left_right_am_png__WEBPACK_IMPORTED_MODULE_118__["default"],
  moveArrowKeysUpDown: _steps_move_arrow_keys_up_down_am_png__WEBPACK_IMPORTED_MODULE_119__["default"],
  // Glide Around
  glideAroundBackAndForth: _steps_glide_around_back_and_forth_am_png__WEBPACK_IMPORTED_MODULE_120__["default"],
  glideAroundPoint: _steps_glide_around_point_am_png__WEBPACK_IMPORTED_MODULE_121__["default"],
  // Code a Cartoon
  codeCartoonSaySomething: _steps_code_cartoon_01_say_something_am_png__WEBPACK_IMPORTED_MODULE_122__["default"],
  codeCartoonAnimate: _steps_code_cartoon_02_animate_am_png__WEBPACK_IMPORTED_MODULE_123__["default"],
  codeCartoonSelectDifferentCharacter: _steps_code_cartoon_03_select_different_character_LTR_png__WEBPACK_IMPORTED_MODULE_124__["default"],
  codeCartoonUseMinusSign: _steps_code_cartoon_04_use_minus_sign_am_png__WEBPACK_IMPORTED_MODULE_125__["default"],
  codeCartoonGrowShrink: _steps_code_cartoon_05_grow_shrink_am_png__WEBPACK_IMPORTED_MODULE_126__["default"],
  codeCartoonSelectDifferentCharacter2: _steps_code_cartoon_06_select_another_different_character_LTR_png__WEBPACK_IMPORTED_MODULE_127__["default"],
  codeCartoonJump: _steps_code_cartoon_07_jump_am_png__WEBPACK_IMPORTED_MODULE_128__["default"],
  codeCartoonChangeScenes: _steps_code_cartoon_08_change_scenes_am_png__WEBPACK_IMPORTED_MODULE_129__["default"],
  codeCartoonGlideAround: _steps_code_cartoon_09_glide_around_am_png__WEBPACK_IMPORTED_MODULE_130__["default"],
  codeCartoonChangeCostumes: _steps_code_cartoon_10_change_costumes_am_png__WEBPACK_IMPORTED_MODULE_131__["default"],
  codeCartoonChooseMoreCharacters: _steps_code_cartoon_11_choose_more_characters_LTR_png__WEBPACK_IMPORTED_MODULE_132__["default"],
  // Talking Tales
  talesAddExtension: _steps_speech_add_extension_am_gif__WEBPACK_IMPORTED_MODULE_3__["default"],
  talesChooseSprite: _steps_talking_2_choose_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_133__["default"],
  talesSaySomething: _steps_talking_3_say_something_am_png__WEBPACK_IMPORTED_MODULE_134__["default"],
  talesAskAnswer: _steps_talking_13_ask_and_answer_am_png__WEBPACK_IMPORTED_MODULE_144__["default"],
  talesChooseBackdrop: _steps_talking_4_choose_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_135__["default"],
  talesSwitchBackdrop: _steps_talking_5_switch_backdrop_am_png__WEBPACK_IMPORTED_MODULE_136__["default"],
  talesChooseAnotherSprite: _steps_talking_6_choose_another_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_137__["default"],
  talesMoveAround: _steps_talking_7_move_around_am_png__WEBPACK_IMPORTED_MODULE_138__["default"],
  talesChooseAnotherBackdrop: _steps_talking_8_choose_another_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_139__["default"],
  talesAnimateTalking: _steps_talking_9_animate_am_png__WEBPACK_IMPORTED_MODULE_140__["default"],
  talesChooseThirdBackdrop: _steps_talking_10_choose_third_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_141__["default"],
  talesChooseSound: _steps_talking_11_choose_sound_am_gif__WEBPACK_IMPORTED_MODULE_142__["default"],
  talesDanceMoves: _steps_talking_12_dance_moves_am_png__WEBPACK_IMPORTED_MODULE_143__["default"]
};


/***/ }),

/***/ "./src/lib/libraries/decks/steps/add-effects.am.png":
/*!**********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/add-effects.am.png ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/a9eb420ec604feed92534f37b800b9aa.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/add-variable.am.gif":
/*!***********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/add-variable.am.gif ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/7b76fad5e5637c425aa71c9ac1e118fb.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/animate-char-add-sound.am.png":
/*!*********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/animate-char-add-sound.am.png ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/7ab9ebc828811bb6d8e80eb030bb366e.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/animate-char-change-color.am.png":
/*!************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/animate-char-change-color.am.png ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/c73f58d0b4ce22bcb6d239d20688c036.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/animate-char-jump.am.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/animate-char-jump.am.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/3851240d5163528a81e129d7b1f49d18.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/animate-char-move.am.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/animate-char-move.am.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/abcb8e59acf2c9cfbf4fe4d0d5f9e55a.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/animate-char-say-something.am.png":
/*!*************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/animate-char-say-something.am.png ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/b7e61f8217ae964665f608c098181c48.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/animate-char-talk.am.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/animate-char-talk.am.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/0b6aa876aaec083cfdb166e030ef2aa6.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/change-size.am.png":
/*!**********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/change-size.am.png ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/aa6fb9a0c9660947da302b776803add9.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/chase-game-change-score.am.png":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/chase-game-change-score.am.png ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/087ce052adadc4ecc4dc45a55f9ae34b.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/chase-game-move-randomly.am.png":
/*!***********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/chase-game-move-randomly.am.png ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/2ce024c7be6a80920d025d93305ed3ac.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/chase-game-play-sound.am.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/chase-game-play-sound.am.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/020b80dcb063733ca3e7c899b28c9c7b.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/chase-game-right-left.am.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/chase-game-right-left.am.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/274df71447815709caf05a24b55edbc7.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/chase-game-up-down.am.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/chase-game-up-down.am.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/03c893e6c5844d9f629d595d18edf113.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/cn-backdrop.am.png":
/*!**********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/cn-backdrop.am.png ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/83e339a2e031786eb06af26689975f2c.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/cn-collect.am.png":
/*!*********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/cn-collect.am.png ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/a8ceaf3c34d2734e7b577b9f0447931a.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/cn-glide.am.png":
/*!*******************************************************!*\
  !*** ./src/lib/libraries/decks/steps/cn-glide.am.png ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/f5ac20c92791e812bebb1f84f20889f6.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/cn-say.am.png":
/*!*****************************************************!*\
  !*** ./src/lib/libraries/decks/steps/cn-say.am.png ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/efe87e3f2abd0c1da0163b829ef2dd3d.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/cn-score.am.png":
/*!*******************************************************!*\
  !*** ./src/lib/libraries/decks/steps/cn-score.am.png ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/b0fb5a7fb75d9697e4afd5c72766866c.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-01-say-something.am.png":
/*!****************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-01-say-something.am.png ***!
  \****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/627ffbd4955dfbb4f7b506ec95f848e8.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-02-animate.am.png":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-02-animate.am.png ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/705b74026b2401e42630204d631c8261.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-04-use-minus-sign.am.png":
/*!*****************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-04-use-minus-sign.am.png ***!
  \*****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/0409513b5d63d007f1d1e426bee06086.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-05-grow-shrink.am.png":
/*!**************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-05-grow-shrink.am.png ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/880e3411fa06ad2bc92c61dae9fadc2c.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-07-jump.am.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-07-jump.am.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/ff57094ec16a8ee5944268392fc915ac.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-08-change-scenes.am.png":
/*!****************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-08-change-scenes.am.png ***!
  \****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/9ea1ac240f9fd8298b014e32b2f50d8c.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-09-glide-around.am.png":
/*!***************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-09-glide-around.am.png ***!
  \***************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/fd602bbac336be796fbf119c2b73557c.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-10-change-costumes.am.png":
/*!******************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-10-change-costumes.am.png ***!
  \******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/c0eb464275b34f5ea79cbce6b1628406.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/fly-flying-heart.am.png":
/*!***************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/fly-flying-heart.am.png ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/29914d5d83dfd804287efed8300ff291.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/fly-keep-score.am.png":
/*!*************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/fly-keep-score.am.png ***!
  \*************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/415c4289c757388231915da751573681.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/fly-make-interactive.am.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/fly-make-interactive.am.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/27191f781e68af6181cc17d58b65b906.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/fly-move-scenery.am.png":
/*!***************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/fly-move-scenery.am.png ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/a97f7141709135fde7ec14f0f3bd5883.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/fly-say-something.am.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/fly-say-something.am.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/1bda571c8873cf45963bc25d2c466ded.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/fly-switch-costume.am.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/fly-switch-costume.am.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/f24cb3680857db6393151f60471be925.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/glide-around-back-and-forth.am.png":
/*!**************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/glide-around-back-and-forth.am.png ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/1cdb828e3168db01d362910b782f3eac.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/glide-around-point.am.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/glide-around-point.am.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/e36325449eeef8307b1e53ca2941d693.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/hide-show.am.png":
/*!********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/hide-show.am.png ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/eb59ba5e2f459fca481c3a7ec6cba42c.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-change-costumes.am.png":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-change-costumes.am.png ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/25691f845d7fe5fecf9d81162707e767.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-choose-sound.am.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-choose-sound.am.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/3b1ed94d86b6afc3099e7a61414f9681.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-click-green-flag.am.png":
/*!***********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-click-green-flag.am.png ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/4ed5c9a0142ec286971fd697f40c550d.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-fly-around.am.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-fly-around.am.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/8fc9dbc44d43f35b930d3a6b117fd66f.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-glide-to-point.am.png":
/*!*********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-glide-to-point.am.png ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/ab92e88034ca30a28fc8f8a20f5515b1.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-grow-shrink.am.png":
/*!******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-grow-shrink.am.png ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/c731b82007c70d1a3b3578f84cbf3bdc.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-left-right.am.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-left-right.am.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/04f60e8a30de1be579d5cb7fa73abdaf.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-record-a-sound.am.gif":
/*!*********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-record-a-sound.am.gif ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/40bd6707a1fc47dbcb849b3cbed6efae.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-switch-backdrops.am.png":
/*!***********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-switch-backdrops.am.png ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/64275dfc41e9f570b55b49916011b7b7.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-type-what-you-want.am.png":
/*!*************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-type-what-you-want.am.png ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/2033b9a79e77022c076afaeb93b1dc44.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-up-down.am.png":
/*!**************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-up-down.am.png ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/40c9556392e985dfdcd0b32891b90e2e.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/intro-1-move.am.gif":
/*!***********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/intro-1-move.am.gif ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/0ce1ab8f67be1a0835bb60fbc9c2b16d.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/intro-2-say.am.gif":
/*!**********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/intro-2-say.am.gif ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/c3edaf1166e0b79b98b999dfe052adbd.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/intro-3-green-flag.am.gif":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/intro-3-green-flag.am.gif ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/c2a191233d9d4bf57177b2e025595d71.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/move-arrow-keys-left-right.am.png":
/*!*************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/move-arrow-keys-left-right.am.png ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/cf8548f04b4a6e5293cb363cef485e52.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/move-arrow-keys-up-down.am.png":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/move-arrow-keys-up-down.am.png ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/0d822db45553b873d8d1cc091053b5d4.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/music-make-beat.am.png":
/*!**************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/music-make-beat.am.png ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/7b36c6172657f3ded128e17ce1eb59e7.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/music-make-beatbox.am.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/music-make-beatbox.am.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/af05f058225e2b7a18b8b75b61d6000d.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/music-make-song.am.png":
/*!**************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/music-make-song.am.png ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/86c17c0bd4aed2c975c52a4b6312bd68.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/music-play-sound.am.png":
/*!***************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/music-play-sound.am.png ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/2be17e488f8ac6e3534935123a04dbb9.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/name-change-color.am.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/name-change-color.am.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/b32556cbe646d9507044d354faf7664c.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/name-grow.am.png":
/*!********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/name-grow.am.png ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/db3b2bc8142e19a78d413e7dce9c1a81.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/name-play-sound.am.png":
/*!**************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/name-play-sound.am.png ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/fafb220e4d4d566dec60276aba8ddf04.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/name-spin.am.png":
/*!********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/name-spin.am.png ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/e493af8e60e7b4a2288432cac33f4af1.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-add-code-to-ball.am.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-add-code-to-ball.am.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/3d6bd6d24e81053604ccdd58da11aa98.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-bounce-around.am.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-bounce-around.am.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/cff3a9cca245529d975091673aa997f9.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-choose-score.am.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-choose-score.am.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/c4f7bf65b458ddcaeda8040ac7578b0f.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-game-over.am.png":
/*!*************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-game-over.am.png ***!
  \*************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/6f2e940de19a22f247fe7941b12398ee.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-insert-change-score.am.png":
/*!***********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-insert-change-score.am.png ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/6c8dfef955bb7ebaeca05aebec94b691.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-move-the-paddle.am.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-move-the-paddle.am.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/170f845f9a1ea4300e262387cf0f1eaa.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-reset-score.am.png":
/*!***************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-reset-score.am.png ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/c2bf69c8c50a57ca72437f694e58f9a2.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pop-game-change-color.am.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pop-game-change-color.am.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/d99a6adbe2ec88ed97f05efcc932d94e.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pop-game-change-score.am.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pop-game-change-score.am.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/e3e215e8d21be4900b41513c154acb05.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pop-game-play-sound.am.png":
/*!******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pop-game-play-sound.am.png ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/1938c670214904f87f1b7be896d2b94d.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pop-game-random-position.am.png":
/*!***********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pop-game-random-position.am.png ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/6146dc907339716f6dc50e7d2d86dc3a.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pop-game-reset-score.am.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pop-game-reset-score.am.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/16911d63e59fceb883a22563c6df82cc.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/record-a-sound-choose-sound.am.png":
/*!**************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/record-a-sound-choose-sound.am.png ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/6681f1372e13e754a27a5b355e82b21b.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/record-a-sound-click-record.am.png":
/*!**************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/record-a-sound-click-record.am.png ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/fa227b03f58d10dfff21e962ef602dc1.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/record-a-sound-play-your-sound.am.png":
/*!*****************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/record-a-sound-play-your-sound.am.png ***!
  \*****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/9288a5f878b81cc6ee4da88a358e000c.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/record-a-sound-press-record-button.am.png":
/*!*********************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/record-a-sound-press-record-button.am.png ***!
  \*********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/b95e56cd13c2805ca5f39a65b575cfea.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/record-a-sound-sounds-tab.am.png":
/*!************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/record-a-sound-sounds-tab.am.png ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/4ae8716f2c0d577fb28b5c2fda2d2f90.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-add-extension.am.gif":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-add-extension.am.gif ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/89c11442a1dc31ed4df5a989d0564dec.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-change-color.am.png":
/*!******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-change-color.am.png ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/588140aa5b374feb5f0b6d4346d3381a.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-grow-shrink.am.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-grow-shrink.am.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/354b563da959b4b26dbcdf9629f2efd8.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-move-around.am.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-move-around.am.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/37552e87ed1e63aab276c8d064f07d12.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-say-something.am.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-say-something.am.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/435037d27bd7e50cc8bb5193a80d3b3c.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-set-voice.am.png":
/*!***************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-set-voice.am.png ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/70edf659ab1a5a64ea0dd26bbdccdf34.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-song.am.png":
/*!**********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-song.am.png ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/cd28cf13fdb8d52177ff87f6f39b31f5.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-spin.am.png":
/*!**********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-spin.am.png ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/970891fbbd32104bb335059b3db5f67c.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/spin-point-in-direction.am.png":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/spin-point-in-direction.am.png ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/77398124a82bce711b40d7fe313d0138.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/spin-turn.am.png":
/*!********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/spin-turn.am.png ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/59a3ed98a00da62389911b2ce1ec9a3a.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/story-conversation.am.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/story-conversation.am.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/3341c49f9486a95fde97d9821e6ca12d.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/story-flip.am.gif":
/*!*********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/story-flip.am.gif ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/c067ee9ef2a42d26644f717cca63fa9b.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/story-hide-character.am.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/story-hide-character.am.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/e88dfbc0b974141a49987d1c2534ff41.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/story-say-something.am.png":
/*!******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/story-say-something.am.png ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/f8331d9b85e630defaa99003b3338b5c.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/story-show-character.am.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/story-show-character.am.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/319221653d7434856d979bdebc0fb962.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/story-switch-backdrop.am.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/story-switch-backdrop.am.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/b32c41da45305c68fe75416fc4b87852.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/switch-costumes.am.png":
/*!**************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/switch-costumes.am.png ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/6bf4fba4d91b6b74ec32f2ba289877af.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-11-choose-sound.am.gif":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-11-choose-sound.am.gif ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/835817052b36228a8662cd5460fc2e24.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-12-dance-moves.am.png":
/*!*********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-12-dance-moves.am.png ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/3777b2bc009955792616ea6846969df4.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-13-ask-and-answer.am.png":
/*!************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-13-ask-and-answer.am.png ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/6e5fa4200df3c09512815704b8cddad6.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-3-say-something.am.png":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-3-say-something.am.png ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/90c22af346af54007dbcbf6759e3d67a.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-5-switch-backdrop.am.png":
/*!************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-5-switch-backdrop.am.png ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/7178a14124c167ac65cd589e8d200af3.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-7-move-around.am.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-7-move-around.am.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/c969e5cca0e7ed26b10529b82cc0f5fc.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-9-animate.am.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-9-animate.am.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/8d44ed242690aaf56f03ca9117bcd576.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/video-add-extension.am.gif":
/*!******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/video-add-extension.am.gif ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/bd1dfdfc8c8399c4875dbf7d7c748309.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/video-animate.am.png":
/*!************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/video-animate.am.png ***!
  \************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/c84ccb2d745435856ffedf67c0fb5f94.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/video-pet.am.png":
/*!********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/video-pet.am.png ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/7611a49875433ae37e5b95ff450b4772.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/video-pop.am.png":
/*!********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/video-pop.am.png ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/320a772d07e21f6725de1e4bf1b5c2e1.png");

/***/ })

}]);
//# sourceMappingURL=am-steps.js.map