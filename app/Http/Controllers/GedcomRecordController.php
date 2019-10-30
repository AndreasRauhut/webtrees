<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function assert;
use function is_string;
use function redirect;

/**
 * Controller for the gedcom record page.
 */
class GedcomRecordController extends AbstractBaseController
{
    /**
     * Show a gedcom record's page.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function show(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref = $request->getAttribute('xref');
        assert(is_string($xref));

        $record = GedcomRecord::getInstance($xref, $tree);
        $record = Auth::checkRecordAccess($record);

        if ($request->getAttribute('slug') !== $record->slug()) {
            return redirect($record->url());
        }

        if ($this->hasCustomPage($record)) {
            return redirect($record->url());
        }

        return $this->viewResponse('gedcom-record-page', [
            'facts'         => $record->facts(),
            'families'      => $record->linkedFamilies($record::RECORD_TYPE),
            'individuals'   => $record->linkedIndividuals($record::RECORD_TYPE),
            'meta_robots'   => 'index,follow',
            'notes'         => $record->linkedNotes($record::RECORD_TYPE),
            'media_objects' => $record->linkedMedia($record::RECORD_TYPE),
            'record'        => $record,
            'sources'       => $record->linkedSources($record::RECORD_TYPE),
            'title'         => $record->fullName(),
        ]);
    }

    /**
     * Is there a better place to display this record?
     *
     * @param GedcomRecord $record
     *
     * @return bool
     */
    private function hasCustomPage(GedcomRecord $record): bool
    {
        return
            $record instanceof Individual ||
            $record instanceof Family ||
            $record instanceof Source ||
            $record instanceof Repository ||
            $record instanceof Note ||
            $record instanceof Media;
    }
}
