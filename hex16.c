/*************************************************************************
> File Name: hex16.c
> Author: 草原狗熊
> Mail: 85080428@qq.com 
> Created Time: 2014年03月20日 星期四 10时42分18秒
************************************************************************/

#include<stdio.h>
#include<string.h>
#include<stdlib.h>
char* md5();
char str[32];
void main(){
    char* str;
    str = (char*)md5();
    printf("s = %s\n",str);
}
char* md5(){
    unsigned char decrypt[16];
    int i;
    for(i = 0; i < 16; i++){
        decrypt[i] = 0x0+(100-i);
    }
    char ret[16][3];
    for(i = 0; i < 16; i++){
        sprintf(ret[i], "%02x",decrypt[i]);
    }

    for(i = 0; i < 16; i++){
       strcat(str,ret[i]);
    }

//    printf("str=%s\n",str);
    return str;
}
