# MediaCT Testing Suite

This package serves as an umbrella meta-package for several of MediaCT's testing 
packages. By requiring these packages here, they're maintainable from a single 
location. Maintaining them seperately for each project is very inefficient and 
leads to mistakes.  

The "run.sh" file contains a series of commands that will need to be run by every pipeline.
This includes PHPMD PHPCS and PHPUnit.

## Packages included
* phpunit/phpunit
* mediact/coding-standard




