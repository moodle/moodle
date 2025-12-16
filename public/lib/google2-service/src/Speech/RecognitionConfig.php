<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Speech;

class RecognitionConfig extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const ENCODING_ENCODING_UNSPECIFIED = 'ENCODING_UNSPECIFIED';
  /**
   * Uncompressed 16-bit signed little-endian samples (Linear PCM).
   */
  public const ENCODING_LINEAR16 = 'LINEAR16';
  /**
   * `FLAC` (Free Lossless Audio Codec) is the recommended encoding because it
   * is lossless--therefore recognition is not compromised--and requires only
   * about half the bandwidth of `LINEAR16`. `FLAC` stream encoding supports
   * 16-bit and 24-bit samples, however, not all fields in `STREAMINFO` are
   * supported.
   */
  public const ENCODING_FLAC = 'FLAC';
  /**
   * 8-bit samples that compand 14-bit audio samples using G.711 PCMU/mu-law.
   */
  public const ENCODING_MULAW = 'MULAW';
  /**
   * Adaptive Multi-Rate Narrowband codec. `sample_rate_hertz` must be 8000.
   */
  public const ENCODING_AMR = 'AMR';
  /**
   * Adaptive Multi-Rate Wideband codec. `sample_rate_hertz` must be 16000.
   */
  public const ENCODING_AMR_WB = 'AMR_WB';
  /**
   * Opus encoded audio frames in Ogg container
   * ([OggOpus](https://wiki.xiph.org/OggOpus)). `sample_rate_hertz` must be one
   * of 8000, 12000, 16000, 24000, or 48000.
   */
  public const ENCODING_OGG_OPUS = 'OGG_OPUS';
  /**
   * Although the use of lossy encodings is not recommended, if a very low
   * bitrate encoding is required, `OGG_OPUS` is highly preferred over Speex
   * encoding. The [Speex](https://speex.org/) encoding supported by Cloud
   * Speech API has a header byte in each block, as in MIME type `audio/x-speex-
   * with-header-byte`. It is a variant of the RTP Speex encoding defined in
   * [RFC 5574](https://tools.ietf.org/html/rfc5574). The stream is a sequence
   * of blocks, one block per RTP packet. Each block starts with a byte
   * containing the length of the block, in bytes, followed by one or more
   * frames of Speex data, padded to an integral number of bytes (octets) as
   * specified in RFC 5574. In other words, each RTP header is replaced with a
   * single byte containing the block length. Only Speex wideband is supported.
   * `sample_rate_hertz` must be 16000.
   */
  public const ENCODING_SPEEX_WITH_HEADER_BYTE = 'SPEEX_WITH_HEADER_BYTE';
  /**
   * MP3 audio. MP3 encoding is a Beta feature and only available in v1p1beta1.
   * Support all standard MP3 bitrates (which range from 32-320 kbps). When
   * using this encoding, `sample_rate_hertz` has to match the sample rate of
   * the file being used.
   */
  public const ENCODING_MP3 = 'MP3';
  /**
   * Opus encoded audio frames in WebM container
   * ([WebM](https://www.webmproject.org/docs/container/)). `sample_rate_hertz`
   * must be one of 8000, 12000, 16000, 24000, or 48000.
   */
  public const ENCODING_WEBM_OPUS = 'WEBM_OPUS';
  /**
   * 8-bit samples that compand 13-bit audio samples using G.711 PCMU/a-law.
   */
  public const ENCODING_ALAW = 'ALAW';
  protected $collection_key = 'speechContexts';
  protected $adaptationType = SpeechAdaptation::class;
  protected $adaptationDataType = '';
  /**
   * A list of up to 3 additional [BCP-47](https://www.rfc-
   * editor.org/rfc/bcp/bcp47.txt) language tags, listing possible alternative
   * languages of the supplied audio. See [Language
   * Support](https://cloud.google.com/speech-to-text/docs/languages) for a list
   * of the currently supported language codes. If alternative languages are
   * listed, recognition result will contain recognition in the most likely
   * language detected including the main language_code. The recognition result
   * will include the language tag of the language detected in the audio. Note:
   * This feature is only supported for Voice Command and Voice Search use cases
   * and performance may vary for other use cases (e.g., phone call
   * transcription).
   *
   * @var string[]
   */
  public $alternativeLanguageCodes;
  /**
   * The number of channels in the input audio data. ONLY set this for MULTI-
   * CHANNEL recognition. Valid values for LINEAR16, OGG_OPUS and FLAC are
   * `1`-`8`. Valid value for MULAW, AMR, AMR_WB and SPEEX_WITH_HEADER_BYTE is
   * only `1`. If `0` or omitted, defaults to one channel (mono). Note: We only
   * recognize the first channel by default. To perform independent recognition
   * on each channel set `enable_separate_recognition_per_channel` to 'true'.
   *
   * @var int
   */
  public $audioChannelCount;
  protected $diarizationConfigType = SpeakerDiarizationConfig::class;
  protected $diarizationConfigDataType = '';
  /**
   * If 'true', adds punctuation to recognition result hypotheses. This feature
   * is only available in select languages. Setting this for requests in other
   * languages has no effect at all. The default 'false' value does not add
   * punctuation to result hypotheses.
   *
   * @var bool
   */
  public $enableAutomaticPunctuation;
  /**
   * This needs to be set to `true` explicitly and `audio_channel_count` > 1 to
   * get each channel recognized separately. The recognition result will contain
   * a `channel_tag` field to state which channel that result belongs to. If
   * this is not true, we will only recognize the first channel. The request is
   * billed cumulatively for all channels recognized: `audio_channel_count`
   * multiplied by the length of the audio.
   *
   * @var bool
   */
  public $enableSeparateRecognitionPerChannel;
  /**
   * The spoken emoji behavior for the call If not set, uses default behavior
   * based on model of choice If 'true', adds spoken emoji formatting for the
   * request. This will replace spoken emojis with the corresponding Unicode
   * symbols in the final transcript. If 'false', spoken emojis are not
   * replaced.
   *
   * @var bool
   */
  public $enableSpokenEmojis;
  /**
   * The spoken punctuation behavior for the call If not set, uses default
   * behavior based on model of choice e.g. command_and_search will enable
   * spoken punctuation by default If 'true', replaces spoken punctuation with
   * the corresponding symbols in the request. For example, "how are you
   * question mark" becomes "how are you?". See https://cloud.google.com/speech-
   * to-text/docs/spoken-punctuation for support. If 'false', spoken punctuation
   * is not replaced.
   *
   * @var bool
   */
  public $enableSpokenPunctuation;
  /**
   * If `true`, the top result includes a list of words and the confidence for
   * those words. If `false`, no word-level confidence information is returned.
   * The default is `false`.
   *
   * @var bool
   */
  public $enableWordConfidence;
  /**
   * If `true`, the top result includes a list of words and the start and end
   * time offsets (timestamps) for those words. If `false`, no word-level time
   * offset information is returned. The default is `false`.
   *
   * @var bool
   */
  public $enableWordTimeOffsets;
  /**
   * Encoding of audio data sent in all `RecognitionAudio` messages. This field
   * is optional for `FLAC` and `WAV` audio files and required for all other
   * audio formats. For details, see AudioEncoding.
   *
   * @var string
   */
  public $encoding;
  /**
   * Required. The language of the supplied audio as a [BCP-47](https://www.rfc-
   * editor.org/rfc/bcp/bcp47.txt) language tag. Example: "en-US". See [Language
   * Support](https://cloud.google.com/speech-to-text/docs/languages) for a list
   * of the currently supported language codes.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Maximum number of recognition hypotheses to be returned. Specifically, the
   * maximum number of `SpeechRecognitionAlternative` messages within each
   * `SpeechRecognitionResult`. The server may return fewer than
   * `max_alternatives`. Valid values are `0`-`30`. A value of `0` or `1` will
   * return a maximum of one. If omitted, will return a maximum of one.
   *
   * @var int
   */
  public $maxAlternatives;
  protected $metadataType = RecognitionMetadata::class;
  protected $metadataDataType = '';
  /**
   * Which model to select for the given request. Select the model best suited
   * to your domain to get best results. If a model is not explicitly specified,
   * then we auto-select a model based on the parameters in the
   * RecognitionConfig. *Model* *Description* latest_long Best for long form
   * content like media or conversation. latest_short Best for short form
   * content like commands or single shot directed speech. command_and_search
   * Best for short queries such as voice commands or voice search. phone_call
   * Best for audio that originated from a phone call (typically recorded at an
   * 8khz sampling rate). video Best for audio that originated from video or
   * includes multiple speakers. Ideally the audio is recorded at a 16khz or
   * greater sampling rate. This is a premium model that costs more than the
   * standard rate. default Best for audio that is not one of the specific audio
   * models. For example, long-form audio. Ideally the audio is high-fidelity,
   * recorded at a 16khz or greater sampling rate. medical_conversation Best for
   * audio that originated from a conversation between a medical provider and
   * patient. medical_dictation Best for audio that originated from dictation
   * notes by a medical provider.
   *
   * @var string
   */
  public $model;
  /**
   * If set to `true`, the server will attempt to filter out profanities,
   * replacing all but the initial character in each filtered word with
   * asterisks, e.g. "f***". If set to `false` or omitted, profanities won't be
   * filtered out.
   *
   * @var bool
   */
  public $profanityFilter;
  /**
   * Sample rate in Hertz of the audio data sent in all `RecognitionAudio`
   * messages. Valid values are: 8000-48000. 16000 is optimal. For best results,
   * set the sampling rate of the audio source to 16000 Hz. If that's not
   * possible, use the native sample rate of the audio source (instead of re-
   * sampling). This field is optional for FLAC and WAV audio files, but is
   * required for all other audio formats. For details, see AudioEncoding.
   *
   * @var int
   */
  public $sampleRateHertz;
  protected $speechContextsType = SpeechContext::class;
  protected $speechContextsDataType = 'array';
  protected $transcriptNormalizationType = TranscriptNormalization::class;
  protected $transcriptNormalizationDataType = '';
  /**
   * Set to true to use an enhanced model for speech recognition. If
   * `use_enhanced` is set to true and the `model` field is not set, then an
   * appropriate enhanced model is chosen if an enhanced model exists for the
   * audio. If `use_enhanced` is true and an enhanced version of the specified
   * model does not exist, then the speech is recognized using the standard
   * version of the specified model.
   *
   * @var bool
   */
  public $useEnhanced;

  /**
   * Speech adaptation configuration improves the accuracy of speech
   * recognition. For more information, see the [speech
   * adaptation](https://cloud.google.com/speech-to-text/docs/adaptation)
   * documentation. When speech adaptation is set it supersedes the
   * `speech_contexts` field.
   *
   * @param SpeechAdaptation $adaptation
   */
  public function setAdaptation(SpeechAdaptation $adaptation)
  {
    $this->adaptation = $adaptation;
  }
  /**
   * @return SpeechAdaptation
   */
  public function getAdaptation()
  {
    return $this->adaptation;
  }
  /**
   * A list of up to 3 additional [BCP-47](https://www.rfc-
   * editor.org/rfc/bcp/bcp47.txt) language tags, listing possible alternative
   * languages of the supplied audio. See [Language
   * Support](https://cloud.google.com/speech-to-text/docs/languages) for a list
   * of the currently supported language codes. If alternative languages are
   * listed, recognition result will contain recognition in the most likely
   * language detected including the main language_code. The recognition result
   * will include the language tag of the language detected in the audio. Note:
   * This feature is only supported for Voice Command and Voice Search use cases
   * and performance may vary for other use cases (e.g., phone call
   * transcription).
   *
   * @param string[] $alternativeLanguageCodes
   */
  public function setAlternativeLanguageCodes($alternativeLanguageCodes)
  {
    $this->alternativeLanguageCodes = $alternativeLanguageCodes;
  }
  /**
   * @return string[]
   */
  public function getAlternativeLanguageCodes()
  {
    return $this->alternativeLanguageCodes;
  }
  /**
   * The number of channels in the input audio data. ONLY set this for MULTI-
   * CHANNEL recognition. Valid values for LINEAR16, OGG_OPUS and FLAC are
   * `1`-`8`. Valid value for MULAW, AMR, AMR_WB and SPEEX_WITH_HEADER_BYTE is
   * only `1`. If `0` or omitted, defaults to one channel (mono). Note: We only
   * recognize the first channel by default. To perform independent recognition
   * on each channel set `enable_separate_recognition_per_channel` to 'true'.
   *
   * @param int $audioChannelCount
   */
  public function setAudioChannelCount($audioChannelCount)
  {
    $this->audioChannelCount = $audioChannelCount;
  }
  /**
   * @return int
   */
  public function getAudioChannelCount()
  {
    return $this->audioChannelCount;
  }
  /**
   * Config to enable speaker diarization and set additional parameters to make
   * diarization better suited for your application. Note: When this is enabled,
   * we send all the words from the beginning of the audio for the top
   * alternative in every consecutive STREAMING responses. This is done in order
   * to improve our speaker tags as our models learn to identify the speakers in
   * the conversation over time. For non-streaming requests, the diarization
   * results will be provided only in the top alternative of the FINAL
   * SpeechRecognitionResult.
   *
   * @param SpeakerDiarizationConfig $diarizationConfig
   */
  public function setDiarizationConfig(SpeakerDiarizationConfig $diarizationConfig)
  {
    $this->diarizationConfig = $diarizationConfig;
  }
  /**
   * @return SpeakerDiarizationConfig
   */
  public function getDiarizationConfig()
  {
    return $this->diarizationConfig;
  }
  /**
   * If 'true', adds punctuation to recognition result hypotheses. This feature
   * is only available in select languages. Setting this for requests in other
   * languages has no effect at all. The default 'false' value does not add
   * punctuation to result hypotheses.
   *
   * @param bool $enableAutomaticPunctuation
   */
  public function setEnableAutomaticPunctuation($enableAutomaticPunctuation)
  {
    $this->enableAutomaticPunctuation = $enableAutomaticPunctuation;
  }
  /**
   * @return bool
   */
  public function getEnableAutomaticPunctuation()
  {
    return $this->enableAutomaticPunctuation;
  }
  /**
   * This needs to be set to `true` explicitly and `audio_channel_count` > 1 to
   * get each channel recognized separately. The recognition result will contain
   * a `channel_tag` field to state which channel that result belongs to. If
   * this is not true, we will only recognize the first channel. The request is
   * billed cumulatively for all channels recognized: `audio_channel_count`
   * multiplied by the length of the audio.
   *
   * @param bool $enableSeparateRecognitionPerChannel
   */
  public function setEnableSeparateRecognitionPerChannel($enableSeparateRecognitionPerChannel)
  {
    $this->enableSeparateRecognitionPerChannel = $enableSeparateRecognitionPerChannel;
  }
  /**
   * @return bool
   */
  public function getEnableSeparateRecognitionPerChannel()
  {
    return $this->enableSeparateRecognitionPerChannel;
  }
  /**
   * The spoken emoji behavior for the call If not set, uses default behavior
   * based on model of choice If 'true', adds spoken emoji formatting for the
   * request. This will replace spoken emojis with the corresponding Unicode
   * symbols in the final transcript. If 'false', spoken emojis are not
   * replaced.
   *
   * @param bool $enableSpokenEmojis
   */
  public function setEnableSpokenEmojis($enableSpokenEmojis)
  {
    $this->enableSpokenEmojis = $enableSpokenEmojis;
  }
  /**
   * @return bool
   */
  public function getEnableSpokenEmojis()
  {
    return $this->enableSpokenEmojis;
  }
  /**
   * The spoken punctuation behavior for the call If not set, uses default
   * behavior based on model of choice e.g. command_and_search will enable
   * spoken punctuation by default If 'true', replaces spoken punctuation with
   * the corresponding symbols in the request. For example, "how are you
   * question mark" becomes "how are you?". See https://cloud.google.com/speech-
   * to-text/docs/spoken-punctuation for support. If 'false', spoken punctuation
   * is not replaced.
   *
   * @param bool $enableSpokenPunctuation
   */
  public function setEnableSpokenPunctuation($enableSpokenPunctuation)
  {
    $this->enableSpokenPunctuation = $enableSpokenPunctuation;
  }
  /**
   * @return bool
   */
  public function getEnableSpokenPunctuation()
  {
    return $this->enableSpokenPunctuation;
  }
  /**
   * If `true`, the top result includes a list of words and the confidence for
   * those words. If `false`, no word-level confidence information is returned.
   * The default is `false`.
   *
   * @param bool $enableWordConfidence
   */
  public function setEnableWordConfidence($enableWordConfidence)
  {
    $this->enableWordConfidence = $enableWordConfidence;
  }
  /**
   * @return bool
   */
  public function getEnableWordConfidence()
  {
    return $this->enableWordConfidence;
  }
  /**
   * If `true`, the top result includes a list of words and the start and end
   * time offsets (timestamps) for those words. If `false`, no word-level time
   * offset information is returned. The default is `false`.
   *
   * @param bool $enableWordTimeOffsets
   */
  public function setEnableWordTimeOffsets($enableWordTimeOffsets)
  {
    $this->enableWordTimeOffsets = $enableWordTimeOffsets;
  }
  /**
   * @return bool
   */
  public function getEnableWordTimeOffsets()
  {
    return $this->enableWordTimeOffsets;
  }
  /**
   * Encoding of audio data sent in all `RecognitionAudio` messages. This field
   * is optional for `FLAC` and `WAV` audio files and required for all other
   * audio formats. For details, see AudioEncoding.
   *
   * Accepted values: ENCODING_UNSPECIFIED, LINEAR16, FLAC, MULAW, AMR, AMR_WB,
   * OGG_OPUS, SPEEX_WITH_HEADER_BYTE, MP3, WEBM_OPUS, ALAW
   *
   * @param self::ENCODING_* $encoding
   */
  public function setEncoding($encoding)
  {
    $this->encoding = $encoding;
  }
  /**
   * @return self::ENCODING_*
   */
  public function getEncoding()
  {
    return $this->encoding;
  }
  /**
   * Required. The language of the supplied audio as a [BCP-47](https://www.rfc-
   * editor.org/rfc/bcp/bcp47.txt) language tag. Example: "en-US". See [Language
   * Support](https://cloud.google.com/speech-to-text/docs/languages) for a list
   * of the currently supported language codes.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Maximum number of recognition hypotheses to be returned. Specifically, the
   * maximum number of `SpeechRecognitionAlternative` messages within each
   * `SpeechRecognitionResult`. The server may return fewer than
   * `max_alternatives`. Valid values are `0`-`30`. A value of `0` or `1` will
   * return a maximum of one. If omitted, will return a maximum of one.
   *
   * @param int $maxAlternatives
   */
  public function setMaxAlternatives($maxAlternatives)
  {
    $this->maxAlternatives = $maxAlternatives;
  }
  /**
   * @return int
   */
  public function getMaxAlternatives()
  {
    return $this->maxAlternatives;
  }
  /**
   * Metadata regarding this request.
   *
   * @param RecognitionMetadata $metadata
   */
  public function setMetadata(RecognitionMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return RecognitionMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Which model to select for the given request. Select the model best suited
   * to your domain to get best results. If a model is not explicitly specified,
   * then we auto-select a model based on the parameters in the
   * RecognitionConfig. *Model* *Description* latest_long Best for long form
   * content like media or conversation. latest_short Best for short form
   * content like commands or single shot directed speech. command_and_search
   * Best for short queries such as voice commands or voice search. phone_call
   * Best for audio that originated from a phone call (typically recorded at an
   * 8khz sampling rate). video Best for audio that originated from video or
   * includes multiple speakers. Ideally the audio is recorded at a 16khz or
   * greater sampling rate. This is a premium model that costs more than the
   * standard rate. default Best for audio that is not one of the specific audio
   * models. For example, long-form audio. Ideally the audio is high-fidelity,
   * recorded at a 16khz or greater sampling rate. medical_conversation Best for
   * audio that originated from a conversation between a medical provider and
   * patient. medical_dictation Best for audio that originated from dictation
   * notes by a medical provider.
   *
   * @param string $model
   */
  public function setModel($model)
  {
    $this->model = $model;
  }
  /**
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }
  /**
   * If set to `true`, the server will attempt to filter out profanities,
   * replacing all but the initial character in each filtered word with
   * asterisks, e.g. "f***". If set to `false` or omitted, profanities won't be
   * filtered out.
   *
   * @param bool $profanityFilter
   */
  public function setProfanityFilter($profanityFilter)
  {
    $this->profanityFilter = $profanityFilter;
  }
  /**
   * @return bool
   */
  public function getProfanityFilter()
  {
    return $this->profanityFilter;
  }
  /**
   * Sample rate in Hertz of the audio data sent in all `RecognitionAudio`
   * messages. Valid values are: 8000-48000. 16000 is optimal. For best results,
   * set the sampling rate of the audio source to 16000 Hz. If that's not
   * possible, use the native sample rate of the audio source (instead of re-
   * sampling). This field is optional for FLAC and WAV audio files, but is
   * required for all other audio formats. For details, see AudioEncoding.
   *
   * @param int $sampleRateHertz
   */
  public function setSampleRateHertz($sampleRateHertz)
  {
    $this->sampleRateHertz = $sampleRateHertz;
  }
  /**
   * @return int
   */
  public function getSampleRateHertz()
  {
    return $this->sampleRateHertz;
  }
  /**
   * Array of SpeechContext. A means to provide context to assist the speech
   * recognition. For more information, see [speech
   * adaptation](https://cloud.google.com/speech-to-text/docs/adaptation).
   *
   * @param SpeechContext[] $speechContexts
   */
  public function setSpeechContexts($speechContexts)
  {
    $this->speechContexts = $speechContexts;
  }
  /**
   * @return SpeechContext[]
   */
  public function getSpeechContexts()
  {
    return $this->speechContexts;
  }
  /**
   * Optional. Use transcription normalization to automatically replace parts of
   * the transcript with phrases of your choosing. For StreamingRecognize, this
   * normalization only applies to stable partial transcripts (stability > 0.8)
   * and final transcripts.
   *
   * @param TranscriptNormalization $transcriptNormalization
   */
  public function setTranscriptNormalization(TranscriptNormalization $transcriptNormalization)
  {
    $this->transcriptNormalization = $transcriptNormalization;
  }
  /**
   * @return TranscriptNormalization
   */
  public function getTranscriptNormalization()
  {
    return $this->transcriptNormalization;
  }
  /**
   * Set to true to use an enhanced model for speech recognition. If
   * `use_enhanced` is set to true and the `model` field is not set, then an
   * appropriate enhanced model is chosen if an enhanced model exists for the
   * audio. If `use_enhanced` is true and an enhanced version of the specified
   * model does not exist, then the speech is recognized using the standard
   * version of the specified model.
   *
   * @param bool $useEnhanced
   */
  public function setUseEnhanced($useEnhanced)
  {
    $this->useEnhanced = $useEnhanced;
  }
  /**
   * @return bool
   */
  public function getUseEnhanced()
  {
    return $this->useEnhanced;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RecognitionConfig::class, 'Google_Service_Speech_RecognitionConfig');
