# Makefile
# This file holds the make rules the Yate BTS module and associated executables

# override DESTDIR at install time to prefix the install directory
DESTDIR :=

# override DEBUG at compile time to enable full debug or remove it all
DEBUG :=

CC  := gcc -Wall
CFLAGS :=  -O2 -DLITTLE_ENDIAN
INCLUDES := -I../..

prefix = /usr/local
exec_prefix = ${prefix}
datarootdir = ${prefix}/share

datadir:= ${datarootdir}
scrdir := /usr/local/share/yate/scripts
shrdir := /usr/local/share/yate

SCRIPTS := gsm_auth.sh
PROGS   := do_comp128 do_milenage
CCOMPILE = $(CC) $(DEFS) $(DEBUG) $(INCLUDES) $(CFLAGS)

MILENAGE:= ./milenage/main.c \
	./milenage/milenage.c ./milenage/rijndael.c
MIL_INC := ./milenage/milenage.h ./milenage/rijndael.h

# include optional local make rules
-include YateLocal.mak

.PHONY: all clean
all: $(PROGS)

install: all
	@mkdir -p "$(DESTDIR)$(scrdir)/" && \
	for i in $(SCRIPTS) ; do \
	    install -D ./$$i "$(DESTDIR)$(scrdir)/" ; \
	done ; \
	for i in $(PROGS) ; do \
	    install -D "$$i" "$(DESTDIR)$(scrdir)/" ; \
	done

uninstall:
	@-for i in $(SCRIPTS) $(PROGS) ; do \
	    rm -f "$(DESTDIR)$(scrdir)/$$i" ; \
	done
	@-rmdir "$(DESTDIR)$(scrdir)"
	@-rmdir "$(DESTDIR)$(shrdir)"

clean:
	@-$(RM) $(PROGS) 2>/dev/null

do_comp128: ./do_comp128.c
	$(CCOMPILE) -o $@ $<

do_milenage: $(MILENAGE) $(MIL_INC)
	$(CCOMPILE) -o $@ $(MILENAGE)
