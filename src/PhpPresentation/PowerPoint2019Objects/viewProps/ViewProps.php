<?php


// TODO : define attributes and childnodes as an array ; // attribute values need to have some type (integer with min max checks or a set of known enums)
//        move most functions to a baseclass
//        add a __call method for dynamic getters and setters

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

namespace PhpOffice\PhpPresentation\PowerPoint2019Objects\viewProps;

use PhpOffice\PhpPresentation\PowerPoint2019Objects\Base\PP2019Element;

/**
 * viewProps element
 */
class ViewProps extends PP2019Element
{
    protected $elementName = 'viewPr';
    protected $elementNamespace = 'p';

    protected $knownAttributes = [
                                  'lastView'     => ['type' => 'enum', 'enumType' => 'ViewPropsLastView'],
                                  'showComments' => ['type' => 'integer']
                                 ];

    protected $childNodeDefaultNamespace = 'p';
    protected $knownChildNodes  = [
                                   'normalViewPr'    => ['type' => 'NormalViewPr'   ],
                                   'slideViewPr'     => ['type' => 'SlideViewPr'    ],
                                   'outlineViewPr'   => ['type' => 'OutlineViewPr'  ],
                                   'notesTextViewPr' => ['type' => 'NotesTextViewPr'],
                                   'gridSpacing'     => ['type' => 'GridSpacing'    ],
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
                             "p" => "http://schemas.openxmlformats.org/presentationml/2006/main",
                             "r" => "http://schemas.openxmlformats.org/officeDocument/2006/relationships"
                            ];

        $this->attributes['lastView']     = "sldView";
        $this->attributes['showComments'] = "0";

        $this->childNodes['p:slideViewPr'] = new SlideViewPr();
        $this->childNodes['p:slideViewPr']->loadDefaultSettings();
    }

}