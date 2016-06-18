PhpDependencyAnalysisSuite
==========================

[![Build Status](https://travis-ci.org/cawolf/PhpDependencyAnalysisSuite.svg?branch=master)](https://travis-ci.org/cawolf/PhpDependencyAnalysisSuite)
[![Code Climate](https://codeclimate.com/github/cawolf/PhpDependencyAnalysisSuite/badges/gpa.svg)](https://codeclimate.com/github/cawolf/PhpDependencyAnalysisSuite)
[![Test Coverage](https://codeclimate.com/github/cawolf/PhpDependencyAnalysisSuite/badges/coverage.svg)](https://codeclimate.com/github/cawolf/PhpDependencyAnalysisSuite/coverage)

This suite will help you integrate the great project [PhpDependencyAnalysis](https://github.com/mamuz/PhpDependencyAnalysis) into your application.

### Todo

* Command to generate configuration files consumed by the [Analyze Command](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/README.md#usage)
* Command to parse the results of the [Analyze Command](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/README.md#usage) and take certain actions, such as
    * alert class cycles
    * alert warnings
    * optionally letting your build process fail if anything is alerted
