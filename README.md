Generic Tools for TYPO3 Sites
=============================

[![Build Status](https://api.travis-ci.org/qbus-agentur/qbtools.png)](https://travis-ci.org/qbus-agentur/qbtools)
[![Coverage Status](https://coveralls.io/repos/github/qbus-agentur/qbtools/badge.svg)](https://coveralls.io/github/qbus-agentur/qbtools)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/qbus-agentur/qbtools/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/qbus-agentur/qbtools/?branch=master)

Introduction
------------

This extension provides a set of `ViewHelpers` and `Hooks` that are used
to implement TYPO3 based websites by [Qbus](https://www.qbus.de/).

Some ViewHelpers (like `qbtools:fetch` or `qbtools:fal`) are not meant
to be used as preferred solution, but are rather available when a proper
implementation (using repository classes) is not possible – e.g. when
data needs to be retrieved from the database in a template of a generic
community extension.

Usage
-----

```sh
$ composer require qbus/qbtools:^3.0
```

### Quick Example

```html
{namespace qbtools=Qbus\Qbtools\ViewHelpers}

<!-- Fetch (top) blog posts of some Extbase model and display using
     a partial of some (external) extension. -->
<qbtools:fetch model="Vendor\\MyBlog\\Domain\\Model\\Post" match="{top: 1}" as="posts">
    <f:for each="{posts}" as="post">
        <qbtools:renderExternal partial="Blog/Teaser" extensionName="MyBlog" arguments="{post: post}"/>
    </f:for>
</qbtools:fetch>


<!-- Render content from page with uid 340 -->
<qbtools:renderContent pid="340"/>

<!-- Render colPos 1 content from page with uid 340 -->
<qbtools:renderContent pid="340" colpos="1" />

<!-- Render content element with uid 230 -->
<qbtools:renderContent uid="230"/>


<!-- Quick an dirty call to a PHP function -->
<qbtools:call func="str_replace" params="{0: '_', 1: ' ', 2: 'foo_bar'}" as="result">
  <!-- will print 'foo bar' -->
  {result}
</qbtools:call>
```
