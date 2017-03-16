# MediaCT Testing Suite

This package serves as an umbrella meta-package for several of MediaCT's testing 
packages. By requiring these packages here, they're maintainable from a single 
location. Maintaining them seperately for each project is very inefficient and 
leads to mistakes.  

The "mediact-testing-suite" file contains a series of commands that will need to be run by every pipeline.

This package can be run locally or it might be run in a CI setting.
An example file for running the testing suite in BitBucket Pipelines can be found in this directory. It is called bitbucket-pipelines.yml.dist
