<?php

/**
 * This file is part of PHPPresentation - A pure PHP library for reading and writing
 * presentations documents.
 *
 * PHPPresentation is free software distributed under the terms of the GNU Lesser
 * General Public License version 3 as published by the Free Software Foundation.
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code. For the full list of
 * contributors, visit https://github.com/PHPOffice/PHPPresentation/contributors.
 *
 * @see        https://github.com/PHPOffice/PHPPresentation
 *
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

declare(strict_types=1);

namespace PhpOffice\PhpPresentation\PowerPoint2019Objects\theme;

use PhpOffice\PhpPresentation\PowerPoint2019Objects\Base\PP2019Element;

/**
 * theme element
 */
class Theme extends PP2019Element
{
    protected $elementName = 'theme';
    protected $elementNamespace = 'a';

    protected $knownAttributes = [
                                  'name' => ['type' => 'string']
                                 ];

    protected $childNodeDefaultNamespace = 'a';
    protected $knownChildNodes  = [
                                   'themeElements'     => ['type' => 'ThemeElements'     ],
                                   'objectDefaults'    => ['type' => 'ObjectDefaults'    ],
                                   'extraClrSchemeLst' => ['type' => 'ExtraClrSchemeLst' ],
                                   'extLst'            => ['type' => 'ExtLst'            ],
                                  ];

    // constructor that prepares some additional fields 
    function __construct()
    {
        parent::__construct();
    }

    public function loadDefaultSettings(): void
    {
        $this->namespaces = [
                             "a" => "http://schemas.openxmlformats.org/drawingml/2006/main",
                            ];

        $this->attributes['showComments'] = "Office &#x30C6;&#x30FC;&#x30DE;";

// TODO
//        $this->childNodes['a:themeElements'] = new ThemeElements();
//        $this->childNodes['a:themeElements']->loadDefaultSettings();
    }

}