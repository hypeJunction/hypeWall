<a name="5.2.0"></a>
# [5.2.0](https://github.com/hypeJunction/hypeWall/compare/5.1.1...v5.2.0) (2017-07-31)


### Features

* **format:** add hooks to format summary and attachments ([80bc992](https://github.com/hypeJunction/hypeWall/commit/80bc992))



<a name="5.1.1"></a>
## [5.1.1](https://github.com/hypeJunction/hypeWall/compare/5.1.0...v5.1.1) (2017-07-31)


### Bug Fixes

* **menus:** wall tools menu now receives the entity ([7ece1f2](https://github.com/hypeJunction/hypeWall/commit/7ece1f2))



<a name="5.1.0"></a>
# [5.1.0](https://github.com/hypeJunction/hypeWall/compare/5.0.3...v5.1.0) (2017-03-22)


### Bug Fixes

* **forms:** use correct input value for location ([b1ae1e3](https://github.com/hypeJunction/hypeWall/commit/b1ae1e3))

### Features

* **wall:** add option to repost cards to wall ([b9af634](https://github.com/hypeJunction/hypeWall/commit/b9af634))



<a name="5.0.3"></a>
## [5.0.3](https://github.com/hypeJunction/hypeWall/compare/5.0.2...v5.0.3) (2017-03-02)


### Bug Fixes

* **action:** use correct input value for description ([6d23b90](https://github.com/hypeJunction/hypeWall/commit/6d23b90)), closes [#91](https://github.com/hypeJunction/hypeWall/issues/91)



<a name="5.0.2"></a>
## [5.0.2](https://github.com/hypeJunction/hypeWall/compare/5.0.1...v5.0.2) (2017-03-02)


### Bug Fixes

* **activate:** remove redundant wire subtype class registration ([b8b5bf4](https://github.com/hypeJunction/hypeWall/commit/b8b5bf4)), closes [#90](https://github.com/hypeJunction/hypeWall/issues/90)



<a name="5.0.1"></a>
## [5.0.1](https://github.com/hypeJunction/hypeWall/compare/5.0.0...v5.0.1) (2017-03-01)


### Bug Fixes

* **forms:** improved edit post experience ([5a0d439](https://github.com/hypeJunction/hypeWall/commit/5a0d439))
* **upgrade:** upgrade scripts now run as they should ([eebbbcc](https://github.com/hypeJunction/hypeWall/commit/eebbbcc))
* **views:** object listing no longer calls removed functions ([f6b962d](https://github.com/hypeJunction/hypeWall/commit/f6b962d))



<a name="5.0.0"></a>
# [5.0.0](https://github.com/hypeJunction/hypeWall/compare/4.4.11...v5.0.0) (2017-02-28)


### Features

* **releases:** upgrade to Elgg 2.3 ([4b76c9f](https://github.com/hypeJunction/hypeWall/commit/4b76c9f))


### BREAKING CHANGES

* releases: Now requires Elgg 2.2
Drops hypeApps requirement, subsequently no longer supports
certain APIs, no longer extends and implements hypeApps classes
and interfaces.
Most of the views have been rewritten to improved user experience.



<a name="4.4.11"></a>
## [4.4.11](https://github.com/hypeJunction/hypeWall/compare/4.4.10...v4.4.11) (2016-11-30)


### Bug Fixes

* **photos:** fix issues with processing file uploads ([ab5f001](https://github.com/hypeJunction/hypeWall/commit/ab5f001)), closes [#27](https://github.com/hypeJunction/hypeWall/issues/27)



<a name="4.4.10"></a>
## [4.4.10](https://github.com/hypeJunction/hypeWall/compare/4.4.9...v4.4.10) (2016-10-25)


### Bug Fixes

* **js:** ensure that post action does not wrap the list ([27a5f0c](https://github.com/hypeJunction/hypeWall/commit/27a5f0c))



<a name="4.4.9"></a>
## [4.4.9](https://github.com/hypeJunction/hypeWall/compare/4.4.8...v4.4.9) (2016-10-24)


### Bug Fixes

* **input:** do not strip tags from the status ([9bf63b4](https://github.com/hypeJunction/hypeWall/commit/9bf63b4))
* **js:** add missing length check ([9107f01](https://github.com/hypeJunction/hypeWall/commit/9107f01))
* **output:** output wall status via longtext output ([fcab534](https://github.com/hypeJunction/hypeWall/commit/fcab534))



<a name="4.4.8"></a>
## [4.4.8](https://github.com/hypeJunction/hypeWall/compare/4.4.7...v4.4.8) (2016-09-11)


### Bug Fixes

* **js:** river is once again instantly updated when hypeLists is enabled ([3712ff3](https://github.com/hypeJunction/hypeWall/commit/3712ff3))



<a name="4.4.7"></a>
## [4.4.7](https://github.com/hypeJunction/hypeWall/compare/4.4.6...v4.4.7) (2016-02-09)


### Bug Fixes

* **lists:** always enable pagination if hypeLists is enabled ([76704b1](https://github.com/hypeJunction/hypeWall/commit/76704b1))

### Features

* **ajax:** instantly update wall list on ajax ([f76dc08](https://github.com/hypeJunction/hypeWall/commit/f76dc08))



<a name="4.4.6"></a>
## [4.4.6](https://github.com/hypeJunction/hypeWall/compare/4.4.5...v4.4.6) (2016-02-09)


### Bug Fixes

* **languages:** fix plugin name appearance in user settings ([14f2c19](https://github.com/hypeJunction/hypeWall/commit/14f2c19))



<a name="4.4.5"></a>
## [4.4.5](https://github.com/hypeJunction/hypeWall/compare/4.4.4...v4.4.5) (2016-02-09)


### Bug Fixes

* **menus:** fix usersettings menu label ([7b8330d](https://github.com/hypeJunction/hypeWall/commit/7b8330d))
* **posts:** make wall posts likeable ([eddecba](https://github.com/hypeJunction/hypeWall/commit/eddecba))



<a name="4.4.4"></a>
## [4.4.4](https://github.com/hypeJunction/hypeWall/compare/4.4.3...v4.4.4) (2016-01-25)


### Bug Fixes

* **notifications:** mark wall post as read with notifier when river view is rendered ([a3f7028](https://github.com/hypeJunction/hypeWall/commit/a3f7028))



<a name="4.4.3"></a>
## [4.4.3](https://github.com/hypeJunction/hypeWall/compare/4.4.2...v4.4.3) (2016-01-24)


### Bug Fixes

* **notifications:** fix bugs and improve formatting ([531a95f](https://github.com/hypeJunction/hypeWall/commit/531a95f))
* **notifications:** fix bugs and improve formatting ([6c9167a](https://github.com/hypeJunction/hypeWall/commit/6c9167a))
* **notifications:** fix bugs and improve formatting ([26473ba](https://github.com/hypeJunction/hypeWall/commit/26473ba))



<a name="4.4.2"></a>
## [4.4.2](https://github.com/hypeJunction/hypeWall/compare/4.4.1...v4.4.2) (2016-01-23)


### Bug Fixes

* **composer:** update dependencies ([911f0c1](https://github.com/hypeJunction/hypeWall/commit/911f0c1))



<a name="4.4.1"></a>
## [4.4.1](https://github.com/hypeJunction/hypeWall/compare/4.4.0...v4.4.1) (2016-01-23)


### Bug Fixes

* **composer:** update dependencies ([09be197](https://github.com/hypeJunction/hypeWall/commit/09be197))
* **lists:** constrain lists by container ([829c3cd](https://github.com/hypeJunction/hypeWall/commit/829c3cd))



<a name="4.4.0"></a>
# [4.4.0](https://github.com/hypeJunction/hypeWall/compare/4.3.2...v4.4.0) (2016-01-23)


### Bug Fixes

* **grunt:** update automated releases ([d9a56f8](https://github.com/hypeJunction/hypeWall/commit/d9a56f8))
* **languages:** add missing widget description string ([4e81e46](https://github.com/hypeJunction/hypeWall/commit/4e81e46))
* **pages:** move group wall page to a resource view ([092e5f1](https://github.com/hypeJunction/hypeWall/commit/092e5f1))
* **views:** add missing namespace ([f97870f](https://github.com/hypeJunction/hypeWall/commit/f97870f))

### Features

* **lists:** all lists are now rendered in a uniform manner ([93fbdf8](https://github.com/hypeJunction/hypeWall/commit/93fbdf8))
* **widgets:** display wall form in the widget by default ([c27b945](https://github.com/hypeJunction/hypeWall/commit/c27b945))




