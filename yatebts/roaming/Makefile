# Makefile
# This file holds the make rules the Yate BTS module and associated executables

# override DESTDIR at install time to prefix the install directory
DESTDIR :=

SCRIPTS := roaming.js handover.js lib_str_util.js
SOUNDS	:=
CONFIG  := 

prefix = /usr/local
exec_prefix = ${prefix}
datarootdir = ${prefix}/share

datadir:= ${datarootdir}
confdir:= /usr/local/etc/yate
moddir := /usr/local/lib/yate
scrdir := /usr/local/share/yate/scripts
shrdir := /usr/local/share/yate

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

uninstall:
	@-for i in $(SCRIPTS) ; do \
	    rm -f "$(DESTDIR)$(scrdir)/$$i" ; \
	done
	@-rmdir "$(DESTDIR)$(scrdir)"
	@-rmdir "$(DESTDIR)$(shrdir)"
