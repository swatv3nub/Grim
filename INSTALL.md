# GRIM Security Scanner Installation Guide

## Prerequisites

- PHP 8.0 or higher
- Composer (for dependency management)
- Required PHP extensions:
  - cURL
  - DOM
  - JSON
  - MBString
  - Zip (for update functionality)

## Installation Steps

### 1. Clone the Repository

```bash
git clone https://github.com/swatv3nub/grim.git
cd grim
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Configure Environment

```bash
# Copy environment file
cp env.example .env

# Edit .env file with your settings
# Set API keys and other configuration options
```

### 4. Verify Installation

```bash
# Check if GRIM is working
php grim.php --version

# List available commands
php grim.php list
```

## Quick Start

### Information Gathering

```bash
# Basic information gathering
php grim.php info example.com

# With custom output format
php grim.php info example.com --output html --file results
```

### Vulnerability Scanning

```bash
# Basic vulnerability scan
php grim.php vuln https://example.com

# High-level scan with custom timeout
php grim.php vuln https://example.com --level high --timeout 60
```

### Web Crawling

```bash
# Crawl for common files and directories
php grim.php crawl https://example.com

# With custom settings
php grim.php crawl https://example.com --threads 20 --max-requests 500
```

### Configuration Management

```bash
# View current configuration
php grim.php config

# Set configuration value
php grim.php config --set scanner.timeout=60

# Export configuration
php grim.php config --export json
```

### Full Scan

```bash
# Run comprehensive scan
php grim.php scan https://example.com --full
```

## Troubleshooting

### Common Issues

1. **Composer not found**: Install Composer from https://getcomposer.org/
2. **PHP extensions missing**: Install required PHP extensions for your system
3. **Permission denied**: Ensure proper file permissions on logs and results directories

### Getting Help

- Check the logs in the `logs/` directory
- Run commands with `--verbose` flag for detailed output
- Review the main README.md for more information

## Development Setup

### Running Tests

```bash
# Install dev dependencies
composer install --dev

# Run test suite
composer test

# Run with coverage
composer test -- --coverage-html coverage/
```

### Code Quality

```bash
# Run PHPStan analysis
composer analyze

# Run code style checks
composer cs

# Fix code style issues
composer cs-fix
```

## Security Notes

- Never commit your `.env` file with real API keys
- Use the tool responsibly and only on systems you own or have permission to test
- Respect rate limits and terms of service for external APIs
- Keep the tool updated for the latest security patches

## Support

For issues and questions:
- Check existing issues on GitHub
- Review the documentation
- Create a new issue with detailed information
