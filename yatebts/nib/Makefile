# Makefile
# This file holds the make rules the Yate BTS module and associated executables

# override DESTDIR at install time to prefix the install directory
DESTDIR :=

SCRIPTS := nib.js welcome.js custom_sms.js
SOUNDS  := welcome.au echo.au

prefix = /usr/local
exec_prefix = ${prefix}
datarootdir = ${prefix}/share

datadir:= ${datarootdir}
confdir:= /usr/local/etc/yate
moddir := /usr/local/lib/yate
scrdir := /usr/local/share/yate/scripts
shrdir := /usr/local/share/yate
snddir := "$(shrdir)/sounds"
webdir := "$(shrdir)/nib_web"

# include optional local make rules
-include YateLocal.mak

.PHONY: all
all:

install: all
	@mkdir -p "$(DESTDIR)$(confdir)/" && \
	lst="`ls -1 ./*.conf ./*.sample ./*.default ./*.sql 2>/dev/null | sed 's/\.sample//g; s/\.default//g; s/[^ ]*\*\.[^ ]*//g' | sort | uniq`" ; \
	for s in $$lst $(CONFIG); do \
	    d="$(DESTDIR)$(confdir)/`echo $$s | sed 's,.*/,,'`" ; \
	    if [ -f "$$d" ]; then \
		echo "Not overwriting existing $$d" ; \
	    else \
		if [ ! -f "$$s" ]; then \
		    test -f "$$s.default" && s="$$s.default" ; \
		    test -f "$$s.sample" && s="$$s.sample" ; \
		fi ; \
		install -m 0644 "$$s" "$$d" ; \
	    fi ; \
	done
	@mkdir -p "$(DESTDIR)$(scrdir)/" && \
	for i in $(SCRIPTS) ; do \
	    install -m 0644 ./$$i "$(DESTDIR)$(scrdir)/" ; \
	done
	@mkdir -p "$(DESTDIR)$(snddir)/" && \
	for i in $(SOUNDS) ; do \
	    install -m 0644 ./$$i "$(DESTDIR)$(snddir)/" ; \
	done
	@mkdir -p "$(DESTDIR)$(webdir)/" && \
	for i in `find "./web" -type f | fgrep -v .svn` ; do \
	    install -D -m 0644 "$$i" "$(DESTDIR)$(webdir)/$${i##*/web/}" ; \
	done ; \
	chmod +x "$(DESTDIR)$(webdir)/ansql/force_update.php"

uninstall:
	@-for i in $(SCRIPTS) ; do \
	    rm -f "$(DESTDIR)$(scrdir)/$$i" ; \
	done
	@-rmdir "$(DESTDIR)$(scrdir)"
	@-for i in $(SOUNDS) ; do \
	    rm -f "$(DESTDIR)$(snddir)/$$i" ; \
	done
	@-rmdir "$(DESTDIR)$(snddir)"
	@-rmdir "$(DESTDIR)$(shrdir)"
