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

import {readFile, writeFile} from 'fs/promises';

const maxStringIdentifierLength = 90;

const readStringsFromLanguages = async (language) => {
    const fileContent = await readFile(`./langs/${language}.js`, 'utf-8');

    const translations = [];
    const tinymce = {
        addI18n: (language, strings) => {
            translations.push(...(Object.keys(strings)));
        },
    };

    eval(fileContent);

    return translations.sort();
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

const getPhpStrings = (stringMap) => Object.entries(stringMap).map(([stringKey, stringValue]) => {
    return `$string['${stringKey}'] = '${stringValue.replace("'", "\\\'")}';`
}).join("\n");


const constructTranslationFile = async(language) => {
    const strings = await readStringsFromLanguages(language);
    const stringMap = getStringMap(strings);

    await writeFile('./strings.php', getPhpStrings(stringMap) + "\n");
    await writeFile('./tinystrings.json', JSON.stringify(stringMap, null, '  '));
};

constructTranslationFile('de');
