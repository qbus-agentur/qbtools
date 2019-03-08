Generic Tools for TYPO3 Sites
=============================

[![Build Status](https://api.travis-ci.org/qbus-agentur/qbtools.png)](https://travis-ci.org/qbus-agentur/qbtools)
[![Coverage Status](https://coveralls.io/repos/github/qbus-agentur/qbtools/badge.svg)](https://coveralls.io/github/qbus-agentur/qbtools)

Introduction
------------

This extension provides a set of `ViewHelpers` and `Hooks` that are used
to implement TYPO3 based websites by [Qbus](https://www.qbus.de/).

Usage
-----

```sh
$ composer require qbus/qbtools:^3.0
```

### Quick Example

```html
{namespace qbtools=Qbus\Qbtools\ViewHelpers}

<!-- Fetch blog posts of some Extbase model and display using
     a partial of some (external) extension -->
<qbtools:fetch model="\Vendor\MyBlog\Domain\Model\Post" match="{uid: 3}" as="posts">
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
