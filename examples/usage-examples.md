# 🚀 GRIM Security Scanner - Complete Usage Examples

## 📋 Table of Contents
- [Quick Start Examples](#-quick-start-examples)
- [Information Gathering Examples](#-information-gathering-examples)
- [Vulnerability Scanning Examples](#-vulnerability-scanning-examples)
- [Web Crawling Examples](#-web-crawling-examples)
- [Configuration Examples](#-configuration-examples)
- [Update Management Examples](#-update-management-examples)
- [Full Scan Examples](#-full-scan-examples)
- [Advanced Workflows](#-advanced-workflows)
- [Output Examples](#-output-examples)
- [Troubleshooting Examples](#-troubleshooting-examples)

---

## 🚀 Quick Start Examples

### Basic Installation Verification
```bash
# Check if GRIM is working
php grim.php --version

# List all available commands
php grim.php list

# Get help on any command
php grim.php --help
```

### First Scan (Safe Target)
```bash
# Test on localhost (safe)
php grim.php info localhost

# Basic vulnerability scan
php grim.php vuln http://localhost --level low

# Simple crawling
php grim.php crawl http://localhost --max-requests 50
```

---

## 🔍 Information Gathering Examples

### Basic Domain Reconnaissance
```bash
# Simple domain lookup
php grim.php info example.com

# With verbose output
php grim.php info example.com --verbose

# Custom timeout
php grim.php info example.com --timeout 60

# Custom user agent
php grim.php info example.com --user-agent "GRIM Scanner v3.0"
```

### Advanced Information Gathering
```bash
# Export results to different formats
php grim.php info example.com --output json --file domain_recon
php grim.php info example.com --output csv --file domain_recon
php grim.php info example.com --output html --file domain_recon

# Combine multiple options
php grim.php info example.com --verbose --timeout 120 --output html --file comprehensive_recon
```

### What Information Gathering Discovers
- **DNS Records**: A, AAAA, MX, NS, TXT, SOA
- **WHOIS Information**: Registrar, creation date, expiration
- **Subdomain Enumeration**: www, mail, ftp, admin, etc.
- **Technology Stack**: Web server, CMS, frameworks
- **Port Scanning**: Open ports and services
- **SSL Certificate**: Validity, issuer, expiration

---

## 🚨 Vulnerability Scanning Examples

### Basic Vulnerability Scans
```bash
# Standard scan
php grim.php vuln https://example.com

# Different scan levels
php grim.php vuln https://example.com --level low
php grim.php vuln https://example.com --level medium
php grim.php vuln https://example.com --level high
php grim.php vuln https://example.com --level critical

# Custom timeout
php grim.php vuln https://example.com --timeout 180
```

### Advanced Vulnerability Scanning
```bash
# Skip SSL verification (for testing)
php grim.php vuln https://example.com --skip-ssl-verify

# Custom user agent
php grim.php vuln https://example.com --user-agent "Mozilla/5.0 (Windows NT 10.0; Win64; x64)"

# Rate limiting
php grim.php vuln https://example.com --max-requests 5

# Export results
php grim.php vuln https://example.com --output html --file vuln_report
```

### Vulnerability Types Tested
- **SQL Injection**: Login forms, search parameters, user inputs
- **Cross-Site Scripting (XSS)**: Reflected, stored, DOM-based
- **Cross-Site Request Forgery (CSRF)**: Form submissions, API calls
- **Directory Traversal**: File access vulnerabilities
- **File Inclusion**: Local and remote file inclusion
- **Command Injection**: OS command execution
- **Authentication Bypass**: Login bypass techniques

---

## 🕷️ Web Crawling Examples

### Basic Crawling
```bash
# Simple crawl
php grim.php crawl https://example.com

# Custom thread count
php grim.php crawl https://example.com --threads 20

# Maximum requests limit
php grim.php crawl https://example.com --max-requests 1000

# Delay between requests
php grim.php crawl https://example.com --delay 200
```

### Advanced Crawling
```bash
# Aggressive crawling
php grim.php crawl https://example.com --threads 50 --max-requests 5000 --delay 50

# Skip SSL verification
php grim.php crawl https://example.com --skip-ssl-verify

# Custom timeout
php grim.php crawl https://example.com --timeout 60

# Export results
php grim.php crawl https://example.com --output json --file crawl_results
```

### What Crawling Discovers
- **Admin Panels**: `/admin`, `/wp-admin`, `/phpmyadmin`, `/cpanel`
- **Backup Files**: `.bak`, `.sql`, `.zip`, `.tar.gz`
- **Common Files**: `robots.txt`, `sitemap.xml`, `.htaccess`
- **Hidden Directories**: `/backup`, `/old`, `/temp`, `/test`
- **Configuration Files**: `.env`, `config.php`, `settings.ini`
- **Source Code**: `.git`, `.svn`, `.DS_Store`

---

## ⚙️ Configuration Examples

### Viewing Configuration
```bash
# Show current configuration
php grim.php config

# List all configuration options
php grim.php config --list

# Get specific configuration value
php grim.php config --get scanner.timeout
```

### Modifying Configuration
```bash
# Set configuration values
php grim.php config --set scanner.timeout=60
php grim.php config --set scanner.max_concurrent_scans=10
php grim.php config --set scanner.user_agent="GRIM Scanner v3.0"

# Reset to defaults
php grim.php config --reset
```

### Configuration Management
```bash
# Export configuration
php grim.php config --export json
php grim.php config --export yaml
php grim.php config --export ini

# Import configuration
php grim.php config --import my_config.json

# Validate configuration
php grim.php config --validate
```

---

## 🔄 Update Management Examples

### Checking for Updates
```bash
# Check for available updates
php grim.php update --check

# Check with verbose output
php grim.php update --check --verbose
```

### Performing Updates
```bash
# Update with backup
php grim.php update --backup

# Force update (even if same version)
php grim.php update --force

# Dry run (see what would be updated)
php grim.php update --dry-run

# Custom update source
php grim.php update --source https://custom-update-server.com
```

---

## 🎯 Full Scan Examples

### Comprehensive Security Assessment
```bash
# Run everything at once
php grim.php scan https://example.com --full

# Full scan with custom settings
php grim.php scan https://example.com --full --level high --timeout 300

# Export full scan results
php grim.php scan https://example.com --full --output html --file full_security_report
```

### Selective Scanning
```bash
# Information gathering only
php grim.php scan https://example.com --info-only

# Vulnerability scanning only
php grim.php scan https://example.com --vuln-only

# Crawling only
php grim.php scan https://example.com --crawl-only
```

---

## 🔧 Advanced Workflows

### Professional Penetration Testing Workflow
```bash
# Phase 1: Reconnaissance
php grim.php info target.com --verbose --output json --file phase1_recon

# Phase 2: Vulnerability Assessment
php grim.php vuln https://target.com --level high --timeout 300 --output json --file phase2_vuln

# Phase 3: Deep Crawling
php grim.php crawl https://target.com --threads 100 --max-requests 10000 --delay 100 --output json --file phase3_crawl

# Phase 4: Comprehensive Report
php grim.php scan https://target.com --full --output html --file final_penetration_test_report
```

### Continuous Security Monitoring
```bash
# Daily quick scan
php grim.php info target.com --output json --file daily_$(date +%Y%m%d)

# Weekly vulnerability scan
php grim.php vuln https://target.com --level medium --output json --file weekly_vuln_$(date +%Y%m%d)

# Monthly comprehensive scan
php grim.php scan https://target.com --full --output html --file monthly_full_$(date +%Y%m%d)
```

### API Security Testing
```bash
# Test API endpoints for vulnerabilities
php grim.php vuln https://api.example.com --level high --timeout 180

# Crawl API documentation
php grim.php crawl https://api.example.com --max-requests 500

# Information gathering on API domain
php grim.php info api.example.com --verbose
```

---

## 📊 Output Examples

### Information Gathering Output
```bash
php grim.php info example.com --output json --file sample_output
```

**Sample Output:**
```json
{
  "target": "example.com",
  "scan_start": "2024-01-15 10:30:00",
  "dns": {
    "A": ["93.184.216.34"],
    "AAAA": ["2606:2800:220:1:248:1893:25c8:1946"],
    "MX": ["mail.example.com"],
    "NS": ["ns1.example.com", "ns2.example.com"],
    "TXT": ["v=spf1 include:_spf.example.com ~all"]
  },
  "whois": {
    "registrar": "Example Registrar",
    "created": "1995-08-14",
    "expires": "2024-08-13",
    "status": "active"
  },
  "subdomains": [
    {"name": "www", "ip": "93.184.216.34"},
    {"name": "mail", "ip": "93.184.216.34"},
    {"name": "ftp", "ip": "93.184.216.34"}
  ],
  "scan_end": "2024-01-15 10:32:15",
  "duration": "2.25 minutes"
}
```

### Vulnerability Scan Output
```bash
php grim.php vuln https://example.com --output html --file vuln_sample
```

**Sample Output:**
```html
<!DOCTYPE html>
<html>
<head>
    <title>GRIM Vulnerability Scan Results</title>
    <style>
        .critical { color: red; font-weight: bold; }
        .high { color: red; }
        .medium { color: orange; }
        .low { color: blue; }
        .info { color: green; }
    </style>
</head>
<body>
    <h1>Vulnerability Scan Results</h1>
    <h2>Target: https://example.com</h2>
    
    <h3>Summary</h3>
    <ul>
        <li>Critical: 0</li>
        <li>High: 2</li>
        <li>Medium: 3</li>
        <li>Low: 5</li>
        <li>Info: 8</li>
    </ul>
    
    <h3>High Severity Findings</h3>
    <div class="high">
        <h4>SQL Injection in Login Form</h4>
        <p><strong>URL:</strong> https://example.com/login</p>
        <p><strong>Payload:</strong> ' OR '1'='1</p>
        <p><strong>Evidence:</strong> Database error in response</p>
    </div>
</body>
</html>
```

### Crawl Results Output
```bash
php grim.php crawl https://example.com --output csv --file crawl_sample
```

**Sample CSV Output:**
```csv
Category,Path,URL,Status,Response_Length,Content_Type
admin_panels,admin,https://example.com/admin,found,1024,text/html
admin_panels,wp-admin,https://example.com/wp-admin,found,2048,text/html
backup_files,backup.sql,https://example.com/backup.sql,found,512,application/octet-stream
common_files,robots.txt,https://example.com/robots.txt,found,256,text/plain
common_files,sitemap.xml,https://example.com/sitemap.xml,found,1024,application/xml
```

---

## 🚨 Troubleshooting Examples

### Common Issues and Solutions

#### 1. Composer Not Found
```bash
# Install Composer on Windows
# Download from: https://getcomposer.org/download/

# Install Composer on Linux/Mac
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

#### 2. PHP Extensions Missing
```bash
# Check PHP extensions
php -m | grep -E "(curl|dom|json|mbstring|zip)"

# Install missing extensions on Ubuntu/Debian
sudo apt-get install php8.0-curl php8.0-dom php8.0-json php8.0-mbstring php8.0-zip

# Install missing extensions on CentOS/RHEL
sudo yum install php-curl php-dom php-json php-mbstring php-zip
```

#### 3. Permission Issues
```bash
# Fix directory permissions
chmod 755 logs/ results/ backups/
chmod 644 .env

# On Windows, run as Administrator if needed
```

#### 4. Network/SSL Issues
```bash
# Skip SSL verification for testing
php grim.php vuln https://example.com --skip-ssl-verify

# Use custom timeout for slow connections
php grim.php info example.com --timeout 120

# Check firewall settings
# Ensure ports 80, 443, 53 are accessible
```

#### 5. Memory Issues
```bash
# Increase PHP memory limit
php -d memory_limit=1G grim.php info example.com

# Or modify php.ini
# memory_limit = 1G
```

---

## 🎯 Real-World Scenarios

### Scenario 1: E-commerce Security Audit
```bash
# 1. Initial reconnaissance
php grim.php info shop.example.com --verbose --output json --file ecommerce_recon

# 2. Vulnerability assessment
php grim.php vuln https://shop.example.com --level high --timeout 300 --output json --file ecommerce_vuln

# 3. Admin panel discovery
php grim.php crawl https://shop.example.com --max-requests 2000 --output json --file ecommerce_crawl

# 4. Generate report
php grim.php scan https://shop.example.com --full --output html --file ecommerce_security_audit
```

### Scenario 2: API Security Testing
```bash
# 1. API endpoint discovery
php grim.php info api.example.com --verbose --output json --file api_recon

# 2. API vulnerability testing
php grim.php vuln https://api.example.com --level critical --timeout 180 --output json --file api_vuln

# 3. API documentation crawling
php grim.php crawl https://api.example.com --max-requests 1000 --output json --file api_crawl
```

### Scenario 3: WordPress Security Assessment
```bash
# 1. WordPress site reconnaissance
php grim.php info blog.example.com --verbose --output json --file wordpress_recon

# 2. WordPress-specific vulnerabilities
php grim.php vuln https://blog.example.com --level high --timeout 240 --output json --file wordpress_vuln

# 3. WordPress file discovery
php grim.php crawl https://blog.example.com --max-requests 3000 --output json --file wordpress_crawl
```

---

## 📚 Best Practices

### 1. **Scanning Strategy**
- Start with low-level scans and escalate gradually
- Use appropriate timeouts for different target types
- Respect rate limits to avoid overwhelming targets

### 2. **Documentation**
- Always document your scanning methodology
- Keep detailed logs of all findings
- Create comprehensive reports for stakeholders

### 3. **Legal Compliance**
- **ONLY** scan systems you own or have explicit permission to test
- Obtain written authorization before testing
- Respect terms of service and rate limits

### 4. **Security**
- Never commit `.env` files with real API keys
- Use isolated testing environments
- Keep the tool updated for latest security patches

---

## 🎉 Getting Started Checklist

- [ ] Install PHP 8.0+ with required extensions
- [ ] Install Composer
- [ ] Clone GRIM repository
- [ ] Run `composer install`
- [ ] Copy `env.example` to `.env`
- [ ] Test with `php grim.php --version`
- [ ] Run first scan on localhost
- [ ] Try information gathering on a test domain
- [ ] Run vulnerability scan on your own test site
- [ ] Experiment with different output formats
- [ ] Read the main README.md for advanced features

---

## 🆘 Need Help?

- **Documentation**: Check the main `README.md`
- **Installation Guide**: See `INSTALL.md`
- **Issues**: Create GitHub issues with detailed information
- **Examples**: This file contains comprehensive examples
- **Tests**: Run `composer test` to verify installation

---

**Remember: With great power comes great responsibility! Use GRIM ethically and legally.** 🛡️

*Happy Security Scanning! 🚀*
