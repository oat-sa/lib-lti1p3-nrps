CHANGELOG
=========

4.0.0
-----

* Added PHP 8 support (and kept >=7.2)
* Updated models to add fluent setters  
* Updated ContextFactory to cast non string context ids  
* Updated oat-sa/lib-lti1p3-core dependency to version 4.0
* Updated MembershipServiceServerBuilderInterface signature to handle limit as integer
* Updated Member and MemberCollection to rely on core collections  
* Updated documentation

3.0.0
-----

* Removed request dependency from the membership server builder interface signatures
* Enhanced membership relation link handling
* Fixed member message serialization
* Updated documentation

2.0.0
-----

* Added Travis integration
* Upgraded for oat-sa/lib-lti1p3-core version 3.0.0
* Updated documentation

1.0.0
-----

* Added NRPS tool side features to get memberships from LTI 1.3 messages

0.1.0
-----

* Provided NRPS tool side (client)
* Provided NRPS platform side (server)
* Ensured IMS NRPS tool certification compliance
