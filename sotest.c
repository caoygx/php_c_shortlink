/*************************************************************************
	> File Name: sotest.c
	> Author: 草原狗熊
	> Mail: 85080428@qq.com 
	> Created Time: 2014年04月02日 星期三 11时00分02秒
 ************************************************************************/

#include<stdio.h>
#include<stdlib.h>
#include<dlfcn.h>
#include "shortlink.h"
int main(){
void *handle;  
        int (*cosine)(int, int);  
        char *error;  
        handle = dlopen ("./liba.so", RTLD_LAZY);  
        if (!handle)  
        {  
                    fputs (dlerror(), stderr);  
                    exit(1);  
              
                }  
        shorturl(handle, "sum");  
}
