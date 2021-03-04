<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace Nines\MediaBundle\Entity;

interface LinkableInterface {
    public function getLinks();

    public function setLinks($links);

    public function addLink(Link $link);

    public function removeLink(Link $link);
}
