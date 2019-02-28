# Bumo PHP SDK

## Introduction

PHP developers can easily operate Bumo blockchain via the Bumo PHP SDK. 

1. [docs](https://github.com/bumoproject/bumo-sdk-java/blob/master/docs) are the usage documentations for the Bumo PHP SDK.

2. [examples](https://github.com/bumoproject/bumo-sdk-java/blob/master/examples) are some examples of a project based on Maven.

3. [libs](https://github.com/bumoproject/bumo-sdk-java/blob/master/libs) are the dependency packages for the Bumo PHP SDK.

4. [src](https://github.com/bumoproject/bumo-sdk-java/blob/master/src) is the source code for the Bumo PHP SDK.


## Require

Ed25519, which is for signature.

php5.6 and above.



## Installation

1、Copy the corresponding version of ed25519 in the libs directory to the PHP extension directory

2、Add 'extension=ed25519.so' in php.ini

3、Execute the following commands to add to project: 
```shell
     composer require bumo/bumo-sdk:^{version}
```
   example: 
```shell
     composer require bumo/bumo-sdk:^v1.0.0
```

## Example project

Bumo PHP SDK provides rich examples for developers' reference

[Sample document entry](docs/SDK.md "")

