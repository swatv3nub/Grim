# GRIM - Information Gathering and Vulnerability Scanning Tool

## Features

### Information Gathering
- WHOIS Lookup
- GeoIP Lookup
- HTTP Headers
- DNS Lookup
- Subnet Calculation
- Nmap Port Scan
- Sub-domain Finder
- Reverse IP Lookup

### Vulnerability Scanning
- SQL Injection Detection
- Cross-Site Scripting (XSS) Detection
- Directory Traversal Detection
- Remote File Inclusion (RFI) Detection
- Local File Inclusion (LFI) Detection
- Server-Side Request Forgery (SSRF) Detection
- Email Header Injection Detection
- Command Injection Detection
- Cross-Site Request Forgery (CSRF) Detection
- Insecure Direct Object References (IDOR) Detection
- Open Redirect Detection
- XML External Entity (XXE) Detection

#### Vulnerability Scanning Details
- **SQL Injection**: Tests for SQL errors using common payloads
- **XSS**: Tests for reflected XSS using script payloads
- **Directory Traversal**: Tests for file system access using path traversal payloads
- **RFI**: Tests for remote file inclusion using external URL payloads
- **LFI**: Tests for local file inclusion using system file paths
- **SSRF**: Tests for server-side request forgery using internal network payloads
- **Email Header Injection**: Tests for CRLF injection and email header manipulation vulnerabilities
- **Command Injection**: Tests for OS command execution vulnerabilities using system command payloads
- **CSRF**: Checks for missing CSRF tokens in forms and state-changing requests
- **IDOR**: Tests for insecure direct object references by comparing responses to different resource IDs
- **Open Redirect**: Tests for unsafe redirects using external domain payloads
- **XXE**: Tests for XML external entity injection using malicious XML payloads

All scans provide color-coded results:
- Red: Potential vulnerability found
- Green: No vulnerability detected

### Crawling
- Admin Panel Discovery
- Backup File Discovery
- General Site Crawling

## Usage

1. Clone the repository
2. Run `sudo apt-get install php-curl php-xml`
3. Run `php grim.php`
4. Enter the target website (without http/https)
5. Follow on screen instructions.


## Requirements
- PHP 7.0+
- cURL extension
- DOM extension


## To-Do
- Improve crawling functionality
- Rework SQL injection detection scan.
- Add a feature to save the scan results to a file / database
- Improve the user interface for the admin panel discovery
- Improve the user interface for the backup file discovery

## License
GPL-3 License
