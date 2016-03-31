# Makefile
# This file holds the make rules for the libyatepbx

DEBUG :=

CXX  := g++ -Wall
AR  := ar
DEFS :=
INCLUDES := -I../.. -I../.. -I.
CFLAGS :=  -O2 -Wno-overloaded-virtual  -fno-exceptions -fPIC -DHAVE_GCC_FORMAT_CHECK -DHAVE_BLOCK_RETURN 
LDFLAGS:= 
YATELIBS := -L../.. -lyate 
INCFILES := ../../yateclass.h ./yatepbx.h

PROGS=
LIBS = libyatepbx.a
OBJS = multiroute.o assist.o

LOCALFLAGS =
LOCALLIBS =
COMPILE = $(CXX) $(DEFS) $(DEBUG) $(INCLUDES) $(CFLAGS)
LINK = $(CXX) $(LDFLAGS)

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

%.o: ./%.cpp $(INCFILES)
	$(COMPILE) -c $<

Makefile: ./Makefile.in ../../config.status
	cd ../.. && ./config.status

libyatepbx.a: $(OBJS)
	$(AR) rcs $@ $^
