PHPTARGET=/share/tikaja-php/${USER}

clean:
	find . -type f -name '*~' -exec rm -f {} \;

deploy-php: clean
	cp -r php/* ${PHPTARGET}
	if [ -f php/.htaccess ]; then cp php/.htaccess ${PHPTARGET}; fi


	

