PhpDependencyAnalysisSuite
==========================

[![Build Status](https://travis-ci.org/cawolf/PhpDependencyAnalysisSuite.svg?branch=master)](https://travis-ci.org/cawolf/PhpDependencyAnalysisSuite)
[![Code Climate](https://codeclimate.com/github/cawolf/PhpDependencyAnalysisSuite/badges/gpa.svg)](https://codeclimate.com/github/cawolf/PhpDependencyAnalysisSuite)
[![Test Coverage](https://codeclimate.com/github/cawolf/PhpDependencyAnalysisSuite/badges/coverage.svg)](https://codeclimate.com/github/cawolf/PhpDependencyAnalysisSuite/coverage)
[![Dependency Status](https://www.versioneye.com/user/projects/5765a90b0735400045bbfce4/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5765a90b0735400045bbfce4)

This suite will help you integrate the great project [PhpDependencyAnalysis](https://github.com/mamuz/PhpDependencyAnalysis) into your application.

## Commands

1. Command to generate configuration files consumed by the [Analyze Command](https://github.com/mamuz/PhpDependencyAnalysis#usage)

### Command to generate configuration files

    bin/phpdasuite generate-config <source> <target> <configuration>

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

## Todo

* Command to parse the results of the [Analyze Command](https://github.com/mamuz/PhpDependencyAnalysis#usage) and take certain actions, such as
    * alert class cycles
    * alert warnings
    * optionally letting your build process fail if anything is alerted
