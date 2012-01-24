PHPTARGET=/share/tikaja-php/${USER}

deploy-php:
	# total black magic here :)
	cp -r php/* ${PHPTARGET}
	if [ -f php/.htaccess ]; then cp php/.htaccess ${PHPTARGET}; fi
	#chmod -R o+r ${PHPTARGET}/*
	#if [ -f ${PHPTARGET}/.htaccess ]; then chmod o+r ${PHPTARGET}/.htaccess; fi
	#chmod o-r ${PHPTARGET}

	

