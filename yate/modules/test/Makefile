# Makefile
# This file holds the make rules for the Telephony Engine test cases

# override DEBUG at compile time to enable full debug or remove it all
DEBUG :=

CXX := g++ -Wall
SED := sed
DEFS :=
INCLUDES := -I../..
CFLAGS := -O0 -Wno-overloaded-virtual  -fno-exceptions -fPIC -DHAVE_GCC_FORMAT_CHECK -DHAVE_BLOCK_RETURN 
LDFLAGS:= 
YATELIBS:= -L../.. -lyate 
MODFLAGS:= -rdynamic -shared -Wl,--unresolved-symbols=ignore-in-shared-libs
MODSTRIP:= -Wl,--retain-symbols-file,/dev/null

MKDEPS  := ../../config.status
PROGS = randcall.yate msgdelay.yate jsext.yate crypto.yate radiotest.yate
LIBS =
OBJS =

LOCALFLAGS =
LOCALLIBS =
COMPILE = $(CXX) $(DEFS) $(DEBUG) $(INCLUDES) $(CFLAGS)
LINK = $(CXX) $(LDFLAGS)
MODLINK = $(CXX) $(MODFLAGS) $(MODSTRIP) $(LDFLAGS)
MODCOMP = $(COMPILE) $(MODFLAGS) $(MODSTRIP) $(LDFLAGS)

prefix = /usr/local
exec_prefix = ${prefix}

# include optional local make rules
-include YateLocal.mak

.PHONY: all debug ddebug xdebug
all: $(LIBS) $(PROGS)

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
	@-$(RM) $(PROGS) $(LIBS) $(OBJS) core 2>/dev/null

%.o: ./%.cpp $(MKDEPS) ../../yateclass.h ../../yatengine.h
	$(COMPILE) -c $<

Makefile: ./Makefile.in $(MKDEPS)
	cd ../.. && ./config.status

lib%.so: %.o
	$(LINK) -shared -o $@ $^

%.yate: ./%.cpp $(MKDEPS) $(INCFILES)
	$(MODCOMP) -o $@ $(LOCALFLAGS) $< $(LOCALLIBS) $(YATELIBS)

jsext.yate: LOCALFLAGS = -I../../libs/yscript
jsext.yate: LOCALLIBS = -lyatescript

radiotest.yate: ../../libyateradio.so
radiotest.yate: LOCALFLAGS = -I../../libs/yradio
radiotest.yate: LOCALLIBS = -lyateradio
