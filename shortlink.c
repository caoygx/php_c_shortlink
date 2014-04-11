/*
+----------------------------------------------------------------------+
| PHP Version 5                                                        |
+----------------------------------------------------------------------+
| Copyright (c) 1997-2013 The PHP Group                                |
+----------------------------------------------------------------------+
| This source file is subject to version 3.01 of the PHP license,      |
| that is bundled with this package in the file LICENSE, and is        |
| available through the world-wide-web at the following url:           |
| http://www.php.net/license/3_01.txt                                  |
| If you did not receive a copy of the PHP license and are unable to   |
| obtain it through the world-wide-web, please send a note to          |
| license@php.net so we can mail you a copy immediately.               |
+----------------------------------------------------------------------+
| Author:                                                              |
+----------------------------------------------------------------------+
*/

/* $Id$ */

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "ext/standard/md5.h"
#include "php_shortlink.h"
#include <stdlib.h> 
#include "md5.h"
char *substr(char *source,int start,int length);
char *shortlink(char *url, char *arr[]);

/* If you declare any globals in php_shortlink.h uncomment this:
ZEND_DECLARE_MODULE_GLOBALS(shortlink)
*/

/* True global resources - no need for thread safety here */
static int le_shortlink;

/* {{{ shortlink_functions[]
*
* Every user visible function must have an entry in shortlink_functions[].
*/
const zend_function_entry shortlink_functions[] = {
    PHP_FE(confirm_shortlink_compiled,	NULL)		/* For testing, remove later. */
    PHP_FE(shortlink,	NULL)
    PHP_FE_END	/* Must be the last line in shortlink_functions[] */
};
/* }}} */

/* {{{ shortlink_module_entry
*/
zend_module_entry shortlink_module_entry = {
    #if ZEND_MODULE_API_NO >= 20010901
    STANDARD_MODULE_HEADER,
    #endif
    "shortlink",
    shortlink_functions,
    PHP_MINIT(shortlink),
    PHP_MSHUTDOWN(shortlink),
    PHP_RINIT(shortlink),		/* Replace with NULL if there's nothing to do at request start */
    PHP_RSHUTDOWN(shortlink),	/* Replace with NULL if there's nothing to do at request end */
    PHP_MINFO(shortlink),
    #if ZEND_MODULE_API_NO >= 20010901
    "0.1", /* Replace with version number for your extension */
    #endif
    STANDARD_MODULE_PROPERTIES
};
/* }}} */

#ifdef COMPILE_DL_SHORTLINK
ZEND_GET_MODULE(shortlink)
#endif

/* {{{ PHP_INI
*/
/* Remove comments and fill if you need to have entries in php.ini
PHP_INI_BEGIN()
STD_PHP_INI_ENTRY("shortlink.global_value",      "42", PHP_INI_ALL, OnUpdateLong, global_value, zend_shortlink_globals, shortlink_globals)
STD_PHP_INI_ENTRY("shortlink.global_string", "foobar", PHP_INI_ALL, OnUpdateString, global_string, zend_shortlink_globals, shortlink_globals)
PHP_INI_END()
*/
/* }}} */

/* {{{ php_shortlink_init_globals
*/
/* Uncomment this function if you have INI entries
static void php_shortlink_init_globals(zend_shortlink_globals *shortlink_globals)
{
shortlink_globals->global_value = 0;
shortlink_globals->global_string = NULL;
}
*/
/* }}} */

/* {{{ PHP_MINIT_FUNCTION
*/
PHP_MINIT_FUNCTION(shortlink)
{
    /* If you have INI entries, uncomment these lines 
    REGISTER_INI_ENTRIES();
    */
    return SUCCESS;
}
/* }}} */

/* {{{ PHP_MSHUTDOWN_FUNCTION
*/
PHP_MSHUTDOWN_FUNCTION(shortlink)
{
    /* uncomment this line if you have INI entries
    UNREGISTER_INI_ENTRIES();
    */
    return SUCCESS;
}
/* }}} */

/* Remove if there's nothing to do at request start */
/* {{{ PHP_RINIT_FUNCTION
*/
PHP_RINIT_FUNCTION(shortlink)
{
    return SUCCESS;
}
/* }}} */

/* Remove if there's nothing to do at request end */
/* {{{ PHP_RSHUTDOWN_FUNCTION
*/
PHP_RSHUTDOWN_FUNCTION(shortlink)
{
    return SUCCESS;
}
/* }}} */

/* {{{ PHP_MINFO_FUNCTION
*/
PHP_MINFO_FUNCTION(shortlink)
{
    php_info_print_table_start();
    php_info_print_table_header(2, "shortlink support", "enabled");
    php_info_print_table_end();

    /* Remove comments if you have entries in php.ini
    DISPLAY_INI_ENTRIES();
    */
}
/* }}} */


/* Remove the following function when you have succesfully modified config.m4
so that your module can be compiled into PHP, it exists only for testing
purposes. */

/* Every user-visible function in PHP should document itself in the source */
/* {{{ proto string confirm_shortlink_compiled(string arg)
Return a string to confirm that the module is compiled in */
PHP_FUNCTION(confirm_shortlink_compiled)
{
    char *arg = NULL;
    int arg_len, len;
    char *strg;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &arg, &arg_len) == FAILURE) {
        return;
    }

    len = spprintf(&strg, 0, "Congratulations! You have successfully modified ext/%.78s/config.m4. Module %.78s is now compiled into PHP.", "shortlink", arg);
    RETURN_STRINGL(strg, len, 0);
}
/* }}} */
/* The previous line is meant for vim and emacs, so it can correctly fold and 
unfold functions in source code. See the corresponding marks just before 
function definition, where the functions purpose is also documented. Please 
follow this convention for the convenience of others editing your code.
*/

