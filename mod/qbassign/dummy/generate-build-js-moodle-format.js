// Ref URL : https://coderrocketfuel.com/article/recursively-list-all-the-files-in-a-directory-using-node-js
const fs = require("fs")
const path = require("path")

const getAllFiles = function(dirPath, arrayOfFiles) {
  files = fs.readdirSync(dirPath)

  arrayOfFiles = arrayOfFiles || []

  files.forEach(function(file) {
    if (fs.statSync(dirPath + "/" + file).isDirectory()) {
      arrayOfFiles = getAllFiles(dirPath + "/" + file, arrayOfFiles)
    } else {
      // arrayOfFiles.push(path.join(__dirname, dirPath, "/", file))
	  let s = path.join(dirPath, "/", file);
	  let s1 = s.replaceAll("\\", "/");
	  let s2 = s1.replace("..",'$.getScript(`${murl}')
	  s2 = s2 + '`),'
	  arrayOfFiles.push(s2)
    }
  })

  return arrayOfFiles
}

const result = getAllFiles("../_next")
console.log(result);
// [ "FILE_PATH", "FILE_PATH", "FILE_PATH" ]