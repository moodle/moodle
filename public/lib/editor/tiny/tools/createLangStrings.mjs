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

import {readdir, readFile, writeFile, mkdir} from 'fs/promises';
import {join as joinPath} from 'path';

const fullyTranslatedLanguage = 'de';
const maxStringIdentifierLength = 90;

const readStringsFromLanguages = async (language) => {
    const fileContent = await readFile(`./langs/${language}.js`, 'utf-8');

    let translations = {};
    const tinymce = {
        addI18n: (language, strings) => {
            translations = strings;
        },
    };

    eval(fileContent);

    return Object.keys(translations).sort().reduce((sortedTranslations, key) => {
        sortedTranslations[key] = translations[key];
        return sortedTranslations;
    }, {});
};

const getStringMap = (strings) => {
    const stringMap = {};

    const getUniqueKeyForString = (string, modifier = 0) => {
        let stringKey = string.toLowerCase()
            .replaceAll(' ', '_')
            .replaceAll(/\{(\d)\}/g, '$1')
            .replaceAll('#', 'hash')
            .replaceAll(/[^a-z0-9_\-\.]/g, '')
            ;

        if (stringKey === '') {
            throw new Error(`The calculated key for '${string}' was empty`);
        }

        stringKey = `tiny:${stringKey}`;

        if (stringKey.length > maxStringIdentifierLength) {
            const modifierLength = modifier === 0 ? 0 : `${modifier}`.length;
            stringKey = stringKey.slice(0, maxStringIdentifierLength - modifierLength);
        }

        if (modifier > 0) {
            stringKey = `${stringKey}${modifier}`;
        }

        if (typeof stringMap[stringKey] !== 'undefined') {
            return getUniqueKeyForString(string, ++modifier);
        }

        return stringKey;
    };

    strings.forEach((string) => {
        const stringKey = getUniqueKeyForString(string);
        if (typeof stringMap[stringKey] !== 'undefined') {
            throw new Error(`Found existing key ${stringKey}`);
        }

        stringMap[stringKey] = string;
    });

    return stringMap;
};

const getPhpStrings = (stringMap, translatedStrings) => Object.entries(stringMap).map(([stringKey, englishString]) => {
    if (translatedStrings[englishString].length === 0) {
        return null;
    }
    return `$string['${stringKey}'] = '${translatedStrings[englishString].replaceAll("'", "\\\'")}';`
})
.filter((value) => value !== null)
.join("\n");

const storeEnglishStrings = async(stringMap) => {
    const englishStrings = Object.entries(stringMap).map(([stringKey, stringValue]) => {
        return `$string['${stringKey}'] = '${stringValue.replace("'", "\\\'")}';`
    }).join("\n");
    await writeFile('./strings.php', englishStrings + "\n");
    await writeFile('./tinystrings.json', JSON.stringify(stringMap, null, '  '));
}

const constructTranslationFile = async(language, englishStringMap = null) => {
    const strings = await readStringsFromLanguages(language);
    console.log(`Generating translation data for ${language} with ${Object.keys(strings).length} strings`);
    const stringMap = englishStringMap === null ? getStringMap(Object.keys(strings)) : englishStringMap;

    const langDir = joinPath('lang', language);
    await mkdir(langDir, {recursive: true});

    const fileContent = `<?php

${getPhpStrings(stringMap, strings)}
`;

    await writeFile(joinPath(langDir, `editor_tiny.php`), fileContent);

    return {
        strings,
        stringMap,
    };
};

const constructTranslationFiles = async() => {
    const {stringMap} = await constructTranslationFile(fullyTranslatedLanguage);
    storeEnglishStrings(stringMap);

    readdir('./langs/').then((files) => {
        files.forEach(async(file) => {
            const langIdent = file.replace('.js', '');
            if (langIdent === fullyTranslatedLanguage) {
                // This language is already done.
                return;
            }
            await constructTranslationFile(langIdent, stringMap);
        });
    });
}

constructTranslationFiles();
