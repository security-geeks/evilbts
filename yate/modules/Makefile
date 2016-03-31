# Makefile
# This file holds the make rules for the Telephony Engine modules

# override DESTDIR at install time to prefix the install directory
DESTDIR :=

# override DEBUG at compile time to enable full debug or remove it all
DEBUG :=

YATE_VERSION := 5.5.1
YATE_RELEASE := devel1
YATE_REVISION:= 6107

CC  := gcc -Wall
CXX := g++ -Wall
MOC := /usr/bin/moc-qt4
QT4_INC := -DQT_SHARED -I/usr/include/qt4 -I/usr/include/qt4/QtUiTools -I/usr/include/qt4 -I/usr/include/qt4/QtGui -I/usr/include/qt4 -I/usr/include/qt4/QtXml -I/usr/include/qt4 -I/usr/include/qt4/QtCore 
QT4_LIB := -lQtUiTools -lQtGui -lQtXml -lQtCore 
QT4_INC_NET := -I/usr/include/qt4/QtNetwork
QT4_LIB_NET := -lQtNetwork
QT4_STATIC_MODULES :=
HAVE_PGSQL := no
PGSQL_INC := 
PGSQL_LIB := -lpq
HAVE_MYSQL := yes
MYSQL_INC := -I/usr/include/mysql -DMYSQL_OPT_RECONNECT=MYSQL_OPT_RECONNECT -DMYSQL_OPT_READ_TIMEOUT=MYSQL_OPT_READ_TIMEOUT -DMYSQL_OPT_WRITE_TIMEOUT=MYSQL_OPT_WRITE_TIMEOUT -DHAVE_MYSQL_SET_CHARSET
MYSQL_LIB := -L/usr/lib/x86_64-linux-gnu -lmysqlclient -lpthread -lz -lm -ldl
HAVE_SQLITE := yes
SQLITE_INC := 
SQLITE_LIB := -lsqlite3 
HAVE_SPANDSP := no
SPANDSP_INC := 
SPANDSP_LIB := -lspandsp
HAVE_OPENSSL := yes
OPENSSL_INC := 
OPENSSL_LIB := -lssl -lcrypto 
HAVE_ZLIB := yes
ZLIB_INC := -I/usr/include
ZLIB_LIB := -lz
HAVE_LIBUSB := yes
LIBUSB_INC := -DHAVE_LIBUSB_VER -I/usr/include/libusb-1.0
LIBUSB_LIB := -lusb-1.0
SED := sed
DEFS :=
INCLUDES := -I.. -I..
CFLAGS :=  -O2 -fno-exceptions -fPIC -DHAVE_GCC_FORMAT_CHECK -DHAVE_BLOCK_RETURN 
CPPFLAGS :=  -O2 -Wno-overloaded-virtual  -fno-exceptions -fPIC -DHAVE_GCC_FORMAT_CHECK -DHAVE_BLOCK_RETURN 
LDFLAGS:= 
YATELIBS:= -L.. -lyate 
MODFLAGS:= -rdynamic -shared -Wl,--unresolved-symbols=ignore-in-shared-libs
MODRELAX:= -rdynamic -shared
MODSTRIP:= -Wl,--retain-symbols-file,/dev/null
INCFILES := ../yateclass.h ../yatengine.h ../yatephone.h ../yateversn.h

JUSTSIG := server/ysigchan.yate server/analog.yate \
	server/ciscosm.yate server/sigtransport.yate \
	sig/isupmangler.yate sig/ss7_lnp_ansi.yate \
	sig/camel_map.yate

