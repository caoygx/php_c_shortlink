dnl $Id$
dnl config.m4 for extension shortlink

dnl Comments in this file start with the string 'dnl'.
dnl Remove where necessary. This file will not work
dnl without editing.

dnl If your extension references something external, use with:

dnl PHP_ARG_WITH(shortlink, for shortlink support,
dnl Make sure that the comment is aligned:
dnl [  --with-shortlink             Include shortlink support])

dnl Otherwise use enable:

PHP_ARG_ENABLE(shortlink, whether to enable shortlink support,
dnl Make sure that the comment is aligned:
[  --enable-shortlink           Enable shortlink support])

if test "$PHP_SHORTLINK" != "no"; then
  dnl Write more examples of tests here...

  dnl # --with-shortlink -> check with-path
  dnl SEARCH_PATH="/usr/local /usr"     # you might want to change this
  dnl SEARCH_FOR="/include/shortlink.h"  # you most likely want to change this
  dnl if test -r $PHP_SHORTLINK/$SEARCH_FOR; then # path given as parameter
  dnl   SHORTLINK_DIR=$PHP_SHORTLINK
  dnl else # search default path list
  dnl   AC_MSG_CHECKING([for shortlink files in default path])
  dnl   for i in $SEARCH_PATH ; do
  dnl     if test -r $i/$SEARCH_FOR; then
  dnl       SHORTLINK_DIR=$i
  dnl       AC_MSG_RESULT(found in $i)
  dnl     fi
  dnl   done
  dnl fi
  dnl
  dnl if test -z "$SHORTLINK_DIR"; then
  dnl   AC_MSG_RESULT([not found])
  dnl   AC_MSG_ERROR([Please reinstall the shortlink distribution])
  dnl fi

  dnl # --with-shortlink -> add include path
  dnl PHP_ADD_INCLUDE($SHORTLINK_DIR/include)

  dnl # --with-shortlink -> check for lib and symbol presence
  dnl LIBNAME=shortlink # you may want to change this
  dnl LIBSYMBOL=shortlink # you most likely want to change this 

  dnl PHP_CHECK_LIBRARY($LIBNAME,$LIBSYMBOL,
  dnl [
  dnl   PHP_ADD_LIBRARY_WITH_PATH($LIBNAME, $SHORTLINK_DIR/lib, SHORTLINK_SHARED_LIBADD)
  dnl   AC_DEFINE(HAVE_SHORTLINKLIB,1,[ ])
  dnl ],[
  dnl   AC_MSG_ERROR([wrong shortlink lib version or lib not found])
  dnl ],[
  dnl   -L$SHORTLINK_DIR/lib -lm
  dnl ])
  dnl
  dnl PHP_SUBST(SHORTLINK_SHARED_LIBADD)

  PHP_NEW_EXTENSION(shortlink, shortlink.c, $ext_shared)
fi
