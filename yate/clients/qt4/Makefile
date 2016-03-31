# Makefile
# This file holds the make rules for the Gtk2 client support

# override DEBUG at compile time to enable full debug or remove it all
DEBUG :=

CXX := g++ -Wall
AR  := ar
MOC := /usr/bin/moc-qt4
QT4_INC := -DQT_SHARED -I/usr/include/qt4 -I/usr/include/qt4/QtUiTools -I/usr/include/qt4 -I/usr/include/qt4/QtGui -I/usr/include/qt4 -I/usr/include/qt4/QtXml -I/usr/include/qt4 -I/usr/include/qt4/QtCore 
QT4_LIB := -lQtUiTools -lQtGui -lQtXml -lQtCore 
QT4_INC_NET := -I/usr/include/qt4/QtNetwork
QT4_LIB_NET := -lQtNetwork
DEFS:=

INCLUDES:=-I. -I. -I../.. $(QT4_INC)
CFLAGS :=  -O2 -Wno-overloaded-virtual  -fno-exceptions -fPIC -DHAVE_GCC_FORMAT_CHECK -DHAVE_BLOCK_RETURN 
LDFLAGS:= 
SONAME_OPT := -shared -Wl,-soname=
YATELIBS := -L../.. -lyate 
INCFILES := ../../yateclass.h ../../yatecbase.h ./qt4client.h

PROGS=
LIBS = qt4client.a
SOURCES = qt4client.cpp
OBJS = $(SOURCES:.cpp=.o) qt4client.moc.o
INST:=
LIBD_DEV:= libyateqt4.so
LIBD_VER:= $(LIBD_DEV).5.5.1
ifeq (no,no)
LIBD:= ../../$(LIBD_VER) ../../$(LIBD_DEV)
INST:= $(LIBD_VER) $(LIBD_DEV)
endif

LOCALFLAGS =
LOCALLIBS =
COMPILE = $(CXX) $(DEFS) $(DEBUG) $(INCLUDES) $(CFLAGS)
LINK = $(CXX) $(LDFLAGS)

ifneq (x040806,x)
DEFS := $(DEFS) -DQT4_VER=040806
endif

prefix = /usr/local
exec_prefix = ${prefix}
datarootdir = ${prefix}/share

bindir = ${exec_prefix}/bin
libdir = ${exec_prefix}/lib
incdir = ${prefix}/include/yate

# include optional local make rules
-include YateLocal.mak

.PHONY: all debug ddebug xdebug
all: $(LIBS) $(LIBD) $(PROGS)

debug:
	$(MAKE) all DEBUG=-g3 MODSTRIP=

ddebug:
	$(MAKE) all DEBUG='-g3 -DDEBUG' MODSTRIP=

xdebug:
	$(MAKE) all DEBUG='-g3 -DXDEBUG' MODSTRIP=

.PHONY: strip
strip: all
	strip --strip-debug --discard-locals $(PROGS)

.PHONY: clean
clean:
	@-$(RM) $(PROGS) $(LIBS) $(LIBD) $(OBJS) core 2>/dev/null

.PHONY: install uninstall
install: all
	$(if $(INST),\
	@mkdir -p "$(DESTDIR)$(libdir)" && \
	for i in $(INST) ; do \
	    if [ -h "../../$$i" ]; then \
		f=`readlink "../../$$i"` ; \
		ln -sf "$$f" "$(DESTDIR)$(libdir)/$$i" ; \
	    else \
		install -m 0644 ../../$$i "$(DESTDIR)$(libdir)/" ; \
	    fi \
	done; \
	mkdir -p "$(DESTDIR)$(incdir)" && \
	install -m 0644 ./qt4client.h "$(DESTDIR)$(incdir)/" \
	)

uninstall:
	$(if $(INST),\
	@-for i in $(INST) ; do \
	    rm "$(DESTDIR)$(libdir)/$$i" ; \
	done; \
	rm "$(DESTDIR)$(incdir)/qt4client.h" && rmdir "$(DESTDIR)$(libdir)" \
	)

%.o: ./%.cpp $(INCFILES)
	$(COMPILE) -c $<

%.moc.o: %.moc $(INCFILES)
	$(COMPILE) -o $@ -c -x c++ $<

%.moc: ./%.h
	$(MOC) $(DEFS) $(INCLUDES) -o $@ $<

Makefile: ./Makefile.in ../../config.status
	cd ../.. && ./config.status

../../$(LIBD_VER): $(OBJS)
	$(LINK) -o $@ $(SONAME_OPT)$(LIBD_VER) $^ $(QT4_LIB) $(YATELIBS)

../../$(LIBD_DEV): ../../$(LIBD_VER)
	cd ../.. && ln -sf $(LIBD_VER) $(LIBD_DEV)

$(LIBS): $(OBJS)
	$(AR) rcs $@ $^
