# Makefile
# This file holds the make rules for miscellaneous Yate data files

# override DESTDIR at install time to prefix the install directory
DESTDIR :=

DATA := snmp_mib.conf \
	NULL-TEAM-MIB.txt YATE-MIB.txt

ifneq (yes,no)
DATA := $(DATA) hostedx115.rbf hostedx40.rbf
endif

prefix = /usr/local
exec_prefix = ${prefix}
datarootdir = ${prefix}/share
datadir = ${datarootdir}
shrdir = $(datadir)/yate
datdir = $(shrdir)/data

# include optional local make rules
-include YateLocal.mak

.PHONY: all
all:

.PHONY: install
install:
	@cd . && mkdir -p "$(DESTDIR)$(datdir)/" && \
	test -z "$(DATA)" || \
	install -m 0644 $(DATA) "$(DESTDIR)$(datdir)/"

.PHONY: uninstall
uninstall:
	@-for i in $(DATA) ; do \
	    rm "$(DESTDIR)$(datdir)/$$i" ; \
	done;
	@-rmdir "$(DESTDIR)$(datdir)"
	@-rmdir "$(DESTDIR)$(datdir)"

Makefile: ./Makefile.in ../../config.status
	cd ../.. && ./config.status
