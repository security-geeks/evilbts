# Makefile
# This file holds the make rules the Yate BTS module and associated executables

# override DESTDIR at install time to prefix the install directory
DESTDIR :=

# override DEBUG at compile time to enable full debug or remove it all
DEBUG :=

AR  := ar
CC  := gcc -Wall
CXX := g++ -Wall
SED := sed
DEFS :=
INCLUDES := -I.
CCFLAGS:= -O2  -Wno-overloaded-virtual  -fno-exceptions -fPIC -DHAVE_GCC_FORMAT_CHECK -DHAVE_BLOCK_RETURN -I/usr/local/include/yate
CFLAGS := $(subst -fno-check-new,,$(CCFLAGS))
LDFLAGS:=  -rdynamic -shared -Wl,--unresolved-symbols=ignore-in-shared-libs
YATELIBS:= -lyate
MODSTRIP:= -Wl,--retain-symbols-file,/dev/null
RPMOPT :=

PROGS := ybts.yate gsmtrx.yate
DOCS  := README COPYING
LIBS  :=
OEXE  :=
OTRAN :=
INCFILES :=

MKDEPS := ./config.status
CLEANS = $(PROGS) $(LIBS) $(OEXE) $(OTRAN) core
COMPILE = $(CC) $(DEFS) $(DEBUG) $(INCLUDES) $(CFLAGS)
CCOMPILE = $(CXX) $(DEFS) $(DEBUG) $(INCLUDES) $(CCFLAGS)
MODCOMP = $(CCOMPILE) $(MODFLAGS) $(MODSTRIP) $(LDFLAGS)
LINK = $(CXX) $(LDFLAGS)

prefix = /usr/local
exec_prefix = ${prefix}
datarootdir = ${prefix}/share

datadir:= ${datarootdir}
docdir := ${datarootdir}/doc/yate-bts-5.0.1
confdir:= /usr/local/etc/yate
moddir := /usr/local/lib/yate
scrdir := /usr/local/share/yate/scripts
shrdir := /usr/local/share/yate

# include optional local make rules
-include YateLocal.mak

.PHONY: all debug ddebug xdebug ndebug
all: $(PROGS)
	$(MAKE) -C ./transceiver all
	$(MAKE) -C ./mbts/apps all
	$(MAKE) -C ./nib/auth all

debug:
	$(MAKE) all DEBUG=-g3 MODSTRIP=

ddebug:
	$(MAKE) all DEBUG='-g3 -DDEBUG' MODSTRIP=

xdebug:
	$(MAKE) all DEBUG='-g3 -DXDEBUG' MODSTRIP=

ndebug:
	$(MAKE) all DEBUG='-g0 -DNDEBUG'

.PHONY: clean distclean cvsclean clean-config-files
clean:
	@-$(RM) $(CLEANS) 2>/dev/null
	@for i in mbts/*; do \
	    test ! -f "$$i/Makefile" || $(MAKE) -C "$$i" clean BUILD_TESTS=yes; \
	done
	$(MAKE) -C ./transceiver clean
	$(MAKE) -C ./nib/auth clean

check-topdir:
	@test -f configure || (echo "Must make this target in the top source directory"; exit 1)

clean-config-files: check-topdir
	-rm -rf auto*.cache
	-rm -f  Makefile mbts/A53/Makefile mbts/CLI/Makefile mbts/CommonLibs/Makefile mbts/Connection/Makefile mbts/Control/Makefile mbts/GPRS/Makefile mbts/GSM/Makefile mbts/Globals/Makefile mbts/Peering/Makefile mbts/SGSNGGSN/Makefile mbts/TRXManager/Makefile mbts/sqlite3/Makefile mbts/apps/Makefile transceiver/Makefile roaming/Makefile nib/Makefile nib/auth/Makefile config.h config.status config.log

distclean: check-topdir clean clean-config-files

cvsclean: distclean
	-rm -f configure yate-bts.spec

.PHONY: strip install uninstall

strip: all
	-strip --strip-debug --discard-locals $(PROGS)

install: all
	@mkdir -p "$(DESTDIR)$(moddir)/server" && \
	for i in $(PROGS) ; do \
	    install -D -m 0644 "$$i" "$(DESTDIR)$(moddir)/server/$$i" ; \
	done
	@mkdir -p "$(DESTDIR)$(confdir)/" && \
	lst="`ls -1 ./*.conf ./*.sample ./*.default ./*.sql 2>/dev/null | sed 's/\.sample//g; s/\.default//g; s/[^ ]*\*\.[^ ]*//g' | sort | uniq`" ; \
	for s in $$lst; do \
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
	@mkdir -p "$(DESTDIR)$(docdir)/" && \
	for i in $(DOCS) ; do \
	    install -m 0644 ./$$i "$(DESTDIR)$(docdir)/" ; \
	done
	$(MAKE) -C ./mbts/apps install
	$(MAKE) -C ./nib install
	$(MAKE) -C ./nib/auth install
	$(MAKE) -C ./roaming install

