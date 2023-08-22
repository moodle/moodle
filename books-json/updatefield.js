const fs = require('fs');

// Recursive function to get files
function getFiles(dir, files = []) {
  // Get an array of all files and directories in the passed directory using fs.readdirSync
  const fileList = fs.readdirSync(dir);
  // Create the full path of the file/directory by concatenating the passed directory and file/directory name
  for (const file of fileList) {
    const name = `${dir}/${file}`;
    // Check if the current file/directory is a directory using fs.statSync
    if (fs.statSync(name).isDirectory()) {
      // If it is a directory, recursively call the getFiles function with the directory path and the files array
      getFiles(name, files);
    } else {
      // If it is a file, push the full path to the files array
      files.push(name);
    }
  }
  return files;
}

const filesInTheFolder = getFiles('./data');
//console.log(filesInTheFolder)

filesInTheFolder.forEach(file => {
  fs.readFile(file, function(err, data) {
    // Check for errors
    if (err) throw err;
    // Converting to JSON
    const course = JSON.parse(data);
    const newcourse = course.map(function(curcourse){
		const chapters = curcourse.book.chapters;
		const curchapters = chapters.map(function(chapter){
			chapter.teacheronly = "no";
			return chapter
		})
		curcourse.chapters = curchapters;
		return curcourse;
	})
	const newfname = file.replace("data", "processdata")
	console.log(newfname)
	fs.writeFile(newfname, JSON.stringify(newcourse, null, 2), err => {
     
		// Checking for errors
		if (err) throw err; 
	   
		console.log("Done writing"); // Success
	});
	
  });
});