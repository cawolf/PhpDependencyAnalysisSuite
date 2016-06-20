PhpDependencyAnalysisSuite
==========================

[![Build Status](https://travis-ci.org/cawolf/PhpDependencyAnalysisSuite.svg?branch=master)](https://travis-ci.org/cawolf/PhpDependencyAnalysisSuite)
[![Code Climate](https://codeclimate.com/github/cawolf/PhpDependencyAnalysisSuite/badges/gpa.svg)](https://codeclimate.com/github/cawolf/PhpDependencyAnalysisSuite)
[![Test Coverage](https://codeclimate.com/github/cawolf/PhpDependencyAnalysisSuite/badges/coverage.svg)](https://codeclimate.com/github/cawolf/PhpDependencyAnalysisSuite/coverage)
[![Dependency Status](https://www.versioneye.com/user/projects/5765a90b0735400045bbfce4/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5765a90b0735400045bbfce4)

This suite will help you integrate the great project [PhpDependencyAnalysis](https://github.com/mamuz/PhpDependencyAnalysis) into your application.

## Commands

1. Command to generate configuration files consumed by the [Analyze Command](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/README.md#usage)

### Command to generate configuration files

    bin/phpdasuite generate-config

TODO: explain arguments and options

## Todo

* Command to parse the results of the [Analyze Command](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/README.md#usage) and take certain actions, such as
    * alert class cycles
    * alert warnings
    * optionally letting your build process fail if anything is alerted
