clean:
	@echo "clean directory"
	find . -type f -name '*~' -exec rm {} \;

version:
	echo "making version"
	bin/version-gen
	echo "new version file"
	cat public/version.php
	# Hmmm, the eval below is done before the new file is created, need to figure
	# out how to make it happen at runtime, after the new version file is created
	# currently have to run this twice after getting an error on first pass
	# need to move all of this into the version-gen script
	#$(eval VERS = $(shell grep -o -E '[0-9]*\.[0-9]*\.[0-9]*' version.php))
	#git add version.php
	#git commit -m "Updating version number to $(VERS)"
	#git tag -a v$(VERS) -m "Updating version number to $(VERS)"
