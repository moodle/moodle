# To build a zip file for upload to the moodle plugin directory
zip: basic.zip

basic.zip:
	@mkdir -p build
	@cd .. && zip -r --exclude=basic/build/ --exclude=basic/Makefile basic/build/basic.zip basic/*

clean:
	@rm -rf build