/* {{{ proto string shortlink(string str)
*/
PHP_FUNCTION(shortlink)
{
    char *str = NULL;
    int argc = ZEND_NUM_ARGS();
    int str_len;
    char *result,*ret;

    if (zend_parse_parameters(argc TSRMLS_CC, "s", &str, &str_len) == FAILURE) 
    return;


//调用内置md5函数测试
        zend_fcall_info fci;
        zend_fcall_info_cache fcc;
        zval *retval;
        zval *handle = NULL;
        zval function_name;
        zval **argv[1];
     
        ZVAL_STRING(&function_name, "md5", 1);
        MAKE_STD_ZVAL(handle);
        ZVAL_STRING(handle, "admin", 1);
     
        argv[0] = &handle;
        fci.size = sizeof(fci);
        fci.function_table = EG(function_table);
        fci.function_name = &function_name;
        fci.symbol_table = NULL;
        fci.object_ptr = NULL;
        fci.retval_ptr_ptr = &retval;
        fci.param_count = 1;
        fci.params = argv;
        fci.no_separation = 0;
        if (zend_call_function(&fci, NULL TSRMLS_CC) == FAILURE) {
              RETURN_BOOL(0);
        }
        else {
             //RETURN_ZVAL(retval, 1, 0);
        }


//测试结束


    //char *url = "http://www.baidu.com/xx/";
    char *url = "admin";
    char *ret_arr[4];
    result = shortlink(str,ret_arr);


    zval *new_array;
    MAKE_STD_ZVAL(new_array);
    array_init(new_array);
    
    int k;
    for(k=0; k<4; k++){
        add_next_index_string(new_array,ret_arr[k],1);
    }
    str_len = spprintf(&ret, 0, "%s",ret_arr[0]);
 //   printf("l=%d\n",str_len);
//    printf("s=%s\n\n\n",result);
//    RETURN_STRINGL(ret,str_len,0);
    *return_value = *new_array;
    RETURN_ZVAL(return_value, 1, 0);
    /*str_len = strlen(result);
    printf("l=%d\n",str_len);
    printf("s=%s\n",result);
    RETURN_STRINGL(result,str_len,0);
*/
//    printf("len = %d",str_len);
//    str_len = strlen(ret);
    //   str_len = spprintf(&ret, 0, "<img src = \"%s\" />",result);
    //	php_error(E_WARNING, "shortlink: not yet implemented");

}
/* }}} */


/*
int main2(int argc, char *argv[])
{
    char *str2;
    char *encrypt = "admin";
    str2 = (char*)md5(encrypt);

    char *str = "1234567890";
    char *ret;
    char r[20];

    substr(str,0,4);

    char *url;
    url = argv[1];
    char *urlhash = str2;
    shortlink(url);
    return 0;
}
*/
char* substr(char *source,int start,int length)
{
    int k;
    int i;
    int j=0;
    char *p;
    //char dest[length+1];
    char * dest = (char *)malloc(length+1);
    k=strlen(source);
    p = source;
    int n;
    for(i=start;i<start+length;i++){
        dest[j++] = *(p+i);
    }
    dest[j] = '\0';
    return dest;
}

char *shortlink(char *url,char *arr[]){
    char *key = "alexis";
    int i;
    char *charset = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

    char md5str[33];
    md5str[0] = '\0';
    unsigned char decrypt[16];

    PHP_MD5_CTX context;
    PHP_MD5Init(&context);
    PHP_MD5Update(&context, url, strlen((char *)url));
    PHP_MD5Final(decrypt, &context);
    make_digest_ex(md5str, decrypt, 16);
    printf("md5 = %s \n",md5str);

    //    char *encrypt = "admin";
    //char *urlhash = (char*)md5(url);
    char *urlhash = md5str;
    int len = strlen(urlhash);
    char *urlhash_piece;
    char s[6];
    char *shorturl;
//    char *arr[4];
    //char** arr;
    //arr = malloc(sizeof(char*)*4);
    for(i = 0; i < 4; i++){
        urlhash_piece= substr(urlhash, i*8, 8);
        int hex = strtol(urlhash_piece,NULL,16) & 0x3fffffff; 
        int j;
        for (j = 0; j < 6; j++) {
            s[j] = charset[hex & 0x0000003d];
            hex = hex >> 5;
        }
        shorturl = (char *)malloc(strlen(s)+1);
        strcpy(shorturl, s);
        //*arr = (char*)malloc(strlen(shorturl)+1);
        //*arr = shorturl;
        //arr++;
        arr[i] = shorturl;
    }
/*
    for(i = 0; i< 4; i++){
        printf("a0 = %s\n",arr[i]);
    }
*/
    /*
    char *short_url = "http://t.cn/";
    int l = strlen(short_url);
    l = l+6+1;
    short_url = (char*)malloc(l);
    strcat(short_url,"http://t.cn/");
    strcat(short_url,s);
    printf("short_url = %s\n\n",short_url);
    */
    
    return arr;

}


/*
* Local variables:
* tab-width: 4
* c-basic-offset: 4
* End:
* vim600: noet sw=4 ts=4 fdm=marker
* vim<600: noet sw=4 ts=4
*/