SUBDIRS :=
MKDEPS  := ../config.status
PROGS := cdrbuild.yate cdrcombine.yate cdrfile.yate regexroute.yate \
	tonegen.yate tonedetect.yate wavefile.yate \
	extmodule.yate conference.yate moh.yate pbx.yate \
	dumbchan.yate callfork.yate mux.yate \
	yrtpchan.yate ystunchan.yate \
	ysipchan.yate \
	yiaxchan.yate \
	yjinglechan.yate jabber/jabberserver.yate jabber/jbfeatures.yate \
	ysockschan.yate filetransfer.yate fileinfo.yate \
	gvoice.yate \
	javascript.yate \
	server/pbxassist.yate server/dbpbx.yate server/lateroute.yate \
	server/park.yate server/queues.yate server/queuesnotify.yate \
	server/regfile.yate server/accfile.yate server/register.yate \
	server/callcounters.yate server/cpuload.yate server/ccongestion.yate \
	server/dbwave.yate \
	server/yradius.yate \
	server/ysnmpagent.yate \
	server/monitoring.yate \
	server/sipfeatures.yate \
	sip/sip_cnam_lnp.yate \
	server/heartbeat.yate server/clustering.yate \
	server/mgcpgw.yate server/mgcpca.yate \
	server/mrcpspeech.yate \
	server/presence.yate server/subscription.yate \
	server/users.yate \
	$(JUSTSIG) \
	server/analogdetect.yate \
	server/cache.yate \
	server/eventlogs.yate \
	client/jabberclient.yate \
	callgen.yate analyzer.yate rmanager.yate msgsniff.yate \
	radio/dummyradio.yate

LIBS :=
DIRS := client server jabber qt4 sip sig radio

ifneq ($(HAVE_PGSQL),no)
PROGS := $(PROGS) server/pgsqldb.yate
endif

ifneq ($(HAVE_MYSQL),no)
PROGS := $(PROGS) server/mysqldb.yate
endif

ifneq ($(HAVE_SQLITE),no)
PROGS := $(PROGS) server/sqlitedb.yate
endif

ifneq (yes,no)
PROGS := $(PROGS) enumroute.yate
endif

ifneq (yes,no)
PROGS := $(PROGS) client/osschan.yate
endif

ifneq (yes,no)
PROGS := $(PROGS) client/alsachan.yate
endif

ifneq (no,no)
PROGS := $(PROGS) client/coreaudio.yate
endif

ifneq (yes,no)
ifeq (no,no)
PROGS := $(PROGS) qt4/updater.yate qt4/customtable.yate qt4/customtext.yate \
	qt4/customtree.yate qt4/widgetlist.yate qt4/clientarchive.yate
endif
endif

ifneq (no,no)
PROGS := $(PROGS) server/zapcard.yate
endif

ifneq (no,no)
ifeq (yes,yes)
PROGS := $(PROGS) server/wpcard.yate
endif
ifeq (no,yes)
ifeq (yes,yes)
PROGS := $(PROGS) server/tdmcard.yate
endif
endif
endif

ifneq (no,no)
PROGS := $(PROGS) server/lksctp.yate
endif

ifneq ($(HAVE_SPANDSP),no)
PROGS := $(PROGS) faxchan.yate
endif

ifneq (no,no)
PROGS := $(PROGS) h323chan.yate
endif

ifneq (yes,no)
PROGS := $(PROGS) gsmcodec.yate
endif

ifneq (yes,no)
PROGS := $(PROGS) ilbccodec.yate
endif

ifneq (yes,no)
PROGS := $(PROGS) ilbcwebrtc.yate
endif

ifneq (yes,no)
PROGS := $(PROGS) isaccodec.yate
endif

ifneq (no,no)
PROGS := $(PROGS) speexcodec.yate
endif

ifneq (no,no)
PROGS := $(PROGS) amrnbcodec.yate
PROGS := $(PROGS) efrcodec.yate
endif

ifneq ($(HAVE_OPENSSL),no)
PROGS := $(PROGS) openssl.yate
endif

ifneq ($(HAVE_ZLIB),no)
PROGS := $(PROGS) zlibcompress.yate
endif

ifeq (yes,yes)
MALLINFO_DEF := -DHAVE_MALLINFO
endif

ifeq (yes,yes)
COREDUMP_INC := -DHAVE_COREDUMPER -I/usr
COREDUMP_LIB := -L/usr -lcoredumper
endif

