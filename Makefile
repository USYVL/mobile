clean:
	@echo "clean directory"
	find . -type f -name '*~' -exec rm {} \;

version:
	echo "making version"
	bin/version-gen
	echo "new version file"
	cat version.php
	git add version.php
	$(eval VERS = $(shell grep -o -E '[0-9]*\.[0-9]*\.[0-9]*' version.php))
	git commit -m "Updating version number to $(VERS)"
	git tag -a v$(VERS) -m "Updating version number to $(VERS)"
