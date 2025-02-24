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

/**
 * Convert audio to MP3.
 *
 * @module     tiny_recordrtc/convert_to_mp3
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import lamejs from './lame.all';

/**
 * Extract Pulse Code Modulation (PCM) data from an AudioBuffer to get raw channel data.
 *
 * @param {AudioBuffer} audioBuffer The AudioBuffer containing the audio data.
 * @returns {Array<Int16Array>} The PCM data for each channel.
 */
const extractPCM = (audioBuffer) => {
    const channelData = [];
    const numberOfChannels = audioBuffer.numberOfChannels;
    const audioBufferLength = audioBuffer.length;

    for (let channel = 0; channel < numberOfChannels; channel++) {
        const rawChannelData = audioBuffer.getChannelData(channel);
        channelData[channel] = new Int16Array(audioBufferLength);
        // Convert floating-point audio samples into 16-bit signed integer values.
        for (let i = 0; i < audioBufferLength; i++) {
            channelData[channel][i] = rawChannelData[i] * 32768;
        }
    }

    return channelData;
};

/**
 * Fetches and decodes the audio data from a given URL into an AudioBuffer.
 *
 * @param {string} sourceUrl - The URL of the source audio file.
 * @returns {Promise<AudioBuffer>} - A promise that resolves with the decoded AudioBuffer object.
 */
const getAudioBuffer = async(sourceUrl) => {
    const response = await fetch(sourceUrl);
    const arrayBuffer = await response.arrayBuffer();
    const audioContext = new (
        window.AudioContext // Default.
        || window.webkitAudioContext // Safari and old versions of Chrome.
    )();
    return audioContext.decodeAudioData(arrayBuffer);
};

/**
 * Converts an AudioBuffer to MP3 format using lamejs.
 *
 * @param {Object} lamejs - The lamejs library object.
 * @param {number} channels - The number of audio channels (1 for mono, 2 for stereo).
 * @param {number} sampleRate - The sample rate of the audio (e.g., 44100 Hz).
 * @param {number} bitRate - The bitrate (in kbps) to encode the MP3.
 * @param {Int16Array} left - The PCM data for the left channel.
 * @param {Int16Array} [right=null] - The PCM data for the right channel (optional for stereo).
 * @returns {Blob} - A Blob containing the MP3 audio data.
 */
const convertAudioBuffer = (lamejs, channels, sampleRate, bitRate, left, right = null) => {
    const mp3Data = [];
    const mp3Encoder = new lamejs.Mp3Encoder(channels, sampleRate, bitRate);
    // Each frame represents 1152 audio samples per channel (for both mono and stereo).
    const sampleBlockSize = 1152;

    // Ensure that the same encoding logic works for both mono and stereo audio by
    // either passing both channels or just the left channel to the MP3 encoder.
    for (let i = 0; i < left.length; i += sampleBlockSize) {
        const leftChunk = left.subarray(i, i + sampleBlockSize);
        const mp3Buf = right
            ? mp3Encoder.encodeBuffer(leftChunk, right.subarray(i, i + sampleBlockSize)) // Stereo.
            : mp3Encoder.encodeBuffer(leftChunk); // Mono.

        if (mp3Buf.length) {
            mp3Data.push(mp3Buf);
        }
    }

    // Preventing loss of the last few samples of audio.
    const mp3Buf = mp3Encoder.flush();
    if (mp3Buf.length) {
        mp3Data.push(new Int8Array(mp3Buf));
    }

    return new Blob(mp3Data, {type: 'audio/mp3'});
};

/**
 * Main function to handle the entire process of converting an audio file to MP3 format.
 *
 * @param {string} sourceUrl - The URL of the source audio file to be converted.
 * @param {number} [bitRate=128] - The bitrate (in kbps) for the MP3 conversion. Default is 128 kbps.
 * @returns {Promise<Blob>} - A promise that resolves with the MP3 file as a Blob.
 *
 * @throws {Error} If the Lamejs module or audio buffer fails to load.
 *
 * @example
 * const mp3Data = await convertMp3('audio-source.wav', 192);
 * window.console.log(mp3Data); // Logs the ArrayBuffer with MP3 data.
 */
export const convertMp3 = async(sourceUrl, bitRate = 128) => {
    const audioBuffer = await getAudioBuffer(sourceUrl);
    const [left, right] = extractPCM(audioBuffer);
    return convertAudioBuffer(lamejs, audioBuffer.numberOfChannels, audioBuffer.sampleRate, bitRate, left, right);
};
