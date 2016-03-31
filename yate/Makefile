# Makefile
# This file holds the make rules for the Telephony Engine

# override DESTDIR at install time to prefix the install directory
DESTDIR :=

# override DEBUG at compile time to enable full debug or remove it all
DEBUG :=

YATE_VERSION := 5.5.1
YATE_RELEASE := devel1
YATE_REVISION:= 6107

CXX := g++ -Wall
SED := sed
DEFS :=
LIBTHR:= -lpthread
INCLUDES := -I. -I.
CFLAGS :=  -O2 -Wno-overloaded-virtual  -fno-exceptions -fPIC -DHAVE_GCC_FORMAT_CHECK -DHAVE_BLOCK_RETURN 
LDFLAGS:= 
LDCONFIG:=true
RPMOPT :=

MKDEPS := ./config.status
PROGS:= yate
YLIB := libyate.so.5.5.1
SLIBS:= $(YLIB) libyate.so \
	libyatescript.so.5.5.1 libyatescript.so \
	libyatesig.so.5.5.1 libyatesig.so \
	libyateasn.so.5.5.1 libyateasn.so \
	libyateradio.so.5.5.1 libyateradio.so \
	libyatemgcp.so.5.5.1 libyatemgcp.so \
	libyatejabber.so.5.5.1 libyatejabber.so
ILIBS:= yscript yasn yradio
INCS := yateclass.h yatemime.h yatengine.h yatephone.h yatecbase.h yatexml.h yatemath.h
GENS := yateversn.h
LIBS :=
MAN8 := yate.8 yate-config.8
DOCS := README COPYING ChangeLog
DOCS_ILBC := LICENSE.txt
DOCS_WEBRTC := LICENSE LICENSE_THIRD_PARTY PATENTS
OBJS := main.o

CLEANS = $(PROGS) $(SLIBS) $(LIBS) $(OBJS) yatepaths.h core
COMPILE = $(CXX) $(DEFS) $(DEBUG) $(INCLUDES) $(CFLAGS)
LINK = $(CXX) $(LDFLAGS)

DOCGEN_F := $(INCS)

prefix = /usr/local
exec_prefix = ${prefix}
datarootdir = ${prefix}/share

datadir = ${datarootdir}
confdir = ${prefix}/etc/yate
bindir = ${exec_prefix}/bin
libdir = ${exec_prefix}/lib
incdir = ${prefix}/include/yate
mandir = ${datarootdir}/man
docdir = ${datarootdir}/doc/yate-5.5.1
vardir = ${prefix}/var/lib/yate
moddir = ${exec_prefix}/lib/yate
shrdir = $(datadir)/yate

# include optional local make rules
-include YateLocal.mak

DOCGEN := /bin/false
DOCGEN_K := /bin/false
DOCGEN_D := /bin/false
APIDOCS :=
APIINDEX:= ./docs/api/index.html
ifneq (_,_)
DOCGEN_K :=  -C ./docs/doc-filter.sh -d docs/api/ $(DOCGEN_F)
DOCGEN := $(DOCGEN_K)
APIDOCS := apidocs
endif
ifneq (_/usr/bin/doxygen,_)
DOCGEN_D := (cat docs/Doxyfile; echo 'INPUT = $(DOCGEN_F)') | /usr/bin/doxygen -
DOCGEN := $(DOCGEN_D)
APIDOCS := apidocs
endif

.PHONY: all everything debug ddebug xdebug ndebug
all: engine modules clients ilibs

everything: engine libs modules clients test apidocs

debug:
	$(MAKE) all DEBUG=-g3 MODSTRIP=

ddebug:
	$(MAKE) all DEBUG='-g3 -DDEBUG' MODSTRIP=

xdebug:
	$(MAKE) all DEBUG='-g3 -DXDEBUG' MODSTRIP=

ndebug:
	$(MAKE) all DEBUG='-g0 -DNDEBUG'

