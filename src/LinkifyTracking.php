<?php

namespace PhilipNewcomer\LinkifyTracking;

class LinkifyTracking
{
    /**
     * @var array The configuration arguments.
     */
    protected $args;

    /**
     * @var array The tracking carriers.
     */
    protected $carriers = [
        [
            'label' => 'DHL',
            'url' => 'http://www.dhl.com/content/g0/en/express/tracking.shtml?brand=DHL&AWB=%s',
            'regex' => '/\b(\d{4}[- ]?\d{4}[- ]?\d{2}|\d{3}[- ]?\d{8}|[A-Z]{3}\d{7})\b/i'
        ],
        [
            'label' => 'FedEx',
            'url' => 'https://www.fedex.com/apps/fedextrack/?action=track&locale=en_US&cntry_code=us&tracknumbers=%s',
            'regex' => '/\b(((96\d\d|6\d)\d{3} ?\d{4}|96\d{2}|\d{4}) ?\d{4} ?\d{4}( ?\d{3})?)\b/i'
        ],
        [
            'label' => 'UPS',
            'url' => 'http://wwwapps.ups.com/WebTracking/processInputRequest?TypeOfInquiryNumber=T&InquiryNumber1=%s',
            'regex' => '/\b(1Z ?[0-9A-Z]{3} ?[0-9A-Z]{3} ?[0-9A-Z]{2} ?[0-9A-Z]{4} ?[0-9A-Z]{3} ?[0-9A-Z]|T\d{3} ?\d{4} ?\d{3})\b/i'
        ],
        [
            'label' => 'USPS',
            'url' => 'https://tools.usps.com/go/TrackConfirmAction?tLabels=%s',
            'regex' => [
                '/\b((420 ?\d{5} ?)?(91|92|93|94|01|03|04|70|23|13)\d{2} ?\d{4} ?\d{4} ?\d{4} ?\d{4}( ?\d{2,6})?)\b/i',
                '/\b((M|P[A-Z]?|D[C-Z]|LK|E[A-C]|V[A-Z]|R[A-Z]|CP|CJ|LC|LJ) ?\d{3} ?\d{3} ?\d{3} ?[A-Z]?[A-Z]?)\b/i',
                '/\b(82 ?\d{3} ?\d{3} ?\d{2})\b/i'
            ]
        ],
        [
            'label' => 'Royal Mail',
            'url' => 'http://www.royalmail.com/portal/rm/track?trackNumber=%s',
            'regex' => '/\b(?!(EA|EB|EC|ED|EE|CP))[A-Za-z]{2}[0-9]{9}+GB\b/i'
        ]
    ];

    /**
     * Constructor.
     *
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->args = $args;
    }

    /**
     * Returns the link for the given tracking number.
     *
     * @param string $trackingNumber The tracking number to process.
     *
     * @return string|null The tracking number link, or null if it could not be generated.
     */
    public function getLinkUrl(string $trackingNumber)
    {
        $data = $this->getLinkData($trackingNumber);

        return $data['url'] ?? null;
    }

    /**
     * Returns the link data for the given tracking number.
     *
     * @param string $trackingNumber The tracking number to process.
     *
     * @return array|null The tracking number data, or null if it could not be generated.
     */
    public function getLinkData(string $trackingNumber)
    {
        foreach ($this->carriers as $carrier) {
            foreach ((array) $carrier['regex'] as $regex) {
                if (! preg_match($regex, $trackingNumber, $matches)) {
                    continue;
                }

                // Even if we've found a tracking number somewhere in the provided string, make sure that the string
                // contains *only* a single tracking number, and nothing else.
                if ($trackingNumber !== $matches[0]) {
                    continue;
                }

                return [
                    'carrier' => $carrier['label'],
                    'url' => sprintf($carrier['url'], rawurlencode($matches[0]))
                ];
            }
        }

        return null;
    }

    /**
     * Linkifies all tracking numbers in the given content.
     *
     * @param string $content The content to process.
     *
     * @return string The content, with tracking numbers linkified.
     */
    public function linkify(string $content)
    {
        foreach ($this->carriers as $carrier) {
            foreach ((array) $carrier['regex'] as $regex) {
                $content = preg_replace_callback(
                    $regex,
                    function ($matches) use ($carrier) {
                        return $this->generateHtmlLink($carrier, $matches[0]);
                    },
                    $content
                );
            }
        }

        return $content;
    }

    /**
     * Generates an HTML link to the given carrier for the given tracking number.
     *
     * @param array $carrier
     * @param string $trackingNumber
     *
     * @return string
     */
    protected function generateHtmlLink(array $carrier, string $trackingNumber)
    {
        $attributes = array_merge(
            [
                'href' => sprintf($carrier['url'], rawurlencode($trackingNumber))
            ],
            $this->args['linkAttributes'] ?? []
        );

        $attributesHtml = '';
        foreach ($attributes as $attribute => $attributeValue) {
            $attributesHtml .= sprintf(' %s="%s"', $attribute, $attributeValue);
        }

        return sprintf(
            '<a %s>%s</a>',
            trim($attributesHtml),
            $trackingNumber
        );
    }
}
