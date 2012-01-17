SOLID=/share/solid/bin/solid
SOLIDUSERDIR=/export/tikaja/db1/$(USER)
LICENSE=/share/solid/soliddb/solid.lic

all: solid.ini

user_dir:
	mkdir -p $(SOLIDUSERDIR)

solid.ini: solid.ini.tmpl user_dir
	@sed -e "s/\\\$${USER}/${USER}/g" solid.ini.tmpl > $(SOLIDUSERDIR)/solid.ini

solid.lic: $(LICENSE) user_dir
	@if [ ! -e "$(SOLIDUSERDIR)/solid.lic" ]; then \
		ln -sf $(LICENCE) $(SOLIDUSERDIR)/solid.lic; \
	fi
	
create_db_dirs: solid.ini
	# hakee hakemistot ini tiedostosta, luo ne jos ne puuttuu
	@egrep '^(BackupDirectory|FileSpec_1|FileSpec_2|TmdDir_1|FileNameTemplate)' solid.ini | \
		( \
			while read dirline; do \
				param=`echo $$dirline | sed -e 's/^.*=\s\(\S\+\).*/\1/'`; \
				dir=$$(dirname "$$param"); \
				if [ ! -d "$$dir" ]; then \
					mkdir -p $$dir; \
				fi; \
			done; \
		)

# this target should be done at most once; this will start solid on foreground
start_new_solid: create_db_dirs solid.lic
	@echo
	@echo '# Starting solid on foreground, shut it down with SHUTDOWN or'
	@echo '# using solcon:'
	@echo '# solcon "'`grep Listen solid.ini|sed -e 's/^.*=\s\(.\+\)\s*/\1/'`'" <username> <password>' 
	@echo
	${SOLID} -f -c $(SOLIDUSERDIR)
	@echo
	@echo "# Remeber to save admin credentials to $(HOME)/.solidcreds"

start_solid: solid.ini
	@echo
	@echo '# Starting solid on using solcon:'
	@echo '# solcon "'`grep Listen solid.ini|sed -e 's/^.*=\s\(.\+\)\s*/\1/'`'" <username> <password>' 
	@echo
	${SOLID} -c $(SOLIDUSERDIR)

stop_solid:
	# ei hajua

#.solidcreds is a file with two lines: 1. username 2. password

send_schema: solid.ini $(HOME)/.solidcreds

	@for i in sql.d/*.sql; do\
		@echo 'Importing $$i, logging to $$i.log...'
		solsql "`grep Listen solid.ini|sed -e 's/^.*=\s\(.\+\)\s*/\1/'`" \
			`head -1 $(HOME)/.solidcreds` `tail -1 $(HOME)/.solidcreds` \
			$$i | tee $$i.log | grep Error; \
	done

drop_schema.sql:

	@cat > drop_schema.sql
	@for i in sql.d/*.sql; do\
		grep CREATE $$i |\
			sed -e 's/CREATE/DROP//g' -e 's/($/;/g' \
			>> drop_schema.sql; \
	done
	@sed -e 's/^\(DROP TABLE.\+\);$$/\1 CASCADE CONSTRAINTS;/' -i drop_schema.sql
	
drop_schema: solid.ini $(HOME)/.solidcreds drop_schema.sql

	date >> drop_schema.sql.log

	solsql "`grep Listen solid.ini|sed -e 's/^.*=\s\(.\+\)\s*/\1/'`" \
		head -1 $(HOME)/.solidcreds` `tail -1 $(HOME)/.solidcreds` \
		drop_schema.sql | tee drop_schema.log | grep Error;

clean:
	rm -f solid.ini
