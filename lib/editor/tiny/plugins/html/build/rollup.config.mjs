import {nodeResolve} from "@rollup/plugin-node-resolve"
export default {
  input: "./codemirror.mjs",
  output: {
    file: "../amd/src/codemirror.js",
    format: "esm"
  },
  plugins: [nodeResolve()]
}
