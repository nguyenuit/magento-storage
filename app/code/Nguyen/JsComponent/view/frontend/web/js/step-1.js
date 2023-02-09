define([
    'uiComponent',
    "Nguyen_JsComponent/js/steps-state",
    "Nguyen_JsComponent/js/multi-steps",
    'Magento_Ui/js/modal/modal',
    'jquery',
    'underscore',
    'ko'
], function (Component, stepsState, multiSteps, uiModal, $, _, ko) {
    'use strict';

    return Component.extend({
        defaults: {
            imports: {
                categoryIds: '${ $.parentName }:options.categoryIds'
            }
        },
        initialize: function () {
            this._super();
        }
    });
});
