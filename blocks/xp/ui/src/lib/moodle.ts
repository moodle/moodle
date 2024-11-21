import { fifoCache } from "./utils";

const M = (window as any).M;
const modules: { [index: string]: any } = {};

/**
 * List of modules that we currently depend on statically.
 *
 * Preferrably, modules should be loaded with getModuleAsync, which
 * does not require their definition to be declared in our apps.
 */
export const commonStaticModulesToDependOn = [
  "core/notification",
  "core/aria",
  "?core/toast",
  "jquery",
];

export async function ajaxRequest<T = any>(method: string, args: any) {
  const Ajax = await getModuleAsync("core/ajax");
  return Ajax.call([{
    methodname: method,
    args,
  }])[0] as Promise<T>;
}

export function getString(id: string, component: string, a?: any) {
  return M.util.get_string(id, component, a);
}

export function getUrl(uri: string) {
  if (uri[0] != "/") {
    uri = "/" + uri;
  }
  return M.cfg.wwwroot + uri;
}

export function hasString(id: string, component: string) {
  return typeof M.str[component] !== "undefined" && typeof M.str[component][id] !== "undefined";
}

export function getModule(name: string): any {
  return modules[name];
}

export async function getModuleAsync(amd: string): Promise<any> {
  if (modules[amd]) {
    return modules[amd];
  }
  return new Promise((resolve, reject) => {
    // @ts-ignore
    window.require([amd], (mod) => {
      modules[amd] = mod;
      resolve(mod);
    }, reject);
  });
}

export function imageUrl(name: string, component: string) {
  return M.util.image_url(name, component);
}

export function isBehatRunning() {
  return M.cfg.behatsiterunning;
}

let loadStringCache = fifoCache<Promise<any>>(64);

export async function loadString(id: string, component: string) {
  const cacheKey = `${id}/${component}`;
  let promise = loadStringCache.get(cacheKey);
  if (!promise) {
    const Str = await getModuleAsync("core/str");
    promise = Str.get_string(id, component);
    loadStringCache.set(cacheKey, promise as Promise<any>);
  }
  return await promise;
}

export async function loadStrings(ids: string[], component: string) {
  const cacheKey = `${ids.join(",")}/${component}`;
  let promise = loadStringCache.get(cacheKey);
  if (!promise) {
    const Str = await getModuleAsync("core/str");
    promise = Str.get_strings(ids.map((id) => ({ key: id, component })));
    loadStringCache.set(cacheKey, promise as Promise<any>);
  }
  return await promise;
}

export const makeDependenciesDefinition = (names: string[]) => {
  let optional: string[] = [];

  const list = names.map((name) => {
    const isOptional = name.charAt(0) === "?";
    const module = isOptional ? name.substring(1) : name;
    if (isOptional) {
      optional.push(module);
    }
    return module;
  });

  return {
    list,
    optional,
    loader: (mods: any[]) => {
      mods.forEach((mod, i) => {
        setModule(list[i], mod);
      });
    },
  };
};

export function setModule(name: string, mod: any) {
  modules[name] = mod;
}