ifneq (yes,no)
PROGS := $(PROGS) radio/ybladerf.yate
endif


LOCALFLAGS =
LOCALLIBS =
EXTERNFLAGS =
EXTERNLIBS =
CCOMPILE = $(CC) $(DEFS) $(DEBUG) $(INCLUDES) $(CFLAGS)
COMPILE = $(CXX) $(DEFS) $(DEBUG) $(INCLUDES) $(CPPFLAGS)
LINK = $(CXX) $(LDFLAGS)
MODLINK = $(CXX) $(MODFLAGS) $(MODSTRIP) $(LDFLAGS)
MODCOMP = $(COMPILE) $(MODFLAGS) $(MODSTRIP) $(LDFLAGS)

prefix = /usr/local
exec_prefix = ${prefix}
moddir = ${exec_prefix}/lib/yate

# include optional local make rules
-include YateLocal.mak

.PHONY: all debug ddebug xdebug
all: subdirs do-all $(LIBS) $(PROGS)

debug:
	$(MAKE) all DEBUG=-g3 MODSTRIP=

ddebug:
	$(MAKE) all DEBUG='-g3 -DDEBUG' MODSTRIP=

xdebug:
	$(MAKE) all DEBUG='-g3 -DXDEBUG' MODSTRIP=

.PHONY: strip
strip: all do-strip
	strip --strip-debug --discard-locals $(PROGS)

