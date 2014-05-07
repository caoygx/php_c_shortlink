#include<stdio.h>
#include<stdlib.h>

int check_date(char c, int i){
    switch(i){
        case 1 :
            if((int)c < 0 || (int)c >10)
                return 0;
            break;
        case 2 :
            if((int)c < 0 || (int)c >10)
                return 0;
            break;
        case 3 :
            if((int)c < 0 || (int)c >10)
                return 0;
            break;
        case 4 :
            if((int)c < 0 || (int)c >10)
                return 0;
            break;
        case 5 :
            if((int)c < 0 || (int)c >10)
                return 0;
            break;
    }
}

void main(int argc,char *argv[]){
    char ch;
    FILE *fp;
    int i;
    if((fp=fopen(argv[1],"r"))==NULL) /* 打开一个由argv[1]所指的文件*/
    {
        printf("not open");
    //    exit(0);
    }
//    char *temp;
    char temp[5];
    int temp_index = 0;
//    temp =  (char*)malloc(5+1);
    while ((ch=fgetc(fp))!=EOF){ 
        //putchar(ch);
//        strcat(temp,(char*)ch);
       if(temp_index < 4){
        temp[temp_index] = ch;
        temp_index++;
       }
    }
    printf("%s",temp);
    fclose(fp);
}
