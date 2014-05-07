/*************************************************************************
> File Name: shortlink.c
> Author: 草原狗熊
> Mail: 85080428@qq.com 
> Created Time: 2014年03月24日 星期一 13时08分27秒
************************************************************************/

#include<stdio.h>
#include<string.h>
#include<stdlib.h>
#include "md5.h"
#include "shortlink.h"
char *charset = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

int main(int argc, char *argv[])
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
    shortlink(url,urlhash);
        return 0;
}
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

char *shortlink(char *url, char *urlhash){
    char *key = "alexis";
    int len = strlen(urlhash);
    int i;

        char * urlhash_piece;
        urlhash_piece = substr(urlhash, 0, 8);


        int hex = strtol(urlhash_piece,NULL,16) & 0x3fffffff; 
        char *short_url = "http://t.cn/";
        int j;

        char s[6];
        for (j = 0; j < 6; j++) {

            s[j] = charset[hex & 0x0000003d];


            hex = hex >> 5;
        }
    int l = strlen(short_url);
    l = l+6+1;
    short_url = (char*)malloc(l);
    strcat(short_url,"http://t.cn/");
    strcat(short_url,s);
        printf("short_url = %s\n\n",short_url);
        return short_url;

}
  
