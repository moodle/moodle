const editor = grapesjs.init({
  container: "#editor",
  fromElement: true,
  width: "auto",
  storageManager: false,
  plugins: ["gjs-preset-webpage"],
  pluginsOpts: {
    "gjs-preset-webpage": {},
  },
});