.PHONY: clean distclean cvsclean clean-config-files clean-packing clean-apidocs
clean:
	@-$(RM) $(CLEANS) 2>/dev/null
	$(MAKE) -C ./engine $@
	$(MAKE) -C ./modules $@
	$(MAKE) -C ./clients $@
	@for i in libs/*; do \
	    test ! -f "$$i/Makefile" || $(MAKE) -C "$$i" clean ; \
	done

check-topdir:
	@test -f configure || (echo "Must make this target in the top source directory"; exit 1)

check-root:
	@test `id -u` = '0' || (echo "You must run this command as root"; exit 1)

check-ldconfig:
	@test "x/usr/lib/x86_64-linux-gnu" = "x$(libdir)" || \
	    grep -l -R "^$(libdir)$$" /etc/ld.so.conf* >/dev/null 2>&1 || \
	    echo "Add manually $(libdir) to /etc/ld.so.conf and run ldconfig (as root)"

clean-config-files: check-topdir
	-rm -rf auto*.cache
	-rm -f  yate.pc yateversn.h yateiss.inc Makefile engine/Makefile modules/Makefile modules/test/Makefile clients/Makefile clients/qt4/Makefile libs/ilbc/Makefile libs/ysip/Makefile libs/yrtp/Makefile libs/ysdp/Makefile libs/yiax/Makefile libs/yjabber/Makefile libs/yscript/Makefile libs/ymgcp/Makefile libs/ysig/Makefile libs/ypbx/Makefile libs/ymodem/Makefile libs/yasn/Makefile libs/ysnmp/Makefile libs/miniwebrtc/Makefile libs/yradio/Makefile share/Makefile share/scripts/Makefile share/skins/Makefile share/sounds/Makefile share/help/Makefile share/data/Makefile conf.d/Makefile yate-config run config.status config.log

clean-packing: check-topdir
	-rm -f packing/rpm/yate.spec packing/portage/yate.ebuild

clean-apidocs: check-topdir
	-rm docs/api/*.*

distclean: check-topdir clean clean-config-files

cvsclean: check-topdir clean clean-apidocs clean-packing clean-config-files
	-rm -f configure yate-config.in

.PHONY: engine libs ilibs modules clients test apidocs-build apidocs-kdoc apidocs-doxygen apidocs-everything check-topdir check-ldconfig windows
engine: library libyate.so $(PROGS)

apidocs-kdoc: check-topdir
	@if [ "x$(DOCGEN_K)" != x/bin/false ]; then \
	    $(DOCGEN_K) ; \
	else \
	    echo "Executable kdoc is not installed!" ; exit 1 ; \
	fi

apidocs-doxygen: check-topdir
	@if [ "x$(DOCGEN_D)" != x/bin/false ]; then \
	    $(DOCGEN_D) ; \
	else \
	    echo "Executable doxygen is not installed!" ; exit 1 ; \
	fi

apidocs-build:
	@if [ "x$(DOCGEN)" != x/bin/false ]; then \
	    cd . ; $(DOCGEN) ; \
	else \
	    echo "Neither kdoc or doxygen is installed!" ; exit 1 ; \
	fi

apidocs-everything: check-topdir
	$(MAKE) apidocs-build DOCGEN_F="$(DOCGEN_F) `echo libs/y*/*.h`"

apidocs: $(APIINDEX)

$(APIINDEX): ./docs/Doxyfile \
    ./yateclass.h ./yatemime.h ./yatengine.h \
    ./yatephone.h ./yatecbase.h ./yatemath.h
	$(MAKE) apidocs-build

.PHONY: strip sex love war
strip: all
	-strip --strip-debug --discard-locals $(PROGS) $(SLIBS)

sex: strip
	@echo 'Stripped for you!'

# Let's have a little fun
love:
	@echo 'Not war?'

war:
	@echo 'Please make love instead!'

modules clients test: engine
	$(MAKE) -C ./$@ all

libs: engine
	@for i in libs/*; do \
	    test ! -f "$$i/Makefile" || $(MAKE) -C "$$i" all ; \
	done

ilibs: engine
	@for i in $(ILIBS); do \
	    test ! -f "libs/$$i/Makefile" || $(MAKE) -C "libs/$$i" all ; \
	done

yatepaths.h: $(MKDEPS)
	@echo '#define CFG_PATH "$(confdir)"' > $@
	@echo '#define MOD_PATH "$(moddir)"' >> $@
	@echo '#define SHR_PATH "$(shrdir)"' >> $@

windows: check-topdir
	@cmp -s yateversn.h $@/yateversn.h || cp -p yateversn.h $@/yateversn.h
	@cmp -s yateiss.inc $@/yateiss.inc || cp -p yateiss.inc $@/yateiss.inc

.PHONY: install install-root install-noconf install-noapi install-api uninstall uninstall-root
install install-root: all $(APIDOCS) install-noapi install-api check-ldconfig

install-noapi: install-noconf
	$(MAKE) -C ./conf.d install

install-noconf: all
	@mkdir -p "$(DESTDIR)$(libdir)/" && \
	for i in $(SLIBS) ; do \
	    if [ -h "$$i" ]; then \
		f=`readlink "$$i"` ; \
		ln -sf "$$f" "$(DESTDIR)$(libdir)/$$i" ; \
	    else \
		install -m 0644 $$i "$(DESTDIR)$(libdir)/" ; \
	    fi \
	done
	@mkdir -p "$(DESTDIR)$(bindir)/" && \
	install $(PROGS) yate-config "$(DESTDIR)$(bindir)/"
	$(MAKE) -C ./modules install
	$(MAKE) -C ./clients install
	$(MAKE) -C ./share install
	@$(LDCONFIG)
	@mkdir -p "$(DESTDIR)$(mandir)/man8/" && \
	for i in $(MAN8) ; do \
	    install -m 0644 ./docs/man/$$i "$(DESTDIR)$(mandir)/man8/" ; \
	done
	@mkdir -p "$(DESTDIR)$(libdir)/pkgconfig/" && \
	install -m 0644 yate.pc "$(DESTDIR)$(libdir)/pkgconfig/"
	@mkdir -p "$(DESTDIR)$(incdir)/" && \
	for i in $(INCS) ; do \
	    install -m 0644 ./$$i "$(DESTDIR)$(incdir)/" ; \
	done
	@for i in $(GENS) ; do \
	    install -m 0644 $$i "$(DESTDIR)$(incdir)/" ; \
	done
	@for i in $(ILIBS) ; do \
		for f in ./libs/$$i/*.h ; do \
		    install -m 0644 $$f "$(DESTDIR)$(incdir)/" ; \
		done ; \
	done
	@mkdir -p "$(DESTDIR)$(docdir)/api/" && \
	for i in $(DOCS) ; do \
	    install -m 0644 ./$$i "$(DESTDIR)$(docdir)/" ; \
	done; \
	for i in $(DOCS_ILBC) ; do \
	    install -m 0644 ./libs/ilbc/$$i "$(DESTDIR)$(docdir)/iLBC-$$i" ; \
	done; \
	for i in $(DOCS_WEBRTC) ; do \
	    install -m 0644 ./libs/miniwebrtc/$$i "$(DESTDIR)$(docdir)/WebRTC-$$i" ; \
	done

install-api: $(APIDOCS)
	@mkdir -p "$(DESTDIR)$(docdir)/api/" && \
	install -m 0644 ./docs/*.html "$(DESTDIR)$(docdir)/" && \
	test -f "$(APIINDEX)" && \
	install -m 0644 ./docs/api/*.* "$(DESTDIR)$(docdir)/api/"

uninstall uninstall-root:
	@-for i in $(SLIBS) ; do \
	    rm "$(DESTDIR)$(libdir)/$$i" ; \
	done; \
	$(MAKE) -C ./clients uninstall
	@$(LDCONFIG)
	@-for i in $(PROGS) yate-config ; do \
	    rm "$(DESTDIR)$(bindir)/$$i" ; \
	done
	@-rm "$(DESTDIR)$(libdir)/pkgconfig/yate.pc" && \
	    rmdir $(DESTDIR)$(libdir)/pkgconfig
	@-for i in $(INCS) $(GENS) ; do \
	    rm "$(DESTDIR)$(incdir)/$$i" ; \
	done; \
	for i in $(ILIBS) ; do \
		for f in ./libs/$$i/*.h ; do \
		    rm "$(DESTDIR)$(incdir)/"`basename $$f` ; \
		done ; \
	done ; \
	rmdir "$(DESTDIR)$(incdir)"
	@-for i in $(MAN8) ; do \
	    rm "$(DESTDIR)$(mandir)/man8/$$i" ; \
	done
	@rm -rf "$(DESTDIR)$(docdir)/"
	$(MAKE) -C ./modules uninstall
	$(MAKE) -C ./share uninstall
	$(MAKE) -C ./conf.d uninstall

install-root uninstall-root: LDCONFIG:=ldconfig

.PHONY: snapshot tarball rpm srpm revision
snapshot tarball: check-topdir revision clean windows apidocs
	@if [ $@ = snapshot ]; then ver="`date '+SVN-%Y%m%d'`"; else ver="5.5.1-devel1"; fi ; \
	wd=`pwd|sed 's,^.*/,,'`; \
	mkdir -p packing/tarballs; cd ..; \
	echo $$wd/tar-exclude >$$wd/tar-exclude; \
	find $$wd -name Makefile >>$$wd/tar-exclude; \
	find $$wd -name 'YateLocal*' >>$$wd/tar-exclude; \
	find $$wd/conf.d -name '*.conf' >>$$wd/tar-exclude; \
	find $$wd -name '*.cache' >>$$wd/tar-exclude; \
	find $$wd -name '*~' >>$$wd/tar-exclude; \
	find $$wd -name '.*.swp' >>$$wd/tar-exclude; \
	if [ $@ = tarball ]; then \
	    find $$wd -name .svn >>$$wd/tar-exclude; \
	    find $$wd -name CVS >>$$wd/tar-exclude; \
	    find $$wd -name .cvsignore >>$$wd/tar-exclude; \
	else \
	    echo "$$wd/packing/rpm/yate.spec" >>$$wd/tar-exclude; \
	fi ; \
	tar czf $$wd/packing/tarballs/yate-$$ver.tar.gz \
	--exclude $$wd/packing/tarballs \
	--exclude $$wd/config.status \
	--exclude $$wd/config.log \
	--exclude $$wd/run \
	--exclude $$wd/yate-config \
	--exclude $$wd/yate.pc \
	--exclude $$wd/yatepaths.h \
	--exclude $$wd/yateversn.h \
	-X $$wd/tar-exclude \
	$$wd; \
	rm $$wd/tar-exclude

rpm: tarball
	rpmbuild -tb $(RPMOPT) packing/tarballs/yate-5.5.1-devel1.tar.gz

srpm: tarball
	rpmbuild -ta $(RPMOPT) packing/tarballs/yate-5.5.1-devel1.tar.gz

revision: check-topdir
	@-rev=`svn info 2>/dev/null | sed -n 's,^Revision: *,,p'`; \
	test -z "$$rev" || echo "$$rev" > packing/revision.txt

%.o: ./%.cpp $(MKDEPS) ./yatengine.h
	$(COMPILE) -c $<

./configure: ./configure.ac
	cd . && ./autogen.sh --silent

config.status: ./configure
	./config.status --recheck

Makefile: ./Makefile.in $(MKDEPS)
	./config.status

yate: $(OBJS) $(LIBS) libyate.so
	$(LINK) -o $@ $(LIBTHR) $^ 

libyate.so: $(YLIB)
	ln -sf $^ $@

.PHONY: library
library $(YLIB): yatepaths.h
	$(MAKE) -C ./engine all

.PHONY: help
help:
	@echo -e 'Usual make targets:\n'\
	'    all engine libs modules clients apidocs test everything\n'\
	'    install uninstall install-noapi install-root uninstall-root\n'\
	'    clean distclean cvsclean (avoid this one!) clean-apidocs\n'\
	'    debug ddebug xdebug (carefull!)\n'\
	'    snapshot tarball rpm srpm'
