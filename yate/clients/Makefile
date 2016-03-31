# Makefile
# This file holds the make rules for the Telephony Engine clients

# override DESTDIR at install time to prefix the install directory
DESTDIR :=

# override DEBUG at compile time to enable full debug or remove it all
DEBUG :=

CXX := g++ -Wall
SED := sed
DEFS :=
MOC := /usr/bin/moc-qt4
QT4_INC := -DQT_SHARED -I/usr/include/qt4 -I/usr/include/qt4/QtUiTools -I/usr/include/qt4 -I/usr/include/qt4/QtGui -I/usr/include/qt4 -I/usr/include/qt4/QtXml -I/usr/include/qt4 -I/usr/include/qt4/QtCore 
QT4_LIB := -lQtUiTools -lQtGui -lQtXml -lQtCore 
QT4_INC_NET := -I/usr/include/qt4/QtNetwork
QT4_LIB_NET := -lQtNetwork
QT4_CLIENT_DEPS := ../libyateqt4.so
QT4_CLIENT_LIBS := -lyateqt4
LIBTHR:= -lpthread
INCLUDES := -I.. -I..
CFLAGS :=  -O2 -Wno-overloaded-virtual  -fno-exceptions -fPIC -DHAVE_GCC_FORMAT_CHECK -DHAVE_BLOCK_RETURN 
LDFLAGS:= 
YATELIBS := -L.. -lyate 
INCFILES := ../yatengine.h ../yatephone.h ../yateversn.h

MKDEPS  := ../config.status
SUBDIRS :=
PROGS :=
LIBS :=
MENUFILES :=
DESKFILES :=

ifneq (yes,no)
SUBDIRS := $(SUBDIRS) qt4
PROGS := $(PROGS) yate-qt4
MENUFILES := $(MENUFILES) yate-qt4.menu
DESKFILES := $(DESKFILES) yate-qt4.desktop
ICONFILES := $(ICONFILES) null_team-16.png null_team-32.png null_team-48.png

ifneq (no,no)
ifeq (no,yes)
QT4_CLIENT_LIBS := customtable customtext customtree widgetlist clientarchive
else
QT4_CLIENT_LIBS := $(strip no)
endif
QT4_CLIENT_LIBS := $(foreach mod,$(QT4_CLIENT_LIBS),../modules/qt4/$(mod).o) qt4/qt4client.a
QT4_CLIENT_DEPS := $(QT4_CLIENT_LIBS)
endif

endif

LOCALFLAGS =
LOCALLIBS =
EXTERNFLAGS =
EXTERNLIBS =
COMPILE = $(CXX) $(DEFS) $(DEBUG) $(INCLUDES) $(CFLAGS)
LINK = $(CXX) $(LDFLAGS)

ifneq (x040806,x)
DEFS := $(DEFS) -DQT4_VER=040806
endif

prefix = /usr/local
exec_prefix = ${prefix}
datarootdir = ${prefix}/share
datadir = ${datarootdir}

bindir = ${exec_prefix}/bin
menudir= $(prefix)/lib/menu
shrdir = $(datadir)/yate
deskdir= $(datadir)/applications
icondir= $(datadir)/pixmaps

# include optional local make rules
-include YateLocal.mak

.PHONY: all
all: do-all $(LIBS) $(PROGS)

.PHONY: strip
strip: all do-strip
	strip --strip-debug --discard-locals $(PROGS)

.PHONY: clean
clean: do-clean
	@-$(RM) $(PROGS) $(LIBS) *.o core 2>/dev/null

.PHONY: install
install: all do-install
	@mkdir -p "$(DESTDIR)$(bindir)/" && \
	test -z "$(PROGS)" || \
	install $(PROGS) "$(DESTDIR)$(bindir)/"
	$(if $(MENUFILES),\
	@mkdir -p "$(DESTDIR)$(menudir)/" && \
	for i in $(MENUFILES) ; do \
	    install -D -m 0644 "./$$i" "$(DESTDIR)$(menudir)/$$i" ; \
	done \
	)
	$(if $(ICONFILES),\
	@mkdir -p "$(DESTDIR)$(icondir)/" && \
	for i in $(ICONFILES) ; do \
	    install -D -m 0644 "./$$i" "$(DESTDIR)$(icondir)/$$i" ; \
	done \
	)
	$(if $(DESKFILES),\
	@mkdir -p "$(DESTDIR)$(deskdir)/" && \
	for i in $(DESKFILES) ; do \
	    install -D -m 0644 "./$$i" "$(DESTDIR)$(deskdir)/$$i" ; \
	done \
	)

.PHONY: uninstall
uninstall: do-uninstall
	@-for i in $(PROGS) ; do \
	    rm "$(DESTDIR)$(bindir)/$$i" ; \
	done
	@-rmdir "$(DESTDIR)$(bindir)"
	$(if $(MENUFILES),\
	@-for i in $(MENUFILES) ; do \
	    rm "$(DESTDIR)$(menudir)/$$i" ; \
	done ; \
	rmdir "$(DESTDIR)$(menudir)" \
	)
	$(if $(DESKFILES),\
	@-for i in $(DESKFILES) ; do \
	    rm "$(DESTDIR)$(deskdir)/$$i" ; \
	done ; \
	rmdir "$(DESTDIR)$(deskdir)" \
	)
	$(if $(ICONFILES),\
	@-for i in $(ICONFILES) ; do \
	    rm "$(DESTDIR)$(icondir)/$$i" ; \
	done ; \
	rmdir "$(DESTDIR)$(icondir)" \
	)

../modules/qt4/%.o: ../modules/qt4/%.cpp
	$(MAKE) -C ../modules qt4/$(notdir $@)

%.o: ./%.cpp $(MKDEPS) $(INCFILES)
	$(COMPILE) -c $<

%.moc.o: %.moc $(INCFILES)
	$(COMPILE) $(QT4_INC) -o $@ -c -x c++ $<

%.moc: ./%.h
	$(MOC) $(DEFS) $(INCLUDES) $(QT4_INC) -o $@ $<

do-all do-strip do-clean do-install do-uninstall:
	$(if $(SUBDIRS),\
	@target=`echo $@ | $(SED) -e 's/^do-//'`; \
	for i in $(SUBDIRS) ; do \
	    if test -f ./$$i/Makefile ; then \
		$(MAKE) -C ./$$i $${target} || exit 1;\
	    fi; \
	done \
	)

Makefile: ./Makefile.in $(MKDEPS)
	cd .. && ./config.status

yate-qt4: $(QT4_CLIENT_DEPS)
yate-qt4: EXTERNFLAGS = $(QT4_INC)
yate-qt4: EXTERNLIBS = $(QT4_CLIENT_LIBS) $(QT4_LIB)

qt4/qt4client.a: ./qt4/qt4client.h ./qt4/qt4client.cpp
	$(MAKE) -C qt4 $(notdir $@)

yate-%: ./main-%.cpp $(MKDEPS) ../libyate.so $(INCFILES)
	$(COMPILE) -o $@ $(LOCALFLAGS) $(EXTERNFLAGS) $< $(LDFLAGS) $(LIBTHR) $(LOCALLIBS) $(YATELIBS) $(EXTERNLIBS)