uninstall:
	$(MAKE) -C ./nib/auth uninstall
	$(MAKE) -C ./nib uninstall
	$(MAKE) -C ./roaming uninstall
	$(MAKE) -C ./mbts/apps uninstall
	@-for i in $(PROGS) ; do \
	    rm -f "$(DESTDIR)$(moddir)/server/$$i" ; \
	done
	@-test -d "$(DESTDIR)$(moddir)/server" && rmdir "$(DESTDIR)$(moddir)/server"
	@-test -d "$(DESTDIR)$(moddir)" && rmdir "$(DESTDIR)$(moddir)"
	@-if [ -d "$(DESTDIR)$(confdir)" ]; then \
	    rmdir "$(DESTDIR)$(confdir)" || echo "Remove conf files by hand if you want so"; \
	fi
	@rm -rf "$(DESTDIR)$(docdir)/"

.PHONY: snapshot tarball rpm srpm revision
snapshot tarball: check-topdir clean
	@if [ $@ = snapshot ]; then ver="`date '+SVN-%Y%m%d'`"; else ver="5.0.1-devel1"; fi ; \
	wd=`pwd|sed 's,^.*/,,'`; \
	mkdir -p tarballs; cd ..; \
	echo $$wd/tar-exclude >$$wd/tar-exclude; \
	find $$wd -name Makefile >>$$wd/tar-exclude; \
	find $$wd -name YateLocal.mak >>$$wd/tar-exclude; \
	find $$wd -name '*.conf' >>$$wd/tar-exclude; \
	find $$wd -name '*.cache' >>$$wd/tar-exclude; \
	find $$wd -name '*~' >>$$wd/tar-exclude; \
	find $$wd -name '.*.swp' >>$$wd/tar-exclude; \
	if [ $@ = tarball ]; then \
	    find $$wd -name .svn >>$$wd/tar-exclude; \
	    find $$wd -name CVS >>$$wd/tar-exclude; \
	    find $$wd -name .cvsignore >>$$wd/tar-exclude; \
	else \
	    find $$wd -name '*.spec' >>$$wd/tar-exclude; \
	fi ; \
	tar czf $$wd/tarballs/$$wd-$$ver.tar.gz \
	--exclude $$wd/tarballs \
	--exclude $$wd/config.h \
	--exclude $$wd/config.status \
	--exclude $$wd/config.log \
	-X $$wd/tar-exclude \
	$$wd; \
	rm $$wd/tar-exclude

rpm: tarball
	rpmbuild -tb $(RPMOPT) tarballs/yate-bts-5.0.1-devel1.tar.gz

srpm: tarball
	rpmbuild -ta $(RPMOPT) tarballs/yate-bts-5.0.1-devel1.tar.gz

revision: check-topdir
	@-rev=`svn info 2>/dev/null | sed -n 's,^Revision: *,,p'`; \
	test -z "$$rev" || echo "$$rev" > revision.txt

%.o: ./%.cpp $(MKDEPS) $(INCFILES)
	$(CCOMPILE) -c $<

%.o: ./%.c $(MKDEPS) $(INCFILES)
	$(COMPILE) -c $<

%.yate: ./%.cpp $(MKDEPS) $(INCFILES)
	$(MODCOMP) -o $@ $(LOCALFLAGS) $< $(LOCALLIBS) $(YATELIBS)

ybts.yate: ybts.cpp ybts.h
ybts.yate: LOCALLIBS = -lyateradio

gsmtrx.yate: ./transceiver/libtransceiver.a
gsmtrx.yate: LOCALFLAGS = -I./transceiver
gsmtrx.yate: LOCALLIBS = -L./transceiver -ltransceiver -lyateradio

./configure: ./configure.ac
	cd . && autoconf

$(MKDEPS): ./configure
	$(MKDEPS) --recheck

Makefile: $(MKDEPS) ./Makefile.in
	$(MKDEPS)

./transceiver/libtransceiver.a:
	$(MAKE) -C ./transceiver

.PHONY: help
help:
	@echo -e 'Usual make targets:\n'\
	'    all install uninstall\n'\
	'    clean distclean cvsclean (avoid this one!)\n'\
	'    debug ddebug xdebug (carefull!)\n'\
	'    revision snapshot tarball rpm srpm'
