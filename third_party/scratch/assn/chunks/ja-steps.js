(window["webpackJsonpGUI"] = window["webpackJsonpGUI"] || []).push([["ja-steps"],{

/***/ "./src/lib/libraries/decks/ja-steps.js":
/*!*********************************************!*\
  !*** ./src/lib/libraries/decks/ja-steps.js ***!
  \*********************************************/
/*! exports provided: jaImages */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "jaImages", function() { return jaImages; });
/* harmony import */ var _steps_intro_1_move_ja_gif__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./steps/intro-1-move.ja.gif */ "./src/lib/libraries/decks/steps/intro-1-move.ja.gif");
/* harmony import */ var _steps_intro_2_say_ja_gif__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./steps/intro-2-say.ja.gif */ "./src/lib/libraries/decks/steps/intro-2-say.ja.gif");
/* harmony import */ var _steps_intro_3_green_flag_ja_gif__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./steps/intro-3-green-flag.ja.gif */ "./src/lib/libraries/decks/steps/intro-3-green-flag.ja.gif");
/* harmony import */ var _steps_speech_add_extension_ja_gif__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./steps/speech-add-extension.ja.gif */ "./src/lib/libraries/decks/steps/speech-add-extension.ja.gif");
/* harmony import */ var _steps_speech_say_something_ja_png__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./steps/speech-say-something.ja.png */ "./src/lib/libraries/decks/steps/speech-say-something.ja.png");
/* harmony import */ var _steps_speech_set_voice_ja_png__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./steps/speech-set-voice.ja.png */ "./src/lib/libraries/decks/steps/speech-set-voice.ja.png");
/* harmony import */ var _steps_speech_move_around_ja_png__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./steps/speech-move-around.ja.png */ "./src/lib/libraries/decks/steps/speech-move-around.ja.png");
/* harmony import */ var _steps_pick_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./steps/pick-backdrop.LTR.gif */ "./src/lib/libraries/decks/steps/pick-backdrop.LTR.gif");
/* harmony import */ var _steps_speech_add_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./steps/speech-add-sprite.LTR.gif */ "./src/lib/libraries/decks/steps/speech-add-sprite.LTR.gif");
/* harmony import */ var _steps_speech_song_ja_png__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./steps/speech-song.ja.png */ "./src/lib/libraries/decks/steps/speech-song.ja.png");
/* harmony import */ var _steps_speech_change_color_ja_png__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./steps/speech-change-color.ja.png */ "./src/lib/libraries/decks/steps/speech-change-color.ja.png");
/* harmony import */ var _steps_speech_spin_ja_png__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./steps/speech-spin.ja.png */ "./src/lib/libraries/decks/steps/speech-spin.ja.png");
/* harmony import */ var _steps_speech_grow_shrink_ja_png__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./steps/speech-grow-shrink.ja.png */ "./src/lib/libraries/decks/steps/speech-grow-shrink.ja.png");
/* harmony import */ var _steps_cn_show_character_LTR_gif__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./steps/cn-show-character.LTR.gif */ "./src/lib/libraries/decks/steps/cn-show-character.LTR.gif");
/* harmony import */ var _steps_cn_say_ja_png__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./steps/cn-say.ja.png */ "./src/lib/libraries/decks/steps/cn-say.ja.png");
/* harmony import */ var _steps_cn_glide_ja_png__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./steps/cn-glide.ja.png */ "./src/lib/libraries/decks/steps/cn-glide.ja.png");
/* harmony import */ var _steps_cn_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./steps/cn-pick-sprite.LTR.gif */ "./src/lib/libraries/decks/steps/cn-pick-sprite.LTR.gif");
/* harmony import */ var _steps_cn_collect_ja_png__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./steps/cn-collect.ja.png */ "./src/lib/libraries/decks/steps/cn-collect.ja.png");
/* harmony import */ var _steps_add_variable_ja_gif__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./steps/add-variable.ja.gif */ "./src/lib/libraries/decks/steps/add-variable.ja.gif");
/* harmony import */ var _steps_cn_score_ja_png__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./steps/cn-score.ja.png */ "./src/lib/libraries/decks/steps/cn-score.ja.png");
/* harmony import */ var _steps_cn_backdrop_ja_png__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./steps/cn-backdrop.ja.png */ "./src/lib/libraries/decks/steps/cn-backdrop.ja.png");
/* harmony import */ var _steps_add_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ./steps/add-sprite.LTR.gif */ "./src/lib/libraries/decks/steps/add-sprite.LTR.gif");
/* harmony import */ var _steps_name_pick_letter_LTR_gif__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! ./steps/name-pick-letter.LTR.gif */ "./src/lib/libraries/decks/steps/name-pick-letter.LTR.gif");
/* harmony import */ var _steps_name_play_sound_ja_png__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! ./steps/name-play-sound.ja.png */ "./src/lib/libraries/decks/steps/name-play-sound.ja.png");
/* harmony import */ var _steps_name_pick_letter2_LTR_gif__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! ./steps/name-pick-letter2.LTR.gif */ "./src/lib/libraries/decks/steps/name-pick-letter2.LTR.gif");
/* harmony import */ var _steps_name_change_color_ja_png__WEBPACK_IMPORTED_MODULE_25__ = __webpack_require__(/*! ./steps/name-change-color.ja.png */ "./src/lib/libraries/decks/steps/name-change-color.ja.png");
/* harmony import */ var _steps_name_spin_ja_png__WEBPACK_IMPORTED_MODULE_26__ = __webpack_require__(/*! ./steps/name-spin.ja.png */ "./src/lib/libraries/decks/steps/name-spin.ja.png");
/* harmony import */ var _steps_name_grow_ja_png__WEBPACK_IMPORTED_MODULE_27__ = __webpack_require__(/*! ./steps/name-grow.ja.png */ "./src/lib/libraries/decks/steps/name-grow.ja.png");
/* harmony import */ var _steps_music_pick_instrument_LTR_gif__WEBPACK_IMPORTED_MODULE_28__ = __webpack_require__(/*! ./steps/music-pick-instrument.LTR.gif */ "./src/lib/libraries/decks/steps/music-pick-instrument.LTR.gif");
/* harmony import */ var _steps_music_play_sound_ja_png__WEBPACK_IMPORTED_MODULE_29__ = __webpack_require__(/*! ./steps/music-play-sound.ja.png */ "./src/lib/libraries/decks/steps/music-play-sound.ja.png");
/* harmony import */ var _steps_music_make_song_ja_png__WEBPACK_IMPORTED_MODULE_30__ = __webpack_require__(/*! ./steps/music-make-song.ja.png */ "./src/lib/libraries/decks/steps/music-make-song.ja.png");
/* harmony import */ var _steps_music_make_beat_ja_png__WEBPACK_IMPORTED_MODULE_31__ = __webpack_require__(/*! ./steps/music-make-beat.ja.png */ "./src/lib/libraries/decks/steps/music-make-beat.ja.png");
/* harmony import */ var _steps_music_make_beatbox_ja_png__WEBPACK_IMPORTED_MODULE_32__ = __webpack_require__(/*! ./steps/music-make-beatbox.ja.png */ "./src/lib/libraries/decks/steps/music-make-beatbox.ja.png");
/* harmony import */ var _steps_chase_game_add_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_33__ = __webpack_require__(/*! ./steps/chase-game-add-backdrop.LTR.gif */ "./src/lib/libraries/decks/steps/chase-game-add-backdrop.LTR.gif");
/* harmony import */ var _steps_chase_game_add_sprite1_LTR_gif__WEBPACK_IMPORTED_MODULE_34__ = __webpack_require__(/*! ./steps/chase-game-add-sprite1.LTR.gif */ "./src/lib/libraries/decks/steps/chase-game-add-sprite1.LTR.gif");
/* harmony import */ var _steps_chase_game_right_left_ja_png__WEBPACK_IMPORTED_MODULE_35__ = __webpack_require__(/*! ./steps/chase-game-right-left.ja.png */ "./src/lib/libraries/decks/steps/chase-game-right-left.ja.png");
/* harmony import */ var _steps_chase_game_up_down_ja_png__WEBPACK_IMPORTED_MODULE_36__ = __webpack_require__(/*! ./steps/chase-game-up-down.ja.png */ "./src/lib/libraries/decks/steps/chase-game-up-down.ja.png");
/* harmony import */ var _steps_chase_game_add_sprite2_LTR_gif__WEBPACK_IMPORTED_MODULE_37__ = __webpack_require__(/*! ./steps/chase-game-add-sprite2.LTR.gif */ "./src/lib/libraries/decks/steps/chase-game-add-sprite2.LTR.gif");
/* harmony import */ var _steps_chase_game_move_randomly_ja_png__WEBPACK_IMPORTED_MODULE_38__ = __webpack_require__(/*! ./steps/chase-game-move-randomly.ja.png */ "./src/lib/libraries/decks/steps/chase-game-move-randomly.ja.png");
/* harmony import */ var _steps_chase_game_play_sound_ja_png__WEBPACK_IMPORTED_MODULE_39__ = __webpack_require__(/*! ./steps/chase-game-play-sound.ja.png */ "./src/lib/libraries/decks/steps/chase-game-play-sound.ja.png");
/* harmony import */ var _steps_chase_game_change_score_ja_png__WEBPACK_IMPORTED_MODULE_40__ = __webpack_require__(/*! ./steps/chase-game-change-score.ja.png */ "./src/lib/libraries/decks/steps/chase-game-change-score.ja.png");
/* harmony import */ var _steps_pop_game_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_41__ = __webpack_require__(/*! ./steps/pop-game-pick-sprite.LTR.gif */ "./src/lib/libraries/decks/steps/pop-game-pick-sprite.LTR.gif");
/* harmony import */ var _steps_pop_game_play_sound_ja_png__WEBPACK_IMPORTED_MODULE_42__ = __webpack_require__(/*! ./steps/pop-game-play-sound.ja.png */ "./src/lib/libraries/decks/steps/pop-game-play-sound.ja.png");
/* harmony import */ var _steps_pop_game_change_score_ja_png__WEBPACK_IMPORTED_MODULE_43__ = __webpack_require__(/*! ./steps/pop-game-change-score.ja.png */ "./src/lib/libraries/decks/steps/pop-game-change-score.ja.png");
/* harmony import */ var _steps_pop_game_random_position_ja_png__WEBPACK_IMPORTED_MODULE_44__ = __webpack_require__(/*! ./steps/pop-game-random-position.ja.png */ "./src/lib/libraries/decks/steps/pop-game-random-position.ja.png");
/* harmony import */ var _steps_pop_game_change_color_ja_png__WEBPACK_IMPORTED_MODULE_45__ = __webpack_require__(/*! ./steps/pop-game-change-color.ja.png */ "./src/lib/libraries/decks/steps/pop-game-change-color.ja.png");
/* harmony import */ var _steps_pop_game_reset_score_ja_png__WEBPACK_IMPORTED_MODULE_46__ = __webpack_require__(/*! ./steps/pop-game-reset-score.ja.png */ "./src/lib/libraries/decks/steps/pop-game-reset-score.ja.png");
/* harmony import */ var _steps_animate_char_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_47__ = __webpack_require__(/*! ./steps/animate-char-pick-sprite.LTR.gif */ "./src/lib/libraries/decks/steps/animate-char-pick-sprite.LTR.gif");
/* harmony import */ var _steps_animate_char_say_something_ja_png__WEBPACK_IMPORTED_MODULE_48__ = __webpack_require__(/*! ./steps/animate-char-say-something.ja.png */ "./src/lib/libraries/decks/steps/animate-char-say-something.ja.png");
/* harmony import */ var _steps_animate_char_add_sound_ja_png__WEBPACK_IMPORTED_MODULE_49__ = __webpack_require__(/*! ./steps/animate-char-add-sound.ja.png */ "./src/lib/libraries/decks/steps/animate-char-add-sound.ja.png");
/* harmony import */ var _steps_animate_char_talk_ja_png__WEBPACK_IMPORTED_MODULE_50__ = __webpack_require__(/*! ./steps/animate-char-talk.ja.png */ "./src/lib/libraries/decks/steps/animate-char-talk.ja.png");
/* harmony import */ var _steps_animate_char_move_ja_png__WEBPACK_IMPORTED_MODULE_51__ = __webpack_require__(/*! ./steps/animate-char-move.ja.png */ "./src/lib/libraries/decks/steps/animate-char-move.ja.png");
/* harmony import */ var _steps_animate_char_jump_ja_png__WEBPACK_IMPORTED_MODULE_52__ = __webpack_require__(/*! ./steps/animate-char-jump.ja.png */ "./src/lib/libraries/decks/steps/animate-char-jump.ja.png");
/* harmony import */ var _steps_animate_char_change_color_ja_png__WEBPACK_IMPORTED_MODULE_53__ = __webpack_require__(/*! ./steps/animate-char-change-color.ja.png */ "./src/lib/libraries/decks/steps/animate-char-change-color.ja.png");
/* harmony import */ var _steps_story_pick_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_54__ = __webpack_require__(/*! ./steps/story-pick-backdrop.LTR.gif */ "./src/lib/libraries/decks/steps/story-pick-backdrop.LTR.gif");
/* harmony import */ var _steps_story_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_55__ = __webpack_require__(/*! ./steps/story-pick-sprite.LTR.gif */ "./src/lib/libraries/decks/steps/story-pick-sprite.LTR.gif");
/* harmony import */ var _steps_story_say_something_ja_png__WEBPACK_IMPORTED_MODULE_56__ = __webpack_require__(/*! ./steps/story-say-something.ja.png */ "./src/lib/libraries/decks/steps/story-say-something.ja.png");
/* harmony import */ var _steps_story_pick_sprite2_LTR_gif__WEBPACK_IMPORTED_MODULE_57__ = __webpack_require__(/*! ./steps/story-pick-sprite2.LTR.gif */ "./src/lib/libraries/decks/steps/story-pick-sprite2.LTR.gif");
/* harmony import */ var _steps_story_flip_ja_gif__WEBPACK_IMPORTED_MODULE_58__ = __webpack_require__(/*! ./steps/story-flip.ja.gif */ "./src/lib/libraries/decks/steps/story-flip.ja.gif");
/* harmony import */ var _steps_story_conversation_ja_png__WEBPACK_IMPORTED_MODULE_59__ = __webpack_require__(/*! ./steps/story-conversation.ja.png */ "./src/lib/libraries/decks/steps/story-conversation.ja.png");
/* harmony import */ var _steps_story_pick_backdrop2_LTR_gif__WEBPACK_IMPORTED_MODULE_60__ = __webpack_require__(/*! ./steps/story-pick-backdrop2.LTR.gif */ "./src/lib/libraries/decks/steps/story-pick-backdrop2.LTR.gif");
/* harmony import */ var _steps_story_switch_backdrop_ja_png__WEBPACK_IMPORTED_MODULE_61__ = __webpack_require__(/*! ./steps/story-switch-backdrop.ja.png */ "./src/lib/libraries/decks/steps/story-switch-backdrop.ja.png");
/* harmony import */ var _steps_story_hide_character_ja_png__WEBPACK_IMPORTED_MODULE_62__ = __webpack_require__(/*! ./steps/story-hide-character.ja.png */ "./src/lib/libraries/decks/steps/story-hide-character.ja.png");
/* harmony import */ var _steps_story_show_character_ja_png__WEBPACK_IMPORTED_MODULE_63__ = __webpack_require__(/*! ./steps/story-show-character.ja.png */ "./src/lib/libraries/decks/steps/story-show-character.ja.png");
/* harmony import */ var _steps_video_add_extension_ja_gif__WEBPACK_IMPORTED_MODULE_64__ = __webpack_require__(/*! ./steps/video-add-extension.ja.gif */ "./src/lib/libraries/decks/steps/video-add-extension.ja.gif");
/* harmony import */ var _steps_video_pet_ja_png__WEBPACK_IMPORTED_MODULE_65__ = __webpack_require__(/*! ./steps/video-pet.ja.png */ "./src/lib/libraries/decks/steps/video-pet.ja.png");
/* harmony import */ var _steps_video_animate_ja_png__WEBPACK_IMPORTED_MODULE_66__ = __webpack_require__(/*! ./steps/video-animate.ja.png */ "./src/lib/libraries/decks/steps/video-animate.ja.png");
/* harmony import */ var _steps_video_pop_ja_png__WEBPACK_IMPORTED_MODULE_67__ = __webpack_require__(/*! ./steps/video-pop.ja.png */ "./src/lib/libraries/decks/steps/video-pop.ja.png");
/* harmony import */ var _steps_fly_choose_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_68__ = __webpack_require__(/*! ./steps/fly-choose-backdrop.LTR.gif */ "./src/lib/libraries/decks/steps/fly-choose-backdrop.LTR.gif");
/* harmony import */ var _steps_fly_choose_character_LTR_png__WEBPACK_IMPORTED_MODULE_69__ = __webpack_require__(/*! ./steps/fly-choose-character.LTR.png */ "./src/lib/libraries/decks/steps/fly-choose-character.LTR.png");
/* harmony import */ var _steps_fly_say_something_ja_png__WEBPACK_IMPORTED_MODULE_70__ = __webpack_require__(/*! ./steps/fly-say-something.ja.png */ "./src/lib/libraries/decks/steps/fly-say-something.ja.png");
/* harmony import */ var _steps_fly_make_interactive_ja_png__WEBPACK_IMPORTED_MODULE_71__ = __webpack_require__(/*! ./steps/fly-make-interactive.ja.png */ "./src/lib/libraries/decks/steps/fly-make-interactive.ja.png");
/* harmony import */ var _steps_fly_object_to_collect_LTR_png__WEBPACK_IMPORTED_MODULE_72__ = __webpack_require__(/*! ./steps/fly-object-to-collect.LTR.png */ "./src/lib/libraries/decks/steps/fly-object-to-collect.LTR.png");
/* harmony import */ var _steps_fly_flying_heart_ja_png__WEBPACK_IMPORTED_MODULE_73__ = __webpack_require__(/*! ./steps/fly-flying-heart.ja.png */ "./src/lib/libraries/decks/steps/fly-flying-heart.ja.png");
/* harmony import */ var _steps_fly_select_flyer_LTR_png__WEBPACK_IMPORTED_MODULE_74__ = __webpack_require__(/*! ./steps/fly-select-flyer.LTR.png */ "./src/lib/libraries/decks/steps/fly-select-flyer.LTR.png");
/* harmony import */ var _steps_fly_keep_score_ja_png__WEBPACK_IMPORTED_MODULE_75__ = __webpack_require__(/*! ./steps/fly-keep-score.ja.png */ "./src/lib/libraries/decks/steps/fly-keep-score.ja.png");
/* harmony import */ var _steps_fly_choose_scenery_LTR_gif__WEBPACK_IMPORTED_MODULE_76__ = __webpack_require__(/*! ./steps/fly-choose-scenery.LTR.gif */ "./src/lib/libraries/decks/steps/fly-choose-scenery.LTR.gif");
/* harmony import */ var _steps_fly_move_scenery_ja_png__WEBPACK_IMPORTED_MODULE_77__ = __webpack_require__(/*! ./steps/fly-move-scenery.ja.png */ "./src/lib/libraries/decks/steps/fly-move-scenery.ja.png");
/* harmony import */ var _steps_fly_switch_costume_ja_png__WEBPACK_IMPORTED_MODULE_78__ = __webpack_require__(/*! ./steps/fly-switch-costume.ja.png */ "./src/lib/libraries/decks/steps/fly-switch-costume.ja.png");
/* harmony import */ var _steps_pong_add_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_79__ = __webpack_require__(/*! ./steps/pong-add-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/pong-add-backdrop.LTR.png");
/* harmony import */ var _steps_pong_add_ball_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_80__ = __webpack_require__(/*! ./steps/pong-add-ball-sprite.LTR.png */ "./src/lib/libraries/decks/steps/pong-add-ball-sprite.LTR.png");
/* harmony import */ var _steps_pong_bounce_around_ja_png__WEBPACK_IMPORTED_MODULE_81__ = __webpack_require__(/*! ./steps/pong-bounce-around.ja.png */ "./src/lib/libraries/decks/steps/pong-bounce-around.ja.png");
/* harmony import */ var _steps_pong_add_a_paddle_LTR_gif__WEBPACK_IMPORTED_MODULE_82__ = __webpack_require__(/*! ./steps/pong-add-a-paddle.LTR.gif */ "./src/lib/libraries/decks/steps/pong-add-a-paddle.LTR.gif");
/* harmony import */ var _steps_pong_move_the_paddle_ja_png__WEBPACK_IMPORTED_MODULE_83__ = __webpack_require__(/*! ./steps/pong-move-the-paddle.ja.png */ "./src/lib/libraries/decks/steps/pong-move-the-paddle.ja.png");
/* harmony import */ var _steps_pong_select_ball_LTR_png__WEBPACK_IMPORTED_MODULE_84__ = __webpack_require__(/*! ./steps/pong-select-ball.LTR.png */ "./src/lib/libraries/decks/steps/pong-select-ball.LTR.png");
/* harmony import */ var _steps_pong_add_code_to_ball_ja_png__WEBPACK_IMPORTED_MODULE_85__ = __webpack_require__(/*! ./steps/pong-add-code-to-ball.ja.png */ "./src/lib/libraries/decks/steps/pong-add-code-to-ball.ja.png");
/* harmony import */ var _steps_pong_choose_score_ja_png__WEBPACK_IMPORTED_MODULE_86__ = __webpack_require__(/*! ./steps/pong-choose-score.ja.png */ "./src/lib/libraries/decks/steps/pong-choose-score.ja.png");
/* harmony import */ var _steps_pong_insert_change_score_ja_png__WEBPACK_IMPORTED_MODULE_87__ = __webpack_require__(/*! ./steps/pong-insert-change-score.ja.png */ "./src/lib/libraries/decks/steps/pong-insert-change-score.ja.png");
/* harmony import */ var _steps_pong_reset_score_ja_png__WEBPACK_IMPORTED_MODULE_88__ = __webpack_require__(/*! ./steps/pong-reset-score.ja.png */ "./src/lib/libraries/decks/steps/pong-reset-score.ja.png");
/* harmony import */ var _steps_pong_add_line_LTR_gif__WEBPACK_IMPORTED_MODULE_89__ = __webpack_require__(/*! ./steps/pong-add-line.LTR.gif */ "./src/lib/libraries/decks/steps/pong-add-line.LTR.gif");
/* harmony import */ var _steps_pong_game_over_ja_png__WEBPACK_IMPORTED_MODULE_90__ = __webpack_require__(/*! ./steps/pong-game-over.ja.png */ "./src/lib/libraries/decks/steps/pong-game-over.ja.png");
/* harmony import */ var _steps_imagine_type_what_you_want_ja_png__WEBPACK_IMPORTED_MODULE_91__ = __webpack_require__(/*! ./steps/imagine-type-what-you-want.ja.png */ "./src/lib/libraries/decks/steps/imagine-type-what-you-want.ja.png");
/* harmony import */ var _steps_imagine_click_green_flag_ja_png__WEBPACK_IMPORTED_MODULE_92__ = __webpack_require__(/*! ./steps/imagine-click-green-flag.ja.png */ "./src/lib/libraries/decks/steps/imagine-click-green-flag.ja.png");
/* harmony import */ var _steps_imagine_choose_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_93__ = __webpack_require__(/*! ./steps/imagine-choose-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/imagine-choose-backdrop.LTR.png");
/* harmony import */ var _steps_imagine_choose_any_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_94__ = __webpack_require__(/*! ./steps/imagine-choose-any-sprite.LTR.png */ "./src/lib/libraries/decks/steps/imagine-choose-any-sprite.LTR.png");
/* harmony import */ var _steps_imagine_fly_around_ja_png__WEBPACK_IMPORTED_MODULE_95__ = __webpack_require__(/*! ./steps/imagine-fly-around.ja.png */ "./src/lib/libraries/decks/steps/imagine-fly-around.ja.png");
/* harmony import */ var _steps_imagine_choose_another_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_96__ = __webpack_require__(/*! ./steps/imagine-choose-another-sprite.LTR.png */ "./src/lib/libraries/decks/steps/imagine-choose-another-sprite.LTR.png");
/* harmony import */ var _steps_imagine_left_right_ja_png__WEBPACK_IMPORTED_MODULE_97__ = __webpack_require__(/*! ./steps/imagine-left-right.ja.png */ "./src/lib/libraries/decks/steps/imagine-left-right.ja.png");
/* harmony import */ var _steps_imagine_up_down_ja_png__WEBPACK_IMPORTED_MODULE_98__ = __webpack_require__(/*! ./steps/imagine-up-down.ja.png */ "./src/lib/libraries/decks/steps/imagine-up-down.ja.png");
/* harmony import */ var _steps_imagine_change_costumes_ja_png__WEBPACK_IMPORTED_MODULE_99__ = __webpack_require__(/*! ./steps/imagine-change-costumes.ja.png */ "./src/lib/libraries/decks/steps/imagine-change-costumes.ja.png");
/* harmony import */ var _steps_imagine_glide_to_point_ja_png__WEBPACK_IMPORTED_MODULE_100__ = __webpack_require__(/*! ./steps/imagine-glide-to-point.ja.png */ "./src/lib/libraries/decks/steps/imagine-glide-to-point.ja.png");
/* harmony import */ var _steps_imagine_grow_shrink_ja_png__WEBPACK_IMPORTED_MODULE_101__ = __webpack_require__(/*! ./steps/imagine-grow-shrink.ja.png */ "./src/lib/libraries/decks/steps/imagine-grow-shrink.ja.png");
/* harmony import */ var _steps_imagine_choose_another_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_102__ = __webpack_require__(/*! ./steps/imagine-choose-another-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/imagine-choose-another-backdrop.LTR.png");
/* harmony import */ var _steps_imagine_switch_backdrops_ja_png__WEBPACK_IMPORTED_MODULE_103__ = __webpack_require__(/*! ./steps/imagine-switch-backdrops.ja.png */ "./src/lib/libraries/decks/steps/imagine-switch-backdrops.ja.png");
/* harmony import */ var _steps_imagine_record_a_sound_ja_gif__WEBPACK_IMPORTED_MODULE_104__ = __webpack_require__(/*! ./steps/imagine-record-a-sound.ja.gif */ "./src/lib/libraries/decks/steps/imagine-record-a-sound.ja.gif");
/* harmony import */ var _steps_imagine_choose_sound_ja_png__WEBPACK_IMPORTED_MODULE_105__ = __webpack_require__(/*! ./steps/imagine-choose-sound.ja.png */ "./src/lib/libraries/decks/steps/imagine-choose-sound.ja.png");
/* harmony import */ var _steps_add_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_106__ = __webpack_require__(/*! ./steps/add-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/add-backdrop.LTR.png");
/* harmony import */ var _steps_add_effects_ja_png__WEBPACK_IMPORTED_MODULE_107__ = __webpack_require__(/*! ./steps/add-effects.ja.png */ "./src/lib/libraries/decks/steps/add-effects.ja.png");
/* harmony import */ var _steps_hide_show_ja_png__WEBPACK_IMPORTED_MODULE_108__ = __webpack_require__(/*! ./steps/hide-show.ja.png */ "./src/lib/libraries/decks/steps/hide-show.ja.png");
/* harmony import */ var _steps_switch_costumes_ja_png__WEBPACK_IMPORTED_MODULE_109__ = __webpack_require__(/*! ./steps/switch-costumes.ja.png */ "./src/lib/libraries/decks/steps/switch-costumes.ja.png");
/* harmony import */ var _steps_change_size_ja_png__WEBPACK_IMPORTED_MODULE_110__ = __webpack_require__(/*! ./steps/change-size.ja.png */ "./src/lib/libraries/decks/steps/change-size.ja.png");
/* harmony import */ var _steps_spin_turn_ja_png__WEBPACK_IMPORTED_MODULE_111__ = __webpack_require__(/*! ./steps/spin-turn.ja.png */ "./src/lib/libraries/decks/steps/spin-turn.ja.png");
/* harmony import */ var _steps_spin_point_in_direction_ja_png__WEBPACK_IMPORTED_MODULE_112__ = __webpack_require__(/*! ./steps/spin-point-in-direction.ja.png */ "./src/lib/libraries/decks/steps/spin-point-in-direction.ja.png");
/* harmony import */ var _steps_record_a_sound_sounds_tab_ja_png__WEBPACK_IMPORTED_MODULE_113__ = __webpack_require__(/*! ./steps/record-a-sound-sounds-tab.ja.png */ "./src/lib/libraries/decks/steps/record-a-sound-sounds-tab.ja.png");
/* harmony import */ var _steps_record_a_sound_click_record_ja_png__WEBPACK_IMPORTED_MODULE_114__ = __webpack_require__(/*! ./steps/record-a-sound-click-record.ja.png */ "./src/lib/libraries/decks/steps/record-a-sound-click-record.ja.png");
/* harmony import */ var _steps_record_a_sound_press_record_button_ja_png__WEBPACK_IMPORTED_MODULE_115__ = __webpack_require__(/*! ./steps/record-a-sound-press-record-button.ja.png */ "./src/lib/libraries/decks/steps/record-a-sound-press-record-button.ja.png");
/* harmony import */ var _steps_record_a_sound_choose_sound_ja_png__WEBPACK_IMPORTED_MODULE_116__ = __webpack_require__(/*! ./steps/record-a-sound-choose-sound.ja.png */ "./src/lib/libraries/decks/steps/record-a-sound-choose-sound.ja.png");
/* harmony import */ var _steps_record_a_sound_play_your_sound_ja_png__WEBPACK_IMPORTED_MODULE_117__ = __webpack_require__(/*! ./steps/record-a-sound-play-your-sound.ja.png */ "./src/lib/libraries/decks/steps/record-a-sound-play-your-sound.ja.png");
/* harmony import */ var _steps_move_arrow_keys_left_right_ja_png__WEBPACK_IMPORTED_MODULE_118__ = __webpack_require__(/*! ./steps/move-arrow-keys-left-right.ja.png */ "./src/lib/libraries/decks/steps/move-arrow-keys-left-right.ja.png");
/* harmony import */ var _steps_move_arrow_keys_up_down_ja_png__WEBPACK_IMPORTED_MODULE_119__ = __webpack_require__(/*! ./steps/move-arrow-keys-up-down.ja.png */ "./src/lib/libraries/decks/steps/move-arrow-keys-up-down.ja.png");
/* harmony import */ var _steps_glide_around_back_and_forth_ja_png__WEBPACK_IMPORTED_MODULE_120__ = __webpack_require__(/*! ./steps/glide-around-back-and-forth.ja.png */ "./src/lib/libraries/decks/steps/glide-around-back-and-forth.ja.png");
/* harmony import */ var _steps_glide_around_point_ja_png__WEBPACK_IMPORTED_MODULE_121__ = __webpack_require__(/*! ./steps/glide-around-point.ja.png */ "./src/lib/libraries/decks/steps/glide-around-point.ja.png");
/* harmony import */ var _steps_code_cartoon_01_say_something_ja_png__WEBPACK_IMPORTED_MODULE_122__ = __webpack_require__(/*! ./steps/code-cartoon-01-say-something.ja.png */ "./src/lib/libraries/decks/steps/code-cartoon-01-say-something.ja.png");
/* harmony import */ var _steps_code_cartoon_02_animate_ja_png__WEBPACK_IMPORTED_MODULE_123__ = __webpack_require__(/*! ./steps/code-cartoon-02-animate.ja.png */ "./src/lib/libraries/decks/steps/code-cartoon-02-animate.ja.png");
/* harmony import */ var _steps_code_cartoon_03_select_different_character_LTR_png__WEBPACK_IMPORTED_MODULE_124__ = __webpack_require__(/*! ./steps/code-cartoon-03-select-different-character.LTR.png */ "./src/lib/libraries/decks/steps/code-cartoon-03-select-different-character.LTR.png");
/* harmony import */ var _steps_code_cartoon_04_use_minus_sign_ja_png__WEBPACK_IMPORTED_MODULE_125__ = __webpack_require__(/*! ./steps/code-cartoon-04-use-minus-sign.ja.png */ "./src/lib/libraries/decks/steps/code-cartoon-04-use-minus-sign.ja.png");
/* harmony import */ var _steps_code_cartoon_05_grow_shrink_ja_png__WEBPACK_IMPORTED_MODULE_126__ = __webpack_require__(/*! ./steps/code-cartoon-05-grow-shrink.ja.png */ "./src/lib/libraries/decks/steps/code-cartoon-05-grow-shrink.ja.png");
/* harmony import */ var _steps_code_cartoon_06_select_another_different_character_LTR_png__WEBPACK_IMPORTED_MODULE_127__ = __webpack_require__(/*! ./steps/code-cartoon-06-select-another-different-character.LTR.png */ "./src/lib/libraries/decks/steps/code-cartoon-06-select-another-different-character.LTR.png");
/* harmony import */ var _steps_code_cartoon_07_jump_ja_png__WEBPACK_IMPORTED_MODULE_128__ = __webpack_require__(/*! ./steps/code-cartoon-07-jump.ja.png */ "./src/lib/libraries/decks/steps/code-cartoon-07-jump.ja.png");
/* harmony import */ var _steps_code_cartoon_08_change_scenes_ja_png__WEBPACK_IMPORTED_MODULE_129__ = __webpack_require__(/*! ./steps/code-cartoon-08-change-scenes.ja.png */ "./src/lib/libraries/decks/steps/code-cartoon-08-change-scenes.ja.png");
/* harmony import */ var _steps_code_cartoon_09_glide_around_ja_png__WEBPACK_IMPORTED_MODULE_130__ = __webpack_require__(/*! ./steps/code-cartoon-09-glide-around.ja.png */ "./src/lib/libraries/decks/steps/code-cartoon-09-glide-around.ja.png");
/* harmony import */ var _steps_code_cartoon_10_change_costumes_ja_png__WEBPACK_IMPORTED_MODULE_131__ = __webpack_require__(/*! ./steps/code-cartoon-10-change-costumes.ja.png */ "./src/lib/libraries/decks/steps/code-cartoon-10-change-costumes.ja.png");
/* harmony import */ var _steps_code_cartoon_11_choose_more_characters_LTR_png__WEBPACK_IMPORTED_MODULE_132__ = __webpack_require__(/*! ./steps/code-cartoon-11-choose-more-characters.LTR.png */ "./src/lib/libraries/decks/steps/code-cartoon-11-choose-more-characters.LTR.png");
/* harmony import */ var _steps_talking_2_choose_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_133__ = __webpack_require__(/*! ./steps/talking-2-choose-sprite.LTR.png */ "./src/lib/libraries/decks/steps/talking-2-choose-sprite.LTR.png");
/* harmony import */ var _steps_talking_3_say_something_ja_png__WEBPACK_IMPORTED_MODULE_134__ = __webpack_require__(/*! ./steps/talking-3-say-something.ja.png */ "./src/lib/libraries/decks/steps/talking-3-say-something.ja.png");
/* harmony import */ var _steps_talking_4_choose_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_135__ = __webpack_require__(/*! ./steps/talking-4-choose-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/talking-4-choose-backdrop.LTR.png");
/* harmony import */ var _steps_talking_5_switch_backdrop_ja_png__WEBPACK_IMPORTED_MODULE_136__ = __webpack_require__(/*! ./steps/talking-5-switch-backdrop.ja.png */ "./src/lib/libraries/decks/steps/talking-5-switch-backdrop.ja.png");
/* harmony import */ var _steps_talking_6_choose_another_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_137__ = __webpack_require__(/*! ./steps/talking-6-choose-another-sprite.LTR.png */ "./src/lib/libraries/decks/steps/talking-6-choose-another-sprite.LTR.png");
/* harmony import */ var _steps_talking_7_move_around_ja_png__WEBPACK_IMPORTED_MODULE_138__ = __webpack_require__(/*! ./steps/talking-7-move-around.ja.png */ "./src/lib/libraries/decks/steps/talking-7-move-around.ja.png");
/* harmony import */ var _steps_talking_8_choose_another_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_139__ = __webpack_require__(/*! ./steps/talking-8-choose-another-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/talking-8-choose-another-backdrop.LTR.png");
/* harmony import */ var _steps_talking_9_animate_ja_png__WEBPACK_IMPORTED_MODULE_140__ = __webpack_require__(/*! ./steps/talking-9-animate.ja.png */ "./src/lib/libraries/decks/steps/talking-9-animate.ja.png");
/* harmony import */ var _steps_talking_10_choose_third_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_141__ = __webpack_require__(/*! ./steps/talking-10-choose-third-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/talking-10-choose-third-backdrop.LTR.png");
/* harmony import */ var _steps_talking_11_choose_sound_ja_gif__WEBPACK_IMPORTED_MODULE_142__ = __webpack_require__(/*! ./steps/talking-11-choose-sound.ja.gif */ "./src/lib/libraries/decks/steps/talking-11-choose-sound.ja.gif");
/* harmony import */ var _steps_talking_12_dance_moves_ja_png__WEBPACK_IMPORTED_MODULE_143__ = __webpack_require__(/*! ./steps/talking-12-dance-moves.ja.png */ "./src/lib/libraries/decks/steps/talking-12-dance-moves.ja.png");
/* harmony import */ var _steps_talking_13_ask_and_answer_ja_png__WEBPACK_IMPORTED_MODULE_144__ = __webpack_require__(/*! ./steps/talking-13-ask-and-answer.ja.png */ "./src/lib/libraries/decks/steps/talking-13-ask-and-answer.ja.png");
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














var jaImages = {
  // Intro
  introMove: _steps_intro_1_move_ja_gif__WEBPACK_IMPORTED_MODULE_0__["default"],
  introSay: _steps_intro_2_say_ja_gif__WEBPACK_IMPORTED_MODULE_1__["default"],
  introGreenFlag: _steps_intro_3_green_flag_ja_gif__WEBPACK_IMPORTED_MODULE_2__["default"],
  // Text to Speech
  speechAddExtension: _steps_speech_add_extension_ja_gif__WEBPACK_IMPORTED_MODULE_3__["default"],
  speechSaySomething: _steps_speech_say_something_ja_png__WEBPACK_IMPORTED_MODULE_4__["default"],
  speechSetVoice: _steps_speech_set_voice_ja_png__WEBPACK_IMPORTED_MODULE_5__["default"],
  speechMoveAround: _steps_speech_move_around_ja_png__WEBPACK_IMPORTED_MODULE_6__["default"],
  speechAddBackdrop: _steps_pick_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_7__["default"],
  speechAddSprite: _steps_speech_add_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_8__["default"],
  speechSong: _steps_speech_song_ja_png__WEBPACK_IMPORTED_MODULE_9__["default"],
  speechChangeColor: _steps_speech_change_color_ja_png__WEBPACK_IMPORTED_MODULE_10__["default"],
  speechSpin: _steps_speech_spin_ja_png__WEBPACK_IMPORTED_MODULE_11__["default"],
  speechGrowShrink: _steps_speech_grow_shrink_ja_png__WEBPACK_IMPORTED_MODULE_12__["default"],
  // Cartoon Network
  cnShowCharacter: _steps_cn_show_character_LTR_gif__WEBPACK_IMPORTED_MODULE_13__["default"],
  cnSay: _steps_cn_say_ja_png__WEBPACK_IMPORTED_MODULE_14__["default"],
  cnGlide: _steps_cn_glide_ja_png__WEBPACK_IMPORTED_MODULE_15__["default"],
  cnPickSprite: _steps_cn_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_16__["default"],
  cnCollect: _steps_cn_collect_ja_png__WEBPACK_IMPORTED_MODULE_17__["default"],
  cnVariable: _steps_add_variable_ja_gif__WEBPACK_IMPORTED_MODULE_18__["default"],
  cnScore: _steps_cn_score_ja_png__WEBPACK_IMPORTED_MODULE_19__["default"],
  cnBackdrop: _steps_cn_backdrop_ja_png__WEBPACK_IMPORTED_MODULE_20__["default"],
  // Add sprite
  addSprite: _steps_add_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_21__["default"],
  // Animate a name
  namePickLetter: _steps_name_pick_letter_LTR_gif__WEBPACK_IMPORTED_MODULE_22__["default"],
  namePlaySound: _steps_name_play_sound_ja_png__WEBPACK_IMPORTED_MODULE_23__["default"],
  namePickLetter2: _steps_name_pick_letter2_LTR_gif__WEBPACK_IMPORTED_MODULE_24__["default"],
  nameChangeColor: _steps_name_change_color_ja_png__WEBPACK_IMPORTED_MODULE_25__["default"],
  nameSpin: _steps_name_spin_ja_png__WEBPACK_IMPORTED_MODULE_26__["default"],
  nameGrow: _steps_name_grow_ja_png__WEBPACK_IMPORTED_MODULE_27__["default"],
  // Make-Music
  musicPickInstrument: _steps_music_pick_instrument_LTR_gif__WEBPACK_IMPORTED_MODULE_28__["default"],
  musicPlaySound: _steps_music_play_sound_ja_png__WEBPACK_IMPORTED_MODULE_29__["default"],
  musicMakeSong: _steps_music_make_song_ja_png__WEBPACK_IMPORTED_MODULE_30__["default"],
  musicMakeBeat: _steps_music_make_beat_ja_png__WEBPACK_IMPORTED_MODULE_31__["default"],
  musicMakeBeatbox: _steps_music_make_beatbox_ja_png__WEBPACK_IMPORTED_MODULE_32__["default"],
  // Chase-Game
  chaseGameAddBackdrop: _steps_chase_game_add_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_33__["default"],
  chaseGameAddSprite1: _steps_chase_game_add_sprite1_LTR_gif__WEBPACK_IMPORTED_MODULE_34__["default"],
  chaseGameRightLeft: _steps_chase_game_right_left_ja_png__WEBPACK_IMPORTED_MODULE_35__["default"],
  chaseGameUpDown: _steps_chase_game_up_down_ja_png__WEBPACK_IMPORTED_MODULE_36__["default"],
  chaseGameAddSprite2: _steps_chase_game_add_sprite2_LTR_gif__WEBPACK_IMPORTED_MODULE_37__["default"],
  chaseGameMoveRandomly: _steps_chase_game_move_randomly_ja_png__WEBPACK_IMPORTED_MODULE_38__["default"],
  chaseGamePlaySound: _steps_chase_game_play_sound_ja_png__WEBPACK_IMPORTED_MODULE_39__["default"],
  chaseGameAddVariable: _steps_add_variable_ja_gif__WEBPACK_IMPORTED_MODULE_18__["default"],
  chaseGameChangeScore: _steps_chase_game_change_score_ja_png__WEBPACK_IMPORTED_MODULE_40__["default"],
  // Make-A-Pop/Clicker Game
  popGamePickSprite: _steps_pop_game_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_41__["default"],
  popGamePlaySound: _steps_pop_game_play_sound_ja_png__WEBPACK_IMPORTED_MODULE_42__["default"],
  popGameAddScore: _steps_add_variable_ja_gif__WEBPACK_IMPORTED_MODULE_18__["default"],
  popGameChangeScore: _steps_pop_game_change_score_ja_png__WEBPACK_IMPORTED_MODULE_43__["default"],
  popGameRandomPosition: _steps_pop_game_random_position_ja_png__WEBPACK_IMPORTED_MODULE_44__["default"],
  popGameChangeColor: _steps_pop_game_change_color_ja_png__WEBPACK_IMPORTED_MODULE_45__["default"],
  popGameResetScore: _steps_pop_game_reset_score_ja_png__WEBPACK_IMPORTED_MODULE_46__["default"],
  // Animate A Character
  animateCharPickBackdrop: _steps_pick_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_7__["default"],
  animateCharPickSprite: _steps_animate_char_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_47__["default"],
  animateCharSaySomething: _steps_animate_char_say_something_ja_png__WEBPACK_IMPORTED_MODULE_48__["default"],
  animateCharAddSound: _steps_animate_char_add_sound_ja_png__WEBPACK_IMPORTED_MODULE_49__["default"],
  animateCharTalk: _steps_animate_char_talk_ja_png__WEBPACK_IMPORTED_MODULE_50__["default"],
  animateCharMove: _steps_animate_char_move_ja_png__WEBPACK_IMPORTED_MODULE_51__["default"],
  animateCharJump: _steps_animate_char_jump_ja_png__WEBPACK_IMPORTED_MODULE_52__["default"],
  animateCharChangeColor: _steps_animate_char_change_color_ja_png__WEBPACK_IMPORTED_MODULE_53__["default"],
  // Tell A Story
  storyPickBackdrop: _steps_story_pick_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_54__["default"],
  storyPickSprite: _steps_story_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_55__["default"],
  storySaySomething: _steps_story_say_something_ja_png__WEBPACK_IMPORTED_MODULE_56__["default"],
  storyPickSprite2: _steps_story_pick_sprite2_LTR_gif__WEBPACK_IMPORTED_MODULE_57__["default"],
  storyFlip: _steps_story_flip_ja_gif__WEBPACK_IMPORTED_MODULE_58__["default"],
  storyConversation: _steps_story_conversation_ja_png__WEBPACK_IMPORTED_MODULE_59__["default"],
  storyPickBackdrop2: _steps_story_pick_backdrop2_LTR_gif__WEBPACK_IMPORTED_MODULE_60__["default"],
  storySwitchBackdrop: _steps_story_switch_backdrop_ja_png__WEBPACK_IMPORTED_MODULE_61__["default"],
  storyHideCharacter: _steps_story_hide_character_ja_png__WEBPACK_IMPORTED_MODULE_62__["default"],
  storyShowCharacter: _steps_story_show_character_ja_png__WEBPACK_IMPORTED_MODULE_63__["default"],
  // Video Sensing
  videoAddExtension: _steps_video_add_extension_ja_gif__WEBPACK_IMPORTED_MODULE_64__["default"],
  videoPet: _steps_video_pet_ja_png__WEBPACK_IMPORTED_MODULE_65__["default"],
  videoAnimate: _steps_video_animate_ja_png__WEBPACK_IMPORTED_MODULE_66__["default"],
  videoPop: _steps_video_pop_ja_png__WEBPACK_IMPORTED_MODULE_67__["default"],
  // Make it Fly
  flyChooseBackdrop: _steps_fly_choose_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_68__["default"],
  flyChooseCharacter: _steps_fly_choose_character_LTR_png__WEBPACK_IMPORTED_MODULE_69__["default"],
  flySaySomething: _steps_fly_say_something_ja_png__WEBPACK_IMPORTED_MODULE_70__["default"],
  flyMoveArrows: _steps_fly_make_interactive_ja_png__WEBPACK_IMPORTED_MODULE_71__["default"],
  flyChooseObject: _steps_fly_object_to_collect_LTR_png__WEBPACK_IMPORTED_MODULE_72__["default"],
  flyFlyingObject: _steps_fly_flying_heart_ja_png__WEBPACK_IMPORTED_MODULE_73__["default"],
  flySelectFlyingSprite: _steps_fly_select_flyer_LTR_png__WEBPACK_IMPORTED_MODULE_74__["default"],
  flyAddScore: _steps_add_variable_ja_gif__WEBPACK_IMPORTED_MODULE_18__["default"],
  flyKeepScore: _steps_fly_keep_score_ja_png__WEBPACK_IMPORTED_MODULE_75__["default"],
  flyAddScenery: _steps_fly_choose_scenery_LTR_gif__WEBPACK_IMPORTED_MODULE_76__["default"],
  flyMoveScenery: _steps_fly_move_scenery_ja_png__WEBPACK_IMPORTED_MODULE_77__["default"],
  flySwitchLooks: _steps_fly_switch_costume_ja_png__WEBPACK_IMPORTED_MODULE_78__["default"],
  // Pong
  pongAddBackdrop: _steps_pong_add_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_79__["default"],
  pongAddBallSprite: _steps_pong_add_ball_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_80__["default"],
  pongBounceAround: _steps_pong_bounce_around_ja_png__WEBPACK_IMPORTED_MODULE_81__["default"],
  pongAddPaddle: _steps_pong_add_a_paddle_LTR_gif__WEBPACK_IMPORTED_MODULE_82__["default"],
  pongMoveThePaddle: _steps_pong_move_the_paddle_ja_png__WEBPACK_IMPORTED_MODULE_83__["default"],
  pongSelectBallSprite: _steps_pong_select_ball_LTR_png__WEBPACK_IMPORTED_MODULE_84__["default"],
  pongAddMoreCodeToBall: _steps_pong_add_code_to_ball_ja_png__WEBPACK_IMPORTED_MODULE_85__["default"],
  pongAddAScore: _steps_add_variable_ja_gif__WEBPACK_IMPORTED_MODULE_18__["default"],
  pongChooseScoreFromMenu: _steps_pong_choose_score_ja_png__WEBPACK_IMPORTED_MODULE_86__["default"],
  pongInsertChangeScoreBlock: _steps_pong_insert_change_score_ja_png__WEBPACK_IMPORTED_MODULE_87__["default"],
  pongResetScore: _steps_pong_reset_score_ja_png__WEBPACK_IMPORTED_MODULE_88__["default"],
  pongAddLineSprite: _steps_pong_add_line_LTR_gif__WEBPACK_IMPORTED_MODULE_89__["default"],
  pongGameOver: _steps_pong_game_over_ja_png__WEBPACK_IMPORTED_MODULE_90__["default"],
  // Imagine a World
  imagineTypeWhatYouWant: _steps_imagine_type_what_you_want_ja_png__WEBPACK_IMPORTED_MODULE_91__["default"],
  imagineClickGreenFlag: _steps_imagine_click_green_flag_ja_png__WEBPACK_IMPORTED_MODULE_92__["default"],
  imagineChooseBackdrop: _steps_imagine_choose_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_93__["default"],
  imagineChooseSprite: _steps_imagine_choose_any_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_94__["default"],
  imagineFlyAround: _steps_imagine_fly_around_ja_png__WEBPACK_IMPORTED_MODULE_95__["default"],
  imagineChooseAnotherSprite: _steps_imagine_choose_another_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_96__["default"],
  imagineLeftRight: _steps_imagine_left_right_ja_png__WEBPACK_IMPORTED_MODULE_97__["default"],
  imagineUpDown: _steps_imagine_up_down_ja_png__WEBPACK_IMPORTED_MODULE_98__["default"],
  imagineChangeCostumes: _steps_imagine_change_costumes_ja_png__WEBPACK_IMPORTED_MODULE_99__["default"],
  imagineGlideToPoint: _steps_imagine_glide_to_point_ja_png__WEBPACK_IMPORTED_MODULE_100__["default"],
  imagineGrowShrink: _steps_imagine_grow_shrink_ja_png__WEBPACK_IMPORTED_MODULE_101__["default"],
  imagineChooseAnotherBackdrop: _steps_imagine_choose_another_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_102__["default"],
  imagineSwitchBackdrops: _steps_imagine_switch_backdrops_ja_png__WEBPACK_IMPORTED_MODULE_103__["default"],
  imagineRecordASound: _steps_imagine_record_a_sound_ja_gif__WEBPACK_IMPORTED_MODULE_104__["default"],
  imagineChooseSound: _steps_imagine_choose_sound_ja_png__WEBPACK_IMPORTED_MODULE_105__["default"],
  // Add a Backdrop
  addBackdrop: _steps_add_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_106__["default"],
  // Add Effects
  addEffects: _steps_add_effects_ja_png__WEBPACK_IMPORTED_MODULE_107__["default"],
  // Hide and Show
  hideAndShow: _steps_hide_show_ja_png__WEBPACK_IMPORTED_MODULE_108__["default"],
  // Switch Costumes
  switchCostumes: _steps_switch_costumes_ja_png__WEBPACK_IMPORTED_MODULE_109__["default"],
  // Change Size
  changeSize: _steps_change_size_ja_png__WEBPACK_IMPORTED_MODULE_110__["default"],
  // Spin
  spinTurn: _steps_spin_turn_ja_png__WEBPACK_IMPORTED_MODULE_111__["default"],
  spinPointInDirection: _steps_spin_point_in_direction_ja_png__WEBPACK_IMPORTED_MODULE_112__["default"],
  // Record a Sound
  recordASoundSoundsTab: _steps_record_a_sound_sounds_tab_ja_png__WEBPACK_IMPORTED_MODULE_113__["default"],
  recordASoundClickRecord: _steps_record_a_sound_click_record_ja_png__WEBPACK_IMPORTED_MODULE_114__["default"],
  recordASoundPressRecordButton: _steps_record_a_sound_press_record_button_ja_png__WEBPACK_IMPORTED_MODULE_115__["default"],
  recordASoundChooseSound: _steps_record_a_sound_choose_sound_ja_png__WEBPACK_IMPORTED_MODULE_116__["default"],
  recordASoundPlayYourSound: _steps_record_a_sound_play_your_sound_ja_png__WEBPACK_IMPORTED_MODULE_117__["default"],
  // Use Arrow Keys
  moveArrowKeysLeftRight: _steps_move_arrow_keys_left_right_ja_png__WEBPACK_IMPORTED_MODULE_118__["default"],
  moveArrowKeysUpDown: _steps_move_arrow_keys_up_down_ja_png__WEBPACK_IMPORTED_MODULE_119__["default"],
  // Glide Around
  glideAroundBackAndForth: _steps_glide_around_back_and_forth_ja_png__WEBPACK_IMPORTED_MODULE_120__["default"],
  glideAroundPoint: _steps_glide_around_point_ja_png__WEBPACK_IMPORTED_MODULE_121__["default"],
  // Code a Cartoon
  codeCartoonSaySomething: _steps_code_cartoon_01_say_something_ja_png__WEBPACK_IMPORTED_MODULE_122__["default"],
  codeCartoonAnimate: _steps_code_cartoon_02_animate_ja_png__WEBPACK_IMPORTED_MODULE_123__["default"],
  codeCartoonSelectDifferentCharacter: _steps_code_cartoon_03_select_different_character_LTR_png__WEBPACK_IMPORTED_MODULE_124__["default"],
  codeCartoonUseMinusSign: _steps_code_cartoon_04_use_minus_sign_ja_png__WEBPACK_IMPORTED_MODULE_125__["default"],
  codeCartoonGrowShrink: _steps_code_cartoon_05_grow_shrink_ja_png__WEBPACK_IMPORTED_MODULE_126__["default"],
  codeCartoonSelectDifferentCharacter2: _steps_code_cartoon_06_select_another_different_character_LTR_png__WEBPACK_IMPORTED_MODULE_127__["default"],
  codeCartoonJump: _steps_code_cartoon_07_jump_ja_png__WEBPACK_IMPORTED_MODULE_128__["default"],
  codeCartoonChangeScenes: _steps_code_cartoon_08_change_scenes_ja_png__WEBPACK_IMPORTED_MODULE_129__["default"],
  codeCartoonGlideAround: _steps_code_cartoon_09_glide_around_ja_png__WEBPACK_IMPORTED_MODULE_130__["default"],
  codeCartoonChangeCostumes: _steps_code_cartoon_10_change_costumes_ja_png__WEBPACK_IMPORTED_MODULE_131__["default"],
  codeCartoonChooseMoreCharacters: _steps_code_cartoon_11_choose_more_characters_LTR_png__WEBPACK_IMPORTED_MODULE_132__["default"],
  // Talking Tales
  talesAddExtension: _steps_speech_add_extension_ja_gif__WEBPACK_IMPORTED_MODULE_3__["default"],
  talesChooseSprite: _steps_talking_2_choose_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_133__["default"],
  talesSaySomething: _steps_talking_3_say_something_ja_png__WEBPACK_IMPORTED_MODULE_134__["default"],
  talesAskAnswer: _steps_talking_13_ask_and_answer_ja_png__WEBPACK_IMPORTED_MODULE_144__["default"],
  talesChooseBackdrop: _steps_talking_4_choose_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_135__["default"],
  talesSwitchBackdrop: _steps_talking_5_switch_backdrop_ja_png__WEBPACK_IMPORTED_MODULE_136__["default"],
  talesChooseAnotherSprite: _steps_talking_6_choose_another_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_137__["default"],
  talesMoveAround: _steps_talking_7_move_around_ja_png__WEBPACK_IMPORTED_MODULE_138__["default"],
  talesChooseAnotherBackdrop: _steps_talking_8_choose_another_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_139__["default"],
  talesAnimateTalking: _steps_talking_9_animate_ja_png__WEBPACK_IMPORTED_MODULE_140__["default"],
  talesChooseThirdBackdrop: _steps_talking_10_choose_third_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_141__["default"],
  talesChooseSound: _steps_talking_11_choose_sound_ja_gif__WEBPACK_IMPORTED_MODULE_142__["default"],
  talesDanceMoves: _steps_talking_12_dance_moves_ja_png__WEBPACK_IMPORTED_MODULE_143__["default"]
};


/***/ }),

/***/ "./src/lib/libraries/decks/steps/add-effects.ja.png":
/*!**********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/add-effects.ja.png ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/65ab1d372165b0364206ecc14c6d5054.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/add-variable.ja.gif":
/*!***********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/add-variable.ja.gif ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/eefe1a1b33b8f834772795c76c7c314b.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/animate-char-add-sound.ja.png":
/*!*********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/animate-char-add-sound.ja.png ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/a3054424b4fb02050bb8ff3586d96b8e.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/animate-char-change-color.ja.png":
/*!************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/animate-char-change-color.ja.png ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/15ca90e336eeead5ed240040107a3432.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/animate-char-jump.ja.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/animate-char-jump.ja.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/65076928469935446dd0d0e6d6d0ab38.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/animate-char-move.ja.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/animate-char-move.ja.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/dd63f78ef996b9805f07b55cc3072439.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/animate-char-say-something.ja.png":
/*!*************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/animate-char-say-something.ja.png ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/5970344a746771f4ab3c5b3e8a9d1905.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/animate-char-talk.ja.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/animate-char-talk.ja.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/de8573a9d1d1f1541c3ea3ddf034d81c.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/change-size.ja.png":
/*!**********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/change-size.ja.png ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/c5eb360fa73fe7003c1da235a09a3744.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/chase-game-change-score.ja.png":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/chase-game-change-score.ja.png ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/9e11bf98a99f3bcb9c4e99c262837e26.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/chase-game-move-randomly.ja.png":
/*!***********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/chase-game-move-randomly.ja.png ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/439b9d98962816fd1b874c4ca3ea250f.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/chase-game-play-sound.ja.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/chase-game-play-sound.ja.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/5aecc4a24833a8d7946837b5e4800d33.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/chase-game-right-left.ja.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/chase-game-right-left.ja.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/eb06231f83fb1ed786235bd03116b7af.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/chase-game-up-down.ja.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/chase-game-up-down.ja.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/34443d8d625ac998829ea12999acf831.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/cn-backdrop.ja.png":
/*!**********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/cn-backdrop.ja.png ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/f34236700cf0f02f2ce35fae9735738f.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/cn-collect.ja.png":
/*!*********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/cn-collect.ja.png ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/ef22f5722e9e140325ee15d33daf4d2a.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/cn-glide.ja.png":
/*!*******************************************************!*\
  !*** ./src/lib/libraries/decks/steps/cn-glide.ja.png ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/6f3597e8e08c902183cc23f96c552738.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/cn-say.ja.png":
/*!*****************************************************!*\
  !*** ./src/lib/libraries/decks/steps/cn-say.ja.png ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/a6cde78d4afcb612158dad0c02f89fde.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/cn-score.ja.png":
/*!*******************************************************!*\
  !*** ./src/lib/libraries/decks/steps/cn-score.ja.png ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/cf3f3029f3fc2c3f44276c6b5fcc2b04.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-01-say-something.ja.png":
/*!****************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-01-say-something.ja.png ***!
  \****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/88b38b133f86a4a5ce8dfdfedba5be68.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-02-animate.ja.png":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-02-animate.ja.png ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/7e526e0979ededbfffd149fb6a858a72.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-04-use-minus-sign.ja.png":
/*!*****************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-04-use-minus-sign.ja.png ***!
  \*****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/545db4ada1ee421e167dd943dfdb7887.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-05-grow-shrink.ja.png":
/*!**************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-05-grow-shrink.ja.png ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/75c7c30b272dbc132ebeabb9acdf8f3a.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-07-jump.ja.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-07-jump.ja.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/e968e54db011506c2eb49c00e111d96d.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-08-change-scenes.ja.png":
/*!****************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-08-change-scenes.ja.png ***!
  \****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/db48ea4e8e180e95e82fe74b5c646f64.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-09-glide-around.ja.png":
/*!***************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-09-glide-around.ja.png ***!
  \***************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/1906946ebc92c5609ff074db8866ceae.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-10-change-costumes.ja.png":
/*!******************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-10-change-costumes.ja.png ***!
  \******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/e213e576443a06968e874a060643f2d1.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/fly-flying-heart.ja.png":
/*!***************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/fly-flying-heart.ja.png ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/33dc9ba6d9094eb07faebd874714065f.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/fly-keep-score.ja.png":
/*!*************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/fly-keep-score.ja.png ***!
  \*************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/e09006bc79d00e60390d88567a123c1c.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/fly-make-interactive.ja.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/fly-make-interactive.ja.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/3ee6d00bfac312b9766b83ceb1328f1a.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/fly-move-scenery.ja.png":
/*!***************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/fly-move-scenery.ja.png ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/a8adc01e50fae54bea8670945e725b74.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/fly-say-something.ja.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/fly-say-something.ja.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/13f77c6e605843bc7205f4ff7360bb22.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/fly-switch-costume.ja.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/fly-switch-costume.ja.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/ef9e763e5c7ff4cefb85de169282747a.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/glide-around-back-and-forth.ja.png":
/*!**************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/glide-around-back-and-forth.ja.png ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/96e6aa8d944aaa6b957200b2369f64fa.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/glide-around-point.ja.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/glide-around-point.ja.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/523223838f1d033ab056e159a48a8525.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/hide-show.ja.png":
/*!********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/hide-show.ja.png ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/ca2ad8b9b2ea366b1e26764395fdc1c8.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-change-costumes.ja.png":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-change-costumes.ja.png ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/073c6a7f5038f379f9e2573596cf1754.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-choose-sound.ja.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-choose-sound.ja.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/5f125c386017c27d0c482cf3e1851a9f.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-click-green-flag.ja.png":
/*!***********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-click-green-flag.ja.png ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/853b010c7071d281511685f7b2201ed4.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-fly-around.ja.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-fly-around.ja.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/ad496477c22fdf19adf8323dba1dc6ab.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-glide-to-point.ja.png":
/*!*********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-glide-to-point.ja.png ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/0a83b313afc13cfb90555b1b3e7a3c95.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-grow-shrink.ja.png":
/*!******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-grow-shrink.ja.png ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/0cf4e90d6cba95ce092048787b01b56e.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-left-right.ja.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-left-right.ja.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/7235428f01cf82496c33a104c52da79c.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-record-a-sound.ja.gif":
/*!*********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-record-a-sound.ja.gif ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/23f2783c089921d04a8cdc1780a3b935.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-switch-backdrops.ja.png":
/*!***********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-switch-backdrops.ja.png ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/e96022b3128bcbb78fbceb52f953e545.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-type-what-you-want.ja.png":
/*!*************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-type-what-you-want.ja.png ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/fb0d1e3d8244ccb2e5aac1df5c0a050b.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-up-down.ja.png":
/*!**************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-up-down.ja.png ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/403e384eb08ae6a9871c089a9711dbf8.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/intro-1-move.ja.gif":
/*!***********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/intro-1-move.ja.gif ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/93e88badfb566c07833ebf97a6d811a5.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/intro-2-say.ja.gif":
/*!**********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/intro-2-say.ja.gif ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/93f5bd705a6cbbdd07220a14ab65d546.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/intro-3-green-flag.ja.gif":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/intro-3-green-flag.ja.gif ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/a9657cf70e7027065b404a39acf3753b.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/move-arrow-keys-left-right.ja.png":
/*!*************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/move-arrow-keys-left-right.ja.png ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/a00bf9025012ede35642a334da2dbd2a.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/move-arrow-keys-up-down.ja.png":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/move-arrow-keys-up-down.ja.png ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/0da996ca9bb0974fcf003c1be3ea4fe6.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/music-make-beat.ja.png":
/*!**************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/music-make-beat.ja.png ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/c0d26a066b8273d910fa3907db29ce29.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/music-make-beatbox.ja.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/music-make-beatbox.ja.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/4cd4a0ee173b6c24448f288bc7588628.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/music-make-song.ja.png":
/*!**************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/music-make-song.ja.png ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/79c8b811f79a6b17b019eeb5ff8c6c0c.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/music-play-sound.ja.png":
/*!***************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/music-play-sound.ja.png ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/a10f9cd5de675406c45d7d25aa9a0efc.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/name-change-color.ja.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/name-change-color.ja.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/fc5f265895e97ae1b99b4747d8171758.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/name-grow.ja.png":
/*!********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/name-grow.ja.png ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/6a19347affc7daaf10876582978616c2.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/name-play-sound.ja.png":
/*!**************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/name-play-sound.ja.png ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/3c15e3076078813b2262dc4559cad0e2.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/name-spin.ja.png":
/*!********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/name-spin.ja.png ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/de394e55d3e0159f44e228852066c196.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-add-code-to-ball.ja.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-add-code-to-ball.ja.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/66b24423e6185e24e338dd3d7fdf4659.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-bounce-around.ja.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-bounce-around.ja.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/0b014953f5426e425c8c638e9ce826f4.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-choose-score.ja.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-choose-score.ja.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/bb3244ce3c74f4de7d6b443296337eba.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-game-over.ja.png":
/*!*************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-game-over.ja.png ***!
  \*************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/3a33a7f9689d4e5498016d9d08842628.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-insert-change-score.ja.png":
/*!***********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-insert-change-score.ja.png ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/8a7dace65747bf3ff175adf44e3d23ec.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-move-the-paddle.ja.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-move-the-paddle.ja.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/321d6faa38c937bd25b372d848dceb06.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-reset-score.ja.png":
/*!***************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-reset-score.ja.png ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/d022b5e61b38354af45e4dbae82355c8.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pop-game-change-color.ja.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pop-game-change-color.ja.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/32c5d697919fc7eaccdcd7c3d58e9406.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pop-game-change-score.ja.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pop-game-change-score.ja.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/f8f446af42fa1fd1a637e99583098a0b.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pop-game-play-sound.ja.png":
/*!******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pop-game-play-sound.ja.png ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/5f79747d4a859c461d971ea816d44048.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pop-game-random-position.ja.png":
/*!***********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pop-game-random-position.ja.png ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/203016fcc4d3dac025eeb5e46767d40e.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pop-game-reset-score.ja.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pop-game-reset-score.ja.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/a784110cc9dc16cec29eab6d50e29b73.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/record-a-sound-choose-sound.ja.png":
/*!**************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/record-a-sound-choose-sound.ja.png ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/0e32b0a65547d68350cedf2108adb8f5.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/record-a-sound-click-record.ja.png":
/*!**************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/record-a-sound-click-record.ja.png ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/257456e7b806842ea879fa4779bc6ede.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/record-a-sound-play-your-sound.ja.png":
/*!*****************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/record-a-sound-play-your-sound.ja.png ***!
  \*****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/16e4d6d19cb4406bc94444530a2c6633.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/record-a-sound-press-record-button.ja.png":
/*!*********************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/record-a-sound-press-record-button.ja.png ***!
  \*********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/938f4fe8fd4168a5dfc51a448eeb19c4.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/record-a-sound-sounds-tab.ja.png":
/*!************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/record-a-sound-sounds-tab.ja.png ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/5fcc5348615a344ca34609400749a5d6.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-add-extension.ja.gif":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-add-extension.ja.gif ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/146ee09e314aec433d9f11892bdceb2a.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-change-color.ja.png":
/*!******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-change-color.ja.png ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/b2ed37a7cb79b52f0bc66e7864519cb4.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-grow-shrink.ja.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-grow-shrink.ja.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/0ce58c2d774109e8bf4303f897ff056e.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-move-around.ja.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-move-around.ja.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/c14b6668c2fb46e465171e417ffb10af.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-say-something.ja.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-say-something.ja.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/ae0be16333fe5394e9642a93ff6d7b39.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-set-voice.ja.png":
/*!***************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-set-voice.ja.png ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/6a516504338645d58d0f5fdb95814f64.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-song.ja.png":
/*!**********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-song.ja.png ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/53563ba5f29495bae5c8a97a34ac16af.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-spin.ja.png":
/*!**********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-spin.ja.png ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/846bd321ef4f6d8b7889da11a26ceff7.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/spin-point-in-direction.ja.png":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/spin-point-in-direction.ja.png ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/d7eb468c0007cbdc69daf82d4f56a3ae.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/spin-turn.ja.png":
/*!********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/spin-turn.ja.png ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/863ff6959492e2a3f09ff2df12e1917a.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/story-conversation.ja.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/story-conversation.ja.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/b5dfbbcf68a4d7b78eb9543087e42d93.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/story-flip.ja.gif":
/*!*********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/story-flip.ja.gif ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/05af09814357208f97eb5c98b4738da9.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/story-hide-character.ja.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/story-hide-character.ja.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/643d30f2243757c5bb6ae94695341164.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/story-say-something.ja.png":
/*!******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/story-say-something.ja.png ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/b82e0b010042cf02c49587d35a2a2235.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/story-show-character.ja.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/story-show-character.ja.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/d0213f6fb317bb4cec02aae00616053f.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/story-switch-backdrop.ja.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/story-switch-backdrop.ja.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/4dec0d12f2c093d45033377cf5879031.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/switch-costumes.ja.png":
/*!**************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/switch-costumes.ja.png ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/ab09795a0477290ebec976c189bac209.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-11-choose-sound.ja.gif":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-11-choose-sound.ja.gif ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/51d3481d4ff3ce7d0539f2300b8b6d89.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-12-dance-moves.ja.png":
/*!*********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-12-dance-moves.ja.png ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/d454bda76c67224752a18aa2d9e4f41a.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-13-ask-and-answer.ja.png":
/*!************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-13-ask-and-answer.ja.png ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/f3feefaa936882602b3f29d82aaaa6d4.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-3-say-something.ja.png":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-3-say-something.ja.png ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/91d1bdffaa8e970b3b827999d6182d07.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-5-switch-backdrop.ja.png":
/*!************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-5-switch-backdrop.ja.png ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/4512813ba51c289096704d3c034c4534.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-7-move-around.ja.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-7-move-around.ja.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/0ffd01af977c0f1d4fba24b727ce7cbf.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-9-animate.ja.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-9-animate.ja.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/dbea9b88d9ed70704ef15a69737f8171.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/video-add-extension.ja.gif":
/*!******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/video-add-extension.ja.gif ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/4efa7f65f48eca9dfcc9fe2bc624b845.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/video-animate.ja.png":
/*!************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/video-animate.ja.png ***!
  \************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/3bb500dcd362dfd4887c6817dcbd961d.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/video-pet.ja.png":
/*!********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/video-pet.ja.png ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/30c4b07fea1f54514fa36ea73a4c3eb7.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/video-pop.ja.png":
/*!********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/video-pop.ja.png ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/a536d7e6abea4620179a6d56d9cb7ecf.png");

/***/ })

}]);
//# sourceMappingURL=ja-steps.js.map