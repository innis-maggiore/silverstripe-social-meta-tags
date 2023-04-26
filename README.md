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
SilverStripe 3.5+


## Installation
1. ``composer require creativecodelabs/silverstripe-social-meta-tags``
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
SocialMetaTags:
  default_title: 'PageTitle' 
  default_description: 'MetaDescription' 
  meta_description_default: true 
  twitter_site: '' 
  titles:	
    ClassName: 'FieldName'
    ClassName1: 'FieldName1'
  descriptions:
    ClassName: 'FieldName'
    ClassName1: 'FieldName1'
  images:	
    ClassName: 'FieldName'
    ClassName1: 'FieldName1'
  types:
    ClassName: 'website'
    ClassName1: 'article'
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
SocialMetaTags:
  default_title: 'PageTitle'
  default_description: ''
  meta_description_default: true
  titles:
    Category: 'Name'
    Product: 'Name'
    Recipe: 'Name'
  descriptions:
    Category: 'ShortDescr'
    Product: 'Summary'
    Recipe: 'MetaDescription'
  images:
    AboutUsPage: 'LogoImage'
    Category: 'BeautyShot'
    Product: 'BeautyShot'
    Recipe: 'Image'
  types:
    Recipe: 'article'
  twitter_site: ''
```
## Adding social media meta tags to DataObjects
By default, this module will only add social media meta tags to SiteTree objects.

To add social media meta tags to DataObjects:
* in `/mysite/_config.php`, add the extension to each DataObject class you wish.
  * e.g. `Recipe::add_extension('SocialMetaTagsExtension');`
* in `/mysite/_config/config.yml`, set options for `titles`, `descriptions`, and `images` (if desired) for your class.
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
This project is licensed under [GPL v3](./LICENSE)