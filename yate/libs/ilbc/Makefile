# Makefile
# This file holds the make rules for the libilbc 

CC  := gcc -Wall
AR  := ar
SED := sed
DEFS :=
INCLUDES := -I../..
CFLAGS :=  -O2 -O3 -funroll-loops -fomit-frame-pointer -fsigned-char -fno-exceptions -fPIC -DHAVE_GCC_FORMAT_CHECK -DHAVE_BLOCK_RETURN
LDFLAGS:= 

PROGS=
LIBS = libilbc.a
OBJS= anaFilter.o iCBSearch.o packing.o \
        constants.o gainquant.o iLBC_decode.o StateConstructW.o \
        createCB.o getCBvec.o iLBC_encode.o StateSearchW.o doCPLC.o \
        helpfun.o syntFilter.o enhancer.o hpInput.o LPCdecode.o \
        filter.o hpOutput.o LPCencode.o FrameClassify.o  iCBConstruct.o lsf.o

LOCALFLAGS =
LOCALLIBS =
COMPILE = $(CC) $(DEFS) $(INCLUDES) $(CFLAGS)
LINK = $(CC) $(LDFLAGS)

prefix = /usr/local
exec_prefix = ${prefix}

.PHONY: all
all: $(LIBS) $(PROGS)

.PHONY: strip
strip: all
	strip --strip-debug --discard-locals $(PROGS)

.PHONY: clean
clean:
	@-$(RM) $(PROGS) $(LIBS) $(OBJS) core 2>/dev/null

%.o: ./%.c
	$(COMPILE) -c $<

Makefile: ./Makefile.in ../../config.status
	cd ../.. && ./config.status

libilbc.a: $(OBJS)
	$(AR) rcs $@ $^
