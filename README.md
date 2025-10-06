# GRIM Security Scanner v5.0.0

🔒 **Advanced Information Gathering and Vulnerability Scanning Tool**

[![PHP Version](https://img.shields.io/badge/PHP-8.0+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-GPL--3.0-green.svg)](https://opensource.org/licenses/GPL-3.0)
[![Build Status](https://img.shields.io/badge/Build-Passing-brightgreen.svg)](https://github.com/swatv3nub/Grim)

## 🚀 What's New in v5.0.0

- **Modern PHP Architecture**: Complete rewrite using PHP 8.0+ features
- **Object-Oriented Design**: Clean, maintainable code with proper separation of concerns
- **Composer Integration**: Modern dependency management
- **CLI Interface**: Professional command-line interface using Symfony Console
- **Advanced Logging**: Comprehensive logging with Monolog
- **Multiple Export Formats**: JSON, CSV, HTML, XML, and Markdown
- **Rate Limiting**: Built-in request rate limiting to avoid detection
- **Configuration Management**: Environment-based configuration system
- **Error Handling**: Robust error handling and recovery
- **Testing Support**: PHPUnit integration for testing

## ✨ Features

### 🔍 Information Gathering
- **Domain Intelligence**: WHOIS, DNS, GeoIP, and subdomain discovery
- **Technology Detection**: Web servers, CMS, frameworks, and programming languages
- **Social Media Analysis**: Social media presence and link discovery
- **Email Intelligence**: MX records and email address enumeration
- **Cloud Infrastructure**: AWS, Azure, GCP, and CDN detection
- **Port Scanning**: Open port detection and service identification

### 🚨 Vulnerability Scanning
- **SQL Injection**: Comprehensive SQL injection detection with multiple payloads
- **Cross-Site Scripting (XSS)**: Reflected and stored XSS detection
- **File Inclusion**: Local and Remote File Inclusion (LFI/RFI) detection
- **Server-Side Request Forgery (SSRF)**: Internal network access detection
- **Command Injection**: OS command execution vulnerability detection
- **Cross-Site Request Forgery (CSRF)**: Missing CSRF token detection
- **Insecure Direct Object References (IDOR)**: Access control bypass detection
- **Open Redirects**: Unsafe redirect vulnerability detection
- **XML External Entity (XXE)**: XML parsing vulnerability detection
- **Email Header Injection**: CRLF injection and header manipulation detection

### 🕷️ Web Crawling
- **Admin Panel Discovery**: Common admin panel path enumeration
- **Backup File Detection**: Backup and configuration file discovery
- **Directory Traversal**: File system access path discovery
- **Custom Wordlists**: Extensible wordlist system for custom scans

### 📊 Reporting & Export
- **Multiple Formats**: JSON, CSV, HTML, XML, and Markdown export
- **Beautiful Reports**: Professional HTML reports with modern styling
- **Structured Data**: Machine-readable output for automation
- **Custom Filenames**: Configurable output file naming

## 🛠️ Installation

### Prerequisites
- PHP 8.0 or higher
- Composer
- cURL extension
- DOM extension
- JSON extension
- MBString extension

### Quick Install
```bash
# Clone the repository
git clone https://github.com/swatv3nub/grim.git
cd grim

# Install dependencies
composer install

# Copy environment configuration
cp env.example .env

# Edit configuration
nano .env
```

### Manual Installation
```bash
# Install PHP extensions (Ubuntu/Debian)
sudo apt-get update
sudo apt-get install php8.0-curl php8.0-dom php8.0-json php8.0-mbstring

# Install PHP extensions (CentOS/RHEL)
sudo yum install php-curl php-dom php-json php-mbstring

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

## ⚙️ Configuration

### Environment Variables
Create a `.env` file in the project root:

```env
# API Keys
VIEWDNS_API_KEY=your_viewdns_api_key_here
MOZ_ACCESS_ID=your_moz_access_id_here
MOZ_SECRET_KEY=your_moz_secret_key_here

# Scanner Configuration
SCAN_TIMEOUT=30
MAX_CONCURRENT_SCANS=5
USER_AGENT=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36

# Security Settings
ENABLE_RATE_LIMITING=true
MAX_REQUESTS_PER_MINUTE=60

# Output Configuration
SAVE_RESULTS=true
RESULTS_DIR=results/
EXPORT_FORMATS=json,csv,html

# Logging
LOG_LEVEL=INFO
LOG_FILE=logs/grim.log
```

### API Keys
- **ViewDNS**: Get your API key from [ViewDNS](https://viewdns.info/api/)
- **Moz**: Get your API credentials from [Moz](https://moz.com/products/api)

## 🚀 Usage

> 📚 **Need examples?** Check out our comprehensive [Usage Examples](examples/usage-examples.md) for detailed command examples and real-world scenarios.

### Basic Scan
```bash
# Run a full scan
php grim-new.php scan --target example.com

# Run with custom options
php grim.php scan --target example.com
  --target example.com \
  --export html \
php grim.php scan \
  --target example.com \
  --export html \
  --output my_scan \
  --verbose
```bash
# Disable specific scanners
php grim-new.php scan --target example.com --no-vuln --no-crawl

# Custom timeout and delay
php grim.php scan --target example.com --no-vuln --no-crawl

# Export to multiple formats
php grim.php scan --target example.com --timeout 60 --delay 2
```

php grim.php scan --target example.com --export all
```bash
# Show help
php grim-new.php --help

# Show scan command help
php grim.php --help

# List available commands
php grim.php scan --help
```

php grim.php list


### Cloud Asset Scanning
```bash
# Scan all AWS assets
php grim.php cloud-scan aws
# Scan a specific Azure asset
php grim.php cloud-scan azure vm-az-demo-1
# Scan all GCP assets
php grim.php cloud-scan gcp
```

### Container Image Scanning
```bash
# Scan a Docker image for vulnerabilities
php grim.php container-scan nginx:latest
```

### Plugin System
```bash
# List loaded plugins (see logs or plugin output)
# Plugins are loaded automatically from the plugins/ directory
```

### Internationalization (i18n)
```php
```
grim/
├── src/                          # Source code
```

### SIEM/Log Export
```bash
# Export a scan result to Splunk
php grim.php export:siem 1 splunk
# Export to ELK
php grim.php export:siem 1 elk
# Export to Graylog
php grim.php export:siem 1 graylog
```

### Result History & Reporting
```bash
# List all past scan results
php grim.php results
# View details for a specific result
php grim.php results 1
```
│   ├── Command/                  # CLI commands
│   ├── Config/                   # Configuration management
│   ├── Scanner/                  # Scanner implementations
│   └── Utils/                    # Utility classes
├── config/                       # Configuration files
├── crawl/                        # Crawling wordlists
├── logs/                         # Log files
├── results/                      # Scan results
├── tests/                        # Test files
├── vendor/                       # Composer dependencies
├── .env                          # Environment configuration
├── composer.json                 # Composer configuration
├── grim-new.php                 # Main entry point
└── README.md                     # This file
```

## 🔧 Development

### Running Tests
```bash
# Run all tests
composer test

# Run specific test
vendor/bin/phpunit tests/Scanner/VulnerabilityScannerTest.php

# Generate coverage report
vendor/bin/phpunit --coverage-html coverage/
```

### Code Quality
```bash
# Static analysis
composer analyze

# Code style check
composer cs

# Fix code style
composer cs-fix
```

### Adding New Scanners
1. Create a new scanner class extending `Scanner`
2. Implement required methods: `initialize()`, `scan()`, `getName()`
3. Add the scanner to the main application
4. Write tests for the new scanner

Example:
```php
<?php

namespace Grim\Scanner;

class CustomScanner extends Scanner
{
    protected function initialize(): void
    {
        // Initialize your scanner
    }

    public function scan(): array
    {
        // Implement your scanning logic
        return $this->results;
    }

    public function getName(): string
    {
        return 'Custom Scanner';
    }
}
```

## 📊 Output Examples

### JSON Export
```json
{
  "target": "example.com",
  "scan_start": "2024-01-15 10:00:00",
  "scan_end": "2024-01-15 10:05:30",
  "duration": "5 minutes 30 seconds",
  "scanners": {
    "information_gathering": {
      "basic": {
        "domain": "example.com",
        "ip_address": "93.184.216.34"
      }
    },
    "vulnerability_scan": [
      {
        "type": "sql_injection",
        "description": "Potential SQL Injection vulnerability detected",
        "severity": "vulnerability",
        "details": {
          "payload": "' OR '1'='1",
          "url": "http://example.com/?id=' OR '1'='1"
        }
      }
    ]
  }
}
```

### HTML Report
The HTML export generates beautiful, professional reports with:
- Modern, responsive design
- Color-coded severity indicators
- Interactive elements
- Professional styling
- Exportable sections

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

### Development Setup
```bash
# Fork and clone the repository
git clone https://github.com/your-username/grim.git
cd grim

# Install development dependencies
composer install --dev

# Create feature branch
git checkout -b feature/amazing-feature

# Make your changes and test
composer test

# Commit and push
git commit -m "Add amazing feature"
git push origin feature/amazing-feature

# Create Pull Request
```

## 📝 License

This project is licensed under the GNU General Public License v3.0 - see the [LICENSE](LICENSE) file for details.

## ⚠️ Disclaimer

This tool is designed for **educational purposes** and **authorized security testing** only. Users are responsible for ensuring they have proper authorization before scanning any systems. The developers are not responsible for any misuse of this tool.

## 🙏 Acknowledgments

- Original GRIM developers for the foundation
- PHP community for excellent libraries
- Security researchers for vulnerability knowledge
- Open source contributors

## 📞 Support

- **Issues**: [GitHub Issues](https://github.com/swatv3nub/grim/issues)
- **Discussions**: [GitHub Discussions](https://github.com/swatv3nub/grim/discussions)
- **Wiki**: [GitHub Wiki](https://github.com/swatv3nub/grim/wiki)

## 🔄 Changelog

### v5.0.0 (2035-08-22)
- Complete codebase rewrite
- Modern PHP 8.0+ architecture
- Composer integration
- CLI interface
- Advanced logging
- Multiple export formats
- Rate limiting
- Configuration management
- Error handling improvements
- Testing framework

### v5.0.0 (Previous)
- Basic vulnerability scanning
- Information gathering
- Web crawling
- Simple CLI interface

---

**Made with ❤️ by the Swanit Anuran [MaskedVirus]**

*Empowering security professionals with advanced scanning capabilities*
