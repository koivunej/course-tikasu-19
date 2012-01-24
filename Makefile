PHPTARGET=/share/tikaja-php/${USER}

deploy-php:
	# total black magic here :)
	cp -r php/* ${PHPTARGET}
	if [ -f php/.htaccess ]; then cp php/.htaccess ${PHPTARGET}; fi
