/*************************************************************************
	> File Name: p.c
	> Author: 草原狗熊
	> Mail: 85080428@qq.com 
	> Created Time: 2014年04月03日 星期四 16时00分22秒
 ************************************************************************/

#include<stdio.h>
void main(){
    char *names[] = {"hello", "world"};
    char *name = "hello";

//    printf("%p\n",1);
    printf("指针地址\n");
    printf("指针地址自身%p\n",&name);
    printf("%p\n",&name[0]);
    printf("%p\n",&name[1]);

    printf("数组地址\n");
    char name2[] = "hello";
    printf("数组地址自身%p\n",&name2);
    printf("%p\n",&name2[0]);
    printf("%p\n",&name2[1]);
//   char **p = name;
//  char *names = "xx";
    printf("name[0]地址=%p\n",&names[0]);
    printf("names地址＝%p\n",names);
//    printf("%s", *++names);
//    printf("names_ad=%p\n",names);
//    printf("name_ad=%p\n",name);
//    printf("%c", ++name);
/*    char *p="abc";
    printf("p_address= %p\n",&p);
    printf("p_value = %p\n",p);*/
}
