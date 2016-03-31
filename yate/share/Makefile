# Makefile
# This file holds the make rules for the Telephony Engine modules

# override DESTDIR at install time to prefix the install directory
DESTDIR :=

SUBDIRS := data help skins scripts sounds

prefix = /usr/local
exec_prefix = ${prefix}
datarootdir = ${prefix}/share
datadir = ${datarootdir}
shrdir = $(datadir)/yate

# include optional local make rules
-include YateLocal.mak

PHONY: all clean install uninstall
all clean install uninstall:
	$(if $(SUBDIRS),\
	@for i in $(SUBDIRS) ; do \
	    if test -f ./$$i/Makefile ; then \
		$(MAKE) -C ./$$i $@ || exit 1;\
	    fi; \
	done \
	)

Makefile: ./Makefile.in ../config.status
	cd .. && ./config.status
