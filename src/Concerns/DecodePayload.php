<?php

namespace Eduardokum\LaravelPix\Concerns;

use Illuminate\Support\Arr;

trait DecodePayload
{
    private static function decodeRecursivePayload($payload, $parent = null): ?array
    {
        $structures = [
            '00' => [
                'type' => 'single',
                'name' => 'Payload Format Indicator',
            ],
            '01' => [
                'type' => 'single',
                'name' => 'Point of Initiation Method',
            ],
            '04' => [
                'type' => 'single',
                'name' => 'Merchant Account Information – Cartões',
            ],
            '26' => [
                'type'      => 'multiple',
                'name'      => 'Merchant Account Information',
                'multiples' => [
                    '00' => [
                        'type' => 'single',
                        'name' => 'Globally Unique Identifier',
                    ],
                    '01' => [
                        'type' => 'single',
                        'name' => 'Pix Key',
                    ],
                    '02' => [
                        'type' => 'single',
                        'name' => 'Payment Description',
                    ],
                    '25' => [
                        'type' => 'single',
                        'name' => 'Payment URL',
                    ],
                ],
            ],
            '52' => [
                'type' => 'single',
                'name' => 'Merchant Category Code',
            ],
            '53' => [
                'type' => 'single',
                'name' => 'Transaction Currency',
            ],
            '54' => [
                'type' => 'single',
                'name' => 'Transaction Amount',
            ],
            '58' => [
                'type' => 'single',
                'name' => 'Country Code',
            ],
            '59' => [
                'type' => 'single',
                'name' => 'Merchant Name',
            ],
            '60' => [
                'type' => 'single',
                'name' => 'Merchant City',
            ],
            '61' => [
                'type' => 'single',
                'name' => 'Postal Code',
            ],
            '62' => [
                'type'      => 'multiple',
                'name'      => 'Additional Data Field Template',
                'multiples' => [
                    '05' => [
                        'type' => 'single',
                        'name' => 'Reference Label',
                    ],
                ],
            ],
            '80' => [
                'type'      => 'multiple',
                'name'      => 'Unreserved Templates',
                'multiples' => [
                    '00' => [
                        'type' => 'single',
                        'name' => 'Globally Unique Identifier',
                    ],
                    '01' => [
                        'type' => 'single',
                        'name' => 'informação arbitrária do arranjo',
                    ],
                ],
            ],
            '63' => [
                'type' => 'single',
                'name' => 'CRC',
            ],
        ];

        if ($parent && ! ($structures = Arr::get($structures, "$parent.multiples"))) {
            return null;
        }

        $aPix = [];
        $i = 0;
        while ($i < strlen($payload)) {
            $code = $codeSearch = substr($payload, $i, 2);
            if ($code >= 26 && $code <= 51) {
                $codeSearch = 26;
            }
            if ($code >= 80 && $code <= 99) {
                $codeSearch = 80;
            }
            $i += 2;
            $size = intval(substr($payload, $i, 2));
            $i += 2;
            if ($structure = Arr::get($structures, $codeSearch)) {
                if ($structure['type'] == 'multiple') {
                    $aPix["$code"] = self::decodeRecursivePayload(substr($payload, $i, $size), $codeSearch);
                } else {
                    $aPix["$code"] = substr($payload, $i, $size);
                }
            }
            $i += $size;
        }

        return $aPix;
    }
}
