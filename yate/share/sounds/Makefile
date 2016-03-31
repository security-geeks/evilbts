# Makefile
# This file holds the make rules for Yate sound files

# override DESTDIR at install time to prefix the install directory
DESTDIR :=

SOUNDS := ring.au ring.wav tone.wav

prefix = /usr/local
exec_prefix = ${prefix}
datarootdir = ${prefix}/share
datadir = ${datarootdir}
shrdir = $(datadir)/yate
snddir = $(shrdir)/sounds

# include optional local make rules
-include YateLocal.mak

.PHONY: all
all:

.PHONY: install
install:
	@cd . && mkdir -p "$(DESTDIR)$(snddir)/" && \
	test -z "$(SOUNDS)" || \
	install -m 0644 $(SOUNDS) "$(DESTDIR)$(snddir)/"

.PHONY: uninstall
uninstall:
	@-for i in $(SOUNDS) ; do \
	    rm "$(DESTDIR)$(snddir)/$$i" ; \
	done;
	@-rmdir "$(DESTDIR)$(snddir)"
	@-rmdir "$(DESTDIR)$(shrdir)"

Makefile: ./Makefile.in ../../config.status
	cd ../.. && ./config.status
