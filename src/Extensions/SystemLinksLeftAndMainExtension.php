<?php

namespace Fromholdio\SystemLinks\Extensions;

use SilverStripe\Core\Extension;

class SystemLinksLeftAndMainExtension extends Extension
{
    public function updateClientConfig(&$config)
    {
        $config['form']['EditorSystemLink'] = [
            'schemaUrl' => $this->getOwner()->Link('methodSchema/Modals/EditorSystemLink'),
        ];
    }
}
