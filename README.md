# LTI 1.3 NRPS Library

> PHP library for [LTI 1.3 Names and Role Provisioning Services](https://www.imsglobal.org/spec/lti-nrps/v2p0) implementations as platforms and / or as tools, based on [lti1p3-core library](https://github.com/oat-sa/lib-lti1p3-core).

# Table of contents

- [Specifications](#specifications)
- [Installation](#installation)
- [Tutorials](#tutorials)
- [Tests](#tests)

## Specifications

- [LTI 1.3 Names and Role Provisioning Services](https://www.imsglobal.org/spec/lti-nrps/v2p0)
- [IMS LTI 1.3 Core](http://www.imsglobal.org/spec/lti/v1p3)
- [IMS Security](https://www.imsglobal.org/spec/security/v1p0)

## Installation

```console
$ composer require oat-sa/lib-lti1p3-nrps
```

## Tutorials

Before using this library, you must first  [configure the lti1p3-core library](https://github.com/oat-sa/lib-lti1p3-core#quick-start).

You can then find below some tutorials, presented by topics.

### Tool

- how to [use the NRPS library as a tool](doc/tool.md)

### Platform

- how to [use the NRPS library as a platform](doc/platform.md)


## Tests

To run tests:

```console
$ vendor/bin/phpunit
```
**Note**: see [phpunit.xml.dist](phpunit.xml.dist) for available test suites.