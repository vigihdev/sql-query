# 🚀 VigihDev SQL Query Builder

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Tests](https://img.shields.io/badge/Tests-41%20Passed-brightgreen.svg)](tests/)
[![Coverage](https://img.shields.io/badge/Assertions-66-blue.svg)](tests/)

> A powerful, fluent SQL Query Builder for PHP with intuitive syntax and comprehensive condition handling.

## ✨ Features

- 🔥 **Fluent Interface** - Chain methods for readable query building
- 🎯 **Type Safety** - Full PHP 8+ type declarations
- 🧩 **Flexible Conditions** - Support for complex WHERE clauses
- 🔧 **Factory Pattern** - Clean condition object creation
- 📝 **Well Tested** - 41 test cases with 66 assertions
- ⚡ **Performance** - Optimized for speed and memory usage

## 🚀 Quick Start

### Installation

```bash
composer require vigihdev/sql-query
```

### Basic Usage

```php
use SqlQuery\Query;

$query = new Query();

// Simple SELECT query
$sql = $query
    ->from('users')
    ->where(['active', '=', 1])
    ->where(['age', '>', 18])
    ->orderBy(['name' => 'ASC'])
    ->limit(10)
    ->toSql();

echo $sql;
// Output: SELECT * FROM users WHERE active = ? AND age > ? ORDER BY name ASC LIMIT 10
```

## 📖 Documentation

### Basic Queries

```php
// SELECT with specific columns
$query->select('id', 'name', 'email')
      ->from('users');

// DISTINCT queries
$query->distinct()
      ->from('categories');

// LIMIT and OFFSET
$query->from('posts')
      ->limit(20)
      ->offset(40);
```

### WHERE Conditions

```php
// Simple conditions
$query->where(['name', '=', 'John']);
$query->where(['age', '>', 25]);

// Multiple conditions (AND)
$query->where(['status', '=', 'active'])
      ->where(['verified', '=', true]);

// OR conditions
$query->where(['role', '=', 'admin'])
      ->orWhere(['role', '=', 'moderator']);

// IN conditions
$query->where(['status', 'IN', ['active', 'pending']]);

// LIKE conditions
$query->where(['name', 'LIKE', '%john%']);

// NULL conditions
$query->where(['deleted_at', 'IS NULL']);
```

### JOINs

```php
// INNER JOIN
$query->from('users u')
      ->innerJoin('posts p', 'u.id', '=', 'p.user_id');

// LEFT JOIN
$query->from('users u')
      ->leftJoin('profiles pr', 'u.id', '=', 'pr.user_id');

// Multiple JOINs
$query->from('users u')
      ->innerJoin('posts p', 'u.id', '=', 'p.user_id')
      ->leftJoin('categories c', 'p.category_id', '=', 'c.id');
```

### Sorting and Grouping

```php
// ORDER BY
$query->orderBy(['created_at' => 'DESC', 'name' => 'ASC']);

// GROUP BY
$query->from('orders')
      ->groupBy('customer_id');
```

### Complex Conditions

```php
// Hash conditions (key-value pairs)
$query->where([
    'status' => 'active',
    'age' => 25,
    'city' => 'Jakarta'
]);

// Composite conditions with AND/OR
$query->where(['AND', 
    ['name', '=', 'John'],
    ['age', '>', 18]
]);
```

## 🏗️ Architecture

### Core Components

- **Query** - Main query builder class
- **Conditions** - Various condition types (Simple, Composite, In, Like, etc.)
- **Factory** - Creates condition objects based on operators
- **Parser** - Parses different condition formats
- **Utils** - Helper utilities for type handling

### Condition Types

| Class | Purpose | Example |
|-------|---------|---------|
| `SimpleCondition` | Basic comparisons | `name = 'John'` |
| `CompositeCondition` | AND/OR logic | `(age > 18 AND status = 'active')` |
| `InCondition` | IN operations | `id IN (1,2,3)` |
| `LikeCondition` | Pattern matching | `name LIKE '%john%'` |
| `IsCondition` | NULL checks | `deleted_at IS NULL` |

## 🧪 Testing

Run the comprehensive test suite:

```bash
# Run all tests
composer test

# Run with coverage
./vendor/bin/phpunit --coverage-html coverage/
```

**Test Statistics:**
- ✅ 41 Test Cases
- ✅ 66 Assertions  
- ✅ 100% Pass Rate
- 🎯 Full Coverage of Core Features

## 📁 Project Structure

```
src/
├── Query.php              # Main query builder
├── AbstractQuery.php      # Base query class
├── Conditions/           # Condition classes
│   ├── SimpleCondition.php
│   ├── CompositeCondition.php
│   └── ...
├── Factories/           # Factory classes
│   └── ConditionFactory.php
├── Parsers/            # Parser classes
│   └── ConditionParser.php
└── Utils/              # Utility classes
    └── ParseValueType.php

tests/
├── QueryTest.php
├── Conditions/         # Condition tests
├── Factories/         # Factory tests
├── Parsers/          # Parser tests
├── Utils/            # Utility tests
└── Integration/      # Integration tests
```

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Write tests for your changes
4. Ensure all tests pass (`composer test`)
5. Commit your changes (`git commit -m 'Add amazing feature'`)
6. Push to the branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**Vigih Dev**
- Email: vigihdev@gmail.com
- GitHub: [@vigihdev](https://github.com/vigihdev)

## 🙏 Acknowledgments

- Built with ❤️ for the PHP community
- Inspired by modern query builders
- Thanks to all contributors and testers

---

<div align="center">

**⭐ Star this repo if you find it helpful!**

[Report Bug](https://github.com/vigihdev/sql-query/issues) • [Request Feature](https://github.com/vigihdev/sql-query/issues) • [Documentation](https://github.com/vigihdev/sql-query/wiki)

</div>
