<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin administration pages are defined here.
 *
 * @package     mod_plugnmeet
 * @category    admin
 * @author     Jibon L. Costa <jibon@mynaparrot.com>
 * @copyright  2022 MynaParrot
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedIf
    $settings->add(new admin_setting_heading('mod_plugnmeet/plugnmeetconfig', get_string('plugnmeetconfig', 'plugnmeet'), ''));
    $settings->add(new admin_setting_configtext(
        'mod_plugnmeet/plugnmeet_server_url',
        get_string('plugnmeet_server_url', 'plugnmeet'),
        get_string('plugnmeet_server_url_des', 'plugnmeet'),
        "https://demo.plugnmeet.com", PARAM_URL));

    $settings->add(new admin_setting_configtext(
        'mod_plugnmeet/plugnmeet_api_key',
        get_string('plugnmeet_api_key', 'plugnmeet'),
        get_string('plugnmeet_api_key_des', 'plugnmeet'),
        "plugnmeet", PARAM_TEXT));

    $settings->add(new admin_setting_configtext(
        'mod_plugnmeet/plugnmeet_secret',
        get_string('plugnmeet_secret', 'plugnmeet'),
        get_string('plugnmeet_secret_des', 'plugnmeet'),
        "zumyyYWqv7KR2kUqvYdq4z4sXg7XTBD2ljT6", PARAM_TEXT));

    $settings->add(new admin_setting_configstoredfile(
        'mod_plugnmeet/custom_logo',
        get_string('custom_logo', 'plugnmeet'),
        get_string('custom_logo_des', 'plugnmeet'),
        'custom_logo', 0,
        array('maxfiles' => 1, 'accepted_types' => array('.png', '.jpg'))));

    $settings->add(new admin_setting_heading(
        'mod_plugnmeet/plugnmeetoptions',
        get_string('plugnmeetoptions', 'plugnmeet'),
        ''));

    $dynacast = array(
        1 => get_string('yes', 'plugnmeet'),
        0 => get_string('no', 'plugnmeet')
    );
    $settings->add(new admin_setting_configselect(
        'mod_plugnmeet/enable_dynacast',
        get_string('enable_dynacast', 'mod_plugnmeet'),
        get_string('enable_dynacast_des', 'mod_plugnmeet'),
        1, $dynacast));

    $simulcast = array(
        1 => get_string('yes', 'plugnmeet'),
        0 => get_string('no', 'plugnmeet')
    );
    $settings->add(new admin_setting_configselect(
        'mod_plugnmeet/enable_simulcast',
        get_string('enable_simulcast', 'mod_plugnmeet'),
        get_string('enable_simulcast_des', 'mod_plugnmeet'),
        1, $simulcast));

    $videocodec = array(
        "vp8" => "vp8",
        "h264" => "h264",
        "av1" => "av1",
        "vp9" => "vp9"
    );
    $settings->add(new admin_setting_configselect(
        'mod_plugnmeet/video_codec',
        get_string('video_codec', 'mod_plugnmeet'),
        get_string('video_codec_des', 'mod_plugnmeet'),
        'vp8', $videocodec));

    $webcamresolution = array(
        "h90" => "h90",
        "h180" => "h180",
        "h216" => "h216",
        "h360" => "h360",
        "h540" => "h540",
        "h720" => "h720",
        "h1080" => "h1080",
        "h1440" => "h1440",
        "h2160" => "h2160",
    );

    $settings->add(new admin_setting_configselect(
        'mod_plugnmeet/default_webcam_resolution',
        get_string('default_webcam_resolution', 'mod_plugnmeet'),
        get_string('default_webcam_resolution_des', 'mod_plugnmeet'),
        'h720', $webcamresolution));

    $screenshareresolution = array(
        "h360fps3" => "h360fps3",
        "h720fps5" => "h720fps5",
        "h720fps15" => "h720fps15",
        "h1080fps15" => "h1080fps15",
        "h1080fps30" => "h1080fps30"
    );
    $settings->add(new admin_setting_configselect(
        'mod_plugnmeet/default_screen_share_resolution',
        get_string('default_screen_share_resolution', 'mod_plugnmeet'),
        get_string('default_screen_share_resolution_des',
            'mod_plugnmeet'), 'vp8', $screenshareresolution));

    $stopmictrackonmute = array(
        1 => get_string('yes', 'mod_plugnmeet'),
        0 => get_string('no', 'mod_plugnmeet')
    );
    $settings->add(new admin_setting_configselect(
        'mod_plugnmeet/stop_mic_track_on_mute',
        get_string('stop_mic_track_on_mute', 'mod_plugnmeet'),
        get_string('stop_mic_track_on_mute_des', 'mod_plugnmeet'),
        1, $stopmictrackonmute));

    $settings->add(new admin_setting_heading(
        'mod_plugnmeet/plugnmeetextra',
        get_string('extraoption', 'plugnmeet'), ''));

    $loadclient = array(
        1 => get_string('remote', 'mod_plugnmeet'),
        2 => get_string('local', 'mod_plugnmeet')
    );
    $settings->add(new admin_setting_configselect(
        'mod_plugnmeet/client_load',
        get_string('client_load', 'mod_plugnmeet'),
        get_string('client_load_des', 'mod_plugnmeet'),
        1, $loadclient));

    $settings->add(new admin_setting_configtext(
        'mod_plugnmeet/client_download_url',
        get_string('client_download_url', 'plugnmeet'),
        get_string('client_download_url_des', 'plugnmeet'),
        "https://github.com/mynaparrot/plugNmeet-client/releases/latest/download/client.zip",
        PARAM_URL));

    $setting = new admin_setting_description(
        'mod_plugnmeet/update_client', "",
        get_string('update_client_code', 'plugnmeet'));
    $setting->nosave = true;
    $settings->add($setting);

    // Design customization part.
    $settings->add(new admin_setting_heading(
        'mod_plugnmeet/design_customization',
        get_string('design_customization', 'plugnmeet'),
        ''));

    $settings->add(new admin_setting_configtext(
        'mod_plugnmeet/custom_css_url',
        get_string('custom_css_url', 'plugnmeet'),
        get_string('custom_css_url_des', 'plugnmeet'),
        "", PARAM_URL));

    $settings->add(new admin_setting_configcolourpicker(
        'mod_plugnmeet/primary_color',
        get_string('primary_color', 'plugnmeet'),
        get_string('primary_color_des', 'plugnmeet'),
        ''));

    $settings->add(new admin_setting_configcolourpicker(
        'mod_plugnmeet/secondary_color',
        get_string('secondary_color', 'plugnmeet'),
        get_string('secondary_color_des', 'plugnmeet'),
        ''));

    $settings->add(new admin_setting_configcolourpicker(
        'mod_plugnmeet/background_color',
        get_string('background_color', 'plugnmeet'),
        get_string('background_color_des', 'plugnmeet'),
        ''));

    $settings->add(new admin_setting_configstoredfile(
        'mod_plugnmeet/background_image',
        get_string('background_image', 'plugnmeet'),
        get_string('background_image_des', 'plugnmeet'), 'background_image',
        0,
        array('maxfiles' => 1, 'accepted_types' => array('.png', '.jpg'))));

    $settings->add(new admin_setting_configcolourpicker(
        'mod_plugnmeet/header_color',
        get_string('header_color', 'plugnmeet'),
        get_string('header_color_des', 'plugnmeet'),
        ''));

    $settings->add(new admin_setting_configcolourpicker(
        'mod_plugnmeet/footer_color',
        get_string('footer_color', 'plugnmeet'),
        get_string('footer_color_des', 'plugnmeet'),
        ''));

    $settings->add(new admin_setting_configcolourpicker(
        'mod_plugnmeet/left_color',
        get_string('left_color', 'plugnmeet'),
        get_string('left_color_des', 'plugnmeet'),
        ''));

    $settings->add(new admin_setting_configcolourpicker(
        'mod_plugnmeet/right_color',
        get_string('right_color', 'plugnmeet'),
        get_string('right_color_des', 'plugnmeet'),
        ''));
}
?>
    <script type="text/javascript">
        window.addEventListener('load', function () {
            document.getElementById('updateClient').addEventListener('click', (e) => {
                e.preventDefault();
                e.target.setAttribute('disabled', 'disabled');

                require(['core/ajax'], function (ajax) {
                    ajax.call([
                        {
                            methodname: 'mod_plugnmeet_update_client',
                            args: {},
                            done: (res) => {
                                e.target.removeAttribute('disabled');
                                alert(res.msg);
                            },
                            fail: (ex) => {
                                e.target.removeAttribute('disabled');
                                alert(ex.error);
                            },
                        },
                    ]);
                });
            });

            const client_load = document.getElementById('id_s_mod_plugnmeet_client_load');
            if (client_load.value === '1') {
                document.getElementById('admin-client_download_url').style.display = 'none';
                document.getElementById('updateClient').parentElement.style.display = 'none';
            }
            client_load.addEventListener('change', function () {
                if (this.value === "2") {
                    document.getElementById('admin-client_download_url').style.display = '';
                    document.getElementById('updateClient').parentElement.style.display = ''
                } else {
                    document.getElementById('admin-client_download_url').style.display = 'none';
                    document.getElementById('updateClient').parentElement.style.display = 'none';
                }
            });
        });
    </script>
<?php
