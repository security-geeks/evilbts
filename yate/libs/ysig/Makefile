# Makefile
# This file holds the make rules for the libyatesig

DEBUG :=

CXX  := g++ -Wall
AR  := ar
DEFS :=
LIBTHR := -lpthread
INCLUDES := -I../.. -I../.. -I. -I../../libs/yasn
CFLAGS :=  -O2 -Wno-overloaded-virtual  -fno-exceptions -fPIC -DHAVE_GCC_FORMAT_CHECK -DHAVE_BLOCK_RETURN 
LDFLAGS:= 
SONAME_OPT := -shared -Wl,-soname=
YATELIBS := -L../.. -lyateasn -lyate 
INCFILES := ../../yateclass.h ./yatesig.h

PROGS= yate-ss7test
LIBS = libyatesig.a
OBJS = engine.o address.o sigcall.o sigtran.o \
	interface.o layer2.o layer3.o layer4.o\
	router.o sccp.o tcap.o dumper.o\
	management.o isup.o tup.o testpart.o \
	q921.o q931.o
LIBD_VER:= libyatesig.so.5.5.1
LIBD_DEV:= libyatesig.so
LIBD:= ../../$(LIBD_VER) ../../$(LIBD_DEV)
YASN:= ../../libyateasn.so
DOCS = classes.png

LOCALFLAGS =
LOCALLIBS =
COMPILE = $(CXX) $(DEFS) $(DEBUG) $(INCLUDES) $(CFLAGS)
LINK = $(CXX) $(LDFLAGS)

prefix = /usr/local
exec_prefix = ${prefix}

# include optional local make rules
-include YateLocal.mak

.PHONY: all debug ddebug xdebug docs
all: $(LIBS) $(LIBD) $(PROGS)

docs: $(DOCS)

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

%.o: ./%.cpp $(INCFILES)
	$(COMPILE) -c $<

Makefile: ./Makefile.in ../../config.status
	cd ../.. && ./config.status

../../$(LIBD_VER): $(OBJS) $(YASN)
	$(LINK) -o $@ $(SONAME_OPT)$(LIBD_VER) $(OBJS) $(YATELIBS)

../../$(LIBD_DEV): ../../$(LIBD_VER)
	cd ../.. && ln -sf $(LIBD_VER) $(LIBD_DEV)

yate-%: ./main-%.cpp $(MKDEPS) $(LIBS) $(YASN) ../../libyate.so $(INCFILES)
	$(COMPILE) -o $@ $(LOCALFLAGS) $< $(LIBTHR) $(LDFLAGS) $(LOCALLIBS) $(YATELIBS)

$(LIBS): $(OBJS)
	$(AR) rcs $@ $^

$(YASN):
	$(MAKE) -C ../yasn

yate-ss7test: LOCALLIBS += -L. -lyatesig

%.png: ./%.dia
	dia --export-to-format=png --export=$@ $<
