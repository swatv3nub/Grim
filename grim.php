<?php
error_reporting(0);
require 'functions.php';
require 'var.php';
require 'payloads.php';
echo $cln;
function update()
    {
        echo "\n\e[91m\e[1m[+] GRIM UPDATE UTILITY [+]\nUpdate in progress, please wait...\n\n$cln";
        system("git fetch origin && git reset --hard origin/master && git clean -f -d");
        echo $bold . $fgreen . "[i] Job finished successfully! Please Restart GRIM \n" . $cln;
        exit;
    }
    
system("clear");
$vuln_scan_enabled = false; // Default to disabled
grim_banner();
if (extension_loaded('curl') || extension_loaded('dom'))
  {
  }
else
  {
    if (!extension_loaded('curl'))
      {
        echo $bold . $red . "\n[!] cURL Module Is Missing! Try 'fix' command OR Install php-curl" . $cln;
      }
    if (!extension_loaded('dom'))
      {
        echo $bold . $red . "\n[!] DOM Module Is Missing! Try 'fix' command OR Install php-xml\n" . $cln;
      }
  }
thephuckinstart:
echo "\n";
userinput(" NOTE - PUT WEBSITE IN THIS FORMAT -> site.com AND DON'T PUT HTTP OR HTTPS HERE
     ENTER THE WEBSITE FOR SCANNING ");
$ip = trim(fgets(STDIN, 1024));
if ($ip == "help")
  {
    echo "\n\n[+] GRIM Help Screen [+] \n\n";
    echo $bold . $lblue . "Commands\n";
    echo "========\n";
    echo $fgreen . "[1] help:$cln View The Help Menu\n";
    echo $bold . $fgreen . "[2] fix:$cln Installs All Required Modules (Suggested If You Are Running The Tool For The First Time)\n";
    echo $bold . $fgreen . "[3] URL:$cln Enter The Domain Name Which You Want To Scan (Format:www.sample.com / sample.com)\n";
    echo $bold . $fgreen . "[4] update:$cln Updates The Script To The Newest Version Available.\n";
    goto thephuckinstart;
  }
elseif ($ip == "fix")
  {
    echo "\n\e[91m\e[1m[+] GRIM FiX MENU [+]\n\n$cln";
    echo $bold . $blue . "[+] Checking If CURL module is installed ...\n";
    if (!extension_loaded('curl'))
      {
        echo $bold . $red . "[!] CURL MODULE IS NOT INSTALLED SEE THE README AND INSTALL REQUIRED PACKAGES MANUALLY ! \n";
        echo $yellow . "[*] Installing CURL. (Operation requires sudo permission so you might be asked for password) \n" . $cln;
        system("sudo apt-get -qq --assume-yes install php-curl");
        echo $bold . $fgreen . "[i] CURL Installed. \n";
      }
    else
      {
        echo $bold . $fgreen . "[i] CURL is already installed, Skipping To Next \n";
      }
    echo $bold . $blue . "[+] Checking If php-XML module is installed ...\n";
    if (!extension_loaded('dom'))
      {
        echo $bold . $red . "[!] php-XML MODULE IS NOT INSTALLED SEE THE README AND INSTALL REQUIRED PACKAGES MANUALLY ! \n";
        echo $yellow . "[*] Installing php-XML. (Operation requires sudo permission so you might be asked for password) \n" . $cln;
        system("sudo apt-get -qq --assume-yes install php-xml");
        echo $bold . $fgreen . "[i] DOM Installed. \n";
      }
    else
      {
        echo $bold . $fgreen . "[i] php-XML is already installed, You Are All SET ;) \n";
      }
    echo $bold . $fgreen . "[i] Job finished successfully! Please Restart GRIM \n";
    exit;
  }
elseif ($ip == "update")
  {
    update();
  }

elseif (strpos($ip, '://') !== false)
  {
    echo $bold . $red . "\n[!] (HTTP/HTTPS) Detected In Input! Enter URL Without Http/Https\n" . $CURLOPT_RETURNTRANSFER;
    goto thephuckinstart;
  }
elseif (strpos($ip, '.') == false)
  {
    echo $bold . $red . "\n[!] Invalid URL Format! Enter A Valid URL\n" . $cln;
    goto thephuckinstart;
  }
elseif (strpos($ip, ' ') !== false)
  {
    echo $bold . $red . "\n[!] Invalid URL Format! Enter A Valid URL\n" . $cln;
    goto thephuckinstart;
  }
else
  {
    echo "\n";
    userinput("Enter 1 For HTTP OR Enter 2 For HTTPS");
    echo $cln . $bold . $fgreen;
    $ipsl = trim(fgets(STDIN, 1024));
    if ($ipsl == "2")
      {
        $ipsl = "https://";
      }
    else
      {
        $ipsl = "http://";
      }
    echo "\n";
    userinput("Enter ViewDNS API Key");
    echo $cln . $bold . $fgreen;
    $apikey = trim(fgets(STDIN, 1024));
scanlist:

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
           | |T~\___!___!___/~T| |           INFORMATION GATHERING AND VULNERABILITY FETCHING TOOL
           | |`IIII_I_I_I_IIII'| |           ------X Project Hackfreaks X------
           |  \,III I I I III,/  |
            \   `~~~~~~~~~~'    /            Telegram : @ProjectHackfreaks
              \   .       .   /              
                \.    ^    ./
                  ^~~~^~~~^

            $lwhite Scanning Site : " . $fgreen . $ipsl . $ip . $blue . "
      \n\n";
      echo $bold . $green . "\n\n [A]  FOR STARTING THE SCANNING \n$white [B]  GO BACK FOR SELECTING OTHER SITE \n$red [Q]  QUIT! \n\n" . $cln;
askscan:
    //$amber [V]  TOGGLE VULNERABILITY SCANNER (" . ($vuln_scan_enabled ? "ENABLED" : "DISABLED") . ") \n
    //$blue [V]  TOGGLE VULNERABILITY SCANNER$amber (default = disabled) 
    userinput("CHOOSE AND PUT ANY ONE OPTION");
    $scan = trim(fgets(STDIN, 1024));

    if (!in_array($scan, array(
        'A',
        'B',
        'V',
        'Q',
        'a',
        'b',
        'v',
        'q',
    ), true))

      {
        echo $bold . $red . "\n[!] Invalid Input! Please Enter a Valid Option! \n\n" . $cln;
        goto askscan;
      }
    else
      {
        if ($scan == "15")
          {
            goto thephuckinstart;
          }
        elseif ($scan == 'q' | $scan == 'Q')
          {
            echo "\n\n\t THANKS FOR USING GRIM, YOU CAN JOIN US ON TELEGRAM CHANNEL TOO @ProjectHackfreaks \n\n";
            die();
          }
        elseif ($scan == 'b' || $scan == 'B')
          {
            system("clear");
            goto thephuckinstart;
          }
        // elseif ($scan == 'v' || $scan == 'V')
        //   {
        //     $vuln_scan_enabled = !$vuln_scan_enabled;
        //     echo $bold . $amber . "\n[!] Vulnerability scanner is now " . ($vuln_scan_enabled ? "ENABLED" : "DISABLED") . "\n" . $cln;
        //     goto askscan;
        //   }
        elseif ($scan == "F" || $scan == "f"){
          echo "\n\e[91m\e[1m[+] GRIM FiX MENU [+]\n\n$cln";
          echo $bold . $blue . "[+] Checking If cURL module is installed ...\n";
          if (!extension_loaded('curl'))
            {
              echo $bold . $red . "[!] cURL Module Not Installed ! \n";
              echo $yellow . "[*] Installing cURL. (Operation requeires sudo permission so you might be asked for password) \n" . $cln;
              system("sudo apt-get -qq --assume-yes install php-curl");
              echo $bold . $fgreen . "[i] cURL Installed. \n";
            }
          else
            {
              echo $bold . $fgreen . "[i] cURL is already installed, Skipping To Next \n";
            }
          echo $bold . $blue . "[+] Checking If php-XML module is installed ...\n";
          if (!extension_loaded('dom'))
            {
              echo $bold . $red . "[!] php-XML Module Not Installed ! \n";
              echo $yellow . "[*] Installing php-XML. (Operation requeires sudo permission so you might be asked for password) \n" . $cln;
              system("sudo apt-get -qq --assume-yes install php-xml");
              echo $bold . $fgreen . "[i] DOM Installed. \n";
            }
          else
            {
              echo $bold . $fgreen . "[i] php-XML is already installed, You Are All SET ;) \n";
            }
          echo $bold . $fgreen . "[i] Job finished successfully! Please Restart GRIM \n";
          exit;
        }
        elseif ($scan == "A" || $scan == "a")
          {

            echo "\n$cln" . "$lyellow" . "[+] Scanning Begins ... \n";
            echo "$blue" . "[i] Scanning Site:\e[92m $ipsl" . "$ip \n";
            echo "\n\n";

            echo "\n$bold" . "$lblue" . "B A S I C   I N F O \n";
            echo "--------------->\n";
            echo "\n\e[0m";

            $reallink = $ipsl . $ip;
            $srccd    = file_get_contents($reallink);
            $lwwww    = str_replace("www.", "", $ip);

            echo "\n$yellow" . "[+] Site Title: ";
            echo "\e[92m";
            echo getTitle($reallink);
            echo "\e[0m";

            echo "\n$yellow" . "[+] Social Links: \n";
            extract_social_links($srccd);

            $wip = gethostbyname($ip);
            echo "\n$yellow" . "[+] IP address: ";
            echo "\e[92m";
            echo $wip . "\n\e[0m";

            echo "$yellow" . "[+] Web Server: ";
            WEBserver($reallink);
            echo "\n";

            echo "$yellow" . "[+] CMS: \e[92m" . CMSdetect($reallink) . " \e[0m";

            // echo "\n$yellow" . "[+] SimilarWeb Rank: ";
            // echo "\e[92m" . get_similarweb_rank($reallink) . "\e[0m";

            echo "\n$yellow" . "[+] Cloudflare: ";
            cloudflaredetect($reallink);

            echo "$yellow" . "[+] Robots File:$cln ";
            robotsdottxt($reallink);
            echo "\n\n$cln";
            echo "\n\n$bold" . $lblue . "W H O I S   L O O K U P\n";
            echo "--------------->";
            echo "\n\n$cln";
            $urlwhois    = "https://api.viewdns.info/whois/v2/?domain={$lwwww}&apikey={$apikey}";
            $resultwhois = file_get_contents($urlwhois);
            $whois       = json_decode($resultwhois, true);
            echo json_encode($whois, JSON_PRETTY_PRINT);
            echo "\n\n$cln";

            echo "\n\n$bold" . $lblue . "G E O  I P  L O O K  U P\n";
            echo "--------------->";
            echo "\n\n$cln";
            $urldlup    = "https://api.viewdns.info/iplocation/?domain={$wip}&apikey={$apikey}";
            $iplocn = file_get_contents($urldlup);
            $iplocnn = json_decode($iplocn, true);
            echo json_encode($iplocnn, JSON_PRETTY_PRINT);
            echo "\n\n";

            echo "\n\n$bold" . $lblue . "H T T P   H E A D E R S\n";
            echo "--------------->";
            echo "\n\n$cln";
            gethttpheader($reallink);
            echo "\n\n";

            echo "\n\n$bold" . $lblue . "D N S   L O O K U P\n";
            echo "--------------->";
            echo "\n\n$cln";
            $urldlup    = "https://api.viewdns.info/dnsrecord/?domain={$lwwww}s&apikey={$apikey}";
            $resultdlup = file_get_contents($urldlup);
            $jsonData = json_decode($resultdlup, true);
            echo json_encode($jsonData, JSON_PRETTY_PRINT);
            echo "\n\n";

            echo "\n\n$bold" . $lblue . "S U B N E T   C A L C U L A T I O N\n";
            echo "--------------->";
            echo "\n\n$cln";
            $urlscal    = "http://api.hackertarget.com/subnetcalc/?q=" . $lwwww;
            $resultscal = file_get_contents($urlscal);
            echo $resultscal;
            // $jsonData2 = json_decode($resultscal, true);
            // echo json_encode($jsonData2, JSON_PRETTY_PRINT);
            echo "\n\n";

            echo "\n\n$bold" . $lblue . "N M A P   P O R T   S C A N\n";
            echo "--------------->";
            echo "\n\n$cln";
            $urlnmap    = "https://api.viewdns.info/portscan/?host={$lwwww}&apikey={$apikey}";
            $resultnmap = file_get_contents($urlnmap);
            $jsonData3 = json_decode($resultnmap, true);
            echo json_encode($jsonData3, JSON_PRETTY_PRINT);
            echo "\n";

            echo "\n\n$bold" . $lblue . "S U B - D O M A I N   F I N D E R\n";
            echo "--------------->";
            echo "\n\n$cln";
            $urlsd      = "https://api.viewdns.info/reversedns/?ip={$wip}&apikey={$apikey}"; 
            $resultsd   = file_get_contents($urlsd);
            $jsonData4 = json_decode($resultsd, true);
            echo json_encode($jsonData4, JSON_PRETTY_PRINT);
            // BOTTOM CODE FOR HACKERTARGET
            // $subdomains = trim($resultsd, "\n");
            // $subdomains = explode("\n", $subdomains);
            // unset($subdomains['0']);
            // $sdcount = count($subdomains);
            // echo "\n$yellow" . "[i] Total Subdomains Found :$cln " . $green . $sdcount . "\n\n$cln";
            // foreach ($subdomains as $subdomain)
            //   {
            //     echo "[+] Subdomain:$cln $fgreen" . (str_replace(",", "\n\e[0m[-] IP:$cln $fgreen", $subdomain));
            //     echo "\n\n$cln";
            //   }
            echo "\n\n";

            echo "\n\n$bold" . $lblue . "R E V E R S E   I P   L O O K U P\n";
            echo "--------------->";
            echo "\n\n";
            $api_url = "https://api.viewdns.info/reverseip/?ip={$wip}&apikey={$apikey}";
            $resultip = file_get_contents($api_url);
            $jsonData5 = json_decode($resultip, true);
            echo json_encode($jsonData5, JSON_PRETTY_PRINT);
            echo "\n\n";
            echo "\n\n";
vuln:
            echo "\n\n$bold" . $lblue . "V U L N E R A B I L I T Y   S C A N N E R\n";
            echo "--------------->$cln";
            echo "Run Vulnerability Scan (y/n): ";
            $scannnn = trim(fgets(STDIN, 1024));
            if ($scannnn == "yes" || $scannnn == "y" || $scannnn == "Y") {
              echo "\n";
              $lulzurl = $ipsl . $ip;
              $html    = file_get_contents($lulzurl);
              $dom     = new DOMDocument;
              @$dom->loadHTML($html);
              $links = $dom->getElementsByTagName('a');
              $vlnk  = 0;
              
              // SQL Injection Scan
              foreach ($links as $link)
                {
                  $lol = $link->getAttribute('href');
                  if (strpos($lol, '?') !== false)
                    {
                      echo "\n$yellow [#] " . $fgreen . $lol . "\n$cln";
                      echo $yellow . " [-] Searching For SQL Errors: ";
                      $sqllist = file_get_contents('sqlerrors.ini');
                      $sqlist  = explode(',', $sqllist);
                      if (strpos($lol, '://') !== false)
                        {
                          $sqlurl = $lol . "'";
                        }
                      else
                        {
                          $sqlurl = $ipsl . $ip . "/" . $lol . "'";
                        }
                      $sqlsc = file_get_contents($sqlurl);
                      $sqlvn = "$red Not Found";
                      foreach ($sqlist as $sqli)
                        {
                          if (strpos($sqlsc, $sqli) !== false)
                              $sqlvn = "$green Found!";
                        }
                      echo $sqlvn;
                      echo "\n$cln";
                      echo "\n";
                      $vlnk++;
                    }
                }
              
              // XSS Scan
              echo "\n$yellow [XSS Scan]";
            
              foreach ($xss_payloads as $payload) {
                  $test_url = $lulzurl . "?test=" . urlencode($payload);
                  $response = file_get_contents($test_url);
                  if (strpos($response, $payload) !== false) {
                      echo "\n$red [-] Potential XSS Vulnerability Found!\nPayload: $payload";
                  } else {
                      echo "\n$green [-] No XSS Vulnerability Detected";
                  }
              }
              echo "\n$cln";
              
              // Directory Traversal Scan
              echo "\n$yellow [Directory Traversal Scan]";
              
              foreach ($traversal_payloads as $payload) {
                  $test_url = $lulzurl . "?file=" . urlencode($payload);
                  $response = file_get_contents($test_url);
                  if (strpos($response, "root:") !== false || strpos($response, "<?php") !== false) {
                      echo "\n$red [-] Potential Directory Traversal Vulnerability Found!\nPayload: $payload";
                  } else {
                      echo "\n$green [-] No Directory Traversal Vulnerability Detected";
                  }
              }
              echo "\n$cln";

              // RFI/LFI Scan
              echo "\n$yellow [RFI/LFI Scan]";
              
              foreach ($rfi_payloads as $payload) {
                  $test_url = $lulzurl . "?file=" . urlencode($payload);
                  $response = file_get_contents($test_url);
                  if (strpos($response, "evil.com") !== false || strpos($response, "base64") !== false) {
                      echo "\n$red [-] Potential RFI Vulnerability Found!\nPayload: $payload";
                  } else {
                      echo "\n$green [-] No RFI Vulnerability Detected";
                  }
              }
              foreach ($lfi_payloads as $payload) {
                  $test_url = $lulzurl . "?file=" . urlencode($payload);
                  $response = file_get_contents($test_url);
                  if (strpos($response, "root:") !== false || strpos($response, "<?php") !== false) {
                      echo "\n$red [-] Potential LFI Vulnerability Found!\nPayload: $payload";
                  } else {
                      echo "\n$green [-] No LFI Vulnerability Detected";
                  }
              }
              echo "\n$cln";

              // SSRF Scan
              echo "\n$yellow [SSRF Scan]";
                        
              foreach ($ssrf_payloads as $payload) {
                  $test_url = $lulzurl . "?url=" . urlencode($payload);
                  $response = file_get_contents($test_url);
                  if (strpos($response, "meta-data") !== false || strpos($response, "localhost") !== false) {
                      echo "\n$red [-] Potential SSRF Vulnerability Found!\nPayload: $payload";
                  } else {
                      echo "\n$green [-] No SSRF Vulnerability Detected";
                  }
              }
              echo "\n$cln";

              // Email Header Injection Scan
              echo "\n$yellow [Email Header Injection Scan]";
              
              foreach ($email_payloads as $payload) {
                // Construct the test URL with payload
                $test_url = $lulzurl . "?email=test@test.com&message=" . urlencode($payload);
                
                // Perform the GET request
                $response = @file_get_contents($test_url);
                
                // Error handling: Check if response is empty
                if ($response === FALSE) {
                    echo "\n$red [-] Request failed for URL: $test_url";
                    continue;
                }
                
                // Check if injected payload is reflected or causes abnormal behavior
                if (strpos($response, "attacker@evil.com") !== false || strpos($response, "Hacked") !== false || strpos($response, "hacker@evil.com") !== false) {
                    echo "\n$red [-] Potential Email Header Injection Vulnerability Found! Payload: $payload";
                } else {
                    echo "\n$green [-] No Email Header Injection Vulnerability Detected for payload: $payload";
                }
            }
            
            echo "\n$cln";

            // Command Injection Scan
            echo "\n$yellow [Command Injection Scan]";
            foreach ($cmd_payloads as $payload) {
                $test_url = $lulzurl . "?cmd=" . urlencode($payload);
                $response = file_get_contents($test_url);
                if (strpos($response, "root:") !== false || strpos($response, "bin") !== false) {
                    echo "\n$red [-] Potential Command Injection Vulnerability Found!\nPayload: $payload";
                } else {
                    echo "\n$green [-] No Command Injection Vulnerability Detected";
                }
            }
            echo "\n$cln";

            // CSRF Scan
            echo "\n$yellow [CSRF Scan]";
            $csrf_test = $lulzurl . "?action=delete&id=1";
            $response = file_get_contents($csrf_test);
            if (strpos($response, "CSRF token") === false && strpos($response, "token") === false) {
                echo "\n$red [-] Potential CSRF Vulnerability - No CSRF Token Found!";
            } else {
                echo "\n$green [-] CSRF Protection Detected";
            }
            echo "\n$cln";

            // IDOR Scan
            echo "\n$yellow [IDOR Scan]";
            $idor_test1 = $lulzurl . "?id=1";
            $idor_test2 = $lulzurl . "?id=2";
            $response1 = file_get_contents($idor_test1);
            $response2 = file_get_contents($idor_test2);
            if ($response1 === $response2) {
                echo "\n$red [-] Potential IDOR Vulnerability - Same Response for Different IDs!";
            } else {
                echo "\n$green [-] No IDOR Vulnerability Detected";
            }
            echo "\n$cln";

            // Open Redirect Scan
            echo "\n$yellow [Open Redirect Scan]";
            foreach ($redirect_payloads as $payload) {
                $test_url = $lulzurl . "?redirect=" . urlencode($payload);
                $response = file_get_contents($test_url);
                if (strpos($response, "Location: " . $payload) !== false) {
                    echo "\n$red [-] Potential Open Redirect Vulnerability Found!\nPayload: $payload";
                } else {
                    echo "\n$green [-] No Open Redirect Vulnerability Detected";
                }
            }
            echo "\n$cln";

            // XXE Scan
            echo "\n$yellow [XXE Scan]";
            foreach ($xss_payloads as $payload) {
              $test_url = $lulzurl . "?xml=" . urlencode($payload);
              $response = file_get_contents($test_url);
                if (strpos($response, "root:") !== false) {
                    echo "\n$red [-] Potential XXE Vulnerability Found!\nPayload: $payload";
                } else {
                    echo "\n$green [-] No XXE Vulnerability Detected";
              }
            }
            echo "\n$cln";
          }
            elseif ($scannnn == "n" || $scannnn == "N" || $scannnn == "no") {
              echo "\n$green [+] Scan Disabled, Moving to Crawler";
              goto csel;
          }
            else {
              echo "\n$red [-] Invalid Input, Please Try Again";
              goto vuln;
          }
csel:
            echo "\n\n$bold" . $lblue . "C R A W L E R \n";
            echo "--------------->";
            echo "\n\n";
            echo "\nCrawling Types & Descriptions:$cln";
            echo "\n\n$bold" . "69:$cln THIS 69 TYPE CRAWLER IS LITE VERSION SCANNER AND SCANNES LESS,SO I PREFER YOU TO USE 420 FOR DEEP SCAN.\n";
            echo "\n$bold" . "420:$cln THIS 420 TYPE CRAWLER TAKES A LITTLE BIT TIME BUT IT DOES DEEP SCANNING!!\n";
            echo "\n$bold" . "Q:$cln Quit GRIM\n\n";
            echo "Select Crawler Type (69/420) or QUIT (Q): ";
            $ctype = trim(fgets(STDIN, 1024));
            if ($ctype == "420")
              {
                echo "\n\t -[ A D V A N C E   C R A W L I N G ]-\n";
                echo "\n\n";
                echo "\n Loading Crawler File ....\n";
                if (file_exists("crawl/admin.ini"))
                  {
                    echo "\n[-] Admin Crawler File Found! Scanning For Admin Pannel [-]\n";
                    $crawllnk = file_get_contents("crawl/admin.ini");
                    $crawls   = explode(',', $crawllnk);
                    echo "\nURLs Loaded: " . count($crawls) . "\n\n";
                    foreach ($crawls as $crawl)
                      {
                        $url    = $ipsl . $ip . "/" . $crawl;
                        $handle = curl_init($url);
                        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
                        $response = curl_exec($handle);
                        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
                        if ($httpCode == 200)
                          {
                            echo "\n\n[U] $url : ";
                            echo "Found!";
                          }
                        elseif ($httpCode == 404)
                          {
                          }
                        else
                          {
                            echo "\n\n[U] $url : ";
                            echo "HTTP Response: " . $httpCode;
                          }
                        curl_close($handle);
                      }
                  }
                else
                  {
                    echo "\n File Not Found, Aborting Crawl ....\n";
                  }
                if (file_exists("crawl/backup.ini"))
                  {
                    echo "\n[-] Backup Crawler File Found! Scanning For Site Backups [-]\n";
                    $crawllnk = file_get_contents("crawl/backup.ini");
                    $crawls   = explode(',', $crawllnk);
                    echo "\nURLs Loaded: " . count($crawls) . "\n\n";
                    foreach ($crawls as $crawl)
                      {
                        $url    = $ipsl . $ip . "/" . $crawl;
                        $handle = curl_init($url);
                        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
                        $response = curl_exec($handle);
                        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
                        if ($httpCode == 200)
                          {
                            echo "\n\n[U] $url : ";
                            echo "Found!";
                          }
                        elseif ($httpCode == 404)
                          {
                          }
                        else
                          {
                            echo "\n\n[U] $url : ";
                            echo "HTTP Response: " . $httpCode;
                          }
                        curl_close($handle);
                      }
                  }
                else
                  {
                    echo "\n File Not Found, Aborting Crawl ....\n";
                  }
                if (file_exists("crawl/others.ini"))
                  {
                    echo "\n[-] General Crawler File Found! Crawling The Site [-]\n";
                    $crawllnk = file_get_contents("crawl/others.ini");
                    $crawls   = explode(',', $crawllnk);
                    echo "\nURLs Loaded: " . count($crawls) . "\n\n";
                    foreach ($crawls as $crawl)
                      {
                        $url    = $ipsl . $ip . "/" . $crawl;
                        $handle = curl_init($url);
                        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
                        $response = curl_exec($handle);
                        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
                        if ($httpCode == 200)
                          {
                            echo "\n\n[U] $url : ";
                            echo "Found!";
                          }
                        elseif ($httpCode == 404)
                          {
                          }
                        else
                          {
                            echo "\n\n[U] $url : ";
                            echo "HTTP Response: " . $httpCode;
                          }
                        curl_close($handle);
                      }
                  }
                else
                  {
                    echo "\n File Not Found, Aborting Crawl ....\n";
                  }
              }
            elseif ($ctype == "69")
              {
                echo "\n\t -[ B A S I C   C R A W L I N G ]-\n";
                echo "\n\n";
                echo "\n Loading Crawler File ....\n";
                if (file_exists("crawl/admin.ini"))
                  {
                    echo "\n[-] Admin Crawler File Found! Scanning For Admin Pannel [-]\n";
                    $crawllnk = file_get_contents("crawl/admin.ini");
                    $crawls   = explode(',', $crawllnk);
                    echo "\nURLs Loaded: " . count($crawls) . "\n\n";
                    foreach ($crawls as $crawl)
                      {
                        $url    = $ipsl . $ip . "/" . $crawl;
                        $handle = curl_init($url);
                        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
                        $response = curl_exec($handle);
                        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
                        if ($httpCode == 200)
                          {
                            echo "\n\n[U] $url : ";
                            echo "Found!";
                          }
                        elseif ($httpCode == 404)
                          {
                          }
                        else
                          {
                            echo ".";
                          }
                        curl_close($handle);
                      }
                  }
                else
                  {
                    echo "\n File Not Found, Aborting Crawl ....\n";
                  }
                if (file_exists("crawl/backup.ini"))
                  {
                    echo "\n[-] Backup Crawler File Found! Scanning For Site Backups [-]\n";
                    $crawllnk = file_get_contents("crawl/backup.ini");
                    $crawls   = explode(',', $crawllnk);
                    echo "\nURLs Loaded: " . count($crawls) . "\n\n";
                    foreach ($crawls as $crawl)
                      {
                        $url    = $ipsl . $ip . "/" . $crawl;
                        $handle = curl_init($url);
                        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
                        $response = curl_exec($handle);
                        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
                        if ($httpCode == 200)
                          {
                            echo "\n\n[U] $url : ";
                            echo "Found!";
                          }
                        elseif ($httpCode == 404)
                          {
                          }
                        curl_close($handle);
                      }
                  }
                else
                  {
                    echo "\n File Not Found, Aborting Crawl ....\n";
                  }
                if (file_exists("crawl/others.ini"))
                  {
                    echo "\n[-] General Crawler File Found! Crawling The Site [-]\n";
                    $crawllnk = file_get_contents("crawl/others.ini");
                    $crawls   = explode(',', $crawllnk);
                    echo "\nURLs Loaded: " . count($crawls) . "\n\n";
                    foreach ($crawls as $crawl)
                      {
                        $url    = $ipsl . $ip . "/" . $crawl;
                        $handle = curl_init($url);
                        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
                        $response = curl_exec($handle);
                        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
                        if ($httpCode == 200)
                          {
                            echo "\n\n[U] $url : ";
                            echo "Found!";
                          }
                        elseif ($httpCode == 404)
                          {
                          }
                        curl_close($handle);
                      }
                  }
                else
                  {
                    echo "\n File Not Found, Aborting Crawl ....\n";
                  }
              }
            elseif($ctype == "q" | $ctype == "Q")
              {
                echo "\n\n\t THANKS FOR USING GRIM, YOU CAN JOIN US ON TELEGRAM CHANNEL TOO @ProjectHackfreaks \n\n";
                die();
              }
            else {
              goto csel;
            }
          }
        }
    }
  
?>
