PHPTARGET=/share/tikaja-php/${USER}

deploy-php:
	# total black magic here :)
	cp -ar php/* ${PHPTARGET}


