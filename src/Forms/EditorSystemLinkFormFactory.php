<?php

namespace Fromholdio\SystemLinks\Forms;

use Fromholdio\SystemLinks\SystemLinks;
use SilverStripe\Admin\Forms\LinkFormFactory;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextField;

class EditorSystemLinkFormFactory extends LinkFormFactory
{
    protected function getFormFields($controller, $name, $context)
    {
        $fields = FieldList::create([
            DropdownField::create(
                'SystemLinkKey',
                _t(__CLASS__ . '.SYSTEMLINKKEY', 'Preset target'),
                SystemLinks::get_map()
            ),
            TextField::create(
                'Description',
                _t(__CLASS__.'.LINKDESCR', 'Link description')
            ),
            CheckboxField::create(
                'TargetBlank',
                _t(__CLASS__.'.LINKOPENNEWWIN', 'Open in new window/tab')
            ),
        ]);

        if ($context['RequireLinkText']) {
            $fields->insertAfter('SystemLinkKey', TextField::create('Text', _t(__CLASS__ . '.LINKTEXT', 'Link text')));
        }

        $this->extend('updateFormFields', $fields, $controller, $name, $context);

        return $fields;
    }

    protected function getValidator($controller, $name, $context)
    {
        if ($context['RequireLinkText']) {
            return RequiredFields::create('Text');
        }

        return null;
    }
}
