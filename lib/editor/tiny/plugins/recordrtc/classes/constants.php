<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace tiny_recordrtc;

/**
 * Constants for Tiny RecordRTC plugin.
 *
 * @package    tiny_recordrtc
 * @copyright  2024 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class constants {

    /** @var string TINYRECORDRTC_AUDIO_TYPE The audio recording type. */
    public const TINYRECORDRTC_AUDIO_TYPE = 'audio';

    /** @var string TINYRECORDRTC_VIDEO_TYPE The video recording type. */
    public const TINYRECORDRTC_VIDEO_TYPE = 'video';

    /** @var string TINYRECORDRTC_SCREEN_TYPE The screen-sharing recording type. */
    public const TINYRECORDRTC_SCREEN_TYPE = 'screen';

    /** @var string TINYRECORDRTC_SCREEN_HD The HD screen-sharing resolution. */
    public const TINYRECORDRTC_SCREEN_HD = '1280,720';

    /** @var string TINYRECORDRTC_SCREEN_FHD The Full-HD screen-sharing resolution. */
    public const TINYRECORDRTC_SCREEN_FHD = '1920,1080';

    /** @var array TINYRECORDRTC_AUDIO_BITRATES The audio bitrate options. */
    public const TINYRECORDRTC_AUDIO_BITRATES = [24000, 32000, 48000, 64000, 96000, 128000, 160000, 192000, 256000, 320000];
}
