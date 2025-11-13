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
 * Color palette setter for Tiny Font Color plugin.
 * This code is mostly reused from the TinyMCE silver theme. However, this
 * code is enhanced to have two different color maps for background and
 * text color. Also, the option to enable or disable custom colors
 * via a colorpicker can be set independently on both text- and
 * background color. If the colorpicker is disabled and the color
 * map is empty for one of the text- or background color, the menu entry as
 * well as the toolbar button will not appear in the editor.
 *
 * @module      tiny_fontcolor
 * @copyright   2023 Luca Bösch <luca.boesch@bfh.ch>
 * @copyright   2023 Stephan Robotta <stephan.robotta@bfh.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// ESLint directives.

import * as pf from './polyfill';
import {
  getBackcolorMap,
  getForecolorMap,
  isBackcolorPickerOn,
  isForecolorPickerOn,
  useCssClasses,
  getBackcolorClasses,
  getForecolorClasses
} from './options';
import {forecolor, backcolor} from './common';
import {isHexString, isNullable} from "./polyfill";

let global$4 = localStorage;

const map$2 = (xs, f) => {
  const len = xs.length;
  const r = new Array(len);
  for (let i = 0; i < len; i++) {
    const x = xs[i];
    r[i] = f(x, i);
  }
  return r;
};

const Label = () => {
  let labels;
  const register = txt => {
    labels = txt;
  };
  const get = (name, ...args) => {
    let val = !isNullable(labels[name]) ? labels[name] : name;
    if (!isNullable(args)) {
      for (let x = 0; x < args.length; x++) {
        val = val.replace('{' + x + '}', args[x]);
      }
    }
    return val;
  };
  return {
    get,
    register,
  };
};
// eslint-disable-next-line
const labels = Label();

const Cell = initial => {
  let value = initial;
  const get = () => {
    return value;
  };
  const set = v => {
    value = v;
  };
  return {
    get,
    set
  };
};

const fireTextColorChange = (editor, data) => {
  editor.dispatch('TextColorChange', data);
};

const storageName = 'tinymce-custom-colors';
const ColorCache = (max = 10, suffix = '') => {
  const storageString = global$4.getItem(storageName + suffix);
  const localstorage = pf.isString(storageString) ? JSON.parse(storageString) : [];
  const prune = list => {
    const diff = max - list.length;
    return diff < 0 ? list.slice(0, max) : list;
  };
  const cache = prune(localstorage);
  const add = key => {
    pf.indexOf(cache, key).each(remove);
    cache.unshift(key);
    if (cache.length > max) {
      cache.pop();
    }
    global$4.setItem(storageName, JSON.stringify(cache));
  };
  const remove = idx => {
    cache.splice(idx, 1);
  };
  const state = () => cache.slice(0);
  return {
    add,
    state
  };
};

// eslint-disable-next-line
const colorCache = ColorCache(10);
// eslint-disable-next-line
const colorCacheBg = ColorCache(10, '-background');

const option$1 = name => editor => editor.options.get(name);

const getColorCols$1 = option$1('color_cols');
const getColors$3 = (editor, name) => {
  if (name === forecolor) {
    return getForecolorMap(editor);
  }
  return getBackcolorMap(editor);
};
const getCurrentColors = (type) => map$2(type === forecolor ? colorCache.state() : colorCacheBg.state(), color => ({
  type: 'choiceitem',
  text: color,
  value: color
}));
const addColor = (color, format) => {
  if (forecolor.includes(format)) {
    colorCache.add(color);
  } else {
    colorCacheBg.add(color);
  }
};

