# Makefile
# This file holds the make rules for Yate client skins

# override DESTDIR at install time to prefix the install directory
DESTDIR :=

prefix = /usr/local
exec_prefix = ${prefix}
datarootdir = ${prefix}/share
datadir = ${datarootdir}
shrdir = $(datadir)/yate
skindir = $(shrdir)/skins

.PHONY: all clean install uninstall
all clean:

install:
	@lst="`cd .; ls -1 -d */* | sed '/CVS\|\.orig/d'`" ; \
	dir="`echo "$$lst" | sed 's,/.*,,' | uniq`" ; \
	for i in $$dir ; do \
	    mkdir -p "$(DESTDIR)$(skindir)/$$i" ; \
	done; \
	for i in $$lst ; do \
	    install -m 0644 "./$$i" "$(DESTDIR)$(skindir)/$$i" ; \
	done;

uninstall:
	@-lst="`cd .; ls -1 -d */* | sed '/CVS\|\.orig/d'`" ; \
	dir="`echo "$$lst" | sed 's,/.*,,' | uniq`" ; \
	for i in $$lst ; do \
	    rm "$(DESTDIR)$(skindir)/$$i" ; \
	done; \
	for i in $$dir ; do \
	    rmdir "$(DESTDIR)$(skindir)/$$i" ; \
	done;
	@-rmdir "$(DESTDIR)$(skindir)"
	@-rmdir "$(DESTDIR)$(shrdir)"

Makefile: ./Makefile.in ../../config.status
	cd ../.. && ./config.status
