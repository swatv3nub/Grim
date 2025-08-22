# GRIM Security Scanner Tests

This directory contains the test suite for GRIM Security Scanner.

## Running Tests

### Prerequisites
- PHP 8.0 or higher
- Composer dependencies installed

### Commands

```bash
# Run all tests
composer test

# Run tests with coverage
composer test -- --coverage-html coverage/

# Run specific test file
./vendor/bin/phpunit tests/Scanner/InformationGatheringScannerTest.php

# Run tests with verbose output
./vendor/bin/phpunit --verbose
```

### Test Structure

- `bootstrap.php` - Test bootstrap file
- `phpunit.xml` - PHPUnit configuration
- `Scanner/` - Scanner class tests
- `Command/` - Command class tests
- `Utils/` - Utility class tests
- `Config/` - Configuration tests

## Writing Tests

Follow these guidelines when writing tests:

1. Test class names should end with `Test`
2. Test method names should be descriptive
3. Use proper assertions and mocks
4. Test both success and failure scenarios
5. Keep tests focused and independent

## Example Test

```php
<?php

namespace Grim\Tests\Scanner;

use PHPUnit\Framework\TestCase;
use Grim\Scanner\InformationGatheringScanner;

class InformationGatheringScannerTest extends TestCase
{
    public function testScannerInitialization()
    {
        $scanner = new InformationGatheringScanner('example.com');
        $this->assertInstanceOf(InformationGatheringScanner::class, $scanner);
    }
}
```
