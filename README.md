# silverstripe-systemlinks

A SilverStripe module that allows developers to define a static list of "system links" - login, logout, lostpassword, etc - and expose these for use in both back-end and templates.

Integrate this with functionality that already allows users to build links, like Linkable/etc, to also let users build links to these outside-of-site-tree targets.

## Requirements

SilverStripe 4

## Installation

`composer require fromholdio/silverstripe-systemlinks`

Setup your config file

```yml
Fromholdio\SystemLinks\SystemLinks:
  links:
    login:
      url: /Security/login
      title: Login
    logout:
      url: /Security/logout
      title: Logout
    lostpassword:
      url: /Security/lostpassword
      title: Lost Password
    cmsadmin:
      url: /admin
      title: 'CMS Admin'
    someotherroute:
      url: /could-also-be-absolute-url
      title: 'Some other route'
```

## Usage example

Documentation will be forthcoming, but also, it's just one class and pretty lean. Check the class itself to see all options at the moment.

Key uses are below:

### Use to fuel values in a dropdown

```php
DropdownField::create(
    'SystemLinkKey',
    'System Link',
    SystemLinks::get_map()
);
```

### Get link value from saved key

```php
$key = $this->SystemLinkKey;

// This returns an ArrayData object
$link = SystemLinks::get_link($key);  
$linkTitle = $link->Title;
$linkURL = $link->URL;

// Alternatively, get link as simple array
$linkArr = SystemLinks::get_raw_link($key);
$linkTitle = $linkArr['title'];
$linkURL = $linkArr['url'];
```

### Get link for use in template .ss file

```php
// $SystemLink is available in templates globally
// It requires a link key to be supplied
<h2>$SystemLink('login').URL</h2>
<h2>$SystemLink('lostpassword').Title</h2>
```
