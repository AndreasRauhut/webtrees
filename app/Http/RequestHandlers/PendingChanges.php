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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\Factories\FamilyFactory;
use Fisharebest\Webtrees\Factories\GedcomRecordFactory;
use Fisharebest\Webtrees\Factories\IndividualFactory;
use Fisharebest\Webtrees\Factories\MediaFactory;
use Fisharebest\Webtrees\Factories\NoteFactory;
use Fisharebest\Webtrees\Factories\RepositoryFactory;
use Fisharebest\Webtrees\Factories\SourceFactory;
use Fisharebest\Webtrees\Factories\SubmitterFactory;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function key;
use function preg_match;
use function reset;
use function route;

/**
 * Show all pending changes.
 */
class PendingChanges implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /** @var FamilyFactory */
    private $family_factory;

    /** @var GedcomRecordFactory */
    private $gedcom_record_factory;

    /** @var IndividualFactory */
    private $individual_factory;

    /** @var MediaFactory */
    private $media_factory;

    /** @var NoteFactory */
    private $note_factory;

    /** @var RepositoryFactory */
    private $repository_factory;

    /** @var SourceFactory */
    private $source_factory;

    /** @var SubmitterFactory */
    private $submitter_factory;

    /** @var TreeService */
    private $tree_service;

    /**
     * @param FamilyFactory       $family_factory
     * @param GedcomRecordFactory $gedcom_record_factory
     * @param IndividualFactory   $individual_factory
     * @param MediaFactory        $media_factory
     * @param NoteFactory         $note_factory
     * @param RepositoryFactory   $repository_factory
     * @param SourceFactory       $source_factory
     * @param SubmitterFactory    $submitter_factory
     * @param TreeService         $tree_service
     */
    public function __construct(
        FamilyFactory $family_factory,
        GedcomRecordFactory $gedcom_record_factory,
        IndividualFactory $individual_factory,
        MediaFactory $media_factory,
        NoteFactory $note_factory,
        RepositoryFactory $repository_factory,
        SourceFactory $source_factory,
        SubmitterFactory $submitter_factory,
        TreeService $tree_service
    ) {
        $this->family_factory        = $family_factory;
        $this->gedcom_record_factory = $gedcom_record_factory;
        $this->individual_factory    = $individual_factory;
        $this->media_factory         = $media_factory;
        $this->note_factory          = $note_factory;
        $this->repository_factory    = $repository_factory;
        $this->source_factory        = $source_factory;
        $this->submitter_factory     = $submitter_factory;
        $this->tree_service          = $tree_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $url = $request->getQueryParams()['url'] ?? route(TreePage::class, ['tree' => $tree->name()]);

        $rows = DB::table('change')
            ->join('user', 'user.user_id', '=', 'change.user_id')
            ->join('gedcom', 'gedcom.gedcom_id', '=', 'change.gedcom_id')
            ->where('status', '=', 'pending')
            ->orderBy('change.gedcom_id')
            ->orderBy('change.xref')
            ->orderBy('change.change_id')
            ->select(['change.*', 'user.user_name', 'user.real_name', 'gedcom_name'])
            ->get();

        $changes = [];
        foreach ($rows as $row) {
            $row->change_time = Carbon::make($row->change_time);

            $change_tree = $this->tree_service->all()->get($row->gedcom_name);

            preg_match('/^0 (?:@' . Gedcom::REGEX_XREF . '@ )?(' . Gedcom::REGEX_TAG . ')/', $row->old_gedcom . $row->new_gedcom, $match);

            switch ($match[1]) {
                case Individual::RECORD_TYPE:
                    $row->record = $this->individual_factory->new($row->xref, $row->old_gedcom, $row->new_gedcom, $change_tree);
                    break;

                case Family::RECORD_TYPE:
                    $row->record = $this->family_factory->new($row->xref, $row->old_gedcom, $row->new_gedcom, $change_tree);
                    break;

                case Source::RECORD_TYPE:
                    $row->record = $this->source_factory->new($row->xref, $row->old_gedcom, $row->new_gedcom, $change_tree);
                    break;

                case Repository::RECORD_TYPE:
                    $row->record = $this->repository_factory->new($row->xref, $row->old_gedcom, $row->new_gedcom, $change_tree);
                    break;

                case Media::RECORD_TYPE:
                    $row->record = $this->media_factory->new($row->xref, $row->old_gedcom, $row->new_gedcom, $change_tree);
                    break;

                case Note::RECORD_TYPE:
                    $row->record = $this->note_factory->new($row->xref, $row->old_gedcom, $row->new_gedcom, $change_tree);
                    break;

                case Submitter::RECORD_TYPE:
                    $row->record = $this->submitter_factory->new($row->xref, $row->old_gedcom, $row->new_gedcom, $change_tree);
                    break;

                default:
                    $row->record = $this->gedcom_record_factory->new($row->xref, $row->old_gedcom, $row->new_gedcom, $change_tree);
                    break;
            }

            $changes[$row->gedcom_name][$row->xref][] = $row;
        }

        $title = I18N::translate('Pending changes');

        // If the current tree has changes, activate that tab.  Otherwise activate the first tab.
        if (($changes[$tree->id()] ?? []) === []) {
            reset($changes);
            $active_tree_name = key($changes);
        } else {
            $active_tree_name = $tree->name();
        }

        return $this->viewResponse('pending-changes-page', [
            'active_tree_name' => $active_tree_name,
            'changes'          => $changes,
            'title'            => $title,
            'tree'             => $tree,
            'trees'            => $this->tree_service->all(),
            'url'              => $url,
        ]);
    }
}
