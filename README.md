PhpDependencyAnalysisSuite
==========================

This suite will help you integrate the great project [PhpDependencyAnalysis](https://github.com/mamuz/PhpDependencyAnalysis) into your application.

[![Build Status](https://travis-ci.org/cawolf/PhpDependencyAnalysisSuite.svg?branch=master)](https://travis-ci.org/cawolf/PhpDependencyAnalysisSuite)
[![Code Climate](https://codeclimate.com/github/cawolf/PhpDependencyAnalysisSuite/badges/gpa.svg)](https://codeclimate.com/github/cawolf/PhpDependencyAnalysisSuite)
[![Test Coverage](https://codeclimate.com/github/cawolf/PhpDependencyAnalysisSuite/badges/coverage.svg)](https://codeclimate.com/github/cawolf/PhpDependencyAnalysisSuite/coverage)
[![Packagist](https://img.shields.io/packagist/v/cawolf/php-dependency-analysis-suite.svg?maxAge=2592000)](https://packagist.org/packages/cawolf/php-dependency-analysis-suite)
[![Packagist](https://img.shields.io/packagist/l/cawolf/php-dependency-analysis-suite.svg?maxAge=2592000)](https://packagist.org/packages/cawolf/php-dependency-analysis-suite)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/e84ad272-306e-4cb8-926d-e5e1e1729e89/big.png)](https://insight.sensiolabs.com/projects/e84ad272-306e-4cb8-926d-e5e1e1729e89)

It includes a command to generate configuration files consumed by the [Analyze Command](https://github.com/mamuz/PhpDependencyAnalysis#usage) and a command to analyze the results and take appropriate actions.

Your typical integration would work like this:

1. Add dependencies to your project

    ```
    composer require --dev "mamuz/php-dependency-analysis" "cawolf/php-dependency-analysis-suite"
    ```

2. Generate base configurations (if you don't already have them) - see [generate-config](#generate-config)

    ```
    vendor/bin/phpdasuite generate-config src/ analysis.json phpda-json.yml
    vendor/bin/phpdasuite generate-config src/ analysis.svg phpda-svg.yml
    ```

3. Run PhpDependencyAnalysis in different namespace depths at once - see [analyze-multiple](#analyze-multiple)

    ```
    vendor/bin/phpdasuite analyze-multiple phpda-json.yml
    vendor/bin/phpdasuite analyze-multiple phpda-svg.yml 1 3
    ```

4. Process analysis result automatically (only for JSON results) - see [process-result](#process-result)

    ```
    vendor/bin/phpdasuite process-result analysis.json
    ```

5. Check the SVG graph (analysis.svg)


generate-config
---------------

This command generates configuration files to be processed by the PhpDependencyAnalysis `analyze` command.

    vendor/bin/phpdasuite generate-config <source> <target> <configuration>

#### Arguments

| *name* | *type* | *description* |
|---|---|---|
| source | `path` | the base path of the source PHP files to analyze |
| target | `file` | the full path to the result output file generated by the analysis |
| configuration | `file` | the full path to the configuration file generated by this command |

#### Available options

| *name* | *type* | *default* | *description* |
|---|---|---|---|
| format | `json`, `svg`, `html` or `script` | `json` | format of the generated result output file, see [available formatter](https://github.com/mamuz/PhpDependencyAnalysis/wiki/3.-Configuration#available-formatter) |
| mode | `usage`, `call` or `inheritance` | `usage` | mode of the analysis, see [available options - mode](https://github.com/mamuz/PhpDependencyAnalysis/wiki/3.-Configuration#available-options) |
| ignore | `array of path` | `[tests]` | array of paths to ignore during analysis, see [available options - ignore](https://github.com/mamuz/PhpDependencyAnalysis/wiki/3.-Configuration#available-options) |
| namespace-depth | `integer` | `1` | depth of namespace of source files to group the results, see [available options - groupLength](https://github.com/mamuz/PhpDependencyAnalysis/wiki/3.-Configuration#available-options) |

#### Examples

* JSON generation
    This is the default format and is the only format to be consumed by the evaluation command. A call could be:

    ```
    vendor/bin/phpdasuite generate-config src/ build/phpda.json phpda-json.yml
    ```

* SVG generation, some paths ignored, increased namespace depth
    This will generate a SVG file to use for development and optimization or to append to your documentation:

    ```
    vendor/bin/phpdasuite generate-config --format=svg --namespace-length=2 --ignore=tests/ --ignore=lib/ src/ build/phpda.svg phpda-svg.yml
    ```

For more configuration examples, see [examples in the PhpDependencyAnalysis wiki](https://github.com/mamuz/PhpDependencyAnalysis/wiki/4.-Examples).

analyze-multiple
----------------

TODO

process-result
--------------

This command parses the result of the PhpDependencyAnalysis `analyze` command and can print custom messages and return custom exit codes. If both cycles and warnings were found, the exit codes for both outcomes will be combined bitwise, so choose your custom exit codes appropriately.

    vendor/bin/phpdasuite process-result <result>

#### Arguments

| *name* | *type* | *description* |
|---|---|---|
| result | `file` | the full path to the result output file generated by the analysis |

#### Available options

| *name* | *type* | *default* | *description* |
|---|---|---|---|
| configuration-file | `file` | _null_ | path to configuration file. Values are resolved in the order "defaults" -> "configuration file" -> "command line options" |
| exit-code-on-cycle | `integer` | `1` | exit code of command if cycles were found |
| exit-code-on-warning | `integer` | `2` | exit code of command if warnings were found |
| message-on-cycle | `string` | `One or more cycles were detected!` | message to be printed if cycles were found |
| message-on-warning | `string` | `One or more warnings were detected!` | message to be printed if warnings were found |
| show-cycles | `switch` | `false` | if active, cycle information will be printed line-by-line |
| show-warnings | `switch` | `false` | if active, warning information will be printed line-by-line |
| success-message | `string` | `No cycles or warnings were detected!` | message to be printed if everything is fine |

#### Examples

* Command line options:

    ```
    vendor/bin/phpdasuite process-result --message-on-warning="Warnings found" --exit-code-on-cycle=123 analysis.json
    ```

* Configuration file:

    ```
    vendor/bin/phpdasuite process-result --configuration-file="configuration.yml" analysis.json
    ```

A configuration file is in YAML format and holds the exact keys as the command accepts. A default configuration file would look like:

```
exit-code-on-cycle: 1
exit-code-on-warning: 2
message-on-cycle: One or more cycles were detected!
message-on-warning: One or more warnings were detected!
show-cycles: false
show-warnings: false
success-message: No cycles or warnings were detected!
```
