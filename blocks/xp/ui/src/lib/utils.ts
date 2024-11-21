export const classNames = (...args: any[]) => args.filter(Boolean).join(" ");

export const fifoCache = <T>(maxItems = 128): { get: (k: string) => T | undefined; set: (k: string, v: T) => void } => {
  let items: { [index: string]: any } = {};
  let keys: string[] = [];

  const purge = () => {
    if (keys.length > maxItems) {
      const idx = Math.max(0, keys.length - maxItems);
      keys.slice(0, idx).forEach((key) => {
        delete items[key];
      });
      keys = keys.slice(idx);
    }
  };

  return {
    set: (key: string, value: any) => {
      items[key] = value;
      keys.push(key);
      purge();
    },
    get: (key: string) => {
      return items[key];
    },
  };
};

let uniqueId = 0;
export const getUniqueId = () => {
  return `xp-${Date.now()}-${uniqueId++}`;
};

export const stripTags = (html: string) => {
  var tmp = document.createElement("div");
  tmp.innerHTML = html;
  return tmp.textContent || tmp.innerText || "";
};

const escapeCharMap: Record<string, string> = {
  '&': '&amp;',
  '<': '&lt;',
  '>': '&gt;',
  '"': '&quot;',
  "'": '&#039;'
};
export const escapeHtml = (text: string) => {
  return text.replace(/[&<>"']/g, function(m) { return escapeCharMap[m]; });
}