const fallbackColor = '#000000';
const hasStyleApi = node => pf.isNonNullable(node.style);
const getCurrentColor = (editor, format) => {
  let color;
  editor.dom.getParents(editor.selection.getStart(), elm => {
    const value = hasStyleApi(elm) ? elm.style[format === forecolor ? 'color' : 'backgroundColor'] : null;
    if (value) {
      color = color ? color : value;
    }
  });
  return pf.Optional.from(color);
};
const applyFormat = (editor, format, value) => {
  editor.undoManager.transact(() => {
    editor.focus();
    editor.formatter.apply(format, {value});
    editor.nodeChanged();
  });
};
const removeFormat = (editor, format) => {
  editor.undoManager.transact(() => {
    editor.focus();
    editor.formatter.remove(format, {value: null}, undefined, true);
    editor.nodeChanged();
  });
};
const registerLabels = txt => {
    labels.register(txt);
};
const registerCommands = editor => {
  editor.addCommand('mceApplyTextcolor', (format, value) => {
    applyFormat(editor, format, value);
  });
  editor.addCommand('mceRemoveTextcolor', format => {
    removeFormat(editor, format);
  });
};
const handleColorChange = (editor, format, value) => {
  if (useCssClasses(editor)) {
    const cssClass = forecolor.includes(format)
      ? getForecolorClasses(editor).find((v) => v[1] === value)
      : getBackcolorClasses(editor).find((v) => v[1] === value);
    if (cssClass) {
      editor.execCommand('mceApplyTextcolor', 'fontcolor_classes', cssClass[0]);
      return;
    }
  }
  editor.execCommand('mceApplyTextcolor', format, value);
};
const handleColorRemove = (editor, format) => {
  if (useCssClasses(editor)) {
    editor.execCommand('mceRemoveTextcolor', 'fontcolor_classes');
    return;
  }
  editor.execCommand('mceRemoveTextcolor', format);
};
const getAdditionalColors = hasCustom => {
  const type = 'choiceitem';
  const remove = {
    type,
    text: labels.get('removeColor'),
    icon: 'color-swatch-remove-color',
    value: 'remove'
  };
  const custom = {
    type,
    text: labels.get('customColor'),
    icon: 'color-picker',
    value: 'custom'
  };
  return hasCustom ? [
    remove,
    custom
  ] : [remove];
};
const applyColor = (editor, format, value, onChoice) => {
  if (value === 'custom') {
    const dialog = colorPickerDialog(editor);
    dialog(colorOpt => {
      colorOpt.each(color => {
        addColor(color, format);
        handleColorChange(editor, format, color);
        onChoice(color);
      });
    }, fallbackColor);
  } else if (value === 'remove') {
    onChoice('');
    handleColorRemove(editor, format);
  } else {
    onChoice(value);
    handleColorChange(editor, format, value);
  }
};
const getColors$1 = (colors, hasCustom, type) => colors.concat(getCurrentColors(type).concat(getAdditionalColors(hasCustom)));
const getFetch$1 = (colors, hasCustom, type) => callback => {
  callback(getColors$1(colors, hasCustom, type));
};
const setIconColor = (splitButtonApi, name, newColor) => {
  const id = name === forecolor ? 'tox-icon-text-color__color' : 'tox-icon-highlight-bg-color__color';
  splitButtonApi.setIconFill(id, newColor);
};
const registerTextColorButton = (editor, name, format, tooltip, lastColor) => {
  let iconName, hasCustom;
  if (name === forecolor) {
    iconName = 'text-color';
    hasCustom = isForecolorPickerOn(editor);
  } else {
    iconName = 'highlight-bg-color';
    hasCustom = isBackcolorPickerOn(editor);
  }
  editor.ui.registry.addSplitButton(name, {
    tooltip,
    presets: 'color',
    icon: iconName,
    select: value => {
      const optCurrentRgb = getCurrentColor(editor, format);
      return optCurrentRgb.bind(currentRgb => pf.fromString(currentRgb).map(rgba => {
        const currentHex = pf.fromRgba(rgba).value;
        return pf.contains$1(value.toLowerCase(), currentHex);
      })).getOr(false);
    },
    columns: getColorCols$1(editor),
    fetch: getFetch$1(getColors$3(editor, name), hasCustom, name),
    onAction: () => {
      applyColor(editor, format, lastColor.get(), pf.noop);
    },
    onItemAction: (_splitButtonApi, value) => {
      applyColor(editor, format, value, newColor => {
        lastColor.set(newColor);
        fireTextColorChange(editor, {
          name,
          color: newColor
        });
      });
    },
    onSetup: splitButtonApi => {
      setIconColor(splitButtonApi, name, lastColor.get());
      const handler = e => {
        if (e.name === name) {
          setIconColor(splitButtonApi, e.name, e.color);
        }
      };
      editor.on('TextColorChange', handler);
      return () => {
        editor.off('TextColorChange', handler);
      };
    }
  });
};
const registerTextColorMenuItem = (editor, name, format, text) => {
  editor.ui.registry.addNestedMenuItem(name, {
    text,
    icon: name === forecolor ? 'text-color' : 'highlight-bg-color',
    getSubmenuItems: () => [{
      type: 'fancymenuitem',
      fancytype: 'colorswatch',
      initData: {
        allowCustomColors: name === forecolor ? isForecolorPickerOn(editor) : isBackcolorPickerOn(editor),
        colors: getColors$3(editor, name),
      },
      onAction: data => {
        applyColor(editor, format, data.value, pf.noop);
      }
    }]
  });
};
const colorPickerDialog = editor => (callback, value) => {
  const onSubmit = api => {
    const data = api.getData();
    const hex = data.colorpicker;
    const err = document.querySelector('.dlg-color-picker-error');
    let isValid = true;
    err.parentNode.parentNode.querySelectorAll('input').forEach((i, x) => {
      if (x < 3) {
        const m = ['R', 'G', 'B'];
        const r = parseInt(i.value);
        if (!i.value.match(/^\d{1,3}$/) || r < 0 || r > 255) {
          err.innerHTML = labels.get('colorPickerErrRgbCode', m[x] + ' = ' + i.value);
          i.focus();
          isValid = false;
        }
      } else if (!isHexString('#' + i.value)) {
        err.innerHTML = labels.get('colorPickerErrHexCode', hex);
        i.focus();
        isValid = false;
      }
    });
    if (isValid) {
      callback(pf.Optional.from(hex));
      api.close();
    } else {
      err.classList.remove('hidden');
      err.classList.add('alert');
    }
  };
  const initialData = {colorpicker: value};
  editor.windowManager.open({
    title: labels.get('colorPickerTitle'),
    size: 'normal',
    body: {
      type: 'panel',
      items: [{
        type: 'htmlpanel',
        html: '<span class="dlg-color-picker-error hidden"></span>',
      }, {
        type: 'colorpicker',
        name: 'colorpicker',
        label: labels.get('colorPickerColor'),
      }]
    },
    buttons: [
      {
        type: 'cancel',
        name: 'cancel',
        text: labels.get('colorPickerCancel'),
      },
      {
        type: 'submit',
        name: 'save',
        text: labels.get('colorPickerSave'),
        primary: true
      }
    ],
    initialData,
    onSubmit,
    onClose: pf.noop,
    onCancel: () => {
      callback(pf.Optional.none());
    }
  });
};
const register$c = (editor, txt) => {
  if (!isForecolorPickerOn(editor) && !isBackcolorPickerOn(editor)
    && getForecolorMap(editor).length === 0 && getBackcolorMap(editor).length === 0) {
    return;
  }
  registerLabels(txt);
  registerCommands(editor);
  if (isForecolorPickerOn(editor) || getForecolorMap(editor).length > 0) {
    // eslint-disable-next-line
    const lastForeColor = Cell(fallbackColor);
    registerTextColorButton(editor, forecolor, 'forecolor', labels.get('btnFgColor'), lastForeColor);
    registerTextColorMenuItem(editor, forecolor, 'forecolor', labels.get('menuItemFgcolor'));
  }
  if (isBackcolorPickerOn(editor) || getBackcolorMap(editor).length > 0) {
    // eslint-disable-next-line
    const lastBackColor = Cell(fallbackColor);
    registerTextColorButton(editor, backcolor, 'hilitecolor', labels.get('btnBgcolor'), lastBackColor);
    registerTextColorMenuItem(editor, backcolor, 'hilitecolor', labels.get('menuItemBgcolor'));
  }

  // The css clases are is theme_<name>/scss are not present in the editor.
  // Therefore, we must add manually our css classes for the color management.
  if (useCssClasses(editor)) {
    editor.on('SkinLoaded', () => {
      editor.formatter.register('fontcolor_classes', {
        inline: 'span',
        attributes: {'class': '%value'},
        links: true,
        // eslint-disable-next-line camelcase
        remove_similar: true,
        // eslint-disable-next-line camelcase
        clear_child_styles: true
      });
      const contentStyles = [];
      getBackcolorClasses(editor).forEach((e) => {
        contentStyles.push(`.${e[0]}{background-color:${e[1]}}`);
      });
      getForecolorClasses(editor).forEach((e) => {
        contentStyles.push(`.${e[0]}{color: ${e[1]}}`);
      });
      // Append the css to the already existing css.
      const mceDefaultStyles = editor.dom.select('#mceDefaultStyles');
      if (mceDefaultStyles.length > 0) {
        mceDefaultStyles[0].innerHTML += contentStyles.join(' ');
      } else {
        const styleEl = document.createElement('style');
        styleEl.setAttribute('type', 'text/css');
        const cssText = document.createTextNode(contentStyles.join(''));
        styleEl.appendChild(cssText);
        editor.dom.select('head')[0].appendChild(styleEl);
      }
    });
  }
};

export {
  register$c,
};
