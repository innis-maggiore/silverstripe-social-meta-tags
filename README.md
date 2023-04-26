# Silverstripe Social Meta Tags
Adds social media meta tags to Silverstripe sites.

## Description
**silverstripe-social-meta-tags** adds social media meta tags for OpenGraph (Facebook) and Twitter to SiteTree and child page types.

Supported social media meta tags include:

* OpenGraph (Facebook)
  * `og:site_name`
  * `og:title`
  * `og:image`
  * `og:description`
  * `og:url`
* Twitter
  * `twitter:site`
  * `twitter:title`
  * `twitter:image`
  * `twitter:description`
  * `twitter:card`

## Requirements
SilverStripe 4.2+

Use the `1.0.1` tag ([ss3 branch](https://github.com/innis-maggiore/silverstripe-social-meta-tags/tree/1.0.1)) for SilverStripe 3 sites.

## Installation
1. ``composer require innis-maggiore/silverstripe-social-meta-tags ^4.0``
2. run a `/dev/build?flush=all`
3. Without any configuration, the module will begin rendering social media meta tags for all page types. 

## Defaults
By default, the module will use the following fields to populate the meta tags:

* `og:type`				--> `"website"`
* `og:site_name`		--> `SiteConfig.Title`
* `og:title`			--> `Title`
* `og:description`		--> `MetaDescription`
* `og:url`				--> `AbsoluteLink()`
* `twitter:title`		--> `Title`
* `twitter:description`	--> `MetaDescription`
* `twitter:card`		--> `"summary"`

NOTE: `twitter:card` will be set to `"summary"` by default, unless an image field is used. If an image field is used, the content will be set to `"summary_large_image"`.

## Configuration
Configure the module by editing ``mysite/_config/config.yml`` and set the following options:
```yml
InnisMaggiore\SocialMetaTags:
  default_title: 'Title' 
  default_description: 'MetaDescription' 
  meta_description_default: true 
  twitter_site: '' 
  titles:	
    Fully\Namespaced\ClassName: 'FieldName'
    Fully\Namespaced\ClassName1: 'FieldName1'
  descriptions:
    Fully\Namespaced\ClassName: 'FieldName'
    Fully\Namespaced\ClassName1: 'FieldName1'
  images:	
    Fully\Namespaced\ClassName: 'FieldName'
    Fully\Namespaced\ClassName1: 'FieldName1'
  types:
    Fully\Namespaced\ClassName: 'website'
    Fully\Namespaced\ClassName1: 'article'
```
* `default_title` - override the default title field to use; `PageTitle` is used by default.
* `default_description` - override the default description field to use; `MetaDescription` is used by default.
* `meta_description_default` - flag that indicates whether to fall back to `MetaDescription` if the chosen description field is empty.
* `twitter_site` - the content to be used for thw `twitter:site` meta tag.
* `titles` - an array of class names, with the value being the name of the Text field to use for title meta tags. Useful for adding social media meta tag output to DataObjects.
* `descriptions` - an array of class names, with the value being the name of the Text field to use for description meta tags. Useful for adding social media meta tag output to DataObjects.
* `images` - an array of class names, with the value being the name of the Image field to use for image meta tags. Useful for adding social media meta tag output to DataObjects.
* `types` - an array of class names, with the value being the content to use for `og:type` met tag.

## Example `config.yml`
```yml
InnisMaggiore\SocialMetaTags:
  default_title: 'PageTitle'
  default_description: ''
  meta_description_default: true
  titles:
    InnisMaggiore\Models\Category: 'Name'
    InnisMaggiore\Models\Product: 'Name'
    InnisMaggiore\Models\Recipe: 'Name'
  descriptions:
    InnisMaggiore\Models\Category: 'ShortDescr'
    InnisMaggiore\Models\Product: 'Summary'
    InnisMaggiore\Models\Recipe: 'MetaDescription'
  images:
    InnisMaggiore\Pagetypes\AboutUsPage: 'LogoImage'
    InnisMaggiore\Models\Category: 'BeautyShot'
    InnisMaggiore\Models\Product: 'BeautyShot'
    InnisMaggiore\Models\Recipe: 'Image'
  types:
    InnisMaggiore\Models\Recipe: 'article'
  twitter_site: ''
```
## Adding social media meta tags to DataObjects
By default, this module will only add social media meta tags to SiteTree objects.

To add social media meta tags to DataObjects:
* in `_config/config.yml`, add the extension to each DataObject class you wish:
```yml
Fully\Namespaced\Dataobject:
  extensions:
    - InnisMaggiore\SilverstripeSocialMetaTags\SocialMetaTagsExtension
```
* in `_config/config.yml`, set options for `titles`, `descriptions`, and `images` (if desired) for your class.
  * for an example, see the above example `config.yml`.
* in your DataObject's class, add new fields in `getCMSFields()`:
```php
  public function getCMSFields() {
      $fields = parent::getCMSFields();
      
      ...
      
      $fields->addFieldToTab('Root.Main', new TextareaField("SocialMetaDescription"));
      $fields->addFieldToTab('Root.Main', new UploadField("SocialMetaImage"));
      
      return $fields;
  }
```
* run a `/dev/build?flush=all`

## License
This project is licensed under the [New BSD License](./LICENSE)
