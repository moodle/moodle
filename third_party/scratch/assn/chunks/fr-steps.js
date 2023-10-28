(window["webpackJsonpGUI"] = window["webpackJsonpGUI"] || []).push([["fr-steps"],{

/***/ "./src/lib/libraries/decks/fr-steps.js":
/*!*********************************************!*\
  !*** ./src/lib/libraries/decks/fr-steps.js ***!
  \*********************************************/
/*! exports provided: frImages */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "frImages", function() { return frImages; });
/* harmony import */ var _steps_intro_1_move_fr_gif__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./steps/intro-1-move.fr.gif */ "./src/lib/libraries/decks/steps/intro-1-move.fr.gif");
/* harmony import */ var _steps_intro_2_say_fr_gif__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./steps/intro-2-say.fr.gif */ "./src/lib/libraries/decks/steps/intro-2-say.fr.gif");
/* harmony import */ var _steps_intro_3_green_flag_fr_gif__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./steps/intro-3-green-flag.fr.gif */ "./src/lib/libraries/decks/steps/intro-3-green-flag.fr.gif");
/* harmony import */ var _steps_speech_add_extension_fr_gif__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./steps/speech-add-extension.fr.gif */ "./src/lib/libraries/decks/steps/speech-add-extension.fr.gif");
/* harmony import */ var _steps_speech_say_something_fr_png__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./steps/speech-say-something.fr.png */ "./src/lib/libraries/decks/steps/speech-say-something.fr.png");
/* harmony import */ var _steps_speech_set_voice_fr_png__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./steps/speech-set-voice.fr.png */ "./src/lib/libraries/decks/steps/speech-set-voice.fr.png");
/* harmony import */ var _steps_speech_move_around_fr_png__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./steps/speech-move-around.fr.png */ "./src/lib/libraries/decks/steps/speech-move-around.fr.png");
/* harmony import */ var _steps_pick_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./steps/pick-backdrop.LTR.gif */ "./src/lib/libraries/decks/steps/pick-backdrop.LTR.gif");
/* harmony import */ var _steps_speech_add_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./steps/speech-add-sprite.LTR.gif */ "./src/lib/libraries/decks/steps/speech-add-sprite.LTR.gif");
/* harmony import */ var _steps_speech_song_fr_png__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./steps/speech-song.fr.png */ "./src/lib/libraries/decks/steps/speech-song.fr.png");
/* harmony import */ var _steps_speech_change_color_fr_png__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./steps/speech-change-color.fr.png */ "./src/lib/libraries/decks/steps/speech-change-color.fr.png");
/* harmony import */ var _steps_speech_spin_fr_png__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./steps/speech-spin.fr.png */ "./src/lib/libraries/decks/steps/speech-spin.fr.png");
/* harmony import */ var _steps_speech_grow_shrink_fr_png__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./steps/speech-grow-shrink.fr.png */ "./src/lib/libraries/decks/steps/speech-grow-shrink.fr.png");
/* harmony import */ var _steps_cn_show_character_LTR_gif__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./steps/cn-show-character.LTR.gif */ "./src/lib/libraries/decks/steps/cn-show-character.LTR.gif");
/* harmony import */ var _steps_cn_say_fr_png__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./steps/cn-say.fr.png */ "./src/lib/libraries/decks/steps/cn-say.fr.png");
/* harmony import */ var _steps_cn_glide_fr_png__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./steps/cn-glide.fr.png */ "./src/lib/libraries/decks/steps/cn-glide.fr.png");
/* harmony import */ var _steps_cn_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./steps/cn-pick-sprite.LTR.gif */ "./src/lib/libraries/decks/steps/cn-pick-sprite.LTR.gif");
/* harmony import */ var _steps_cn_collect_fr_png__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./steps/cn-collect.fr.png */ "./src/lib/libraries/decks/steps/cn-collect.fr.png");
/* harmony import */ var _steps_add_variable_fr_gif__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./steps/add-variable.fr.gif */ "./src/lib/libraries/decks/steps/add-variable.fr.gif");
/* harmony import */ var _steps_cn_score_fr_png__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./steps/cn-score.fr.png */ "./src/lib/libraries/decks/steps/cn-score.fr.png");
/* harmony import */ var _steps_cn_backdrop_fr_png__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./steps/cn-backdrop.fr.png */ "./src/lib/libraries/decks/steps/cn-backdrop.fr.png");
/* harmony import */ var _steps_add_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ./steps/add-sprite.LTR.gif */ "./src/lib/libraries/decks/steps/add-sprite.LTR.gif");
/* harmony import */ var _steps_name_pick_letter_LTR_gif__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! ./steps/name-pick-letter.LTR.gif */ "./src/lib/libraries/decks/steps/name-pick-letter.LTR.gif");
/* harmony import */ var _steps_name_play_sound_fr_png__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! ./steps/name-play-sound.fr.png */ "./src/lib/libraries/decks/steps/name-play-sound.fr.png");
/* harmony import */ var _steps_name_pick_letter2_LTR_gif__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! ./steps/name-pick-letter2.LTR.gif */ "./src/lib/libraries/decks/steps/name-pick-letter2.LTR.gif");
/* harmony import */ var _steps_name_change_color_fr_png__WEBPACK_IMPORTED_MODULE_25__ = __webpack_require__(/*! ./steps/name-change-color.fr.png */ "./src/lib/libraries/decks/steps/name-change-color.fr.png");
/* harmony import */ var _steps_name_spin_fr_png__WEBPACK_IMPORTED_MODULE_26__ = __webpack_require__(/*! ./steps/name-spin.fr.png */ "./src/lib/libraries/decks/steps/name-spin.fr.png");
/* harmony import */ var _steps_name_grow_fr_png__WEBPACK_IMPORTED_MODULE_27__ = __webpack_require__(/*! ./steps/name-grow.fr.png */ "./src/lib/libraries/decks/steps/name-grow.fr.png");
/* harmony import */ var _steps_music_pick_instrument_LTR_gif__WEBPACK_IMPORTED_MODULE_28__ = __webpack_require__(/*! ./steps/music-pick-instrument.LTR.gif */ "./src/lib/libraries/decks/steps/music-pick-instrument.LTR.gif");
/* harmony import */ var _steps_music_play_sound_fr_png__WEBPACK_IMPORTED_MODULE_29__ = __webpack_require__(/*! ./steps/music-play-sound.fr.png */ "./src/lib/libraries/decks/steps/music-play-sound.fr.png");
/* harmony import */ var _steps_music_make_song_fr_png__WEBPACK_IMPORTED_MODULE_30__ = __webpack_require__(/*! ./steps/music-make-song.fr.png */ "./src/lib/libraries/decks/steps/music-make-song.fr.png");
/* harmony import */ var _steps_music_make_beat_fr_png__WEBPACK_IMPORTED_MODULE_31__ = __webpack_require__(/*! ./steps/music-make-beat.fr.png */ "./src/lib/libraries/decks/steps/music-make-beat.fr.png");
/* harmony import */ var _steps_music_make_beatbox_fr_png__WEBPACK_IMPORTED_MODULE_32__ = __webpack_require__(/*! ./steps/music-make-beatbox.fr.png */ "./src/lib/libraries/decks/steps/music-make-beatbox.fr.png");
/* harmony import */ var _steps_chase_game_add_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_33__ = __webpack_require__(/*! ./steps/chase-game-add-backdrop.LTR.gif */ "./src/lib/libraries/decks/steps/chase-game-add-backdrop.LTR.gif");
/* harmony import */ var _steps_chase_game_add_sprite1_LTR_gif__WEBPACK_IMPORTED_MODULE_34__ = __webpack_require__(/*! ./steps/chase-game-add-sprite1.LTR.gif */ "./src/lib/libraries/decks/steps/chase-game-add-sprite1.LTR.gif");
/* harmony import */ var _steps_chase_game_right_left_fr_png__WEBPACK_IMPORTED_MODULE_35__ = __webpack_require__(/*! ./steps/chase-game-right-left.fr.png */ "./src/lib/libraries/decks/steps/chase-game-right-left.fr.png");
/* harmony import */ var _steps_chase_game_up_down_fr_png__WEBPACK_IMPORTED_MODULE_36__ = __webpack_require__(/*! ./steps/chase-game-up-down.fr.png */ "./src/lib/libraries/decks/steps/chase-game-up-down.fr.png");
/* harmony import */ var _steps_chase_game_add_sprite2_LTR_gif__WEBPACK_IMPORTED_MODULE_37__ = __webpack_require__(/*! ./steps/chase-game-add-sprite2.LTR.gif */ "./src/lib/libraries/decks/steps/chase-game-add-sprite2.LTR.gif");
/* harmony import */ var _steps_chase_game_move_randomly_fr_png__WEBPACK_IMPORTED_MODULE_38__ = __webpack_require__(/*! ./steps/chase-game-move-randomly.fr.png */ "./src/lib/libraries/decks/steps/chase-game-move-randomly.fr.png");
/* harmony import */ var _steps_chase_game_play_sound_fr_png__WEBPACK_IMPORTED_MODULE_39__ = __webpack_require__(/*! ./steps/chase-game-play-sound.fr.png */ "./src/lib/libraries/decks/steps/chase-game-play-sound.fr.png");
/* harmony import */ var _steps_chase_game_change_score_fr_png__WEBPACK_IMPORTED_MODULE_40__ = __webpack_require__(/*! ./steps/chase-game-change-score.fr.png */ "./src/lib/libraries/decks/steps/chase-game-change-score.fr.png");
/* harmony import */ var _steps_pop_game_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_41__ = __webpack_require__(/*! ./steps/pop-game-pick-sprite.LTR.gif */ "./src/lib/libraries/decks/steps/pop-game-pick-sprite.LTR.gif");
/* harmony import */ var _steps_pop_game_play_sound_fr_png__WEBPACK_IMPORTED_MODULE_42__ = __webpack_require__(/*! ./steps/pop-game-play-sound.fr.png */ "./src/lib/libraries/decks/steps/pop-game-play-sound.fr.png");
/* harmony import */ var _steps_pop_game_change_score_fr_png__WEBPACK_IMPORTED_MODULE_43__ = __webpack_require__(/*! ./steps/pop-game-change-score.fr.png */ "./src/lib/libraries/decks/steps/pop-game-change-score.fr.png");
/* harmony import */ var _steps_pop_game_random_position_fr_png__WEBPACK_IMPORTED_MODULE_44__ = __webpack_require__(/*! ./steps/pop-game-random-position.fr.png */ "./src/lib/libraries/decks/steps/pop-game-random-position.fr.png");
/* harmony import */ var _steps_pop_game_change_color_fr_png__WEBPACK_IMPORTED_MODULE_45__ = __webpack_require__(/*! ./steps/pop-game-change-color.fr.png */ "./src/lib/libraries/decks/steps/pop-game-change-color.fr.png");
/* harmony import */ var _steps_pop_game_reset_score_fr_png__WEBPACK_IMPORTED_MODULE_46__ = __webpack_require__(/*! ./steps/pop-game-reset-score.fr.png */ "./src/lib/libraries/decks/steps/pop-game-reset-score.fr.png");
/* harmony import */ var _steps_animate_char_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_47__ = __webpack_require__(/*! ./steps/animate-char-pick-sprite.LTR.gif */ "./src/lib/libraries/decks/steps/animate-char-pick-sprite.LTR.gif");
/* harmony import */ var _steps_animate_char_say_something_fr_png__WEBPACK_IMPORTED_MODULE_48__ = __webpack_require__(/*! ./steps/animate-char-say-something.fr.png */ "./src/lib/libraries/decks/steps/animate-char-say-something.fr.png");
/* harmony import */ var _steps_animate_char_add_sound_fr_png__WEBPACK_IMPORTED_MODULE_49__ = __webpack_require__(/*! ./steps/animate-char-add-sound.fr.png */ "./src/lib/libraries/decks/steps/animate-char-add-sound.fr.png");
/* harmony import */ var _steps_animate_char_talk_fr_png__WEBPACK_IMPORTED_MODULE_50__ = __webpack_require__(/*! ./steps/animate-char-talk.fr.png */ "./src/lib/libraries/decks/steps/animate-char-talk.fr.png");
/* harmony import */ var _steps_animate_char_move_fr_png__WEBPACK_IMPORTED_MODULE_51__ = __webpack_require__(/*! ./steps/animate-char-move.fr.png */ "./src/lib/libraries/decks/steps/animate-char-move.fr.png");
/* harmony import */ var _steps_animate_char_jump_fr_png__WEBPACK_IMPORTED_MODULE_52__ = __webpack_require__(/*! ./steps/animate-char-jump.fr.png */ "./src/lib/libraries/decks/steps/animate-char-jump.fr.png");
/* harmony import */ var _steps_animate_char_change_color_fr_png__WEBPACK_IMPORTED_MODULE_53__ = __webpack_require__(/*! ./steps/animate-char-change-color.fr.png */ "./src/lib/libraries/decks/steps/animate-char-change-color.fr.png");
/* harmony import */ var _steps_story_pick_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_54__ = __webpack_require__(/*! ./steps/story-pick-backdrop.LTR.gif */ "./src/lib/libraries/decks/steps/story-pick-backdrop.LTR.gif");
/* harmony import */ var _steps_story_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_55__ = __webpack_require__(/*! ./steps/story-pick-sprite.LTR.gif */ "./src/lib/libraries/decks/steps/story-pick-sprite.LTR.gif");
/* harmony import */ var _steps_story_say_something_fr_png__WEBPACK_IMPORTED_MODULE_56__ = __webpack_require__(/*! ./steps/story-say-something.fr.png */ "./src/lib/libraries/decks/steps/story-say-something.fr.png");
/* harmony import */ var _steps_story_pick_sprite2_LTR_gif__WEBPACK_IMPORTED_MODULE_57__ = __webpack_require__(/*! ./steps/story-pick-sprite2.LTR.gif */ "./src/lib/libraries/decks/steps/story-pick-sprite2.LTR.gif");
/* harmony import */ var _steps_story_flip_fr_gif__WEBPACK_IMPORTED_MODULE_58__ = __webpack_require__(/*! ./steps/story-flip.fr.gif */ "./src/lib/libraries/decks/steps/story-flip.fr.gif");
/* harmony import */ var _steps_story_conversation_fr_png__WEBPACK_IMPORTED_MODULE_59__ = __webpack_require__(/*! ./steps/story-conversation.fr.png */ "./src/lib/libraries/decks/steps/story-conversation.fr.png");
/* harmony import */ var _steps_story_pick_backdrop2_LTR_gif__WEBPACK_IMPORTED_MODULE_60__ = __webpack_require__(/*! ./steps/story-pick-backdrop2.LTR.gif */ "./src/lib/libraries/decks/steps/story-pick-backdrop2.LTR.gif");
/* harmony import */ var _steps_story_switch_backdrop_fr_png__WEBPACK_IMPORTED_MODULE_61__ = __webpack_require__(/*! ./steps/story-switch-backdrop.fr.png */ "./src/lib/libraries/decks/steps/story-switch-backdrop.fr.png");
/* harmony import */ var _steps_story_hide_character_fr_png__WEBPACK_IMPORTED_MODULE_62__ = __webpack_require__(/*! ./steps/story-hide-character.fr.png */ "./src/lib/libraries/decks/steps/story-hide-character.fr.png");
/* harmony import */ var _steps_story_show_character_fr_png__WEBPACK_IMPORTED_MODULE_63__ = __webpack_require__(/*! ./steps/story-show-character.fr.png */ "./src/lib/libraries/decks/steps/story-show-character.fr.png");
/* harmony import */ var _steps_video_add_extension_fr_gif__WEBPACK_IMPORTED_MODULE_64__ = __webpack_require__(/*! ./steps/video-add-extension.fr.gif */ "./src/lib/libraries/decks/steps/video-add-extension.fr.gif");
/* harmony import */ var _steps_video_pet_fr_png__WEBPACK_IMPORTED_MODULE_65__ = __webpack_require__(/*! ./steps/video-pet.fr.png */ "./src/lib/libraries/decks/steps/video-pet.fr.png");
/* harmony import */ var _steps_video_animate_fr_png__WEBPACK_IMPORTED_MODULE_66__ = __webpack_require__(/*! ./steps/video-animate.fr.png */ "./src/lib/libraries/decks/steps/video-animate.fr.png");
/* harmony import */ var _steps_video_pop_fr_png__WEBPACK_IMPORTED_MODULE_67__ = __webpack_require__(/*! ./steps/video-pop.fr.png */ "./src/lib/libraries/decks/steps/video-pop.fr.png");
/* harmony import */ var _steps_fly_choose_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_68__ = __webpack_require__(/*! ./steps/fly-choose-backdrop.LTR.gif */ "./src/lib/libraries/decks/steps/fly-choose-backdrop.LTR.gif");
/* harmony import */ var _steps_fly_choose_character_LTR_png__WEBPACK_IMPORTED_MODULE_69__ = __webpack_require__(/*! ./steps/fly-choose-character.LTR.png */ "./src/lib/libraries/decks/steps/fly-choose-character.LTR.png");
/* harmony import */ var _steps_fly_say_something_fr_png__WEBPACK_IMPORTED_MODULE_70__ = __webpack_require__(/*! ./steps/fly-say-something.fr.png */ "./src/lib/libraries/decks/steps/fly-say-something.fr.png");
/* harmony import */ var _steps_fly_make_interactive_fr_png__WEBPACK_IMPORTED_MODULE_71__ = __webpack_require__(/*! ./steps/fly-make-interactive.fr.png */ "./src/lib/libraries/decks/steps/fly-make-interactive.fr.png");
/* harmony import */ var _steps_fly_object_to_collect_LTR_png__WEBPACK_IMPORTED_MODULE_72__ = __webpack_require__(/*! ./steps/fly-object-to-collect.LTR.png */ "./src/lib/libraries/decks/steps/fly-object-to-collect.LTR.png");
/* harmony import */ var _steps_fly_flying_heart_fr_png__WEBPACK_IMPORTED_MODULE_73__ = __webpack_require__(/*! ./steps/fly-flying-heart.fr.png */ "./src/lib/libraries/decks/steps/fly-flying-heart.fr.png");
/* harmony import */ var _steps_fly_select_flyer_LTR_png__WEBPACK_IMPORTED_MODULE_74__ = __webpack_require__(/*! ./steps/fly-select-flyer.LTR.png */ "./src/lib/libraries/decks/steps/fly-select-flyer.LTR.png");
/* harmony import */ var _steps_fly_keep_score_fr_png__WEBPACK_IMPORTED_MODULE_75__ = __webpack_require__(/*! ./steps/fly-keep-score.fr.png */ "./src/lib/libraries/decks/steps/fly-keep-score.fr.png");
/* harmony import */ var _steps_fly_choose_scenery_LTR_gif__WEBPACK_IMPORTED_MODULE_76__ = __webpack_require__(/*! ./steps/fly-choose-scenery.LTR.gif */ "./src/lib/libraries/decks/steps/fly-choose-scenery.LTR.gif");
/* harmony import */ var _steps_fly_move_scenery_fr_png__WEBPACK_IMPORTED_MODULE_77__ = __webpack_require__(/*! ./steps/fly-move-scenery.fr.png */ "./src/lib/libraries/decks/steps/fly-move-scenery.fr.png");
/* harmony import */ var _steps_fly_switch_costume_fr_png__WEBPACK_IMPORTED_MODULE_78__ = __webpack_require__(/*! ./steps/fly-switch-costume.fr.png */ "./src/lib/libraries/decks/steps/fly-switch-costume.fr.png");
/* harmony import */ var _steps_pong_add_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_79__ = __webpack_require__(/*! ./steps/pong-add-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/pong-add-backdrop.LTR.png");
/* harmony import */ var _steps_pong_add_ball_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_80__ = __webpack_require__(/*! ./steps/pong-add-ball-sprite.LTR.png */ "./src/lib/libraries/decks/steps/pong-add-ball-sprite.LTR.png");
/* harmony import */ var _steps_pong_bounce_around_fr_png__WEBPACK_IMPORTED_MODULE_81__ = __webpack_require__(/*! ./steps/pong-bounce-around.fr.png */ "./src/lib/libraries/decks/steps/pong-bounce-around.fr.png");
/* harmony import */ var _steps_pong_add_a_paddle_LTR_gif__WEBPACK_IMPORTED_MODULE_82__ = __webpack_require__(/*! ./steps/pong-add-a-paddle.LTR.gif */ "./src/lib/libraries/decks/steps/pong-add-a-paddle.LTR.gif");
/* harmony import */ var _steps_pong_move_the_paddle_fr_png__WEBPACK_IMPORTED_MODULE_83__ = __webpack_require__(/*! ./steps/pong-move-the-paddle.fr.png */ "./src/lib/libraries/decks/steps/pong-move-the-paddle.fr.png");
/* harmony import */ var _steps_pong_select_ball_LTR_png__WEBPACK_IMPORTED_MODULE_84__ = __webpack_require__(/*! ./steps/pong-select-ball.LTR.png */ "./src/lib/libraries/decks/steps/pong-select-ball.LTR.png");
/* harmony import */ var _steps_pong_add_code_to_ball_fr_png__WEBPACK_IMPORTED_MODULE_85__ = __webpack_require__(/*! ./steps/pong-add-code-to-ball.fr.png */ "./src/lib/libraries/decks/steps/pong-add-code-to-ball.fr.png");
/* harmony import */ var _steps_pong_choose_score_fr_png__WEBPACK_IMPORTED_MODULE_86__ = __webpack_require__(/*! ./steps/pong-choose-score.fr.png */ "./src/lib/libraries/decks/steps/pong-choose-score.fr.png");
/* harmony import */ var _steps_pong_insert_change_score_fr_png__WEBPACK_IMPORTED_MODULE_87__ = __webpack_require__(/*! ./steps/pong-insert-change-score.fr.png */ "./src/lib/libraries/decks/steps/pong-insert-change-score.fr.png");
/* harmony import */ var _steps_pong_reset_score_fr_png__WEBPACK_IMPORTED_MODULE_88__ = __webpack_require__(/*! ./steps/pong-reset-score.fr.png */ "./src/lib/libraries/decks/steps/pong-reset-score.fr.png");
/* harmony import */ var _steps_pong_add_line_LTR_gif__WEBPACK_IMPORTED_MODULE_89__ = __webpack_require__(/*! ./steps/pong-add-line.LTR.gif */ "./src/lib/libraries/decks/steps/pong-add-line.LTR.gif");
/* harmony import */ var _steps_pong_game_over_fr_png__WEBPACK_IMPORTED_MODULE_90__ = __webpack_require__(/*! ./steps/pong-game-over.fr.png */ "./src/lib/libraries/decks/steps/pong-game-over.fr.png");
/* harmony import */ var _steps_imagine_type_what_you_want_fr_png__WEBPACK_IMPORTED_MODULE_91__ = __webpack_require__(/*! ./steps/imagine-type-what-you-want.fr.png */ "./src/lib/libraries/decks/steps/imagine-type-what-you-want.fr.png");
/* harmony import */ var _steps_imagine_click_green_flag_fr_png__WEBPACK_IMPORTED_MODULE_92__ = __webpack_require__(/*! ./steps/imagine-click-green-flag.fr.png */ "./src/lib/libraries/decks/steps/imagine-click-green-flag.fr.png");
/* harmony import */ var _steps_imagine_choose_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_93__ = __webpack_require__(/*! ./steps/imagine-choose-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/imagine-choose-backdrop.LTR.png");
/* harmony import */ var _steps_imagine_choose_any_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_94__ = __webpack_require__(/*! ./steps/imagine-choose-any-sprite.LTR.png */ "./src/lib/libraries/decks/steps/imagine-choose-any-sprite.LTR.png");
/* harmony import */ var _steps_imagine_fly_around_fr_png__WEBPACK_IMPORTED_MODULE_95__ = __webpack_require__(/*! ./steps/imagine-fly-around.fr.png */ "./src/lib/libraries/decks/steps/imagine-fly-around.fr.png");
/* harmony import */ var _steps_imagine_choose_another_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_96__ = __webpack_require__(/*! ./steps/imagine-choose-another-sprite.LTR.png */ "./src/lib/libraries/decks/steps/imagine-choose-another-sprite.LTR.png");
/* harmony import */ var _steps_imagine_left_right_fr_png__WEBPACK_IMPORTED_MODULE_97__ = __webpack_require__(/*! ./steps/imagine-left-right.fr.png */ "./src/lib/libraries/decks/steps/imagine-left-right.fr.png");
/* harmony import */ var _steps_imagine_up_down_fr_png__WEBPACK_IMPORTED_MODULE_98__ = __webpack_require__(/*! ./steps/imagine-up-down.fr.png */ "./src/lib/libraries/decks/steps/imagine-up-down.fr.png");
/* harmony import */ var _steps_imagine_change_costumes_fr_png__WEBPACK_IMPORTED_MODULE_99__ = __webpack_require__(/*! ./steps/imagine-change-costumes.fr.png */ "./src/lib/libraries/decks/steps/imagine-change-costumes.fr.png");
/* harmony import */ var _steps_imagine_glide_to_point_fr_png__WEBPACK_IMPORTED_MODULE_100__ = __webpack_require__(/*! ./steps/imagine-glide-to-point.fr.png */ "./src/lib/libraries/decks/steps/imagine-glide-to-point.fr.png");
/* harmony import */ var _steps_imagine_grow_shrink_fr_png__WEBPACK_IMPORTED_MODULE_101__ = __webpack_require__(/*! ./steps/imagine-grow-shrink.fr.png */ "./src/lib/libraries/decks/steps/imagine-grow-shrink.fr.png");
/* harmony import */ var _steps_imagine_choose_another_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_102__ = __webpack_require__(/*! ./steps/imagine-choose-another-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/imagine-choose-another-backdrop.LTR.png");
/* harmony import */ var _steps_imagine_switch_backdrops_fr_png__WEBPACK_IMPORTED_MODULE_103__ = __webpack_require__(/*! ./steps/imagine-switch-backdrops.fr.png */ "./src/lib/libraries/decks/steps/imagine-switch-backdrops.fr.png");
/* harmony import */ var _steps_imagine_record_a_sound_fr_gif__WEBPACK_IMPORTED_MODULE_104__ = __webpack_require__(/*! ./steps/imagine-record-a-sound.fr.gif */ "./src/lib/libraries/decks/steps/imagine-record-a-sound.fr.gif");
/* harmony import */ var _steps_imagine_choose_sound_fr_png__WEBPACK_IMPORTED_MODULE_105__ = __webpack_require__(/*! ./steps/imagine-choose-sound.fr.png */ "./src/lib/libraries/decks/steps/imagine-choose-sound.fr.png");
/* harmony import */ var _steps_add_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_106__ = __webpack_require__(/*! ./steps/add-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/add-backdrop.LTR.png");
/* harmony import */ var _steps_add_effects_fr_png__WEBPACK_IMPORTED_MODULE_107__ = __webpack_require__(/*! ./steps/add-effects.fr.png */ "./src/lib/libraries/decks/steps/add-effects.fr.png");
/* harmony import */ var _steps_hide_show_fr_png__WEBPACK_IMPORTED_MODULE_108__ = __webpack_require__(/*! ./steps/hide-show.fr.png */ "./src/lib/libraries/decks/steps/hide-show.fr.png");
/* harmony import */ var _steps_switch_costumes_fr_png__WEBPACK_IMPORTED_MODULE_109__ = __webpack_require__(/*! ./steps/switch-costumes.fr.png */ "./src/lib/libraries/decks/steps/switch-costumes.fr.png");
/* harmony import */ var _steps_change_size_fr_png__WEBPACK_IMPORTED_MODULE_110__ = __webpack_require__(/*! ./steps/change-size.fr.png */ "./src/lib/libraries/decks/steps/change-size.fr.png");
/* harmony import */ var _steps_spin_turn_fr_png__WEBPACK_IMPORTED_MODULE_111__ = __webpack_require__(/*! ./steps/spin-turn.fr.png */ "./src/lib/libraries/decks/steps/spin-turn.fr.png");
/* harmony import */ var _steps_spin_point_in_direction_fr_png__WEBPACK_IMPORTED_MODULE_112__ = __webpack_require__(/*! ./steps/spin-point-in-direction.fr.png */ "./src/lib/libraries/decks/steps/spin-point-in-direction.fr.png");
/* harmony import */ var _steps_record_a_sound_sounds_tab_fr_png__WEBPACK_IMPORTED_MODULE_113__ = __webpack_require__(/*! ./steps/record-a-sound-sounds-tab.fr.png */ "./src/lib/libraries/decks/steps/record-a-sound-sounds-tab.fr.png");
/* harmony import */ var _steps_record_a_sound_click_record_fr_png__WEBPACK_IMPORTED_MODULE_114__ = __webpack_require__(/*! ./steps/record-a-sound-click-record.fr.png */ "./src/lib/libraries/decks/steps/record-a-sound-click-record.fr.png");
/* harmony import */ var _steps_record_a_sound_press_record_button_fr_png__WEBPACK_IMPORTED_MODULE_115__ = __webpack_require__(/*! ./steps/record-a-sound-press-record-button.fr.png */ "./src/lib/libraries/decks/steps/record-a-sound-press-record-button.fr.png");
/* harmony import */ var _steps_record_a_sound_choose_sound_fr_png__WEBPACK_IMPORTED_MODULE_116__ = __webpack_require__(/*! ./steps/record-a-sound-choose-sound.fr.png */ "./src/lib/libraries/decks/steps/record-a-sound-choose-sound.fr.png");
/* harmony import */ var _steps_record_a_sound_play_your_sound_fr_png__WEBPACK_IMPORTED_MODULE_117__ = __webpack_require__(/*! ./steps/record-a-sound-play-your-sound.fr.png */ "./src/lib/libraries/decks/steps/record-a-sound-play-your-sound.fr.png");
/* harmony import */ var _steps_move_arrow_keys_left_right_fr_png__WEBPACK_IMPORTED_MODULE_118__ = __webpack_require__(/*! ./steps/move-arrow-keys-left-right.fr.png */ "./src/lib/libraries/decks/steps/move-arrow-keys-left-right.fr.png");
/* harmony import */ var _steps_move_arrow_keys_up_down_fr_png__WEBPACK_IMPORTED_MODULE_119__ = __webpack_require__(/*! ./steps/move-arrow-keys-up-down.fr.png */ "./src/lib/libraries/decks/steps/move-arrow-keys-up-down.fr.png");
/* harmony import */ var _steps_glide_around_back_and_forth_fr_png__WEBPACK_IMPORTED_MODULE_120__ = __webpack_require__(/*! ./steps/glide-around-back-and-forth.fr.png */ "./src/lib/libraries/decks/steps/glide-around-back-and-forth.fr.png");
/* harmony import */ var _steps_glide_around_point_fr_png__WEBPACK_IMPORTED_MODULE_121__ = __webpack_require__(/*! ./steps/glide-around-point.fr.png */ "./src/lib/libraries/decks/steps/glide-around-point.fr.png");
/* harmony import */ var _steps_code_cartoon_01_say_something_fr_png__WEBPACK_IMPORTED_MODULE_122__ = __webpack_require__(/*! ./steps/code-cartoon-01-say-something.fr.png */ "./src/lib/libraries/decks/steps/code-cartoon-01-say-something.fr.png");
/* harmony import */ var _steps_code_cartoon_02_animate_fr_png__WEBPACK_IMPORTED_MODULE_123__ = __webpack_require__(/*! ./steps/code-cartoon-02-animate.fr.png */ "./src/lib/libraries/decks/steps/code-cartoon-02-animate.fr.png");
/* harmony import */ var _steps_code_cartoon_03_select_different_character_LTR_png__WEBPACK_IMPORTED_MODULE_124__ = __webpack_require__(/*! ./steps/code-cartoon-03-select-different-character.LTR.png */ "./src/lib/libraries/decks/steps/code-cartoon-03-select-different-character.LTR.png");
/* harmony import */ var _steps_code_cartoon_04_use_minus_sign_fr_png__WEBPACK_IMPORTED_MODULE_125__ = __webpack_require__(/*! ./steps/code-cartoon-04-use-minus-sign.fr.png */ "./src/lib/libraries/decks/steps/code-cartoon-04-use-minus-sign.fr.png");
/* harmony import */ var _steps_code_cartoon_05_grow_shrink_fr_png__WEBPACK_IMPORTED_MODULE_126__ = __webpack_require__(/*! ./steps/code-cartoon-05-grow-shrink.fr.png */ "./src/lib/libraries/decks/steps/code-cartoon-05-grow-shrink.fr.png");
/* harmony import */ var _steps_code_cartoon_06_select_another_different_character_LTR_png__WEBPACK_IMPORTED_MODULE_127__ = __webpack_require__(/*! ./steps/code-cartoon-06-select-another-different-character.LTR.png */ "./src/lib/libraries/decks/steps/code-cartoon-06-select-another-different-character.LTR.png");
/* harmony import */ var _steps_code_cartoon_07_jump_fr_png__WEBPACK_IMPORTED_MODULE_128__ = __webpack_require__(/*! ./steps/code-cartoon-07-jump.fr.png */ "./src/lib/libraries/decks/steps/code-cartoon-07-jump.fr.png");
/* harmony import */ var _steps_code_cartoon_08_change_scenes_fr_png__WEBPACK_IMPORTED_MODULE_129__ = __webpack_require__(/*! ./steps/code-cartoon-08-change-scenes.fr.png */ "./src/lib/libraries/decks/steps/code-cartoon-08-change-scenes.fr.png");
/* harmony import */ var _steps_code_cartoon_09_glide_around_fr_png__WEBPACK_IMPORTED_MODULE_130__ = __webpack_require__(/*! ./steps/code-cartoon-09-glide-around.fr.png */ "./src/lib/libraries/decks/steps/code-cartoon-09-glide-around.fr.png");
/* harmony import */ var _steps_code_cartoon_10_change_costumes_fr_png__WEBPACK_IMPORTED_MODULE_131__ = __webpack_require__(/*! ./steps/code-cartoon-10-change-costumes.fr.png */ "./src/lib/libraries/decks/steps/code-cartoon-10-change-costumes.fr.png");
/* harmony import */ var _steps_code_cartoon_11_choose_more_characters_LTR_png__WEBPACK_IMPORTED_MODULE_132__ = __webpack_require__(/*! ./steps/code-cartoon-11-choose-more-characters.LTR.png */ "./src/lib/libraries/decks/steps/code-cartoon-11-choose-more-characters.LTR.png");
/* harmony import */ var _steps_talking_2_choose_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_133__ = __webpack_require__(/*! ./steps/talking-2-choose-sprite.LTR.png */ "./src/lib/libraries/decks/steps/talking-2-choose-sprite.LTR.png");
/* harmony import */ var _steps_talking_3_say_something_fr_png__WEBPACK_IMPORTED_MODULE_134__ = __webpack_require__(/*! ./steps/talking-3-say-something.fr.png */ "./src/lib/libraries/decks/steps/talking-3-say-something.fr.png");
/* harmony import */ var _steps_talking_4_choose_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_135__ = __webpack_require__(/*! ./steps/talking-4-choose-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/talking-4-choose-backdrop.LTR.png");
/* harmony import */ var _steps_talking_5_switch_backdrop_fr_png__WEBPACK_IMPORTED_MODULE_136__ = __webpack_require__(/*! ./steps/talking-5-switch-backdrop.fr.png */ "./src/lib/libraries/decks/steps/talking-5-switch-backdrop.fr.png");
/* harmony import */ var _steps_talking_6_choose_another_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_137__ = __webpack_require__(/*! ./steps/talking-6-choose-another-sprite.LTR.png */ "./src/lib/libraries/decks/steps/talking-6-choose-another-sprite.LTR.png");
/* harmony import */ var _steps_talking_7_move_around_fr_png__WEBPACK_IMPORTED_MODULE_138__ = __webpack_require__(/*! ./steps/talking-7-move-around.fr.png */ "./src/lib/libraries/decks/steps/talking-7-move-around.fr.png");
/* harmony import */ var _steps_talking_8_choose_another_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_139__ = __webpack_require__(/*! ./steps/talking-8-choose-another-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/talking-8-choose-another-backdrop.LTR.png");
/* harmony import */ var _steps_talking_9_animate_fr_png__WEBPACK_IMPORTED_MODULE_140__ = __webpack_require__(/*! ./steps/talking-9-animate.fr.png */ "./src/lib/libraries/decks/steps/talking-9-animate.fr.png");
/* harmony import */ var _steps_talking_10_choose_third_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_141__ = __webpack_require__(/*! ./steps/talking-10-choose-third-backdrop.LTR.png */ "./src/lib/libraries/decks/steps/talking-10-choose-third-backdrop.LTR.png");
/* harmony import */ var _steps_talking_11_choose_sound_fr_gif__WEBPACK_IMPORTED_MODULE_142__ = __webpack_require__(/*! ./steps/talking-11-choose-sound.fr.gif */ "./src/lib/libraries/decks/steps/talking-11-choose-sound.fr.gif");
/* harmony import */ var _steps_talking_12_dance_moves_fr_png__WEBPACK_IMPORTED_MODULE_143__ = __webpack_require__(/*! ./steps/talking-12-dance-moves.fr.png */ "./src/lib/libraries/decks/steps/talking-12-dance-moves.fr.png");
/* harmony import */ var _steps_talking_13_ask_and_answer_fr_png__WEBPACK_IMPORTED_MODULE_144__ = __webpack_require__(/*! ./steps/talking-13-ask-and-answer.fr.png */ "./src/lib/libraries/decks/steps/talking-13-ask-and-answer.fr.png");
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














var frImages = {
  // Intro
  introMove: _steps_intro_1_move_fr_gif__WEBPACK_IMPORTED_MODULE_0__["default"],
  introSay: _steps_intro_2_say_fr_gif__WEBPACK_IMPORTED_MODULE_1__["default"],
  introGreenFlag: _steps_intro_3_green_flag_fr_gif__WEBPACK_IMPORTED_MODULE_2__["default"],
  // Text to Speech
  speechAddExtension: _steps_speech_add_extension_fr_gif__WEBPACK_IMPORTED_MODULE_3__["default"],
  speechSaySomething: _steps_speech_say_something_fr_png__WEBPACK_IMPORTED_MODULE_4__["default"],
  speechSetVoice: _steps_speech_set_voice_fr_png__WEBPACK_IMPORTED_MODULE_5__["default"],
  speechMoveAround: _steps_speech_move_around_fr_png__WEBPACK_IMPORTED_MODULE_6__["default"],
  speechAddBackdrop: _steps_pick_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_7__["default"],
  speechAddSprite: _steps_speech_add_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_8__["default"],
  speechSong: _steps_speech_song_fr_png__WEBPACK_IMPORTED_MODULE_9__["default"],
  speechChangeColor: _steps_speech_change_color_fr_png__WEBPACK_IMPORTED_MODULE_10__["default"],
  speechSpin: _steps_speech_spin_fr_png__WEBPACK_IMPORTED_MODULE_11__["default"],
  speechGrowShrink: _steps_speech_grow_shrink_fr_png__WEBPACK_IMPORTED_MODULE_12__["default"],
  // Cartoon Network
  cnShowCharacter: _steps_cn_show_character_LTR_gif__WEBPACK_IMPORTED_MODULE_13__["default"],
  cnSay: _steps_cn_say_fr_png__WEBPACK_IMPORTED_MODULE_14__["default"],
  cnGlide: _steps_cn_glide_fr_png__WEBPACK_IMPORTED_MODULE_15__["default"],
  cnPickSprite: _steps_cn_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_16__["default"],
  cnCollect: _steps_cn_collect_fr_png__WEBPACK_IMPORTED_MODULE_17__["default"],
  cnVariable: _steps_add_variable_fr_gif__WEBPACK_IMPORTED_MODULE_18__["default"],
  cnScore: _steps_cn_score_fr_png__WEBPACK_IMPORTED_MODULE_19__["default"],
  cnBackdrop: _steps_cn_backdrop_fr_png__WEBPACK_IMPORTED_MODULE_20__["default"],
  // Add sprite
  addSprite: _steps_add_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_21__["default"],
  // Animate a name
  namePickLetter: _steps_name_pick_letter_LTR_gif__WEBPACK_IMPORTED_MODULE_22__["default"],
  namePlaySound: _steps_name_play_sound_fr_png__WEBPACK_IMPORTED_MODULE_23__["default"],
  namePickLetter2: _steps_name_pick_letter2_LTR_gif__WEBPACK_IMPORTED_MODULE_24__["default"],
  nameChangeColor: _steps_name_change_color_fr_png__WEBPACK_IMPORTED_MODULE_25__["default"],
  nameSpin: _steps_name_spin_fr_png__WEBPACK_IMPORTED_MODULE_26__["default"],
  nameGrow: _steps_name_grow_fr_png__WEBPACK_IMPORTED_MODULE_27__["default"],
  // Make-Music
  musicPickInstrument: _steps_music_pick_instrument_LTR_gif__WEBPACK_IMPORTED_MODULE_28__["default"],
  musicPlaySound: _steps_music_play_sound_fr_png__WEBPACK_IMPORTED_MODULE_29__["default"],
  musicMakeSong: _steps_music_make_song_fr_png__WEBPACK_IMPORTED_MODULE_30__["default"],
  musicMakeBeat: _steps_music_make_beat_fr_png__WEBPACK_IMPORTED_MODULE_31__["default"],
  musicMakeBeatbox: _steps_music_make_beatbox_fr_png__WEBPACK_IMPORTED_MODULE_32__["default"],
  // Chase-Game
  chaseGameAddBackdrop: _steps_chase_game_add_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_33__["default"],
  chaseGameAddSprite1: _steps_chase_game_add_sprite1_LTR_gif__WEBPACK_IMPORTED_MODULE_34__["default"],
  chaseGameRightLeft: _steps_chase_game_right_left_fr_png__WEBPACK_IMPORTED_MODULE_35__["default"],
  chaseGameUpDown: _steps_chase_game_up_down_fr_png__WEBPACK_IMPORTED_MODULE_36__["default"],
  chaseGameAddSprite2: _steps_chase_game_add_sprite2_LTR_gif__WEBPACK_IMPORTED_MODULE_37__["default"],
  chaseGameMoveRandomly: _steps_chase_game_move_randomly_fr_png__WEBPACK_IMPORTED_MODULE_38__["default"],
  chaseGamePlaySound: _steps_chase_game_play_sound_fr_png__WEBPACK_IMPORTED_MODULE_39__["default"],
  chaseGameAddVariable: _steps_add_variable_fr_gif__WEBPACK_IMPORTED_MODULE_18__["default"],
  chaseGameChangeScore: _steps_chase_game_change_score_fr_png__WEBPACK_IMPORTED_MODULE_40__["default"],
  // Make-A-Pop/Clicker Game
  popGamePickSprite: _steps_pop_game_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_41__["default"],
  popGamePlaySound: _steps_pop_game_play_sound_fr_png__WEBPACK_IMPORTED_MODULE_42__["default"],
  popGameAddScore: _steps_add_variable_fr_gif__WEBPACK_IMPORTED_MODULE_18__["default"],
  popGameChangeScore: _steps_pop_game_change_score_fr_png__WEBPACK_IMPORTED_MODULE_43__["default"],
  popGameRandomPosition: _steps_pop_game_random_position_fr_png__WEBPACK_IMPORTED_MODULE_44__["default"],
  popGameChangeColor: _steps_pop_game_change_color_fr_png__WEBPACK_IMPORTED_MODULE_45__["default"],
  popGameResetScore: _steps_pop_game_reset_score_fr_png__WEBPACK_IMPORTED_MODULE_46__["default"],
  // Animate A Character
  animateCharPickBackdrop: _steps_pick_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_7__["default"],
  animateCharPickSprite: _steps_animate_char_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_47__["default"],
  animateCharSaySomething: _steps_animate_char_say_something_fr_png__WEBPACK_IMPORTED_MODULE_48__["default"],
  animateCharAddSound: _steps_animate_char_add_sound_fr_png__WEBPACK_IMPORTED_MODULE_49__["default"],
  animateCharTalk: _steps_animate_char_talk_fr_png__WEBPACK_IMPORTED_MODULE_50__["default"],
  animateCharMove: _steps_animate_char_move_fr_png__WEBPACK_IMPORTED_MODULE_51__["default"],
  animateCharJump: _steps_animate_char_jump_fr_png__WEBPACK_IMPORTED_MODULE_52__["default"],
  animateCharChangeColor: _steps_animate_char_change_color_fr_png__WEBPACK_IMPORTED_MODULE_53__["default"],
  // Tell A Story
  storyPickBackdrop: _steps_story_pick_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_54__["default"],
  storyPickSprite: _steps_story_pick_sprite_LTR_gif__WEBPACK_IMPORTED_MODULE_55__["default"],
  storySaySomething: _steps_story_say_something_fr_png__WEBPACK_IMPORTED_MODULE_56__["default"],
  storyPickSprite2: _steps_story_pick_sprite2_LTR_gif__WEBPACK_IMPORTED_MODULE_57__["default"],
  storyFlip: _steps_story_flip_fr_gif__WEBPACK_IMPORTED_MODULE_58__["default"],
  storyConversation: _steps_story_conversation_fr_png__WEBPACK_IMPORTED_MODULE_59__["default"],
  storyPickBackdrop2: _steps_story_pick_backdrop2_LTR_gif__WEBPACK_IMPORTED_MODULE_60__["default"],
  storySwitchBackdrop: _steps_story_switch_backdrop_fr_png__WEBPACK_IMPORTED_MODULE_61__["default"],
  storyHideCharacter: _steps_story_hide_character_fr_png__WEBPACK_IMPORTED_MODULE_62__["default"],
  storyShowCharacter: _steps_story_show_character_fr_png__WEBPACK_IMPORTED_MODULE_63__["default"],
  // Video Sensing
  videoAddExtension: _steps_video_add_extension_fr_gif__WEBPACK_IMPORTED_MODULE_64__["default"],
  videoPet: _steps_video_pet_fr_png__WEBPACK_IMPORTED_MODULE_65__["default"],
  videoAnimate: _steps_video_animate_fr_png__WEBPACK_IMPORTED_MODULE_66__["default"],
  videoPop: _steps_video_pop_fr_png__WEBPACK_IMPORTED_MODULE_67__["default"],
  // Make it Fly
  flyChooseBackdrop: _steps_fly_choose_backdrop_LTR_gif__WEBPACK_IMPORTED_MODULE_68__["default"],
  flyChooseCharacter: _steps_fly_choose_character_LTR_png__WEBPACK_IMPORTED_MODULE_69__["default"],
  flySaySomething: _steps_fly_say_something_fr_png__WEBPACK_IMPORTED_MODULE_70__["default"],
  flyMoveArrows: _steps_fly_make_interactive_fr_png__WEBPACK_IMPORTED_MODULE_71__["default"],
  flyChooseObject: _steps_fly_object_to_collect_LTR_png__WEBPACK_IMPORTED_MODULE_72__["default"],
  flyFlyingObject: _steps_fly_flying_heart_fr_png__WEBPACK_IMPORTED_MODULE_73__["default"],
  flySelectFlyingSprite: _steps_fly_select_flyer_LTR_png__WEBPACK_IMPORTED_MODULE_74__["default"],
  flyAddScore: _steps_add_variable_fr_gif__WEBPACK_IMPORTED_MODULE_18__["default"],
  flyKeepScore: _steps_fly_keep_score_fr_png__WEBPACK_IMPORTED_MODULE_75__["default"],
  flyAddScenery: _steps_fly_choose_scenery_LTR_gif__WEBPACK_IMPORTED_MODULE_76__["default"],
  flyMoveScenery: _steps_fly_move_scenery_fr_png__WEBPACK_IMPORTED_MODULE_77__["default"],
  flySwitchLooks: _steps_fly_switch_costume_fr_png__WEBPACK_IMPORTED_MODULE_78__["default"],
  // Pong
  pongAddBackdrop: _steps_pong_add_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_79__["default"],
  pongAddBallSprite: _steps_pong_add_ball_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_80__["default"],
  pongBounceAround: _steps_pong_bounce_around_fr_png__WEBPACK_IMPORTED_MODULE_81__["default"],
  pongAddPaddle: _steps_pong_add_a_paddle_LTR_gif__WEBPACK_IMPORTED_MODULE_82__["default"],
  pongMoveThePaddle: _steps_pong_move_the_paddle_fr_png__WEBPACK_IMPORTED_MODULE_83__["default"],
  pongSelectBallSprite: _steps_pong_select_ball_LTR_png__WEBPACK_IMPORTED_MODULE_84__["default"],
  pongAddMoreCodeToBall: _steps_pong_add_code_to_ball_fr_png__WEBPACK_IMPORTED_MODULE_85__["default"],
  pongAddAScore: _steps_add_variable_fr_gif__WEBPACK_IMPORTED_MODULE_18__["default"],
  pongChooseScoreFromMenu: _steps_pong_choose_score_fr_png__WEBPACK_IMPORTED_MODULE_86__["default"],
  pongInsertChangeScoreBlock: _steps_pong_insert_change_score_fr_png__WEBPACK_IMPORTED_MODULE_87__["default"],
  pongResetScore: _steps_pong_reset_score_fr_png__WEBPACK_IMPORTED_MODULE_88__["default"],
  pongAddLineSprite: _steps_pong_add_line_LTR_gif__WEBPACK_IMPORTED_MODULE_89__["default"],
  pongGameOver: _steps_pong_game_over_fr_png__WEBPACK_IMPORTED_MODULE_90__["default"],
  // Imagine a World
  imagineTypeWhatYouWant: _steps_imagine_type_what_you_want_fr_png__WEBPACK_IMPORTED_MODULE_91__["default"],
  imagineClickGreenFlag: _steps_imagine_click_green_flag_fr_png__WEBPACK_IMPORTED_MODULE_92__["default"],
  imagineChooseBackdrop: _steps_imagine_choose_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_93__["default"],
  imagineChooseSprite: _steps_imagine_choose_any_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_94__["default"],
  imagineFlyAround: _steps_imagine_fly_around_fr_png__WEBPACK_IMPORTED_MODULE_95__["default"],
  imagineChooseAnotherSprite: _steps_imagine_choose_another_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_96__["default"],
  imagineLeftRight: _steps_imagine_left_right_fr_png__WEBPACK_IMPORTED_MODULE_97__["default"],
  imagineUpDown: _steps_imagine_up_down_fr_png__WEBPACK_IMPORTED_MODULE_98__["default"],
  imagineChangeCostumes: _steps_imagine_change_costumes_fr_png__WEBPACK_IMPORTED_MODULE_99__["default"],
  imagineGlideToPoint: _steps_imagine_glide_to_point_fr_png__WEBPACK_IMPORTED_MODULE_100__["default"],
  imagineGrowShrink: _steps_imagine_grow_shrink_fr_png__WEBPACK_IMPORTED_MODULE_101__["default"],
  imagineChooseAnotherBackdrop: _steps_imagine_choose_another_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_102__["default"],
  imagineSwitchBackdrops: _steps_imagine_switch_backdrops_fr_png__WEBPACK_IMPORTED_MODULE_103__["default"],
  imagineRecordASound: _steps_imagine_record_a_sound_fr_gif__WEBPACK_IMPORTED_MODULE_104__["default"],
  imagineChooseSound: _steps_imagine_choose_sound_fr_png__WEBPACK_IMPORTED_MODULE_105__["default"],
  // Add a Backdrop
  addBackdrop: _steps_add_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_106__["default"],
  // Add Effects
  addEffects: _steps_add_effects_fr_png__WEBPACK_IMPORTED_MODULE_107__["default"],
  // Hide and Show
  hideAndShow: _steps_hide_show_fr_png__WEBPACK_IMPORTED_MODULE_108__["default"],
  // Switch Costumes
  switchCostumes: _steps_switch_costumes_fr_png__WEBPACK_IMPORTED_MODULE_109__["default"],
  // Change Size
  changeSize: _steps_change_size_fr_png__WEBPACK_IMPORTED_MODULE_110__["default"],
  // Spin
  spinTurn: _steps_spin_turn_fr_png__WEBPACK_IMPORTED_MODULE_111__["default"],
  spinPointInDirection: _steps_spin_point_in_direction_fr_png__WEBPACK_IMPORTED_MODULE_112__["default"],
  // Record a Sound
  recordASoundSoundsTab: _steps_record_a_sound_sounds_tab_fr_png__WEBPACK_IMPORTED_MODULE_113__["default"],
  recordASoundClickRecord: _steps_record_a_sound_click_record_fr_png__WEBPACK_IMPORTED_MODULE_114__["default"],
  recordASoundPressRecordButton: _steps_record_a_sound_press_record_button_fr_png__WEBPACK_IMPORTED_MODULE_115__["default"],
  recordASoundChooseSound: _steps_record_a_sound_choose_sound_fr_png__WEBPACK_IMPORTED_MODULE_116__["default"],
  recordASoundPlayYourSound: _steps_record_a_sound_play_your_sound_fr_png__WEBPACK_IMPORTED_MODULE_117__["default"],
  // Use Arrow Keys
  moveArrowKeysLeftRight: _steps_move_arrow_keys_left_right_fr_png__WEBPACK_IMPORTED_MODULE_118__["default"],
  moveArrowKeysUpDown: _steps_move_arrow_keys_up_down_fr_png__WEBPACK_IMPORTED_MODULE_119__["default"],
  // Glide Around
  glideAroundBackAndForth: _steps_glide_around_back_and_forth_fr_png__WEBPACK_IMPORTED_MODULE_120__["default"],
  glideAroundPoint: _steps_glide_around_point_fr_png__WEBPACK_IMPORTED_MODULE_121__["default"],
  // Code a Cartoon
  codeCartoonSaySomething: _steps_code_cartoon_01_say_something_fr_png__WEBPACK_IMPORTED_MODULE_122__["default"],
  codeCartoonAnimate: _steps_code_cartoon_02_animate_fr_png__WEBPACK_IMPORTED_MODULE_123__["default"],
  codeCartoonSelectDifferentCharacter: _steps_code_cartoon_03_select_different_character_LTR_png__WEBPACK_IMPORTED_MODULE_124__["default"],
  codeCartoonUseMinusSign: _steps_code_cartoon_04_use_minus_sign_fr_png__WEBPACK_IMPORTED_MODULE_125__["default"],
  codeCartoonGrowShrink: _steps_code_cartoon_05_grow_shrink_fr_png__WEBPACK_IMPORTED_MODULE_126__["default"],
  codeCartoonSelectDifferentCharacter2: _steps_code_cartoon_06_select_another_different_character_LTR_png__WEBPACK_IMPORTED_MODULE_127__["default"],
  codeCartoonJump: _steps_code_cartoon_07_jump_fr_png__WEBPACK_IMPORTED_MODULE_128__["default"],
  codeCartoonChangeScenes: _steps_code_cartoon_08_change_scenes_fr_png__WEBPACK_IMPORTED_MODULE_129__["default"],
  codeCartoonGlideAround: _steps_code_cartoon_09_glide_around_fr_png__WEBPACK_IMPORTED_MODULE_130__["default"],
  codeCartoonChangeCostumes: _steps_code_cartoon_10_change_costumes_fr_png__WEBPACK_IMPORTED_MODULE_131__["default"],
  codeCartoonChooseMoreCharacters: _steps_code_cartoon_11_choose_more_characters_LTR_png__WEBPACK_IMPORTED_MODULE_132__["default"],
  // Talking Tales
  talesAddExtension: _steps_speech_add_extension_fr_gif__WEBPACK_IMPORTED_MODULE_3__["default"],
  talesChooseSprite: _steps_talking_2_choose_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_133__["default"],
  talesSaySomething: _steps_talking_3_say_something_fr_png__WEBPACK_IMPORTED_MODULE_134__["default"],
  talesAskAnswer: _steps_talking_13_ask_and_answer_fr_png__WEBPACK_IMPORTED_MODULE_144__["default"],
  talesChooseBackdrop: _steps_talking_4_choose_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_135__["default"],
  talesSwitchBackdrop: _steps_talking_5_switch_backdrop_fr_png__WEBPACK_IMPORTED_MODULE_136__["default"],
  talesChooseAnotherSprite: _steps_talking_6_choose_another_sprite_LTR_png__WEBPACK_IMPORTED_MODULE_137__["default"],
  talesMoveAround: _steps_talking_7_move_around_fr_png__WEBPACK_IMPORTED_MODULE_138__["default"],
  talesChooseAnotherBackdrop: _steps_talking_8_choose_another_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_139__["default"],
  talesAnimateTalking: _steps_talking_9_animate_fr_png__WEBPACK_IMPORTED_MODULE_140__["default"],
  talesChooseThirdBackdrop: _steps_talking_10_choose_third_backdrop_LTR_png__WEBPACK_IMPORTED_MODULE_141__["default"],
  talesChooseSound: _steps_talking_11_choose_sound_fr_gif__WEBPACK_IMPORTED_MODULE_142__["default"],
  talesDanceMoves: _steps_talking_12_dance_moves_fr_png__WEBPACK_IMPORTED_MODULE_143__["default"]
};


/***/ }),

/***/ "./src/lib/libraries/decks/steps/add-effects.fr.png":
/*!**********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/add-effects.fr.png ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/bf25a4537fdd4b19d2ffaa78d6d2ac2e.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/add-variable.fr.gif":
/*!***********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/add-variable.fr.gif ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/8546e0aa3da858efb897fa9f28e318c0.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/animate-char-add-sound.fr.png":
/*!*********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/animate-char-add-sound.fr.png ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/d425c11f4d1cf0ca95f2c1bd3b12fc7a.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/animate-char-change-color.fr.png":
/*!************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/animate-char-change-color.fr.png ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/177e196642c72d6d9923ff97b92b5487.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/animate-char-jump.fr.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/animate-char-jump.fr.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/0e9dcd0ca0a7e8fbd59e524772e14e6f.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/animate-char-move.fr.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/animate-char-move.fr.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/9bbec89eef4f2805b97bb75aa4d16916.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/animate-char-say-something.fr.png":
/*!*************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/animate-char-say-something.fr.png ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/06e56846ebeed5e2ef82303b21eac79b.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/animate-char-talk.fr.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/animate-char-talk.fr.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/72dfd1c271e3446c98588c1df8375c79.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/change-size.fr.png":
/*!**********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/change-size.fr.png ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/b3434e2c3289a191c1cd0bae8f42e40f.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/chase-game-change-score.fr.png":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/chase-game-change-score.fr.png ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/52751f7aa807d010b954d8eb7455bd6d.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/chase-game-move-randomly.fr.png":
/*!***********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/chase-game-move-randomly.fr.png ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/eadfc9ee8173c6dd36711ad0def4aa76.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/chase-game-play-sound.fr.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/chase-game-play-sound.fr.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/b58712894f9e8212b8f2d9837313bcda.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/chase-game-right-left.fr.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/chase-game-right-left.fr.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/39733530bdecaaedd9e1138b0d721b91.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/chase-game-up-down.fr.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/chase-game-up-down.fr.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/57613c458e26e8a2f081914c400e0f2d.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/cn-backdrop.fr.png":
/*!**********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/cn-backdrop.fr.png ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/ed2ab4d4ceb124d2932bec09d46e879e.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/cn-collect.fr.png":
/*!*********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/cn-collect.fr.png ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/61fd9334257fb3e5c4b58d7e3e44a4f9.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/cn-glide.fr.png":
/*!*******************************************************!*\
  !*** ./src/lib/libraries/decks/steps/cn-glide.fr.png ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/37b333a757b82384481143e6a8a8a028.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/cn-say.fr.png":
/*!*****************************************************!*\
  !*** ./src/lib/libraries/decks/steps/cn-say.fr.png ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/243ba1cab865c7f08e62bbd990388a33.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/cn-score.fr.png":
/*!*******************************************************!*\
  !*** ./src/lib/libraries/decks/steps/cn-score.fr.png ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/aafdb0913dfecee499113319d28476f4.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-01-say-something.fr.png":
/*!****************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-01-say-something.fr.png ***!
  \****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/b7ba333221af228158700406b979433b.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-02-animate.fr.png":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-02-animate.fr.png ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/7379cdbd6905d4c1a0360c590fbd0326.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-04-use-minus-sign.fr.png":
/*!*****************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-04-use-minus-sign.fr.png ***!
  \*****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/458d921b6919d7e4af37b3eefcd61d41.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-05-grow-shrink.fr.png":
/*!**************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-05-grow-shrink.fr.png ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/e1fa8dc086c91523fa06ef1cece626f2.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-07-jump.fr.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-07-jump.fr.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/75a690894204cca8f71cfe1d68b7e37e.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-08-change-scenes.fr.png":
/*!****************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-08-change-scenes.fr.png ***!
  \****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/3afa0de4e605ab4e12f08a2dde168f16.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-09-glide-around.fr.png":
/*!***************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-09-glide-around.fr.png ***!
  \***************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/3851f59f95ebfabeaaa6782638da3688.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/code-cartoon-10-change-costumes.fr.png":
/*!******************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/code-cartoon-10-change-costumes.fr.png ***!
  \******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/65862a040280c056deebce666d89a153.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/fly-flying-heart.fr.png":
/*!***************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/fly-flying-heart.fr.png ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/355419bd5da877c354f3b8d10373809b.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/fly-keep-score.fr.png":
/*!*************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/fly-keep-score.fr.png ***!
  \*************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/a892ef3f968267b3e97604e035d213ae.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/fly-make-interactive.fr.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/fly-make-interactive.fr.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/b8de61ad932da909c68a0d977925d660.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/fly-move-scenery.fr.png":
/*!***************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/fly-move-scenery.fr.png ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/acdb8e8e7a37e145222e31b8ede7164d.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/fly-say-something.fr.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/fly-say-something.fr.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/c006b7c279437ebfca535116b5783587.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/fly-switch-costume.fr.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/fly-switch-costume.fr.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/b965b86a63bd2e3938a25c4b487ab2f0.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/glide-around-back-and-forth.fr.png":
/*!**************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/glide-around-back-and-forth.fr.png ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/db7b3ad8f40ef4d6a1d00e6028767fe3.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/glide-around-point.fr.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/glide-around-point.fr.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/d0ccc4e2ad3d9f57f0ebac857cfe159e.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/hide-show.fr.png":
/*!********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/hide-show.fr.png ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/2049d134c0d0d0b21b509ce29b269ef6.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-change-costumes.fr.png":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-change-costumes.fr.png ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/f8524e7f0fd4d7c143962a5ec1b6dc8c.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-choose-sound.fr.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-choose-sound.fr.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/a4b0a42b7cf2e32cda2a39aef414dd26.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-click-green-flag.fr.png":
/*!***********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-click-green-flag.fr.png ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/64aee6843c4e7eb8caf9a7722d2afd4a.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-fly-around.fr.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-fly-around.fr.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/3d235a83ce6c2c1a8682a1cb05f3f4e6.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-glide-to-point.fr.png":
/*!*********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-glide-to-point.fr.png ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/840618fe2c3409bf06e241ad228f8c49.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-grow-shrink.fr.png":
/*!******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-grow-shrink.fr.png ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/8b56042c850d52a039e1433f8c181621.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-left-right.fr.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-left-right.fr.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/6234f9a3c00a78feec541f5c97e42d10.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-record-a-sound.fr.gif":
/*!*********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-record-a-sound.fr.gif ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/ea841cc5db252c3cb83c9e5b32aa09d6.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-switch-backdrops.fr.png":
/*!***********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-switch-backdrops.fr.png ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/94c2bfbd71ae42cc194f59a65a5b3b5e.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-type-what-you-want.fr.png":
/*!*************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-type-what-you-want.fr.png ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/f7f41ffe0e883ffa88638689bb495abd.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/imagine-up-down.fr.png":
/*!**************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/imagine-up-down.fr.png ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/8c9c73fd5e26517ba772eb2eddcc4faf.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/intro-1-move.fr.gif":
/*!***********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/intro-1-move.fr.gif ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/b82d26829f397ca02df0fe4d147b1349.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/intro-2-say.fr.gif":
/*!**********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/intro-2-say.fr.gif ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/8dcf30ee03b1f96142713f6a21e7e671.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/intro-3-green-flag.fr.gif":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/intro-3-green-flag.fr.gif ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/fa225d8aaa82e8bd988f7de81f958fd1.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/move-arrow-keys-left-right.fr.png":
/*!*************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/move-arrow-keys-left-right.fr.png ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/f0ff2c7485c4e313d7ae28a47e6d1a8c.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/move-arrow-keys-up-down.fr.png":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/move-arrow-keys-up-down.fr.png ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/4b3bd7db00faf76701acb2b594fdf234.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/music-make-beat.fr.png":
/*!**************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/music-make-beat.fr.png ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/4e965b5172ce9075a422ce3e59d7eff6.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/music-make-beatbox.fr.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/music-make-beatbox.fr.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/abf0245d946d6a7c800130aaaf0d8d69.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/music-make-song.fr.png":
/*!**************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/music-make-song.fr.png ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/7c3fd35d02fb31f965034f6a407dfd09.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/music-play-sound.fr.png":
/*!***************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/music-play-sound.fr.png ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/31b24b73d4cc5529c7bff20240f8d9a7.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/name-change-color.fr.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/name-change-color.fr.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/d5f268fc10bf09e60699d08eaeca47df.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/name-grow.fr.png":
/*!********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/name-grow.fr.png ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/ab10b725ce6fb5c9dd5f3c37232d8338.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/name-play-sound.fr.png":
/*!**************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/name-play-sound.fr.png ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/38c798640ddb4bfcebf96e5a01c1888d.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/name-spin.fr.png":
/*!********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/name-spin.fr.png ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/00a9ffedff54c860a9f5262d5ff6182b.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-add-code-to-ball.fr.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-add-code-to-ball.fr.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/d377787fe8b37b2d9844b598983b4520.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-bounce-around.fr.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-bounce-around.fr.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/6d1f668f8bdac92107802d0c39dd46de.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-choose-score.fr.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-choose-score.fr.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/ed7940adc4d7aab3c12ca85f4b9829b7.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-game-over.fr.png":
/*!*************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-game-over.fr.png ***!
  \*************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/47c4287dfe181e76adcb046a730a32cd.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-insert-change-score.fr.png":
/*!***********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-insert-change-score.fr.png ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/c0b872b00d283182d294d7f8ad0c0123.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-move-the-paddle.fr.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-move-the-paddle.fr.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/c1c4d0faddba29e446f7e160625ee129.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pong-reset-score.fr.png":
/*!***************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pong-reset-score.fr.png ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/d81a41702187c1f06c63392dbc4d469e.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pop-game-change-color.fr.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pop-game-change-color.fr.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/669838c949027c81125e6c4558dba83f.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pop-game-change-score.fr.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pop-game-change-score.fr.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/48c578dd8c0b3e433654e5dfa121ed3d.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pop-game-play-sound.fr.png":
/*!******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pop-game-play-sound.fr.png ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/9db8b80fdd0548691b4d4dd465c09958.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pop-game-random-position.fr.png":
/*!***********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pop-game-random-position.fr.png ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/9e7c3387b4d40843dd7de860b99f891b.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/pop-game-reset-score.fr.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/pop-game-reset-score.fr.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/d1d964ae69a69c027271193b091beb01.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/record-a-sound-choose-sound.fr.png":
/*!**************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/record-a-sound-choose-sound.fr.png ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/366b044c409bfa890e694755348e0a00.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/record-a-sound-click-record.fr.png":
/*!**************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/record-a-sound-click-record.fr.png ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/72cfe4db2cad2868d87441845a84fc90.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/record-a-sound-play-your-sound.fr.png":
/*!*****************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/record-a-sound-play-your-sound.fr.png ***!
  \*****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/8af372db0c4e80a801e7edf6d32988b8.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/record-a-sound-press-record-button.fr.png":
/*!*********************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/record-a-sound-press-record-button.fr.png ***!
  \*********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/8532d3793e98ba696a1b245ddeaccc0f.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/record-a-sound-sounds-tab.fr.png":
/*!************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/record-a-sound-sounds-tab.fr.png ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/edd78ac58b4f596220f92f4d7350ed5d.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-add-extension.fr.gif":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-add-extension.fr.gif ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/0598212953c12b5ea114e4e3507bdb41.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-change-color.fr.png":
/*!******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-change-color.fr.png ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/a35729ab3d62285fe57c83eb5675c46b.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-grow-shrink.fr.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-grow-shrink.fr.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/a3b57c8028d87666560574ce8fdda00d.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-move-around.fr.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-move-around.fr.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/a881a843bd19473b795b605e7ce5aae0.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-say-something.fr.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-say-something.fr.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/eb7bd6894442e34f37987c8306628669.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-set-voice.fr.png":
/*!***************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-set-voice.fr.png ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/8e14a426ef76620de754a1037a9fbbf8.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-song.fr.png":
/*!**********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-song.fr.png ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/39113da497f4d54b5821314699155e85.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/speech-spin.fr.png":
/*!**********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/speech-spin.fr.png ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/e485262edecb52903f73c266c495b4c2.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/spin-point-in-direction.fr.png":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/spin-point-in-direction.fr.png ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/253fa5adcd78c605265035ce8d0a3ae3.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/spin-turn.fr.png":
/*!********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/spin-turn.fr.png ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/f45966d8381f5ccb7081e0e5c0ca354c.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/story-conversation.fr.png":
/*!*****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/story-conversation.fr.png ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/f458f667589ed4a3c76ee53b4fb625f6.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/story-flip.fr.gif":
/*!*********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/story-flip.fr.gif ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/6ea81c0b8146a35b7e5a060b724b9f30.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/story-hide-character.fr.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/story-hide-character.fr.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/d428e28d12e69c505ea5901232284ff3.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/story-say-something.fr.png":
/*!******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/story-say-something.fr.png ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/1396fce6d3bc76601d81c4d310e34620.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/story-show-character.fr.png":
/*!*******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/story-show-character.fr.png ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/fc5624d0e7b68f76c0158c539478206f.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/story-switch-backdrop.fr.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/story-switch-backdrop.fr.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/7dab76e7336d4582ed4dfc2d3cba7562.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/switch-costumes.fr.png":
/*!**************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/switch-costumes.fr.png ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/d4a0baf2a9ec9e43104009ab8cf7a31d.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-11-choose-sound.fr.gif":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-11-choose-sound.fr.gif ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/6cca52098bf9facbf9ab844c151d60ea.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-12-dance-moves.fr.png":
/*!*********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-12-dance-moves.fr.png ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/c10529d4d7f2c770da58e6f68e240057.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-13-ask-and-answer.fr.png":
/*!************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-13-ask-and-answer.fr.png ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/0fd5c5def395806d9172e70b50bd7acd.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-3-say-something.fr.png":
/*!**********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-3-say-something.fr.png ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/7c4547dc4fdd10a5c98cbb746215f903.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-5-switch-backdrop.fr.png":
/*!************************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-5-switch-backdrop.fr.png ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/b8bd004acab4f77157f5e813435cb200.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-7-move-around.fr.png":
/*!********************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-7-move-around.fr.png ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/6786ff65282af8aa444fcd7840eea577.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/talking-9-animate.fr.png":
/*!****************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/talking-9-animate.fr.png ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/52c7680290979655aab0d071f380f0ad.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/video-add-extension.fr.gif":
/*!******************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/video-add-extension.fr.gif ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/8dfd4b7f764b4e80a49405fcea8f3fd8.gif");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/video-animate.fr.png":
/*!************************************************************!*\
  !*** ./src/lib/libraries/decks/steps/video-animate.fr.png ***!
  \************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/4747f77515b56bb4cc34dc858f5039aa.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/video-pet.fr.png":
/*!********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/video-pet.fr.png ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/ed8a6dddc7b55ce8a0bcaee2d3affb80.png");

/***/ }),

/***/ "./src/lib/libraries/decks/steps/video-pop.fr.png":
/*!********************************************************!*\
  !*** ./src/lib/libraries/decks/steps/video-pop.fr.png ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "static/assets/337c4f6e1e3ae0199b7acb60b265a777.png");

/***/ })

}]);
//# sourceMappingURL=fr-steps.js.map