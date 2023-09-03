/* global tinymce, window */
import i18n from 'i18n';
import TinyMCEActionRegistrar from 'lib/TinyMCEActionRegistrar';
import React from 'react';
import ReactDOM from 'react-dom';
import jQuery from 'jquery';
import {createInsertLinkModal} from 'containers/InsertLinkModal/InsertLinkModal';
import {loadComponent} from 'lib/Injector';
import ShortcodeSerialiser from 'lib/ShortcodeSerialiser';
import 'lang/en.js';

const commandName = 'sslinksystem';

// Link to phone number
TinyMCEActionRegistrar
    .addAction('sslink', {
        text: i18n._t('Admin.LINKLABEL_SYSTEMLINK', 'Link to preset target'),
        // eslint-disable-next-line no-console
        onclick: (editor) => editor.execCommand(commandName),
        priority: 52,
    })
    .addCommandWithUrlTest(commandName, /^\[system_link.+]$/);

const plugin = {
    init(editor) {
        editor.addCommand(commandName, () => {
            const field = window.jQuery(`#${editor.id}`).entwine('ss');

            field.openLinkSystemDialog();
        });
    },
};

const modalId = 'insert-link__dialog-wrapper--system';
const sectionConfigKey = 'SilverStripe\\Admin\\LeftAndMain';
const formName = 'EditorSystemLink';

const InsertLinkSystemModal = loadComponent(createInsertLinkModal(sectionConfigKey, formName));

jQuery.entwine('ss', ($) => {
    $('textarea.htmleditor').entwine({
        openLinkSystemDialog() {
            let dialog = $(`#${modalId}`);

            if (!dialog.length) {
                dialog = $(`<div id="${modalId}" />`);
                $('body').append(dialog);
            }
            dialog.addClass('insert-link__dialog-wrapper');

            dialog.setElement(this);
            dialog.open();
        },
    });

    /**
     * Assumes that $('.insert-link__dialog-wrapper').entwine({}); is defined for shared functions
     */
    $(`#${modalId}`).entwine({
        renderModal(isOpen) {
            const handleHide = () => this.close();
            const handleInsert = (...args) => this.handleInsert(...args);
            const attrs = this.getOriginalAttributes();
            const selection = tinymce.activeEditor.selection;
            const selectionContent = selection.getContent() || '';
            const tagName = selection.getNode().tagName;
            const requireLinkText = false;
            // const requireLinkText = tagName !== 'A' && selectionContent.trim() === '';

            // create/update the react component
            ReactDOM.render(
                <InsertLinkSystemModal
                    isOpen={isOpen}
                    onInsert={handleInsert}
                    onClosed={handleHide}
                    title={i18n._t('Admin.LINK_SYSTEMLINK', 'Insert preset link')}
                    bodyClassName="modal__dialog"
                    className="insert-link__dialog-wrapper--system"
                    fileAttributes={attrs}
                    identifier="Admin.InsertLinkSystemModal"
                    requireLinkText={requireLinkText}
                />,
                this[0]
            );
        },

        getOriginalAttributes() {
            const editor = this.getElement().getEditor();
            const node = $(editor.getSelectedNode());

            // Get href
            const hrefParts = (node.attr('href') || '').split('#');
            if (!hrefParts[0]) {
                return {};
            }

            const shortcode = ShortcodeSerialiser.match('system_link', false, hrefParts[0]);
            if (!shortcode) {
                return {};
            }

            return {
                SystemLinkKey: shortcode.properties.key ? shortcode.properties.key : '',
                Description: node.attr('title'),
                TargetBlank: !!node.attr('target')
            };
        },

        buildAttributes(data) {
            const shortcode = ShortcodeSerialiser.serialise({
                name: 'system_link',
                properties: { key: data.SystemLinkKey },
            }, true);

            const href = `${shortcode}`;

            return {
                href,
                target: data.TargetBlank ? '_blank' : '',
                title: data.Description,
            };
        },
    });
});

// Adds the plugin class to the list of available TinyMCE plugins
tinymce.PluginManager.add(commandName, (editor) => plugin.init(editor));
export default plugin;
