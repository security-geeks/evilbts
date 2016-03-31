# Makefile
# This file holds the make rules for Yate client help files

# override DESTDIR at install time to prefix the install directory
DESTDIR :=

prefix = /usr/local
exec_prefix = ${prefix}
datarootdir = ${prefix}/share
datadir = ${datarootdir}
shrdir = $(datadir)/yate
helpdir = $(shrdir)/help

.PHONY: all clean install uninstall
all clean:

install:
	@lst="`cd .; ls -1 -d *.yhlp`" ; \
	mkdir -p "$(DESTDIR)$(helpdir)/" && \
	for i in $$lst ; do \
	    install -D -m 0644 "./$$i" "$(DESTDIR)$(helpdir)/$$i" ; \
	done;

uninstall:
	@-rm "$(DESTDIR)$(helpdir)/"*.yhlp
	@-rmdir "$(DESTDIR)$(helpdir)"
	@-rmdir "$(DESTDIR)$(shrdir)"

Makefile: ./Makefile.in ../../config.status
	cd ../.. && ./config.status
