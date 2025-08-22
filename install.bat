@echo off
REM GRIM Security Scanner v3.0.0 Installation Script for Windows
REM This script will help you install and configure GRIM on Windows

setlocal enabledelayedexpansion

REM Colors for output (Windows 10+ supports ANSI colors)
set "RED=[91m"
set "GREEN=[92m"
set "YELLOW=[93m"
set "BLUE=[94m"
set "NC=[0m"

REM Function to print colored output
:print_status
echo %BLUE%[INFO]%NC% %~1
goto :eof

:print_success
echo %GREEN%[SUCCESS]%NC% %~1
goto :eof

:print_warning
echo %YELLOW%[WARNING]%NC% %~1
goto :eof

:print_error
echo %RED%[ERROR]%NC% %~1
goto :eof

REM Function to check if command exists
:command_exists
where %1 >nul 2>&1
if %errorlevel% equ 0 (
    set "command_exists_result=1"
) else (
    set "command_exists_result=0"
)
goto :eof

REM Function to check PHP version
:check_php_version
call :command_exists php
if !command_exists_result! equ 0 (
    call :print_error "PHP is not installed"
    exit /b 1
)

for /f "tokens=1-3 delims=." %%a in ('php -r "echo PHP_VERSION;"') do (
    set "PHP_MAJOR=%%a"
    set "PHP_MINOR=%%b"
)

if !PHP_MAJOR! geq 8 (
    if !PHP_MINOR! geq 0 (
        call :print_success "PHP version is compatible"
        exit /b 0
    )
)

call :print_error "PHP version requirement not met. Required: PHP 8.0+"
exit /b 1

REM Function to check PHP extensions
:check_php_extensions
set "missing_extensions="
set "extensions=curl dom json mbstring"

for %%e in (%extensions%) do (
    php -m | findstr /i "^%%e$" >nul
    if !errorlevel! neq 0 (
        if defined missing_extensions (
            set "missing_extensions=!missing_extensions!, %%e"
        ) else (
            set "missing_extensions=%%e"
        )
    ) else (
        call :print_success "PHP extension '%%e' is installed"
    )
)

if defined missing_extensions (
    call :print_error "Missing PHP extensions: !missing_extensions!"
    exit /b 1
)

call :print_success "All required PHP extensions are installed"
exit /b 0

REM Function to install Composer
:install_composer
call :command_exists composer
if !command_exists_result! equ 1 (
    call :print_success "Composer is already installed"
    exit /b 0
)

call :print_status "Installing Composer..."

REM Download Composer installer
curl -sS https://getcomposer.org/installer -o composer-setup.php

REM Run installer
php composer-setup.php --install-dir=%USERPROFILE%\bin --filename=composer.bat

REM Clean up
del composer-setup.php

REM Add to PATH if not already there
echo %PATH% | findstr /i "%USERPROFILE%\bin" >nul
if !errorlevel! neq 0 (
    setx PATH "%PATH%;%USERPROFILE%\bin"
    call :print_warning "Added Composer to PATH. Please restart your terminal."
)

call :print_success "Composer installed successfully"
exit /b 0

REM Function to setup environment
:setup_environment
call :print_status "Setting up environment configuration..."

if not exist .env (
    if exist env.example (
        copy env.example .env >nul
        call :print_success "Environment file created from template"
        call :print_warning "Please edit .env file with your API keys and configuration"
    ) else (
        call :print_warning "No env.example found. Please create .env file manually"
    )
) else (
    call :print_success "Environment file already exists"
)
exit /b 0

REM Function to create directories
:create_directories
call :print_status "Creating necessary directories..."

if not exist logs mkdir logs
if not exist results mkdir results
if not exist config mkdir config
if not exist tests mkdir tests

call :print_success "Directories created successfully"
exit /b 0

REM Function to install dependencies
:install_dependencies
call :print_status "Installing PHP dependencies..."

if exist composer.json (
    composer install --no-dev --optimize-autoloader
    call :print_success "Dependencies installed successfully"
) else (
    call :print_error "composer.json not found"
    exit /b 1
)
exit /b 0

REM Function to run tests
:run_tests
call :print_status "Running tests..."

if exist composer.json (
    composer show | findstr /i "phpunit" >nul
    if !errorlevel! equ 0 (
        composer test
        call :print_success "Tests completed successfully"
    ) else (
        call :print_warning "PHPUnit not available. Skipping tests."
    )
) else (
    call :print_warning "composer.json not found. Skipping tests."
)
exit /b 0

REM Function to display completion message
:display_completion
echo.
echo %GREEN%========================================%NC%
echo %GREEN%  GRIM Security Scanner v3.0.0%NC%
echo %GREEN%  Installation Completed!%NC%
echo %GREEN%========================================%NC%
echo.
echo %BLUE%Next steps:%NC%
echo 1. Edit .env file with your API keys
echo 2. Run: php grim-new.php --help
echo 3. Start scanning: php grim-new.php scan --target example.com
echo.
echo %BLUE%Documentation:%NC%
echo README-NEW.md - Complete documentation
echo https://github.com/swatv3nub/grim
echo.
exit /b 0

REM Main installation function
:main
echo %BLUE%========================================%NC%
echo %BLUE%  GRIM Security Scanner v3.0.0%NC%
echo %BLUE%  Installation Script for Windows%NC%
echo %BLUE%========================================%NC%
echo.

REM Check system requirements
call :print_status "Checking system requirements..."

REM Check PHP version
call :check_php_version
if !errorlevel! neq 0 (
    call :print_error "PHP version requirement not met. Please upgrade to PHP 8.0+"
    pause
    exit /b 1
)

REM Check PHP extensions
call :check_php_extensions
if !errorlevel! neq 0 (
    call :print_error "Required PHP extensions are missing. Please install them manually."
    call :print_status "Required extensions: curl, dom, json, mbstring"
    pause
    exit /b 1
)

REM Install Composer
call :install_composer

REM Create directories
call :create_directories

REM Setup environment
call :setup_environment

REM Install dependencies
call :install_dependencies

REM Run tests (optional)
call :run_tests

REM Display completion message
call :display_completion

pause
exit /b 0

REM Run main function
call :main
