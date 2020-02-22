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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Factories\SubmitterFactory;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_search;
use function assert;
use function is_string;
use function redirect;

use const PHP_INT_MAX;

/**
 * Show a submitter's page.
 */
class SubmitterPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    // Show the submitter's facts in this order:
    private const FACT_ORDER = [
        1 => 'NAME',
        'ADDR',
        'PHON',
        'EMAIL',
        'WWW',
        'LANG',
        'OBJE',
        'RFN',
        'RIN',
        'NOTE',
        'CHAN',
    ];

    /** @var SubmitterFactory */
    private $submitter_factory;

    /**
     * SubmitterPage constructor.
     *
     * @param SubmitterFactory $submitter_factory
     */
    private function __construct(SubmitterFactory $submitter_factory)
    {
        $this->submitter_factory = $submitter_factory;
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

        $xref = $request->getAttribute('xref');
        assert(is_string($xref));

        $submitter = $this->submitter_factory->make($xref, $tree);
        $submitter = Auth::checkSubmitterAccess($submitter, false);

        // Redirect to correct xref/slug
        if ($submitter->xref() !== $xref || $request->getAttribute('slug') !== $submitter->slug()) {
            return redirect($submitter->url(), StatusCodeInterface::STATUS_MOVED_PERMANENTLY);
        }

        return $this->viewResponse('submitter-page', [
            'facts'            => $this->facts($submitter),
            'submitter'        => $submitter,
            'families'         => $submitter->linkedFamilies('SUBM'),
            'individuals'      => $submitter->linkedIndividuals('SUBM'),
            'meta_description' => '',
            'meta_robots'      => 'index,follow',
            'title'            => $submitter->fullName(),
            'tree'             => $tree,
        ]);
    }

    /**
     * @param Submitter $record
     *
     * @return Collection<Fact>
     */
    private function facts(Submitter $record): Collection
    {
        return $record->facts()
            ->sort(static function (Fact $x, Fact $y): int {
                $sort_x = array_search($x->getTag(), self::FACT_ORDER, true) ?: PHP_INT_MAX;
                $sort_y = array_search($y->getTag(), self::FACT_ORDER, true) ?: PHP_INT_MAX;

                return $sort_x <=> $sort_y;
            });
    }
}
