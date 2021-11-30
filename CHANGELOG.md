# Changelog

## 2.0.0
- Ensure generated URLs are properly formatted (via `rawurlencode()`)
- Sort regular expressions by specificity to decrease likelihood of partial matches when using `LinkifyTracking::linkify()`.
- Prevent double linkification when using `LinkifyTracking::linkify()`.
- Update DHL tracking number patterns based on information available [here](https://www.dhl.com/us-en/home/tracking/id-labels.html). Due to ambiguity, some patterns are intentionally broad.

## 1.1.0
 - Added Royal Mail (UK) support

## 1.0.0
 - Initial release
