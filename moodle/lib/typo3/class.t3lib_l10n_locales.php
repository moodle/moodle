<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Xavier Perseguers <typo3@perseguers.ch>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Locales.
 *
 * Defining backend system languages
 * When adding new keys, remember to:
 * 		- Add character encoding for lang. key in t3lib/class.t3lib_cs.php (default for new languages is "utf-8")
 * 		- Add mappings for language in t3lib/class.t3lib_cs.php (TYPO3/ISO, language/script, script/charset)
 * 		- Update 'setup' extension labels (sysext/setup/mod/locallang.xlf)
 * That's it!
 *
 * @package	Core
 * @subpackage	t3lib
 * @author	Xavier Perseguers <typo3@perseguers.ch>
 */
class t3lib_l10n_Locales implements t3lib_Singleton {

	/**
	 * Supported TYPO3 languages with locales
	 * @var array
	 */
	protected $languages = array(
		'default' => 'English',
		'af' => 'Afrikaans',
		'ar' => 'Arabic',
		'bs' => 'Bosnian',
		'bg' => 'Bulgarian',
		'ca' => 'Catalan',
		'ch' => 'Chinese (Simpl.)',
		'cs' => 'Czech',
		'da' => 'Danish',
		'de' => 'German',
		'el' => 'Greek',
		'eo' => 'Esperanto',
		'es' => 'Spanish',
		'et' => 'Estonian',
		'eu' => 'Basque',
		'fa' => 'Persian',
		'fi' => 'Finnish',
		'fo' => 'Faroese',
		'fr' => 'French',
		'fr_CA' => 'French (Canada)',
		'gl' => 'Galician',
		'he' => 'Hebrew',
		'hi' => 'Hindi',
		'hr' => 'Croatian',
		'hu' => 'Hungarian',
		'is' => 'Icelandic',
		'it' => 'Italian',
		'ja' => 'Japanese',
		'ka' => 'Georgian',
		'kl' => 'Greenlandic',
		'km' => 'Khmer',
		'ko' => 'Korean',
		'lt' => 'Lithuanian',
		'lv' => 'Latvian',
		'ms' => 'Malay',
		'nl' => 'Dutch',
		'no' => 'Norwegian',
		'pl' => 'Polish',
		'pt' => 'Portuguese',
		'pt_BR' => 'Brazilian Portuguese',
		'ro' => 'Romanian',
		'ru' => 'Russian',
		'sk' => 'Slovak',
		'sl' => 'Slovenian',
		'sq' => 'Albanian',
		'sr' => 'Serbian',
		'sv' => 'Swedish',
		'th' => 'Thai',
		'tr' => 'Turkish',
		'uk' => 'Ukrainian',
		'vi' => 'Vietnamese',
		'zh' => 'Chinese (Trad.)',
	);

	/**
	 * Supported TYPO3 locales
	 * @deprecated since TYPO3 4.6, will be removed in TYPO3 6.0
	 * @var array
	 */
	protected $locales = array();

	/**
	 * Mapping with codes used by TYPO3 4.5 and below
	 * @var array
	 */
	protected $isoReverseMapping = array(
		'bs' => 'ba',		// Bosnian
		'cs' => 'cz',		// Czech
		'da' => 'dk',		// Danish
		'el' => 'gr',		// Greek
		'fr_CA' => 'qc',	// French (Canada)
		'gl' => 'ga',		// Galician
		'ja' => 'jp',		// Japanese
		'ka' => 'ge',		// Georgian
		'kl' => 'gl',		// Greenlandic
		'ko' => 'kr',		// Korean
		'ms' => 'my',		// Malay
		'pt_BR' => 'br',	// Portuguese (Brazil)
		'sl' => 'si',		// Slovenian
		'sv' => 'se',		// Swedish
		'uk' => 'ua',		// Ukrainian
		'vi' => 'vn',		// Vietnamese
		'zh' => 'hk',		// Chinese (China)
		'zh_CN' => 'ch',	// Chinese (Simplified)
		'zh_HK' => 'hk',	// Chinese (China)
	);

	/**
	 * @var array
	 */
	protected $isoMapping;

