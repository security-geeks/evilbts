# Makefile
# This file holds the make rules for the Telephony Engine

# override DEBUG at compile time to enable full debug or remove it all
DEBUG :=

CC  := gcc -Wall
CXX := g++ -Wall
OBJCXX := g++ -Wall
SED := sed
DEFS :=
LIBAUX:= -ldl 
LIBTHR:= -lpthread
INCLUDES := -I.. -I..
CFLAGS :=  -O2 -fno-exceptions -fPIC -DHAVE_GCC_FORMAT_CHECK -DHAVE_BLOCK_RETURN 
CPPFLAGS :=  -O2 -Wno-overloaded-virtual  -fno-exceptions -fPIC -DHAVE_GCC_FORMAT_CHECK -DHAVE_BLOCK_RETURN 
LDFLAGS:= 
SONAME_OPT := -shared -Wl,-soname=
LDCONFIG:=true
OBJCFLAGS := 

MKDEPS := ../config.status
YLIB:= libyate.so.5.5.1
CINC := ../yateclass.h ../yatemime.h
EINC := $(CINC) ../yatengine.h
PINC := $(EINC) ../yatephone.h
CLINC:= $(PINC) ../yatecbase.h
LIBS :=
CLSOBJS := TelEngine.o ObjList.o HashList.o Mutex.o Thread.o Socket.o Resolver.o \
	String.o DataBlock.o NamedList.o \
	URI.o Mime.o Array.o Iterator.o XML.o \
	Hasher.o YMD5.o YSHA1.o YSHA256.o Base64.o Cipher.o Compressor.o \
	Math.o
ENGOBJS := Configuration.o Message.o Engine.o Plugin.o
TELOBJS := DataFormat.o Channel.o
CLIOBJS := Client.o ClientLogic.o

LIBOBJS := $(CLSOBJS) $(ENGOBJS) $(TELOBJS) $(CLIOBJS)
CLEANS = $(LIBOBJS) core
CCOMPILE = $(CC) $(DEFS) $(DEBUG) $(INCLUDES) $(CFLAGS)
COMPILE = $(CXX) $(DEFS) $(DEBUG) $(INCLUDES) $(CPPFLAGS)
OBJCCOMPILE = $(OBJCXX) $(OBJCFLAGS) $(DEFS) $(DEBUG) $(INCLUDES) $(CPPFLAGS)
LINK = $(CXX) $(LDFLAGS)

ifeq (no,yes)
REGEX_INC:= -I../engine/regex -UDEBUG
LIBOBJS := $(LIBOBJS) regex.o
else
REGEX_INC:=
endif

ifeq (no,yes)
MACOSX_INC:=  -I../engine/macosx
MACOSX_LIB:= -lobjc -framework Foundation
LIBOBJS := $(LIBOBJS) MacOSXUtils.o
else
MACOSX_INC:=
MACOSX_LIB:=
endif

prefix = /usr/local
exec_prefix = ${prefix}

bindir = ${exec_prefix}/bin
libdir = ${exec_prefix}/lib
incdir = ${prefix}/include/yate
moddir = ${exec_prefix}/lib/yate

# include optional local make rules
-include YateLocal.mak

.PHONY: all debug ddebug xdebug
all: ../$(YLIB)

debug:
	$(MAKE) all DEBUG=-g3

ddebug:
	$(MAKE) all DEBUG='-g3 -DDEBUG'

xdebug:
	$(MAKE) all DEBUG='-g3 -DXDEBUG'

.PHONY: clean
clean:
	@-$(RM) $(CLEANS) 2>/dev/null

.PHONY: strip
strip: all
	-strip --strip-debug --discard-locals ../$(YLIB)

Engine.o: ./Engine.cpp $(MKDEPS) $(EINC) ../yateversn.h ../yatepaths.h
	$(COMPILE) -DHAVE_POLL -DFDSIZE_HACK=8192 -DHAVE_PRCTL -DHAVE_GETCWD $(MACOSX_INC) -c $<

Channel.o: ./Channel.cpp $(MKDEPS) $(PINC)
	$(COMPILE) -c $<

DataBlock.o: ./DataBlock.cpp $(MKDEPS) $(EINC)
	$(COMPILE) -I./tables -c $<

DataFormat.o: ./DataFormat.cpp $(MKDEPS) $(PINC)
	$(COMPILE) -c $<

Socket.o: ./Socket.cpp $(MKDEPS) $(CINC)
	$(COMPILE) -DHAVE_POLL -DFDSIZE_HACK=8192  -DHAVE_NTOP -DHAVE_PTON -DHAVE_GHBN_R -DHAVE_GHBN2_R -DHAVE_GHBN2  -c $<

Resolver.o: ./Resolver.cpp $(MKDEPS) $(CINC)
	$(COMPILE)  -c $<

Mutex.o: ./Mutex.cpp $(MKDEPS) $(CINC)
	$(COMPILE)  -DHAVE_TIMEDLOCK -DHAVE_TIMEDWAIT -c $<

Thread.o: ./Thread.cpp $(MKDEPS) $(CINC)
	$(COMPILE)  -DHAVE_PRCTL -c $<

TelEngine.o: ./TelEngine.cpp $(MKDEPS) $(CINC)
	$(COMPILE) -DATOMIC_OPS -DHAVE_GMTOFF -DHAVE_INT_TZ -c $<

Client.o: ./Client.cpp $(MKDEPS) $(CLINC)
	$(COMPILE) -c $<

ClientLogic.o: ./ClientLogic.cpp $(MKDEPS) $(CLINC)
	$(COMPILE) -c $<

String.o: ./String.cpp $(MKDEPS) $(CINC)
	$(COMPILE) $(REGEX_INC) -c $<

regex.o: ../engine/regex/regex.c $(MKDEPS)
	$(CCOMPILE) -DSTDC_HEADERS $(REGEX_INC) -c $<

MacOSXUtils.o: ../engine/macosx/MacOSXUtils.mm $(MKDEPS)
	$(OBJCCOMPILE) $(MACOSX_INC) -c $<

%.o: ./%.cpp $(MKDEPS) $(EINC)
	$(COMPILE) -c $<

Makefile: ./Makefile.in $(MKDEPS)
	cd .. && ./config.status

../$(YLIB): $(LIBOBJS) $(LIBS)
	$(LINK) -o $@ $(SONAME_OPT)$(YLIB) $^ $(LIBTHR) $(LIBAUX) $(MACOSX_LIB) -lresolv
