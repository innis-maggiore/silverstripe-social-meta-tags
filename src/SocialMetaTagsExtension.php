<?php

namespace InnisMaggiore\SilverstripeSocialMetaTags;

use Silverstripe\ORM\DataExtension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextareaField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Core\Convert;
use SilverStripe\Core\Config\Config;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\ErrorPage\ErrorPage;
use SilverStripe\Assets\Image;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\CMS\Model\RedirectorPage;

class SocialMetaTagsExtension extends DataExtension {
    private static $db = array(
        "SocialMetaDescription" => "Text"
    );

    private static $has_one = array(
        "SocialMetaImage"   => Image::class
    );

    private static $owns = array(
        "SocialMetaImage"
    );

    public function updateCMSFields(FieldList $fields) {
        $owner = $this->getOwner();

        if (is_subclass_of($owner, SiteTree::class) && !is_a($owner, RedirectorPage::class) && !is_a($owner, ErrorPage::class)) {
            $fields->addFieldToTab('Root.Main.Metadata', TextareaField::create("SocialMetaDescription")->addExtraClass("stacked"));
            $fields->addFieldToTab('Root.Main.Metadata', UploadField::create("SocialMetaImage")->addExtraClass("stacked"));
        }
    }

    public function MetaTags(&$tags) {
        $owner = $this->getOwner();
        $className = $owner->ClassName;

        if ($className != ErrorPage::class) {
            $siteConfig = SiteConfig::current_site_config();
            $siteTitle = Convert::raw2att($siteConfig->Title);

            $ogType = "website";

            // get specified fields
            $configClass = "InnisMaggiore\\SocialMetaTags";
            $twitterSite = Config::inst()->get($configClass, 'twitter_site');

            // get defaults
            $defaultTitle = Config::inst()->get($configClass, 'default_title') ?: "Title";
            $defaultDescription = Config::inst()->get($configClass, 'default_description') ?: "MetaDescription";
            $metaDescriptionDefault = Config::inst()->get($configClass, 'meta_description_default');

            // get customized fields
            $descriptionsConfig = Config::inst()->get($configClass,'descriptions') ?: array();
            $imagesConfig = Config::inst()->get($configClass, 'images') ?: array();
            $titlesConfig = Config::inst()->get($configClass, 'titles') ?: array();
            $typesConfig = Config::inst()->get($configClass, 'types') ?: array();

            // if customized title field exists and is populated, use it
            if (array_key_exists($className, $titlesConfig)) {
                $titleTextField = $titlesConfig[$className];
                if (isset($owner->$titleTextField) && $owner->$titleTextField != "") {
                    $titleText = Convert::raw2att($owner->$titleTextField);
                }
            }

            // fall back to default title field if customized title field doesn't exist or is empty.
            if (!isset($titleText)) {
                if ($defaultTitle != "") {
                    $titleText = Convert::raw2att($owner->$defaultTitle);
                }
            }

            // get image by customized field
            if (array_key_exists($className, $imagesConfig)) {
                $imageField = $imagesConfig[$className];
                $image = $owner->$imageField();
                if ($image && $image->ID != 0) {
                    $imageLink = $image->AbsoluteLink();
                }
            }

            // if customized image field isn't populated, fall back to social meta image
            if (!isset($imageLink)) {
                $image = $owner->SocialMetaImage();
                if ($image && $image->ID != 0) {
                    $imageLink = $image->AbsoluteLink();
                }
            }

            // if customized type is set, use it.
            if (array_key_exists($className, $typesConfig)) {
                $ogType = $typesConfig[$className];
            }

            // if customized description field exists and is populated, use it
            if (array_key_exists($className, $descriptionsConfig)) {
                $descriptionTextField = $descriptionsConfig[$className];
                if (isset($owner->$descriptionTextField) && $owner->$descriptionTextField != "") {
                    $descriptionText = Convert::raw2att($owner->$descriptionTextField);
                }
            }

            // fall back to SocialMetaDescription, default description field, and MetaDescription, depending on configs and what is populated
            if (!isset($descriptionText)) {
                if ($owner->SocialMetaDescription != "") {
                    $descriptionText = Convert::raw2att($owner->SocialMetaDescription);
                } else if ($defaultDescription != "" && isset($owner->$defaultDescription) && $owner->$defaultDescription != "") {
                    $descriptionText = Convert::raw2att($owner->$defaultDescription);
                } else if ($metaDescriptionDefault && isset($owner->MetaDescription) && $owner->MetaDescription != "") {
                    $descriptionText = Convert::raw2att($owner->MetaDescription);
                }
            }

            // get link
            $link = $owner->AbsoluteLink();

            /****************************************
             *  Add Social Meta Tags to tag output  *
             ****************************************/

            // OpenGraph
            $tags .= "\n<!-- OpenGraph Meta Tags -->\n";

            // og:type
            $tags .= "<meta property=\"og:site_name\" content=\"{$siteTitle}\" />\n";

            // og:site_name
            $tags .= "<meta property=\"og:type\" content=\"{$ogType}\" />\n";

            // og:title
            if (isset($titleText)) {
                $tags .= "<meta property=\"og:title\" content=\"{$titleText}\" />\n";
            }

            // og:image
            if (isset($imageLink)) {
                $tags .= "<meta property=\"og:image\" content=\"{$imageLink}\" />\n";
            }

            // og:description
            if (isset($descriptionText)) {
                $tags .= "<meta property=\"og:description\" content=\"{$descriptionText}\" />\n";
            }

            // og:url
            if ($link != "") {
                $tags .= "<meta property=\"og:url\" content=\"{$link}\" />\n";
            }

            // Twitter
            $tags .= "\n<!-- Twitter Meta Tags -->\n";

            // twitter:site
            if ($twitterSite) {
                $tags .= "<meta name=\"twitter:site\" content=\"{$twitterSite}\" />\n";
            }

            // twitter:title
            if (isset($titleText)) {
                $tags .= "<meta name=\"twitter:title\" content=\"{$titleText}\" />\n";
            }

            // twitter:image
            if (isset($imageLink)) {
                $tags .= "<meta name=\"twitter:image\" content=\"{$imageLink}\" />\n";
            }

            // twitter:description
            if (isset($descriptionText)) {
                $tags .= "<meta name=\"twitter:description\" content=\"{$descriptionText}\" />\n";
            }

            // twitter:card - summary / summary_large_image
            if (isset($imageLink)) {
                $tags .= "<meta name=\"twitter:card\" content=\"summary_large_image\" />\n";
            } else {
                $tags .= "<meta name=\"twitter:card\" content=\"summary\" />\n";
            }

        }
    }
}
