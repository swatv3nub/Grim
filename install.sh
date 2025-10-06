#!/bin/bash

# GRIM Security Scanner v5.0.0 Installation Script
# This script will help you install and configure GRIM

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to check if command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Function to check PHP version
check_php_version() {
    if command_exists php; then
        PHP_VERSION=$(php -r "echo PHP_VERSION;")
        PHP_MAJOR=$(echo $PHP_VERSION | cut -d. -f1)
        PHP_MINOR=$(echo $PHP_VERSION | cut -d. -f2)
        
        if [ "$PHP_MAJOR" -ge 8 ] && [ "$PHP_MINOR" -ge 0 ]; then
            print_success "PHP version $PHP_VERSION is compatible"
            return 0
        else
            print_error "PHP version $PHP_VERSION is not compatible. Required: PHP 8.0+"
            return 1
        fi
    else
        print_error "PHP is not installed"
        return 1
    fi
}

# Function to check PHP extensions
check_php_extensions() {
    local extensions=("curl" "dom" "json" "mbstring")
    local missing_extensions=()
    
    for ext in "${extensions[@]}"; do
        if php -m | grep -q "^$ext$"; then
            print_success "PHP extension '$ext' is installed"
        else
            missing_extensions+=("$ext")
        fi
    done
    
    if [ ${#missing_extensions[@]} -eq 0 ]; then
        return 0
    else
        print_error "Missing PHP extensions: ${missing_extensions[*]}"
        return 1
    fi
}

# Function to install PHP extensions (Ubuntu/Debian)
install_php_extensions_ubuntu() {
    print_status "Installing PHP extensions for Ubuntu/Debian..."
    
    if command_exists apt-get; then
        sudo apt-get update
        sudo apt-get install -y php-curl php-dom php-json php-mbstring
        print_success "PHP extensions installed successfully"
    else
        print_error "apt-get not found. Please install PHP extensions manually."
        return 1
    fi
}

# Function to install PHP extensions (CentOS/RHEL)
install_php_extensions_centos() {
    print_status "Installing PHP extensions for CentOS/RHEL..."
    
    if command_exists yum; then
        sudo yum install -y php-curl php-dom php-json php-mbstring
        print_success "PHP extensions installed successfully"
    else
        print_error "yum not found. Please install PHP extensions manually."
        return 1
    fi
}

# Function to install Composer
install_composer() {
    if command_exists composer; then
        print_success "Composer is already installed"
        return 0
    fi
    
    print_status "Installing Composer..."
    
    # Download Composer installer
    curl -sS https://getcomposer.org/installer | php
    
    # Move to global location
    if [ -w /usr/local/bin ]; then
        sudo mv composer.phar /usr/local/bin/composer
        sudo chmod +x /usr/local/bin/composer
    else
        mv composer.phar ~/bin/composer
        chmod +x ~/bin/composer
        echo 'export PATH="$HOME/bin:$PATH"' >> ~/.bashrc
        source ~/.bashrc
    fi
    
    print_success "Composer installed successfully"
}

# Function to setup environment
setup_environment() {
    print_status "Setting up environment configuration..."
    
    if [ ! -f .env ]; then
        if [ -f env.example ]; then
            cp env.example .env
            print_success "Environment file created from template"
            print_warning "Please edit .env file with your API keys and configuration"
        else
            print_warning "No env.example found. Please create .env file manually"
        fi
    else
        print_success "Environment file already exists"
    fi
}

# Function to create directories
create_directories() {
    print_status "Creating necessary directories..."
    
    mkdir -p logs results config tests
    
    # Set permissions
    chmod 755 logs results config tests
    
    print_success "Directories created successfully"
}

# Function to install dependencies
install_dependencies() {
    print_status "Installing PHP dependencies..."
    
    if [ -f composer.json ]; then
        composer install --no-dev --optimize-autoloader
        print_success "Dependencies installed successfully"
    else
        print_error "composer.json not found"
        return 1
    fi
}

# Function to run tests
run_tests() {
    print_status "Running tests..."
    
    if [ -f composer.json ] && composer show | grep -q phpunit; then
        composer test
        print_success "Tests completed successfully"
    else
        print_warning "PHPUnit not available. Skipping tests."
    fi
}

# Function to display completion message
display_completion() {
    echo ""
    echo -e "${GREEN}========================================${NC}"
    echo -e "${GREEN}  GRIM Security Scanner v5.0.0${NC}"
    echo -e "${GREEN}  Installation Completed!${NC}"
    echo -e "${GREEN}========================================${NC}"
    echo ""
    echo -e "${BLUE}Next steps:${NC}"
    echo "1. Edit .env file with your API keys"
    echo "2. Run: php grim-new.php --help"
    echo "3. Start scanning: php grim-new.php scan --target example.com"
    echo ""
    echo -e "${BLUE}Documentation:${NC}"
    echo "README-NEW.md - Complete documentation"
    echo "https://github.com/swatv3nub/grim"
    echo ""
}

# Main installation function
main() {
    echo -e "${BLUE}========================================${NC}"
    echo -e "${BLUE}  GRIM Security Scanner v5.0.0${NC}"
    echo -e "${BLUE}  Installation Script${NC}"
    echo -e "${BLUE}========================================${NC}"
    echo ""
    
    # Check if running as root
    if [ "$EUID" -eq 0 ]; then
        print_warning "Running as root. This is not recommended for security reasons."
        read -p "Continue anyway? (y/N): " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            exit 1
        fi
    fi
    
    # Check system requirements
    print_status "Checking system requirements..."
    
    # Check PHP version
    if ! check_php_version; then
        print_error "PHP version requirement not met. Please upgrade to PHP 8.0+"
        exit 1
    fi
    
    # Check PHP extensions
    if ! check_php_extensions; then
        print_status "Installing missing PHP extensions..."
        
        # Detect OS and install extensions
        if [ -f /etc/os-release ]; then
            . /etc/os-release
            case $ID in
                ubuntu|debian)
                    install_php_extensions_ubuntu
                    ;;
                centos|rhel|fedora)
                    install_php_extensions_centos
                    ;;
                *)
                    print_error "Unsupported OS: $ID. Please install PHP extensions manually."
                    exit 1
                    ;;
            esac
        else
            print_error "Could not detect OS. Please install PHP extensions manually."
            exit 1
        fi
        
        # Verify extensions are now installed
        if ! check_php_extensions; then
            print_error "Failed to install PHP extensions. Please install manually."
            exit 1
        fi
    fi
    
    # Install Composer
    install_composer
    
    # Create directories
    create_directories
    
    # Setup environment
    setup_environment
    
    # Install dependencies
    install_dependencies
    
    # Run tests (optional)
    run_tests
    
    # Display completion message
    display_completion
}

# Run main function
main "$@"