	/**
	 * Dependencies for locales
	 * @var array
	 */
	protected $localeDependencies;

	/**
	 * Initializes the languages.
	 *
	 * @static
	 * @return void
	 */
	public static function initialize() {
		/** @var $instance t3lib_l10n_Locales */
		$instance = t3lib_div::makeInstance('t3lib_l10n_Locales');
		$instance->isoMapping = array_flip($instance->isoReverseMapping);

			// Allow user-defined locales
		if (isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['localization']['locales']['user']) && is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['localization']['locales']['user'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['SYS']['localization']['locales']['user'] as $locale => $name) {
				if (!isset($instance->languages[$locale])) {
					$instance->languages[$locale] = $name;
				}
			}
		}

			// Initializes the locale dependencies with TYPO3 supported locales
		$instance->localeDependencies = array();
		foreach ($instance->languages as $locale => $name) {
			if (strlen($locale) == 5) {
				$instance->localeDependencies[$locale] = array(substr($locale, 0, 2));
			}
		}
			// Merge user-provided locale dependencies
		if (isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['localization']['locales']['dependencies']) && is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['localization']['locales']['dependencies'])) {
			$instance->localeDependencies = t3lib_div::array_merge_recursive_overrule($instance->localeDependencies, $GLOBALS['TYPO3_CONF_VARS']['SYS']['localization']['locales']['dependencies']);
		}

		/**
		 * @deprecated since TYPO3 4.6, will be removed in TYPO3 6.0
		 */
		$instance->locales = array_keys($instance->languages);

		/**
		 * @deprecated since TYPO3 4.6, will be removed in TYPO3 6.0
		 */
		define('TYPO3_languages', implode('|', $instance->getLocales()));
	}

	/**
	 * Returns the locales.
	 *
	 * @return array
	 */
	public function getLocales() {
		return array_keys($this->languages);
	}

	/**
	 * Returns the supported languages indexed by their corresponding locale.
	 *
	 * @return array
	 */
	public function getLanguages() {
		return $this->languages;
	}

	/**
	 * Returns the mapping between TYPO3 (old) language codes and ISO codes.
	 *
	 * @return array
	 */
	public function getIsoMapping() {
		return $this->isoMapping;
	}

	/**
	 * Returns the locales as referenced by the TER and TYPO3 localization files.
	 *
	 * @return array
	 * @deprecated since TYPO3 4.6
	 */
	public function getTerLocales() {
		return $this->convertToTerLocales(array_keys($this->languages));
	}

	/**
	 * Returns the dependencies of a given locale, if any.
	 *
	 * @param string $locale
	 * @return array
	 */
	public function getLocaleDependencies($locale) {
		$dependencies = array();
		if (isset($this->localeDependencies[$locale])) {
			$dependencies = $this->localeDependencies[$locale];

				// Search for dependencies recursively
			$localeDependencies = $dependencies;
			foreach ($localeDependencies as $dependency) {
				if (isset($this->localeDependencies[$dependency])) {
					$dependencies = array_merge($dependencies, $this->getLocaleDependencies($dependency));
				}
			}
		}
		return $dependencies;
	}

	/**
	 * Returns the dependencies of a given locale using TER compatible locale codes.
	 *
	 * @param string $locale
	 * @return array
	 * @deprecated since TYPO3 4.6
	 */
	public function getTerLocaleDependencies($locale) {
		$terLocale = isset($this->isoMapping[$locale])
				? $this->isoMapping[$locale]
				: $locale;
		return $this->convertToTerLocales($this->getLocaleDependencies($terLocale));
	}

	/**
	 * Converts an array of ISO locale codes into their TER equivalent.
	 *
	 * @param array $locales
	 * @return array
	 * @deprecated since TYPO3 4.6
	 */
	protected function convertToTerLocales(array $locales) {
		$terLocales = array();
		foreach ($locales as $locale) {
			$terLocales[] = isset($this->isoReverseMapping[$locale]) ? $this->isoReverseMapping[$locale] : $locale;
		}
		return $terLocales;
	}

}


if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['t3lib/l10n/class.t3lib_l10n_locales.php'])) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['t3lib/l10n/class.t3lib_l10n_locales.php']);
}

?>