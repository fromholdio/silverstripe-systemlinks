<?php

namespace Fromholdio\SystemLinks\Extensions;

use Fromholdio\SystemLinks\Forms\EditorSystemLinkFormFactory;
use SilverStripe\Admin\ModalController;
use SilverStripe\Core\Extension;

class SystemLinkModalExtension extends Extension
{
    private static $allowed_actions = [
        'EditorSystemLink',
    ];

    /**
     * @return ModalController
     */
    public function getOwner()
    {
        /** @var ModalController $owner */
        $owner = $this->owner;
        return $owner;
    }

    public function EditorSystemLink()
    {
        $showLinkText = $this->getOwner()->getRequest()->getVar('requireLinkText');
        $factory = EditorSystemLinkFormFactory::singleton();
        return $factory->getForm(
            $this->getOwner(),
            "EditorSystemLink",
            ['RequireLinkText' => isset($showLinkText)]
        );
    }
}
