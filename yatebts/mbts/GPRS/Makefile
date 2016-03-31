# Makefile.head start

# This file is part of the Yate-BTS Project http://www.yatebts.com
#
# Yet Another Telephony Engine - Base Transceiver Station
# Copyright (C) 2013-2014 Null Team Impex SRL
# Copyright (C) 2014 Legba, Inc
#
# This software is distributed under multiple licenses;
# see the COPYING file in the main directory for licensing
# information for this specific distribution.
#
# This use of this software may be subject to additional restrictions.
# See the LEGAL file in the main directory for details.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

TOP_INCLUDES := -I../.. -I../.. -I.
GSM_INCLUDES := $(TOP_INCLUDES) -I./../CommonLibs -I./../A53 \
    -I./../Connection -I./../GSM -I./../Globals -I./../sqlite3
ALL_INCLUDES := $(GSM_INCLUDES) -I./../Control -I./../CLI -I./../GPRS \
    -I./../Peering -I./../SGSNGGSN -I./../TRXManager

SQL_LIBS := -L../sqlite3 -lsqlite3
GSM_LIBS := -L../GSM -lGSM -L../Connection -lConnection -L../Peering -lPeering \
    -L../CommonLibs -lCommonLibs -L../Globals -lGlobals $(SQL_LIBS)
ALL_LIBS := -L../CLI -lCLI -L../Control -lControl -L../GPRS -lGPRS \
    -L../SGSNGGSN -lSGSNGGSN -L../TRXManager -lTRXManager $(GSM_LIBS)
A53_LIBS := -L../A53 -lA53

SQL_DEPS := ../sqlite3/libsqlite3.a
GSM_DEPS := $(SQL_DEPS) ../CommonLibs/libCommonLibs.a ../Connection/libConnection.a \
    ../GSM/libGSM.a ../Globals/libGlobals.a
ALL_DEPS := $(GSM_DEPS) ../CLI/libCLI.a ../Control/libControl.a ../GPRS/libGPRS.a \
    ../Peering/libPeering.a ../SGSNGGSN/libSGSNGGSN.a ../SGSNGGSN/libSGSNGGSN.a \
    ../TRXManager/libTRXManager.a
A53_DEPS := ../A53/libA53.a

prefix = /usr/local
exec_prefix = ${prefix}
moddir := /usr/local/lib/yate

DEBUG :=
CXX  := g++ -Wall
AR  := ar
DEFS :=
OBJS :=
LIBS :=
PROGS:=
FILES:=
LIBTHR := -lpthread
CCFLAGS:=  -O2 -DLITTLE_ENDIAN -Wno-overloaded-virtual -fPIC -DHAVE_GCC_FORMAT_CHECK -DHAVE_BLOCK_RETURN -I/usr/local/include/yate -DHAVE_CONFIG_H
CFLAGS :=$(subst -Wno-overloaded-virtual,,$(CCFLAGS))
LDFLAGS:= 

# Makefile.head end

# Makefile
# This file holds the make rules for the GPRS lib

INCLUDES := $(ALL_INCLUDES)
INCFILES := ../../config.h BSSG.h BSSGMessages.h ByteVector.h FEC.h GPRSExport.h \
    GPRSInternal.h GPRSRLC.h GPRSTDMA.h MAC.h MsgBase.h MSInfo.h RLCEngine.h RLCHdr.h \
    RLCMessages.h RList.h ScalarTypes.h TBF.h

LIBS := libGPRS.a
OBJS := BSSG.o BSSGMessages.o ByteVector.o FEC.o GPRSCLI.o MAC.o MsgBase.o MSInfo.o \
    RLC.o RLCEngine.o RLCMessages.o TBF.o

# Makefile.tail start

COMPILE = $(CXX) $(DEFS) $(DEBUG) $(INCLUDES) $(CCFLAGS)
CCOMPILE = $(CC) -std=gnu99 $(CFLAGS) $(DEFS) $(DEBUG) $(INCLUDES)
LINK = $(CXX) $(LDFLAGS)

# include optional local make rules
-include YateLocal.mak

.PHONY: all debug ddebug xdebug tests
all: $(PROGS) $(LIBS)

debug:
	$(MAKE) all DEBUG=-g3 MODSTRIP=

ddebug:
	$(MAKE) all DEBUG='-g3 -DDEBUG' MODSTRIP=

xdebug:
	$(MAKE) all DEBUG='-g3 -DXDEBUG' MODSTRIP=

tests:
	$(MAKE) all BUILD_TESTS=yes

.PHONY: strip clean install uninstall
strip: all
	strip --strip-debug --discard-locals $(PROGS)

clean:
	@-$(RM) $(PROGS) $(LIBS) $(OBJS) $(EXTRACLEAN) core 2>/dev/null

install: $(PROGS)
	@mkdir -p "$(DESTDIR)$(moddir)/server/bts" && \
	for i in $(PROGS) ; do \
	    install -D "$$i" "$(DESTDIR)$(moddir)/server/bts/$$i" ; \
	done
	for i in $(FILES) ; do \
	    install -m 0644 "$$i" "$(DESTDIR)$(moddir)/server/bts/$$i" ; \
	done

uninstall:
	@-for i in $(FILES) ; do \
	    rm -f "$(DESTDIR)$(moddir)/server/bts/$$i" ; \
	done
	@-for i in $(PROGS) ; do \
	    rm -f "$(DESTDIR)$(moddir)/server/bts/$$i" ; \
	done
	@-test -d "$(DESTDIR)$(moddir)/server/bts" && rmdir "$(DESTDIR)$(moddir)/server/bts"

%.o: ./%.cpp $(INCFILES)
	$(COMPILE) -c $<

%.o: ./%.c $(INCFILES)
	$(CCOMPILE) -c $<

Makefile: ./Makefile.in ../../config.status
	cd ../.. && ./config.status

$(LIBS): $(OBJS)
	$(AR) rcs $@ $^

%: %.cpp $(MKDEPS) $(INCFILES) $(LIBS) $(LIBDEPS)
	$(COMPILE) -o $@ $(LOCALFLAGS) $< $(LIBS) $(LIBTHR) $(LDFLAGS) $(LOCALLIBS)

../%.a:
	$(MAKE) -C `dirname $@`

# Makefile.tail end

