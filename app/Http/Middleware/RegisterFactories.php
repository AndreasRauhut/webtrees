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

namespace Fisharebest\Webtrees\Http\Middleware;

use Fisharebest\Webtrees\Application;
use Fisharebest\Webtrees\Cache;
use Fisharebest\Webtrees\Factories\FamilyFactory;
use Fisharebest\Webtrees\Factories\GedcomRecordFactory;
use Fisharebest\Webtrees\Factories\IndividualFactory;
use Fisharebest\Webtrees\Factories\MediaFactory;
use Fisharebest\Webtrees\Factories\NoteFactory;
use Fisharebest\Webtrees\Factories\RepositoryFactory;
use Fisharebest\Webtrees\Factories\SourceFactory;
use Fisharebest\Webtrees\Factories\SubmitterFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function app;

/**
 * Middleware to register various factory objects.
 */
class RegisterFactories implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $cache = app('cache.array');
        assert($cache instanceof Cache);

        $app = app();
        assert($app instanceof Application);

        $app->instance(FamilyFactory::class, new FamilyFactory($cache));
        $app->instance(GedcomRecordFactory::class, new GedcomRecordFactory($cache));
        $app->instance(IndividualFactory::class, new IndividualFactory($cache));
        $app->instance(MediaFactory::class, new MediaFactory($cache));
        $app->instance(NoteFactory::class, new NoteFactory($cache));
        $app->instance(RepositoryFactory::class, new RepositoryFactory($cache));
        $app->instance(SourceFactory::class, new SourceFactory($cache));
        $app->instance(SubmitterFactory::class, new SubmitterFactory($cache));

        return $handler->handle($request);
    }
}
