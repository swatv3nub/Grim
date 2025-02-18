<?php
$rhversion = "2.0.0";
$white = "\e[97m";
$black = "\e[30m\e[1m";
$yellow = "\e[93m";
$orange = "\e[38;5;208m";
$blue   = "\e[34m";
$lblue  = "\e[36m";
$cln    = "\e[0m";
$green  = "\e[92m";
$fgreen = "\e[32m";
$red    = "\e[91m";
$magenta = "\e[35m";
$purple = "\e[38;5;129m";  // For Command Injection
$teal   = "\e[38;5;86m";    // For CSRF
$coral  = "\e[38;5;203m";  // For IDOR
$sky    = "\e[38;5;117m";  // For Open Redirect
$amber  = "\e[38;5;214m";  // For XXE
$bluebg = "\e[44m";
$lbluebg = "\e[106m";
$greenbg = "\e[42m";
$lgreenbg = "\e[102m";
$yellowbg = "\e[43m";
$lyellowbg = "\e[103m";
$redbg = "\e[101m";
$grey = "\e[37m";
$cyan = "\e[36m";
$bold   = "\e[1m";
$italic = "\e[3m";
function grim_banner(){
  system("clear");
    echo $bold . $orange . "
                 _,.-------.,_
             ,;~'             '~;,
           ,;                     ;,
          ;                         ;
         ,'                         ',
        ,;                           ;,
        ; ;                         ; ;           ██████╗ ██████╗ ██╗███╗   ███╗
        | ;   ______       ______   ; |          ██╔════╝ ██╔══██╗██║████╗ ████║
        |  `/         .           \'  |          ██║  ███╗██████╔╝██║██╔████╔██║
        |  ~  ,-~~~^~, | ,~^~~~-,  ~  |          ██║   ██║██╔══██╗██║██║╚██╔╝██║
        |   |        }:{        |   |            ╚██████╔╝██║  ██║██║██║ ╚═╝ ██║
        |   l       / | \       !   |             ╚═════╝ ╚═╝  ╚═╝╚═╝╚═╝     ╚═╝
        .~  (__,.--       --.,__)  ~.                        |
        |     ---;' / | \ `;---     |                        |
         \__.       \/^\/       .__/                         |
          V| \                 / |V                          v
           | |T~\___!___!___/~T| |           
           | |`IIII_I_I_I_IIII'| |           Information Gathering and Vulnerability Scanning Tool
           |  \,III I I I III,/  |
            \   `~~~~~~~~~~'    /            
              \   .       .   /              
                \.    ^    ./
                  ^~~~^~~~^  
                                                       
  \n";
}
?>
