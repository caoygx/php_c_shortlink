/*************************************************************************
	> File Name: paddress.c
	> Author: 草原狗熊
	> Mail: 85080428@qq.com 
	> Created Time: 2014年04月04日 星期五 11时18分15秒
 ************************************************************************/

#include<stdio.h>
#include<stdlib.h>
void main(){
    char *c;
    printf("c1_add=%p\n",&c);
    printf("c1_val=%p\n",c);
    
    c=malloc(sizeof(char(*)));

    printf("c2_add=%p\n",&c);
    printf("c2_add=%p\n",c);
}
