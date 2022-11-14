# Changelog

## 2.1.0 - 2022-11-14

- Added support for SVGs in `Asset` layers. Make sure `inkscape` is installed on your server -- it's required to pre-transform the SVGs. -- [#13](https://github.com/alpshq/craft-share-previews/pull/13)

## 2.0.0 - 2022-11-14

- 🎉 Added support for Craft 4 -- [0a80d66](https://github.com/alpshq/craft-share-previews/commit/0a80d66)

Please report any issues associated with Craft 4 in [the issue tracker at GitHub](https://github.com/alpshq/craft-share-previews/issues).

## 1.7.1 - 2022-02-07

- Fixed an issue obtaining local copy of assets stored on S3 volumes -- [17614cc](https://github.com/alpshq/craft-share-previews/commit/17614cc)
- Slightly enhanced wording of an assets layer's Twig expression instructions -- [321e0f8](https://github.com/alpshq/craft-share-previews/commit/321e0f8)
- The Twig expression of an asset layer is evaluated even if no entry was supplied -- [55cec76](https://github.com/alpshq/craft-share-previews/commit/55cec76)

## 1.7.0 - 2022-02-04

- The `fontCachePath` property was removed from the settings model -- [#9](https://github.com/alpshq/craft-share-previews/pull/9)
- The namespace of `alps\sharepreviews\ImageBeforeRenderEvent` changed to `alps\sharepreviews\events\ImageBeforeRenderEvent` -- [1c9846f](https://github.com/alpshq/craft-share-previews/commit/1c9846f6343682bb5e19524b2df99c8e7c051042)

## 1.6.0 - 2022-02-03

- It's now possible to use Twig expressions within asset layers

## 1.5.4 - 2021-11-18

- More enhancements on text fitting algorithm

## 1.5.3 - 2021-11-18

- Enhanced text fitting algorithm

## 1.5.2 - 2021-11-18

- Resolved `Headers already sent` issue

## 1.5.1 - 2021-11-17

- Fixed class import & property issues

## 1.5.0 - 2021-11-17

- There is now an ImageBeforeRenderEvent when an image is about to render.
- Added support for specifying a font's line height.

## 1.4.1 - 2021-07-27

Fixed some compatibility issues with templates using non default dimensions.

## 1.4.0 - 2021-07-23

Added the possibility to make a template's width and height configurable through config file.

## 1.3.1 - 2021-07-22

Fixed a bug which came from a missing default value.

## 1.3.0 - 2021-07-16

Added support for **custom fonts** 🥳

## 1.2.0 - 2021-07-12

Removed the illuminate/collections dependency

## 1.1.2 - 2021-06-16

Cleaned some things up

## 1.1.1 - 2021-06-16

Craft license added

## 1.1.0 - 2021-06-16

Updated fonts

## 1.0.0 - 2021-06-16

Initial release 🥳
