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
 * solidFill element
 */
class SolidFill extends PP2019Element
{
    protected $elementName = 'solidFill';
    protected $elementNamespace = 'a';

    protected $knownAttributes = [];

    protected $childNodeDefaultNamespace = 'a';
    protected $knownChildNodes  = [
                                   'schemeClr' => ['type' => 'SchemeClr'],
                                  ];

    // constructor that prepares some additional fields 
    function __construct()
    {
        parent::__construct();
    }

    public function loadDefaultSettings(): void
    {
        $this->namespaces = [];
        $this->attributes = [];
        $this->childNodes = [];;
    }

}