.PHONY: clean
clean: do-clean
	@-$(RM) $(PROGS) $(LIBS) *.o qt4/*.o qt4/*.moc core 2>/dev/null
	@-for i in $(PROGS) ; do \
	    $(RM) -rf $$i.dSYM 2>/dev/null; \
	done;

.PHONY: install
install: all do-install
	@mkdir -p "$(DESTDIR)$(moddir)/" && \
	for i in $(DIRS) ; do \
	    mkdir -p "$(DESTDIR)$(moddir)/$$i" ; \
	done; \
	for i in $(PROGS) ; do \
	    install -D -m 0644 "$$i" "$(DESTDIR)$(moddir)/$$i" ; \
	done;

.PHONY: uninstall
uninstall: do-uninstall
	@-for i in $(PROGS) ; do \
	    rm "$(DESTDIR)$(moddir)/$$i" ; \
	done; \
	for i in $(DIRS) $(SUBDIRS) ; do \
	    rmdir "$(DESTDIR)$(moddir)/$$i" ; \
	done;
	@-rmdir "$(DESTDIR)$(moddir)"

.PHONY: subdirs
subdirs:
	@mkdir -p $(DIRS)

qt4/%.o: ./qt4/%.cpp $(MKDEPS) $(INCFILES)
	$(MAKE) $(patsubst %.o,%.moc,$@)
	$(COMPILE) -c -o $@ $(QT4_INC) -I../clients/qt4 -I./qt4 $<

%.o: ./%.cpp $(MKDEPS) $(INCFILES)
	$(COMPILE) -c $<

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

lib%.so: %.o
	$(LINK) -shared -o $@ $^ $(YATELIBS)

server/%.yate: ./server/%.cpp $(MKDEPS) $(INCFILES)
	mkdir -p server && $(MODCOMP) -o $@ $(LOCALFLAGS) $(EXTERNFLAGS) $< $(LOCALLIBS) $(YATELIBS) $(EXTERNLIBS)

client/%.yate: ./client/%.cpp $(MKDEPS) $(INCFILES)
	mkdir -p client && $(MODCOMP) -o $@ $(LOCALFLAGS) $(EXTERNFLAGS) $< $(LOCALLIBS) $(YATELIBS) $(EXTERNLIBS)

qt4/%.yate: ./qt4/%.cpp ../libyateqt4.so $(MKDEPS) $(INCFILES)
	$(MAKE) $(patsubst %.yate,%.moc,$@)
	$(MODCOMP) -o $@ $(LOCALFLAGS) $(QT4_INC) $(EXTERNFLAGS) -I../clients/qt4 -Iqt4 $< $(LOCALLIBS) ../libyateqt4.so $(YATELIBS) $(QT4_LIB) $(EXTERNLIBS)

qt4/%.moc: ./qt4/%.h $(MKDEPS) $(INCFILES)
	mkdir -p qt4 && $(MOC) $(DEFS) $(INCLUDES) $(QT4_INC) -I../clients/qt4 -I./qt4 -o $@ $<

sig/%.yate: ./sig/%.cpp $(MKDEPS) $(INCFILES)
	mkdir -p sig && $(MODCOMP) -o $@ $(LOCALFLAGS) $(EXTERNFLAGS) $< $(LOCALLIBS) $(YATELIBS) $(EXTERNLIBS)

sip/%.yate: ./sip/%.cpp $(MKDEPS) $(INCFILES)
	mkdir -p sip && $(MODCOMP) -o $@ $(LOCALFLAGS) $(EXTERNFLAGS) $< $(LOCALLIBS) $(YATELIBS) $(EXTERNLIBS)

%.yate: ./%.cpp $(MKDEPS) $(INCFILES)
	$(MODCOMP) -o $@ $(LOCALFLAGS) $(EXTERNFLAGS) $< $(LOCALLIBS) $(YATELIBS) $(EXTERNLIBS)

# Take special care of the modules that depend on optional libs

$(JUSTSIG) server/wpcard.yate server/tdmcard.yate server/zapcard.yate: ../libyatesig.so
$(JUSTSIG): LOCALFLAGS = -I../libs/ysig
server/wpcard.yate server/tdmcard.yate: LOCALFLAGS = -I../libs/ysig 
server/zapcard.yate: LOCALFLAGS = -I../libs/ysig 
$(JUSTSIG) server/wpcard.yate server/tdmcard.yate server/zapcard.yate: LOCALLIBS = -lyatesig

sig/ss7_lnp_ansi.yate: LOCALFLAGS = -I../libs/ysig -I../libs/yasn
sig/ss7_lnp_ansi.yate: LOCALLIBS = -lyatesig -lyateasn

sig/camel_map.yate: ../libs/yasn/libyasn.a
sig/camel_map.yate: LOCALFLAGS = -I../libs/ysig -I../libs/yasn
sig/camel_map.yate: LOCALLIBS = -lyatesig -lyateasn

server/analogdetect.yate: ../libs/ymodem/libyatemodem.a
server/analogdetect.yate: LOCALFLAGS = -I../libs/ymodem
server/analogdetect.yate: LOCALLIBS = -L../libs/ymodem -lyatemodem

h323chan.yate: EXTERNFLAGS = 
h323chan.yate: EXTERNLIBS = 

server/pgsqldb.yate: EXTERNFLAGS = $(PGSQL_INC)
server/pgsqldb.yate: EXTERNLIBS = $(PGSQL_LIB)

server/mysqldb.yate: EXTERNFLAGS = $(MYSQL_INC)
server/mysqldb.yate: EXTERNLIBS = $(MYSQL_LIB)

server/sqlitedb.yate: EXTERNFLAGS = $(SQLITE_INC)
server/sqlitedb.yate: EXTERNLIBS = $(SQLITE_LIB)

client/alsachan.yate: EXTERNLIBS = -lasound

client/coreaudio.yate: EXTERNLIBS = -framework CoreServices -framework CoreAudio -framework AudioUnit -framework AudioToolbox

yiaxchan.yate: ../libs/yiax/libyateiax.a
yiaxchan.yate: LOCALFLAGS = -I../libs/yiax
yiaxchan.yate: LOCALLIBS = -L../libs/yiax -lyateiax

yjinglechan.yate jabber/jabberserver.yate jabber/jbfeatures.yate client/jabberclient.yate: ../libyatejabber.so
yjinglechan.yate jabber/jabberserver.yate jabber/jbfeatures.yate client/jabberclient.yate: LOCALFLAGS = -I../libs/yjabber
yjinglechan.yate jabber/jabberserver.yate jabber/jbfeatures.yate client/jabberclient.yate: LOCALLIBS = -lyatejabber

server/dbpbx.yate server/pbxassist.yate: ../libs/ypbx/libyatepbx.a
server/dbpbx.yate server/pbxassist.yate: LOCALFLAGS = -I../libs/ypbx
server/dbpbx.yate server/pbxassist.yate: LOCALLIBS = -L../libs/ypbx -lyatepbx

server/mgcpca.yate: ../libyatemgcp.so ../libyatesig.so ../libs/ysdp/libyatesdp.a
server/mgcpca.yate: LOCALFLAGS = -I../libs/ymgcp -I../libs/ysig -I../libs/ysdp
server/mgcpca.yate: LOCALLIBS = -lyatemgcp -lyatesig -L../libs/ysdp -lyatesdp

server/mgcpgw.yate: ../libyatemgcp.so ../libs/ysdp/libyatesdp.a
server/mgcpgw.yate: LOCALFLAGS = -I../libs/ymgcp -I../libs/ysdp
server/mgcpgw.yate: LOCALLIBS = -lyatemgcp -L../libs/ysdp -lyatesdp

server/lksctp.yate: EXTERNFLAGS = 
server/lksctp.yate: EXTERNLIBS = -lsctp

ilbccodec.yate: ../libs/ilbc/libilbc.a
ilbccodec.yate: LOCALFLAGS = -I/home/evilsocket/Desktop/yate/yate/libs/ilbc
ilbccodec.yate: LOCALLIBS = -L../libs/ilbc -lilbc

ilbcwebrtc.yate: ../libs/miniwebrtc/libminiwebrtc.a
ilbcwebrtc.yate: LOCALFLAGS = -I/home/evilsocket/Desktop/yate/yate/libs/miniwebrtc/audio/coding_ilbc -I../libs/miniwebrtc/audio/common/processing -I../libs/miniwebrtc
ilbcwebrtc.yate: LOCALLIBS = -L../libs/miniwebrtc -lminiwebrtc

isaccodec.yate: ../libs/miniwebrtc/libminiwebrtc.a
isaccodec.yate: LOCALFLAGS = -I/home/evilsocket/Desktop/yate/yate/libs/miniwebrtc/audio/coding_isac/main -I../libs/miniwebrtc/audio/common/processing -I../libs/miniwebrtc
isaccodec.yate: LOCALLIBS = -L../libs/miniwebrtc -lminiwebrtc

gsmcodec.yate: EXTERNFLAGS = -I/usr/include
gsmcodec.yate: EXTERNLIBS = -lgsm

speexcodec.yate: EXTERNFLAGS = 
speexcodec.yate: EXTERNLIBS = -lspeex

amrnbcodec.yate efrcodec.yate: EXTERNFLAGS = 
amrnbcodec.yate efrcodec.yate: EXTERNLIBS = -lamrnb

faxchan.yate: EXTERNFLAGS = $(SPANDSP_INC)
faxchan.yate: EXTERNLIBS = $(SPANDSP_LIB)

ysipchan.yate: ../libs/ysip/libyatesip.a ../libs/ysdp/libyatesdp.a
ysipchan.yate: LOCALFLAGS = -I../libs/ysip -I../libs/ysdp
ysipchan.yate: LOCALLIBS = -L../libs/ysip -lyatesip -L../libs/ysdp -lyatesdp

yrtpchan.yate: ../libs/yrtp/libyatertp.a
yrtpchan.yate: LOCALFLAGS = -I../libs/yrtp
yrtpchan.yate: LOCALLIBS = -L../libs/yrtp -lyatertp

openssl.yate: EXTERNFLAGS = $(OPENSSL_INC)
openssl.yate: EXTERNLIBS = $(OPENSSL_LIB)

rmanager.yate: EXTERNFLAGS = $(COREDUMP_INC) $(MALLINFO_DEF)
rmanager.yate: EXTERNLIBS = $(COREDUMP_LIB)

qt4/updater.yate: EXTERNFLAGS = $(QT4_INC_NET)
qt4/updater.yate: EXTERNLIBS = $(QT4_LIB_NET)

javascript.yate: ../libyatescript.so ../libs/ypbx/libyatepbx.a
javascript.yate: LOCALFLAGS = -I../libs/yscript -I../libs/ypbx
javascript.yate: LOCALLIBS = -lyatescript -L../libs/ypbx -lyatepbx

zlibcompress.yate: EXTERNFLAGS = $(ZLIB_INC)
zlibcompress.yate: EXTERNLIBS = $(ZLIB_LIB)

server/ysnmpagent.yate: ../libyateasn.so ../libs/ysnmp/libysnmp.a
server/ysnmpagent.yate: LOCALFLAGS = -I../libs/yasn -I../libs/ysnmp
server/ysnmpagent.yate: LOCALLIBS = -L../libs/ysnmp -lysnmp -lyateasn

radio/ybladerf.yate radio/dummyradio.yate: ../libyateradio.so
radio/ybladerf.yate radio/dummyradio.yate: LOCALFLAGS = -I../libs/yradio
radio/ybladerf.yate radio/dummyradio.yate: LOCALLIBS = -lyateradio
radio/ybladerf.yate: EXTERNFLAGS = $(LIBUSB_INC)
radio/ybladerf.yate: EXTERNLIBS = $(LIBUSB_LIB)

../libyatesig.so ../libs/ysig/libyatesig.a: ../libs/ysig/yatesig.h
	$(MAKE) -C ../libs/ysig

../libs/ilbc/libilbc.a:
	$(MAKE) -C ../libs/ilbc

../libs/miniwebrtc/libminiwebrtc.a:
	$(MAKE) -C ../libs/miniwebrtc

../libs/ysip/libyatesip.a: ../libs/ysip/yatesip.h
	$(MAKE) -C ../libs/ysip

../libs/yrtp/libyatertp.a: ../libs/yrtp/yatertp.h
	$(MAKE) -C ../libs/yrtp

../libs/ysdp/libyatesdp.a: ../libs/ysdp/yatesdp.h
	$(MAKE) -C ../libs/ysdp

../libs/yiax/libyateiax.a: ../libs/yiax/yateiax.h
	$(MAKE) -C ../libs/yiax

../libyatemgcp.so ../libs/ymgcp/libyatemgcp.a: ../libs/ymgcp/yatemgcp.h
	$(MAKE) -C ../libs/ymgcp

../libs/ymodem/libyatemodem.a: ../libs/ymodem/yatemodem.h
	$(MAKE) -C ../libs/ymodem

../libyatejabber.so ../libs/yjabber/libyatejabber.a: ../libs/yjabber/xmpputils.h ../libs/yjabber/yatejabber.h ../libs/yjabber/yatejingle.h
	$(MAKE) -C ../libs/yjabber

../libyatescript.so ../libs/yjabber/libyatescript.a: ../libs/yscript/yatescript.h
	$(MAKE) -C ../libs/yscript

../libs/ypbx/libyatepbx.a: ../libs/ypbx/yatepbx.h
	$(MAKE) -C ../libs/ypbx

../libyateqt4.so: ../clients/qt4/qt4client.h
	$(MAKE) -C ../clients/qt4

../libyateasn.so ../libs/yasn/libyasn.a: ../libs/yasn/yateasn.h
	$(MAKE) -C ../libs/yasn

../libs/ysnmp/libysnmp.a: ../libs/ysnmp/yatesnmp.h
	$(MAKE) -C ../libs/ysnmp

../libyateradio.so ../libs/yradio/libyateradio.a: ../libs/yradio/yateradio.h
	$(MAKE) -C ../libs/yradio
