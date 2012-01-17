solid.ini: solid.ini.tmpl
	sed -e "s/\\\$${USER}/${USER}/g" solid.ini.tmpl > solid.ini

clean:
	rm -f solid.ini
