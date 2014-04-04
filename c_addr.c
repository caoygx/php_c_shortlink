/*************************************************************************
	> File Name: c_addr.c
	> Author: 草原狗熊
	> Mail: 85080428@qq.com 
	> Created Time: 2014年04月03日 星期四 18时29分44秒
 ************************************************************************/

#include<stdio.h>
void  main(){
    
    char* names[] = {"aa","bb"};
    printf("%p\n",&names[0]);
//    printf("%d\n",names); //0x7fff003d4f40
    //printf("%s\n",(*(0x7fff18849070)+1)[0]);
    printf("%s\n",(*names)[0]);

/*
    char *s = "hello";
    printf("%p\n",s);
    printf("%s\n",0x400648);

    char *a = "a";
    char  b[] = "b";
//    b = a;
    printf("a_add=%p\n",&a);
    printf("a_val=%p\n",a);
    printf("b_add=%p\n",&b);
    printf("b_val=%p\n",b);
    */
}
