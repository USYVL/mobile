clean:
	@echo "clean directory"
	find . -type f -name '*~' -exec rm {} \;
