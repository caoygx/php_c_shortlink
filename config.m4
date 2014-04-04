dnl $Id$
dnl config.m4 for extension imgurl

dnl Comments in this file start with the string 'dnl'.
dnl Remove where necessary. This file will not work
dnl without editing.

dnl If your extension references something external, use with:

dnl PHP_ARG_WITH(imgurl, for imgurl support,
dnl Make sure that the comment is aligned:
dnl [  --with-imgurl             Include imgurl support])

dnl Otherwise use enable:

PHP_ARG_ENABLE(imgurl, whether to enable imgurl support,
dnl Make sure that the comment is aligned:
[  --enable-imgurl           Enable imgurl support])

if test "$PHP_IMGURL" != "no"; then
  dnl Write more examples of tests here...

  dnl # --with-imgurl -> check with-path
  dnl SEARCH_PATH="/usr/local /usr"     # you might want to change this
  dnl SEARCH_FOR="/include/imgurl.h"  # you most likely want to change this
  dnl if test -r $PHP_IMGURL/$SEARCH_FOR; then # path given as parameter
  dnl   IMGURL_DIR=$PHP_IMGURL
  dnl else # search default path list
  dnl   AC_MSG_CHECKING([for imgurl files in default path])
  dnl   for i in $SEARCH_PATH ; do
  dnl     if test -r $i/$SEARCH_FOR; then
  dnl       IMGURL_DIR=$i
  dnl       AC_MSG_RESULT(found in $i)
  dnl     fi
  dnl   done
  dnl fi
  dnl
  dnl if test -z "$IMGURL_DIR"; then
  dnl   AC_MSG_RESULT([not found])
  dnl   AC_MSG_ERROR([Please reinstall the imgurl distribution])
  dnl fi

  dnl # --with-imgurl -> add include path
  dnl PHP_ADD_INCLUDE($IMGURL_DIR/include)

  dnl # --with-imgurl -> check for lib and symbol presence
  dnl LIBNAME=imgurl # you may want to change this
  dnl LIBSYMBOL=imgurl # you most likely want to change this 

  dnl PHP_CHECK_LIBRARY($LIBNAME,$LIBSYMBOL,
  dnl [
  dnl   PHP_ADD_LIBRARY_WITH_PATH($LIBNAME, $IMGURL_DIR/lib, IMGURL_SHARED_LIBADD)
  dnl   AC_DEFINE(HAVE_IMGURLLIB,1,[ ])
  dnl ],[
  dnl   AC_MSG_ERROR([wrong imgurl lib version or lib not found])
  dnl ],[
  dnl   -L$IMGURL_DIR/lib -lm
  dnl ])
  dnl
  PHP_SUBST(IMGURL_SHARED_LIBADD)
  PHP_ADD_LIBRARY_WITH_PATH(md5, /usr/lib, MYEXT_SHARED_LIBADD)

  PHP_NEW_EXTENSION(imgurl, imgurl.c, $ext_shared)
fi
