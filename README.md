# linkify-tracking
Linkifies package tracking numbers for DHL, FedEx, Royal Mail (UK), UPS, and USPS.

## Installation

```
composer require philipnewcomer/linkify-tracking
```

## Usage

First instantiate an instance of the library:

```php
$linkifyTracking = new PhilipNewcomer\LinkifyTracking\LinkifyTracking;
```

### Get the tracking URL for a single tracking number

```php
$linkifyTracking->getLinkUrl('12345678901234567890');
```

Result:

```
https://tools.usps.com/go/TrackConfirmAction?tLabels=12345678901234567890
```

### Get the link data for a single tracking number

```php
$linkifyTracking->getLinkData('12345678901234567890');
```

Result:

```php
[
    'carrier' => 'USPS',
    'url' => 'https://tools.usps.com/go/TrackConfirmAction?tLabels=12345678901234567890'
]
```

### Convert all tracking numbers in the given content to HTML links

```php
$content = '
Here is a tracking number: 12345678901234567890
And another tracking number: 12345678901234567890
';
$linkifyTracking->linkify($content)
```

Result:
```html
Here is a tracking number: <a href="https://tools.usps.com/go/TrackConfirmAction?tLabels=12345678901234567890">12345678901234567890</a>
And another tracking number: <a href="https://tools.usps.com/go/TrackConfirmAction?tLabels=12345678901234567890">12345678901234567890</a>
```

## Configuration

Configuration arguments may be passed to the `LinkifyTracking` constructor.

The following arguments may be provided:
 * `linkAttributes`: An array of attributes which should be added to the generated HTML links

Example:

```php
$linkifyTracking = new PhilipNewcomer\LinkifyTracking\LinkifyTracking([
    'linkAttributes' => [
        'class' => 'tracking-link',
        'target' => '_blank'
    ]
]);
```

## Credits

Tracking number regular expressions for DHL, FedEx, UPS, and USPS are taken from [https://github.com/darkain/php-tracking-urls](https://github.com/darkain/php-tracking-urls).
