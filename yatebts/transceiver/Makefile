# Makefile
# This file holds the make rules for the transceiver


DEBUG :=

CXX  := g++ -Wall
AR  := ar
DEFS :=
INCLUDES := -I.. -I.. -I.
CCFLAGS:=  -O2 -DLITTLE_ENDIAN -Wno-overloaded-virtual -fPIC -DHAVE_GCC_FORMAT_CHECK -DHAVE_BLOCK_RETURN -I/usr/local/include/yate -DHAVE_CONFIG_H
CFLAGS := $(subst -fno-check-new,,$(CCFLAGS))
LDFLAGS:= 
YATELIBS:= -lyate
INCFILES := transceiver.h

LOCALLIBS :=
LIBS := libtransceiver.a
OBJS := transceiver.o sigproc.o gsmutil.o
PROGS :=

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
	@-$(RM) $(PROGS) $(LIBS) $(OBJS) $(EXTRACLEAN) core 2>/dev/null

%.o: ./%.cpp $(INCFILES)
	$(COMPILE) -c $<

Makefile: ./Makefile.in ../config.status
	cd .. && ./config.status

libtransceiver.a: $(OBJS)
	$(AR) rcs $@ $^
